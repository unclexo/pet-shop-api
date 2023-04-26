<?php

namespace Tests\Unit\Services\Token;


use App\Services\Token\JwtTokenService;
use Lcobucci\JWT\Token;
use Tests\TestCase;

class JwtTokenServiceTest extends TestCase
{
    public function test_jwt_token_service_exists_in_service_container()
    {
        $this->assertInstanceOf(JwtTokenService::class, app('jwt'));
    }

    public function test_it_can_create_jwt_token()
    {
        $this->assertInstanceOf(Token::class, $token = app('jwt')->token());
        $this->assertTrue(str_starts_with($token->toString(), 'eyJ'));
    }
}
