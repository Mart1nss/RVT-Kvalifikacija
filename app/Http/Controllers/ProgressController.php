<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class ProgressController extends Controller
{
    /**
     * Parāda lietotāja progresa statistiku
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Ielādē attiecības, kas nepieciešamas statistikai, ja tās vēl nav ielādētas
        if (!$user->relationLoaded('reviews')) {
            $user->load(['reviews', 'favorites', 'notes', 'forums', 'forumReplies', 'tickets', 'readBooks']);
        }
        
        // Pamata statistika
        $stats = [
            'books_read' => $user->readBooks->count(),
            'favorites' => $user->favorites->count(),
            'notes' => $user->notes->count(),
            'reviews' => $user->reviews->count(),
            'forums' => $user->forums->count() + $user->forumReplies->count(),
            'tickets' => $user->tickets->count(),
        ];
        
        // Konta informācija
        $createdAt = $user->created_at;
        $accountAge = $this->calculateAccountAge($createdAt);
        
        // Aprēķina populārākos žanrus, ja pieejami
        $topGenres = $this->getTopGenres($user);
        
        $loginHours = $this->getLoginHourDistribution($user);
        
        // Iegūst aktīvāko stundu UTC laikā - tiks konvertēta skatā
        $typicalActiveTimeUTC = $this->getMostActiveHour($user);
        
        return view('myProgress', compact(
            'stats', 
            'createdAt', 
            'accountAge', 
            'topGenres', 
            'typicalActiveTimeUTC',
            'loginHours'
        ));
    }
    
    /**
     * Aprēķina konta vecumu cilvēkam lasāmā formātā
     *
     * @param Carbon $createdAt
     * @return string
     */
    private function calculateAccountAge($createdAt)
    {
        $now = Carbon::now();
        $years = $now->diffInYears($createdAt);
        $months = $now->diffInMonths($createdAt) % 12;
        $days = $now->diffInDays($createdAt) % 30;
        
        if ($years > 0) {
            return $years . ' ' . ($years == 1 ? 'year' : 'years');
        }
        else if ($months > 0) {
            return $months . ' ' . ($months == 1 ? 'month' : 'months');
        }
        else {
            $days = max(1, $days);
            return $days . ' ' . ($days == 1 ? 'day' : 'days');
        }
    }
    
    /**
     * Iegūst populārākos 3 žanrus, pamatojoties uz lietotāja izlasītajām grāmatām un atsauksmēm
     *
     * @param User $user
     * @return array
     */
    private function getTopGenres($user)
    {
        $user->load('readBooks.product.category', 'reviews.product.category');
        
        $genreCounts = [];

        foreach ($user->readBooks as $readBook) {
            if ($readBook->product && $readBook->product->category) {
                $categoryName = $readBook->product->category->name;
                if (!isset($genreCounts[$categoryName])) {
                    $genreCounts[$categoryName] = 0;
                }
                $genreCounts[$categoryName]++;
            }
        }
        
        arsort($genreCounts);
        
        // Iegūst populārākos 3 žanrus ar to skaitu
        $topGenres = [];
        $position = 1;
        foreach ($genreCounts as $genre => $count) {
            if ($position > 3) break;
            $topGenres[] = [
                'position' => $position,
                'name' => $genre,
                'count' => round($count, 0)
            ];
            $position++;
        }
        
        return $topGenres;
    }
    
    /**
     * Iegūst aktīvāko stundu no pieteikšanās vēstures (UTC laikā)
     *
     * @param User $user
     * @return int|null
     */
    private function getMostActiveHour($user)
    {
        $loginData = DB::table('user_logins')
            ->where('user_id', $user->id)
            ->select('hour_of_day', DB::raw('count(*) as login_count'))
            ->groupBy('hour_of_day')
            ->orderBy('login_count', 'desc')
            ->first();
            
        return $loginData ? $loginData->hour_of_day : null;
    }
    
    /**
     * Iegūst pieteikšanās stundu sadalījumu visiem lietotāja pieteikumiem
     *
     * @param User $user
     * @return array
     */
    private function getLoginHourDistribution($user)
    {
        $distribution = DB::table('user_logins')
            ->where('user_id', $user->id)
            ->select('hour_of_day', DB::raw('count(*) as login_count'))
            ->groupBy('hour_of_day')
            ->orderBy('hour_of_day')
            ->pluck('login_count', 'hour_of_day')
            ->toArray();
            
        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[$i] = $distribution[$i] ?? 0;
        }
        
        return $result;
    }
}
