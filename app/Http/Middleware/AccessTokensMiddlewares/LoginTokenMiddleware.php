<?php

namespace App\Http\Middleware\AccessTokensMiddlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $tokenName = 'login-token';

        if ($user && $user->tokens()->where('name', $tokenName)->first()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}