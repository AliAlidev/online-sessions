<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;


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
        return User::all();
    }

    function getAuthToken(Request $request) {
        $data = $request->all();
        $guest_id = strtolower($data['fingerprint']);
        $user = User::firstOrCreate(['email' => $guest_id . '@guest.com'],[
            'name' => 'Guest_' . $guest_id,
            'email' => $guest_id . '@guest.com',
            'password' => bcrypt('guest')
        ]);
        $token = JWTAuth::customClaims([])->fromUser($user);
        return response()->json(['status' => 'success', 'token' => $token]);
    }
}
