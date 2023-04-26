<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureJwtTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $token = $request->bearerToken()) {
            return \response(null, 403);
        }

        try {
            $jwt = app('jwt');

            if (! $jwt->validate($token)) {
                return \response(null, 401);
            }

            if (! ($parsedToken = $jwt->parse($token)) instanceof Token) {
                return \response(null, 401);
            }

            if (!$user = User::where(
                    'uuid',
                    $parsedToken->claims()->get('user_uuid')
                )->first()
            ) {
                return \response(null, 401);
            }

            auth()->setUser($user);
        } catch (Throwable $e) {
            return \response(null, 401);
        }

        return $next($request);
    }
}
