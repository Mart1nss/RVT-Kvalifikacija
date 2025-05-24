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
     * Parāda pieteikšanās skatu.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Apstrādā ienākošo autentifikācijas pieprasījumu.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        
        // Reģistrē pieteikšanās laiku UTC
        $now = Carbon::now('UTC');
        UserLogin::create([
            'user_id' => $user->id,
            'hour_of_day' => $now->hour,
        ]);
        
        // Saglabā tikai pēdējos 7 pieteikšanās ierakstus
        $this->trimLoginRecords($user->id);
        
        if ($user->userPreferences()->count() === 0) {
            return redirect()->route('preferences.show');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Iznīcina autentificētu sesiju.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    /**
     * Saglabā tikai pēdējos 7 pieteikšanās ierakstus lietotājam
     *
     * @param int $userId
     * @return void
     */
    private function trimLoginRecords($userId)
    {
        // Iegūst saglabājamos pieteikšanās ID (pēdējos 7)
        $loginIdsToKeep = UserLogin::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->pluck('id');
            
        // Dzēš visus vecākos pieteikšanās ierakstus
        UserLogin::where('user_id', $userId)
            ->whereNotIn('id', $loginIdsToKeep)
            ->delete();
    }
}
