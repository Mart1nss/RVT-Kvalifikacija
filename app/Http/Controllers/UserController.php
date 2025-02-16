<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AuditLogService;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->filled('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Apply user type filter
        if ($request->filled('filter') && in_array($request->filter, ['admin', 'user'])) {
            $query->where('usertype', $request->filter);
        }

        // Apply sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nameAZ':
                $query->orderBy('name', 'asc');
                break;
            case 'nameZA':
                $query->orderBy('name', 'desc');
                break;
            case 'lastOnline':
                $query->orderBy('last_online', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('components.user-table', compact('users'))->render(),
                'total' => $users->total()
            ]);
        }

        return view('userManage', compact('users'));
    }

    public function updateUserType(Request $request, User $user)
    {
        $request->validate([
            'usertype' => 'required|in:user,admin',
        ]);

        $oldRole = $user->usertype;
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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('user.manage')->with('success', 'User deleted successfully.');
    }
}
