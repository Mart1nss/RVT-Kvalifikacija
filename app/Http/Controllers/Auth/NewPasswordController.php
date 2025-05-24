<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Parāda paroles atiestatīšanas skatu.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Apstrādā ienākošo jaunās paroles pieprasījumu.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Šeit mēs mēģināsim atiestatīt lietotāja paroli. Ja tas būs veiksmīgi, mēs
        // atjaunināsim paroli faktiskajā lietotāja modelī un saglabāsim to
        // datubāzē. Pretējā gadījumā mēs analizēsim kļūdu un atgriezīsim atbildi.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Ja parole tika veiksmīgi atiestatīta, mēs pārvirzīsim lietotāju atpakaļ uz
        // lietojumprogrammas sākuma autentificēto skatu. Ja rodas kļūda, mēs varam
        // pārvirzīt viņus atpakaļ uz vietu, no kurienes viņi nāca, ar kļūdas ziņojumu.
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')
                ->with('success', 'Your password has been successfully reset!')
                ->with('registered_email', $request->email)
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
