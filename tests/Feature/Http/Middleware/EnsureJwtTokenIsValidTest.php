<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EnsureJwtTokenIsValid;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureJwtTokenIsValidTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_request_is_forbidden_if_it_has_no_bearer_token()
    {
        $this->assertSame(403, $this->callMiddleware()->status());
    }

    public function test_the_request_is_unauthorized_if_token_is_invalid()
    {
        $request = new Request();

        $request->headers->add([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.'
                . 'eyJzdWIiOiIxMjM0NTY3ODkwIn0.'
                . '2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q'
        ]);

        $this->assertSame(401, $this->callMiddleware($request)->status());

        $request = new Request();

        $request->headers->add([
            'Authorization' => 'Bearer an.invalid.token',
        ]);

        $this->assertSame(401, $this->callMiddleware($request)->status());
    }

    public function test_the_request_is_unauthorized_if_the_token_is_stale()
    {
        $token = app('jwt')
            ->expiresAt(CarbonImmutable::now()->subHour(1))
            ->token()
            ->toString();

        $request = new Request();
        $request->headers->add(['Authorization' => 'Bearer '.$token]);

        $this->assertSame(401, $this->callMiddleware($request)->status());
    }

    public function test_the_request_is_authorized_if_token_is_valid()
    {
        $user = User::factory()->create();

        $request = new Request();
        $request->headers->add([
            'Authorization' => 'Bearer '.app('jwt')
                ->claimWith('user_uuid', $user->uuid)
                ->token()
                ->toString()
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
