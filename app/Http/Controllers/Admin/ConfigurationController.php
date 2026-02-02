<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.configuration.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'campus_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Update campus_name
        Setting::updateOrCreate(['key' => 'campus_name'], ['value' => $request->campus_name]);

        // Handle File Upload
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            
            // Delete old logo if it exists and isn't a default one (Optional, skipped for simplicity)

            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => 'images/' . $filename]);
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
