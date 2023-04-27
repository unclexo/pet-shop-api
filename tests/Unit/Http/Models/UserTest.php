<?php

namespace Tests\Unit\Http\Models;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_optionally_tokenize_its_payload()
    {
        $user = User::factory()->create();

        $user->tokenize();

        $this->assertStringContainsString('eyJ', $user->token);
    }
}
