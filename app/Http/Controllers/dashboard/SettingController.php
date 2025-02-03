<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    function bunnySetting()
    {
        $roles = Setting::get();
        return view('dashboard.setting.bunny', ['roles' => $roles]);
    }
}
