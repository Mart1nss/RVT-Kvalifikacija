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
     * Display user progress statistics
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Load relationships needed for stats if not already loaded
        if (!$user->relationLoaded('reviews')) {
            $user->load(['reviews', 'favorites', 'notes', 'forums', 'forumReplies', 'tickets', 'readBooks']);
        }
        
        // Basic stats
        $stats = [
            'books_read' => $user->readBooks->count(), // Now using actual read books count
            'favorites' => $user->favorites->count(),
            'notes' => $user->notes->count(),
            'reviews' => $user->reviews->count(),
            'forums' => $user->forums->count() + $user->forumReplies->count(), // Total forum activity
            'tickets' => $user->tickets->count(),
        ];
        
        // Account information
        $createdAt = $user->created_at;
        $accountAge = $this->calculateAccountAge($createdAt);
        
        // Calculate top genres if available
        $topGenres = $this->getTopGenres($user);
        
        // Get login hour distribution for the chart
        $loginHours = $this->getLoginHourDistribution($user);
        
        // Get most active hour in UTC - will be converted in the view
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
     * Calculate account age in a human-readable format
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
     * Get top 3 genres based on user's read books and reviews
     *
     * @param User $user
     * @return array
     */
    private function getTopGenres($user)
    {
        // Load user's read books and reviews with product and category information
        $user->load('readBooks.product.category', 'reviews.product.category');
        
        // Count genres from read books and reviews
        $genreCounts = [];
        
        // Process read books - these are explicitly marked as read
        foreach ($user->readBooks as $readBook) {
            if ($readBook->product && $readBook->product->category) {
                $categoryName = $readBook->product->category->name;
                if (!isset($genreCounts[$categoryName])) {
                    $genreCounts[$categoryName] = 0;
                }
                $genreCounts[$categoryName]++;
            }
        }
        
        // Sort by count (descending)
        arsort($genreCounts);
        
        // Get top 3 genres with their counts
        $topGenres = [];
        $position = 1;
        foreach ($genreCounts as $genre => $count) {
            if ($position > 3) break;
            $topGenres[] = [
                'position' => $position,
                'name' => $genre,
                'count' => round($count, 0) // Display as whole numbers only
            ];
            $position++;
        }
        
        return $topGenres;
    }
    
    /**
     * Get most active hour from login history (in UTC)
     *
     * @param User $user
     * @return int|null
     */
    private function getMostActiveHour($user)
    {
        // Get login data from all available records
        $loginData = DB::table('user_logins')
            ->where('user_id', $user->id)
            ->select('hour_of_day', DB::raw('count(*) as login_count'))
            ->groupBy('hour_of_day')
            ->orderBy('login_count', 'desc')
            ->first();
            
        return $loginData ? $loginData->hour_of_day : null;
    }
    
    /**
     * Get login hour distribution for all user logins
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
            
        // Fill in missing hours with zero
        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[$i] = $distribution[$i] ?? 0;
        }
        
        return $result;
    }
}
