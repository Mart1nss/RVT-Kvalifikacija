<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import facade
use App\Models\User;
use App\Services\AuditLogService;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('last_online', 'desc')->get();
        return view('userManage', compact('users'));
    }

    public function updateUserType(Request $request, User $user)
    {
        $request->validate([
            'usertype' => 'required|in:user,admin',
        ]);

        $oldRole = $user->usertype; // Store the old role before changing it
        $user->usertype = $request->usertype;
        $user->save();

        AuditLogService::log(
            "Changed user role for",
            "user",
            "Changed {$user->name}'s role from '{$oldRole}' to '{$request->usertype}'",
            $user->id,
            $user->name
        );

        return redirect()->route('user.manage')->with('success', 'User type updated successfully.');
    }

    public function update(Request $request, User $user)
    {
        if (Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->update($request->only(['name', 'email']));

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        AuditLogService::log(
            "Deleted user",
            "user",
            "Deleted user account",
            $user->id,
            $user->name
        );

        $user->delete();
        return redirect()->route('user.manage')->with('success', 'User deleted successfully.');
    }
}
