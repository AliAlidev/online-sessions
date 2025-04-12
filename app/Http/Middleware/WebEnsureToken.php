<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
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
        $token = $request->header('pageToken');
        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token is invalid'], 400);
        } else {
            try {
                $user = JWTAuth::setToken($token)->authenticate();
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }
            } catch (TokenExpiredException $e) {
                return response()->json(['success' => false, 'message' => 'Token has expired'], 400);
            } catch (TokenInvalidException $e) {
                return response()->json(['success' => false, 'message' => 'Token is invalid'], 400);
            } catch (JWTException $e) {
                return response()->json(['success' => false, 'message' => 'Token error: ' . $e->getMessage()], 400);
            }
        }
        return $next($request);
    }
}
