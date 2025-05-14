<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if a user is banned and handle the ban for web routes.
 * 
 * This middleware is applied to all web routes through the web middleware group in Kernel.php.
 * It checks if the authenticated user is banned and if so, logs them out and redirects to the login page.
 */
class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in and banned
        if (Auth::check() && Auth::user()->isBanned()) {
            // Get the ban reason if available to display to the user
            $banReason = Auth::user()->getBanReason();
            $message = 'Your account has been banned.';
            
            if ($banReason) {
                $message .= ' Reason: ' . $banReason;
            }
            
            // Log the user out - this removes the authentication
            Auth::logout();
            
            // Invalidate the session - this clears all session data
            Session::flush();
            
            // Regenerate the CSRF token for security
            $request->session()->regenerateToken();
            
            // Redirect to login with error message
            // The error message will be displayed on the login page
            return redirect()->route('login')->with('error', $message);
        }
        
        // If the user is not banned, continue with the request
        return $next($request);
    }
}
