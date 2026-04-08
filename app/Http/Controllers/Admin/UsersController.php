<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorComplaint;
use App\Models\User;
use App\Models\UserAdminNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Legacy JSON endpoint (used by AJAX-based quick-view modals).
     */
    public function showJson(User $user)
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

    /**
     * Full admin detail page for a user (learner-focused).
     * For instructors, redirect to the instructor profile page.
     */
    public function show(User $user)
    {
        if ($user->isInstructor() && $user->instructorProfile) {
            return redirect()->route('admin.instructors.show', $user->instructorProfile);
        }

        $user->load([
            'learnerWallet',
            'learnerTransactions' => fn ($q) => $q->latest()->limit(50),
            'adminNotes.admin',
        ]);

        $bookings = $user->learnerBookings()
            ->with(['instructor'])
            ->latest()
            ->limit(100)
            ->get();

        $reviewsGiven = $user->reviewsGiven()
            ->with(['instructor', 'booking'])
            ->latest()
            ->get();

        $complaintsFiled = InstructorComplaint::with(['instructorProfile.user', 'creator'])
            ->where('reporter_user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_bookings'     => $user->learnerBookings()->count(),
            'completed_bookings' => $user->learnerBookings()->where('status', 'completed')->count(),
            'cancelled_bookings' => $user->learnerBookings()->where('status', 'cancelled')->count(),
            'total_spent'        => $user->learnerBookings()->where('status', 'completed')->sum('amount'),
            'wallet_balance'     => optional($user->learnerWallet)->balance ?? 0,
            'reviews_given'      => $reviewsGiven->count(),
            'complaints_filed'   => $complaintsFiled->count(),
            'avg_rating_given'   => $reviewsGiven->count() ? round($reviewsGiven->avg('rating'), 2) : null,
        ];

        return view('admin.users.show', [
            'user'            => $user,
            'bookings'        => $bookings,
            'reviewsGiven'    => $reviewsGiven,
            'complaintsFiled' => $complaintsFiled,
            'stats'           => $stats,
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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $user->name . ' has been ' . ($user->is_active ? 'activated' : 'deactivated') . '.',
                'is_active' => $user->is_active,
            ]);
        }

        return redirect()->back()->with('message', $user->name . ' has been ' . ($user->is_active ? 'activated' : 'deactivated') . '.');
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

    // ==================================================================
    //  ADMIN NOTES ON USERS
    // ==================================================================

    public function storeNote(Request $request, User $user)
    {
        $request->validate([
            'note'   => 'required|string|max:5000',
            'pinned' => 'nullable|boolean',
        ]);

        UserAdminNote::create([
            'user_id'  => $user->id,
            'admin_id' => Auth::id(),
            'note'     => $request->input('note'),
            'pinned'   => (bool) $request->input('pinned', false),
        ]);

        return redirect()->back()->with('message', 'Note added.');
    }

    public function deleteNote(UserAdminNote $userAdminNote)
    {
        $userAdminNote->delete();
        return redirect()->back()->with('message', 'Note deleted.');
    }

    public function toggleNotePin(UserAdminNote $userAdminNote)
    {
        $userAdminNote->pinned = ! $userAdminNote->pinned;
        $userAdminNote->save();
        return redirect()->back()->with('message', $userAdminNote->pinned ? 'Note pinned.' : 'Note unpinned.');
    }
}
