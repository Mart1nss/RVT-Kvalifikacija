<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if a user is banned and handle the ban for API routes.
 * 
 * This middleware is applied to all API routes through the api middleware group in Kernel.php.
 * It checks if the authenticated user is banned and if so, returns a JSON response with an error message.
 * Unlike the web middleware, it doesn't log the user out since API requests are stateless.
 */
class CheckBannedApi
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
            // Get the ban reason if available to include in the response
            $banReason = Auth::user()->getBanReason();
            $message = 'Your account has been banned.';
            
            if ($banReason) {
                $message .= ' Reason: ' . $banReason;
            }
            
            // Return JSON response for API routes with a 403 Forbidden status code
            // This informs the client that the user is banned and cannot access the API
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'account_banned'
            ], 403);
        }
        
        // If the user is not banned, continue with the request
        return $next($request);
    }
}
