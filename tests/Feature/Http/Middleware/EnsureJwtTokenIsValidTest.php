<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EnsureJwtTokenIsValid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureJwtTokenIsValidTest extends TestCase
{
    public function test_the_request_is_forbidden_if_it_has_no_bearer_token()
    {
        $response = (new EnsureJwtTokenIsValid())->handle(
            new Request(),
            fn () => new Response()
        );

        $this->assertSame(403, $response->status());
    }

    public function test_the_request_is_unauthorized_if_token_is_invalid()
    {
        $request = new Request();

        $request->headers->add([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.'
                . 'eyJzdWIiOiIxMjM0NTY3ODkwIn0.'
                . '2gSBz9EOsQRN9I-3iSxJoFt7NtgV6Rm0IL6a8CAwl3Q'
        ]);

        $response = (new EnsureJwtTokenIsValid())->handle(
            $request,
            fn () => new Response()
        );

        $this->assertSame(401, $response->status());

        $request = new Request();

        $request->headers->add([
            'Authorization' => 'Bearer an.invalid.token',
        ]);

        $response = (new EnsureJwtTokenIsValid())->handle(
            $request,
            fn () => new Response()
        );

        $this->assertSame(401, $response->status());
    }
}
