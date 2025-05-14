<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserLogin;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        
        // Record login time in UTC
        $now = Carbon::now('UTC');
        UserLogin::create([
            'user_id' => $user->id,
            'hour_of_day' => $now->hour,
        ]);
        
        // Keep only the latest 7 login records
        $this->trimLoginRecords($user->id);
        
        if ($user->userPreferences()->count() === 0) {
            return redirect()->route('preferences.show');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    /**
     * Keep only the latest 7 login records for a user
     *
     * @param int $userId
     * @return void
     */
    private function trimLoginRecords($userId)
    {
        // Get login IDs to keep (the latest 7)
        $loginIdsToKeep = UserLogin::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->pluck('id');
            
        // Delete all older login records
        UserLogin::where('user_id', $userId)
            ->whereNotIn('id', $loginIdsToKeep)
            ->delete();
    }
}
