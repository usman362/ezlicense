<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedSignup;
use App\Models\BlockedSignupAttempt;
use App\Services\BlockedSignupChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockedSignupsController extends Controller
{
    public function index(Request $request)
    {
        $query = BlockedSignup::with(['originalUser:id,name,email', 'blockedBy:id,name'])
            ->withCount('attempts');

        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }
        if ($q = $request->input('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('email', 'like', "%{$q}%")
                  ->orWhere('phone_normalized', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhere('reason', 'like', "%{$q}%");
            });
        }

        $blocked = $query->latest('blocked_at')->paginate(20)->withQueryString();

        $counts = [
            'all'      => BlockedSignup::count(),
            'active'   => BlockedSignup::where('is_active', true)->count(),
            'released' => BlockedSignup::where('is_active', false)->count(),
            'attempts' => BlockedSignupAttempt::count(),
        ];

        return view('admin.blocked-signups.index', compact('blocked', 'counts'));
    }

    public function show(BlockedSignup $blockedSignup)
    {
        $blockedSignup->loadMissing(['originalUser', 'blockedBy', 'attempts']);
        return view('admin.blocked-signups.show', ['block' => $blockedSignup]);
    }

    /** Manually add an email/phone to the block list (catch-all use case). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email'  => 'required|email|max:191',
            'phone'  => 'nullable|string|max:30',
            'name'   => 'nullable|string|max:150',
            'reason' => 'required|string|max:500',
        ]);

        BlockedSignup::updateOrCreate(
            ['email' => strtolower(trim($data['email']))],
            [
                'phone_normalized'  => BlockedSignupChecker::normalisePhone($data['phone'] ?? null),
                'name'              => $data['name'] ?? null,
                'reason'            => $data['reason'],
                'blocked_by_user_id'=> Auth::id(),
                'blocked_at'        => now(),
                'is_active'         => true,
            ]
        );

        return back()->with('message', 'Block added.');
    }

    /** Toggle active/released. */
    public function toggle(BlockedSignup $blockedSignup)
    {
        $blockedSignup->update(['is_active' => ! $blockedSignup->is_active]);
        return back()->with('message', $blockedSignup->is_active ? 'Block re-activated.' : 'Block released — this person can now register again.');
    }

    public function destroy(BlockedSignup $blockedSignup)
    {
        $blockedSignup->delete();
        return back()->with('message', 'Block deleted permanently.');
    }
}
