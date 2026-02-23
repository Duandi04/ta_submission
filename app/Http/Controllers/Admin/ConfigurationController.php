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
        return back()->with('info', 'Pengaturan sistem saat ini bersifat statis.');
    }
}
