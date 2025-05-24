<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Parāda paroles atiestatīšanas saites pieprasījuma skatu.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Apstrādā ienākošo paroles atiestatīšanas saites pieprasījumu.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Mēs nosūtīsim paroles atiestatīšanas saiti šim lietotājam. Kad būsim mēģinājuši
        // nosūtīt saiti, mēs pārbaudīsim atbildi, lai redzētu ziņojumu, kas
        // jāparāda lietotājam. Visbeidzot, mēs nosūtīsim atbilstošu atbildi.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
