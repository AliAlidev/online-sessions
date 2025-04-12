<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Laravel\Passport\PersonalAccessTokenResult;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token =  $request->cookie('encrypted_token');
        if (!empty($token)) {
            try {
                $token = $this->decryptToken($token);
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
        } else {
            return response()->json(['status' => 'error', 'message' => 'No valid token found'], 401);
        }
        return $next($request);
    }

    function decryptToken($token)
    {
        $token = explode('|', Crypt::decryptString($token));
        if ($token[1])
            return $token[1];

        return null;
    }
}
