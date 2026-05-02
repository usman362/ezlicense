<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Coupon::withCount('redemptions');

        if ($s = $request->input('search')) {
            $query->where('code', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%");
        }
        if ($status = $request->input('status')) {
            if ($status === 'active') $query->where('is_active', true);
            if ($status === 'inactive') $query->where('is_active', false);
            if ($status === 'expired') $query->where('expires_at', '<', now());
        }

        $coupons = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })->count(),
            'redemptions' => \App\Models\CouponRedemption::count(),
            'discount_given' => round((float) \App\Models\CouponRedemption::sum('discount_applied'), 2),
        ];

        return view('admin.coupons.index', ['coupons' => $coupons, 'stats' => $stats]);
    }

    public function create(): View
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'is_active' => true, 'max_uses_per_user' => 1])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['code'] = strtoupper(trim($data['code']));
        Coupon::create($data);
        return redirect()->route('admin.coupons.index')->with('message', 'Coupon "' . $data['code'] . '" created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', ['coupon' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $this->validateData($request, $coupon);
        $data['code'] = strtoupper(trim($data['code']));
        $coupon->update($data);
        return redirect()->route('admin.coupons.index')->with('message', 'Coupon updated.');
    }

    public function toggle(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);
        return back()->with('message', $coupon->is_active ? 'Coupon activated.' : 'Coupon deactivated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('message', 'Coupon deleted.');
    }

    private function validateData(Request $request, ?Coupon $existing = null): array
    {
        $codeRule = 'required|string|max:50|alpha_dash';
        if ($existing) {
            $codeRule .= '|unique:coupons,code,' . $existing->id;
        } else {
            $codeRule .= '|unique:coupons,code';
        }

        return $request->validate([
            'code' => $codeRule,
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percent,fixed',
            'amount' => 'required|numeric|min:0.01|max:999999',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'required|integer|min:1|max:100',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'first_booking_only' => 'nullable|boolean',
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'first_booking_only' => $request->boolean('first_booking_only'),
        ];
    }
}
