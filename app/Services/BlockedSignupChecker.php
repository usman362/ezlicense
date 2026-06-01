<?php

namespace App\Services;

use App\Models\BlockedSignup;
use App\Models\BlockedSignupAttempt;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Anti-spam guard for new signups (instructor or learner).
 *
 * When an admin blocks/bans an instructor, we record their email + phone in the
 * `blocked_signups` table. Every new account creation (via signup, invite, etc.)
 * runs through check() — if it matches, registration is refused and the attempt
 * is logged for admin review.
 */
class BlockedSignupChecker
{
    /**
     * Check if an email or phone is on the active block list.
     * Returns the matching BlockedSignup, or null if clean.
     */
    public static function check(?string $email, ?string $phone = null): ?BlockedSignup
    {
        $email = $email ? strtolower(trim($email)) : null;
        $phoneNorm = self::normalisePhone($phone);

        $query = BlockedSignup::where('is_active', true);

        $query->where(function ($q) use ($email, $phoneNorm) {
            if ($email) {
                $q->orWhere('email', $email);
            }
            if ($phoneNorm && strlen($phoneNorm) >= 8) {
                $q->orWhere('phone_normalized', $phoneNorm);
            }
        });

        return $query->first();
    }

    /**
     * Record a blocked attempt for admin visibility.
     */
    public static function logAttempt(BlockedSignup $blocked, ?string $email, ?string $phone, ?string $name, ?string $ip, string $context = 'signup'): void
    {
        BlockedSignupAttempt::create([
            'blocked_signup_id' => $blocked->id,
            'email'             => $email ? strtolower(trim($email)) : null,
            'phone'             => $phone,
            'attempted_name'    => $name,
            'ip_address'        => $ip,
            'user_agent'        => substr((string) request()->userAgent(), 0, 500),
            'context'           => $context,
        ]);
    }

    /**
     * Called when an admin blocks/bans a user — automatically adds them
     * to the block list so they can't sneak back in under a fresh signup.
     */
    public static function add(User $user, ?string $reason = null, ?int $blockedByUserId = null): BlockedSignup
    {
        return BlockedSignup::updateOrCreate(
            [
                'email' => strtolower(trim($user->email)),
            ],
            [
                'original_user_id'    => $user->id,
                'phone_normalized'    => self::normalisePhone($user->phone),
                'name'                => $user->name,
                'reason'              => $reason ?: 'Blocked by admin',
                'blocked_by_user_id'  => $blockedByUserId ?: auth()->id(),
                'blocked_at'          => now(),
                'is_active'           => true,
            ]
        );
    }

    /**
     * Reverse a block (admin override / mistaken-block recovery).
     */
    public static function release(BlockedSignup $blocked): void
    {
        $blocked->update(['is_active' => false]);
    }

    /**
     * Normalise phone for fuzzy matching — strip everything except digits,
     * drop leading 0 / international prefix.
     */
    public static function normalisePhone(?string $phone): ?string
    {
        if (! $phone) return null;
        $digits = preg_replace('/\D+/', '', $phone);
        // Drop leading 0 / +61 country code so 0412345678 and +61412345678 match
        $digits = ltrim($digits, '0');
        if (Str::startsWith($digits, '61') && strlen($digits) > 9) {
            $digits = substr($digits, 2);
        }
        return $digits ?: null;
    }
}
