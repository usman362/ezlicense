<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('admin.settings', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->input('settings', []);

        foreach ($data as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            if (! $setting) {
                continue;
            }

            // Don't overwrite secret fields if the value is the mask placeholder
            if ($setting->type === 'secret' && $value === '••••••••') {
                continue;
            }

            $setting->update(['value' => $value]);
            \Illuminate\Support\Facades\Cache::forget("site_setting.{$key}");
        }

        \Illuminate\Support\Facades\Cache::forget('site_settings.all');

        return redirect()->route('admin.settings')->with('message', 'Settings saved successfully.');
    }

    /**
     * Seed default settings if table is empty (first visit).
     */
    public function seed()
    {
        SiteSetting::seedDefaults();

        return redirect()->route('admin.settings')->with('message', 'Default settings have been initialised.');
    }
}
