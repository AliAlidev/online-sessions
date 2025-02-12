<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function bunnySetting(Request $request)
    {
        try {
            if ($request->ajax()) {
                if ($request->get('global')['api_key']) {
                    if (!checkApiKey($request->get('global')['api_key']))
                        return response()->json(['success' => false, 'message' => 'Invalid API Key']);
                }
                Setting::updateOrCreate([
                    'type' => 'global'
                ], [
                    'type' => 'global',
                    'data' => [$request->get('global')]
                ]);

                if (isset($request->get('image')['storage_zone_name']) || isset($request->get('image')['storage_access_token'])) {
                    if (!checkImageConfig($request->get('image')))
                        return response()->json(['success' => false, 'message' => 'Invalid Image Settings']);
                }
                if (isset($request->get('image')['image_pull_zone']) && $request->get('global')['api_key']) {
                    if (!checkPullZoneAvailability($request->get('global')['api_key'], $request->get('image')['image_pull_zone']))
                        return response()->json(['success' => false, 'message' => 'Invalid PullZone Name']);
                }
                Setting::updateOrCreate([
                    'type' => 'image'
                ], [
                    'type' => 'image',
                    'data' => [$request->get('image')]
                ]);

                if (isset($request->get('video')['video_api_key']) || isset($request->get('video')['video_library_id'])) {
                    if (!checkVideoConfig($request->get('video')))
                        return response()->json(['success' => false, 'message' => 'Invalid Video Settings']);
                }
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
            $globalSetting = $settings->where('type', 'global')->first();
            if ($imageSetting)
                $imageSetting = $imageSetting->data[0];
            if ($videoSetting)
                $videoSetting = $videoSetting->data[0];
            if ($globalSetting)
                $globalSetting = $globalSetting->data[0];
            return view('dashboard.setting.bunny', ['imageSetting' => $imageSetting, 'videoSetting' => $videoSetting, 'globalSetting' => $globalSetting]);
        } catch (Exception $th) {
            createServerError($th, "bunnySetting", "setting");
            return false;
        }
    }
}
