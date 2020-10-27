<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\User;
use App\Usuario;
use App\Filial;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('Token');

        $request->guest = $guard;
        
        if(!$token && $guard == 'public'){
            return $next($request);
        }

        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], 401);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
        
        try {
            $user = User::find($credentials->sub);
            $request->auth = $user;
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'User not found.'
            ], 401);
        }

        return $next($request);
    }
}