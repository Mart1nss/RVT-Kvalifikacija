<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter; // Added
use Illuminate\Validation\ValidationException; // Added

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot check
        if (!empty($request->input('middle_name'))) {
            return redirect()->route('login')
                             ->with('success', 'Registration successful! Please login to continue.');
        }

        // Rate limiting
        $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();
        $maxAttempts = 5;
        $decayInMinutes = 1;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        RateLimiter::hit($throttleKey, $decayInMinutes * 60);

        $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:10',
                'regex:/^[a-zA-Z0-9]+$/', // Only letters and numbers, no spaces or symbols
                'unique:'.User::class
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Proper email format with @ and domain
                'unique:' . User::class
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.regex' => 'NAME CAN ONLY CONTAIN LETTERS AND NUMBERS (NO SPACES OR SYMBOLS)',
            'name.min' => 'NAME MUST BE AT LEAST 3 CHARACTERS',
            'name.max' => 'NAME CANNOT EXCEED 10 CHARACTERS',
            'name.unique' => 'THIS USERNAME IS ALREADY TAKEN.',
            'email.regex' => 'PLEASE ENTER A VALID EMAIL ADDRESS',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        RateLimiter::clear($throttleKey);

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please login to continue.')
            ->with('registered_email', $request->email);
    }
}
