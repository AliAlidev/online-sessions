<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTAuth;

class GuestController extends Controller
{

    public function landing()
    {
        return view('landing');
    }

    public function apiAction(Request $request)
    {
        $token = $request->cookie('encrypted_token');
        if (!empty($token)) {
            $decryptedToken = Crypt::decryptString($token);
            return response()->json(['status' => 'success', 'token' => $decryptedToken]);
        }

        return response()->json(['status' => 'error', 'message' => 'No valid token found'], 401);
    }

    public function action(Request $request)
    {
        return JWTAuth::authenticate();
        return User::all();
    }
}
