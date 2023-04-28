<?php

namespace App\Http\Middleware;

use App\Models\JwtToken;
use App\Models\User;
use App\Traits\NeedsCustomResponse;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureJwtTokenIsValid
{
    use NeedsCustomResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $token = $request->bearerToken()) {
            $this->throwHttpResponseException(
                statusCode: Response::HTTP_FORBIDDEN,
            );
        }

        try {
            $jwt = app('jwt');

            if (! $jwt->validate($token)) {
                $this->throwHttpResponseException(
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            if (! ($parsedToken = $jwt->parse($token)) instanceof Token) {
                $this->throwHttpResponseException(
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            if (
                $parsedToken->isExpired(CarbonImmutable::now())
                || ! $parsedToken->hasBeenIssuedBy(config('app.url'))
            ) {
                $this->throwHttpResponseException(
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            if (! $user = User::where(
                    'uuid',
                    $parsedToken->claims()->get('user_uuid')
                )->first()
            ) {
                $this->throwHttpResponseException(
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            if (! ($user->jwtToken instanceof JwtToken)) {
                $this->throwHttpResponseException(
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            auth()->setUser($user);
        } catch (Throwable $e) {
            $this->throwHttpResponseException(
                statusCode: Response::HTTP_UNAUTHORIZED,
            );
        }

        return $next($request);
    }
}
