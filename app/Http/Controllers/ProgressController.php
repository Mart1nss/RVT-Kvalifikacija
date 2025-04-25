<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Get authenticated user with all needed relationships
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Load relationships needed for stats if not already loaded
        if (!$user->relationLoaded('reviews')) {
            $user->load(['reviews', 'favorites', 'notes', 'forums', 'forumReplies', 'tickets']);
        }
        
        // Basic stats
        $stats = [
            'books_read' => $user->reviews->count(), // Assuming reviews indicate books read
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
        
        // TODO: Implement typical active time calculation
        // This would require tracking user login times
        // $typicalActiveTime = $this->calculateTypicalActiveTime($user);
        
        return view('myProgress', compact('stats', 'createdAt', 'accountAge', 'topGenres'));
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
        
        if ($years > 0) {
            return $years . ' ' . ($years == 1 ? 'year' : 'years');
        } else {
            return $months . ' ' . ($months == 1 ? 'month' : 'months');
        }
    }
    
    /**
     * Get top 3 genres based on user's read books
     *
     * @param User $user
     * @return array
     */
    private function getTopGenres($user)
    {
        // Load user's reviews with book and category information
        $user->load('reviews.product.category');
        
        // Count genres from read books
        $genreCounts = [];
        foreach ($user->reviews as $review) {
            if ($review->product && $review->product->category) {
                $categoryName = $review->product->category->name;
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
                'count' => $count
            ];
            $position++;
        }
        
        return $topGenres;
    }
    
    /**
     * TODO: Future implementation to calculate typical active time
     * Would require tracking login/activity times in a new table
     *
     * @param User $user
     * @return string
     */
    private function calculateTypicalActiveTime($user)
    {
        // This would analyze user login/activity patterns
        // and return the most common active time period
        // For now, return a placeholder
        return '21:00 - 22:00';
    }
}
