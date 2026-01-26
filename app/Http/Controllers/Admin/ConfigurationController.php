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
        $data = $request->validate([
            'campus_name' => 'required|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
