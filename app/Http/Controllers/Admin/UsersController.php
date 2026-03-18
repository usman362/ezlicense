<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Status filter
        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function show(User $user)
    {
        $user->load(['instructorProfile', 'learnerWallet']);

        $bookingStats = [
            'total'     => $user->isLearner() ? $user->learnerBookings()->count() : ($user->isInstructor() ? $user->instructorBookings()->count() : 0),
            'completed' => $user->isLearner() ? $user->learnerBookings()->where('status', 'completed')->count() : ($user->isInstructor() ? $user->instructorBookings()->where('status', 'completed')->count() : 0),
        ];

        return response()->json([
            'user' => $user,
            'booking_stats' => $bookingStats,
        ]);
    }

    public function toggleActive(Request $request, User $user)
    {
        $newStatus = ! $user->is_active;

        // If deactivating, require a reason
        if (! $newStatus) {
            $request->validate(['reason' => 'required|string|max:500']);
            $user->deactivation_reason = $request->input('reason');
            $user->deactivated_at = now();
        } else {
            $user->deactivation_reason = null;
            $user->deactivated_at = null;
        }

        $user->is_active = $newStatus;
        $user->save();

        return response()->json([
            'message' => $user->name . ' has been ' . ($user->is_active ? 'activated' : 'deactivated') . '.',
            'is_active' => $user->is_active,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:learner,instructor,admin']);
        $user->role = $request->input('role');
        $user->save();

        return redirect()->back()->with('message', $user->name . '\'s role updated to ' . ucfirst($user->role) . '.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete admin users.');
        }

        $name = $user->name;
        $user->is_active = false;
        $user->save();

        return redirect()->back()->with('message', $name . ' has been deactivated.');
    }
}
