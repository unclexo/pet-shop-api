<?php

namespace Tests\Unit\Http\Models;


use App\Models\JwtToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_optionally_tokenize_its_payload()
    {
        $user = User::factory()->create();

        $user->tokenize('User creation');

        $this->assertStringContainsString('eyJ', $user->token);
    }

    public function test_a_user_has_a_jwt_token()
    {
        $user = User::factory()->create();

        $user->tokenize('User creation');

        $this->assertInstanceOf(JwtToken::class, $user->jwtToken);

        $this->assertSame($user->uuid, $user->jwtToken->user_uuid);
    }
}
