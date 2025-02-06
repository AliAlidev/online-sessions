<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function bunnySetting(Request $request)
    {
        if ($request->ajax()) {
            Setting::updateOrCreate([
                'type' => 'image'
            ], [
                'type' => 'image',
                'data' => [$request->get('image')]
            ]);
            Setting::updateOrCreate([
                'type' => 'video'
            ], [
                'type' => 'video',
                'data' => [$request->get('video')]
            ]);
            session()->flash('success', 'Setting has been saved successfully');
            return response()->json(['success' => true, 'url' => route('settings.bunny')]);
        }
        $settings = Setting::get();
        $imageSetting = $settings->where('type', 'image')->first();
        $videoSetting = $settings->where('type', 'video')->first();
        if ($imageSetting)
            $imageSetting = $imageSetting->data[0];
        if ($videoSetting)
            $videoSetting = $videoSetting->data[0];
        return view('dashboard.setting.bunny', ['imageSetting' => $imageSetting, 'videoSetting' => $videoSetting]);
    }
}
