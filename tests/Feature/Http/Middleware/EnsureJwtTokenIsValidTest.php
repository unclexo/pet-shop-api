<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EnsureJwtTokenIsValid;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureJwtTokenIsValidTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_raises_exception_if_request_has_no_bearer_token()
    {
        $this->expectException(HttpResponseException::class);

        $this->callMiddleware();
    }

    public function test_it_raises_exception_if_request_has_invalid_token()
    {
        $request = new Request();

        $request->headers->add([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.'
                . 'eyJzdWIiOiIxMjM0NTY3ODkwIn0.'
                . '2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q'
        ]);

        $this->expectException(HttpResponseException::class);

        $this->callMiddleware($request);
    }

    public function test_it_raises_exception_if_token_has_bad_format()
    {
        $request = new Request();

        $request->headers->add([
            'Authorization' => 'Bearer an.invalid.token',
        ]);

        $this->expectException(HttpResponseException::class);

        $this->callMiddleware($request);
    }

    public function test_it_raises_exception_if_request_has_an_expired_token()
    {
        $token = app('jwt')
            ->expiresAt(CarbonImmutable::now()->addHour(1))
            ->token()
            ->toString();

        $request = new Request();
        $request->headers->add(['Authorization' => 'Bearer '.$token]);

        $this->expectException(HttpResponseException::class);

        $this->callMiddleware($request);
    }

    public function test_it_raises_exception_if_token_has_been_issued_by_others()
    {
        $token = app('jwt')
            ->issuedBy('test_issuer')
            ->token()
            ->toString();

        $request = new Request();
        $request->headers->add(['Authorization' => 'Bearer '.$token]);

        $this->expectException(HttpResponseException::class);

        $this->callMiddleware($request);
    }

    public function test_the_request_is_authorized_if_token_is_valid()
    {
        $user = User::factory()->create();
        $user->tokenize('User creation');

        $request = new Request();
        $request->headers->add([
            'Authorization' => 'Bearer '.$user->token
        ]);

        $this->assertSame(
            200, $this->callMiddleware($request)->getStatusCode()
        );

        $this->assertSame($user->uuid, auth()->user()->uuid);
    }

    private function callMiddleware(Request $request = null): Response
    {
        return (new EnsureJwtTokenIsValid())->handle(
            $request ?? new Request(),
            fn () => new Response()
        );
    }
}
