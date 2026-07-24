<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except('_token', '_method');
        
        foreach ($inputs as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }
        
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
