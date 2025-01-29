<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Passport\PersonalAccessTokenResult;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class WebEnsureToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('encrypted_token');

        if (empty($token)) {
            $guest_id = strtolower(Str::random(10));
            $user = User::create([
                'name' => 'Guest_' . $guest_id,
                'email' => $guest_id . '@guest.com',
                'password' => bcrypt('guest')
            ]);
            $token = JWTAuth::customClaims([])->fromUser($user);
            return redirect()->to($request->path())->withHeaders(['token' => $token]);
        } else {
            try {
                $user = JWTAuth::setToken($token)->authenticate();
                if (!$user) {
                    return response()->json(['message' => 'User not found'], 404);
                }
            } catch (TokenExpiredException $e) {
                return response()->json(['message' => 'Token has expired'], 400);
            } catch (TokenInvalidException $e) {
                return response()->json(['message' => 'Token is invalid'], 400);
            } catch (JWTException $e) {
                return response()->json(['message' => 'Token error: ' . $e->getMessage()], 400);
            }
        }
        return $next($request);
    }
}
