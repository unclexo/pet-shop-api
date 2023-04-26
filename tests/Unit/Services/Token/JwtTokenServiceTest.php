<?php

namespace Tests\Unit\Services\Token;


use App\Services\Token\JwtTokenService;
use Tests\TestCase;

class JwtTokenServiceTest extends TestCase
{
    public function test_jwt_token_service_exists_in_service_container()
    {
        $this->assertInstanceOf(JwtTokenService::class, app('jwt'));
    }
}
