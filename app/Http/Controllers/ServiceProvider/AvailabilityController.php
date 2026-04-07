<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider as ProviderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index()
    {
        $provider = ProviderModel::with(['availabilitySlots', 'availabilityBlocks'])
            ->where('user_id', Auth::id())->firstOrFail();
        return view('service-provider.availability', compact('provider'));
    }

    public function storeSlot(Request $request)
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        $provider = ProviderModel::where('user_id', Auth::id())->firstOrFail();
        $provider->availabilitySlots()->create($data);
        return back()->with('success', 'Slot added.');
    }

    public function destroySlot($slotId)
    {
        $provider = ProviderModel::where('user_id', Auth::id())->firstOrFail();
        $provider->availabilitySlots()->where('id', $slotId)->delete();
        return back()->with('success', 'Slot removed.');
    }
}
