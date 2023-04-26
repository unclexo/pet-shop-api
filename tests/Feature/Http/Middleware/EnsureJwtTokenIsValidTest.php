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
}
