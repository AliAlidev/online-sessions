<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\PersonalAccessTokenResult;
use Symfony\Component\HttpFoundation\Response;

class EnsureToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->cookie('encrypted_token');
        if (empty($token)) {
            $guest_id = strtolower(Str::random(10));
            $user = User::create([
                'name' => 'Guest_' . $guest_id,
                'email' => $guest_id . '@guest.com',
                'password' => bcrypt('guest')
            ]);

            $tokenResult = $user->createToken('QR Access Token');
            $token = $tokenResult->accessToken;
            $cookie = cookie('encrypted_token', $token, 60 * 24 * 30 * 3);
            return response('Token created')->cookie($cookie);
        }
        if (!Auth::guard('api')->check()) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
            Auth::guard('api')->setRequest($request)->attempt(['email' => '0tbi8idfuq@guest.com', 'password' => 'guest']);
            dd(Auth::guard('api')->user());
        }
        return $next($request);
    }
}
