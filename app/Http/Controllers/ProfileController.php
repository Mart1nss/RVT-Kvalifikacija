<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Profila kontrolieris
 * 
 * Šis kontrolieris apstrādā lietotāja profila darbības - profila skatīšanu, 
 * rediģēšanu, atjaunināšanu un dzēšanu.
 */
class ProfileController extends Controller
{
    /**
     * Parāda lietotāja profila rediģēšanas skatu.
     *
     * @param Request
     * @return View
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atjaunina lietotāja profila informāciju.
     *
     * @param ProfileUpdateRequest
     * @return RedirectResponse 
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Dzēš lietotāja kontu un saistītos datus.
     * 
     * Pirms dzēšanas tiek verificēta lietotāja parole. Pēc dzēšanas lietotājs tiek
     * izrakstīts no sistēmas un notiek sesijas datu attīrīšana.
     *
     * @param Request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        $user->notes()->delete();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Account deleted successfully');
    }
}
