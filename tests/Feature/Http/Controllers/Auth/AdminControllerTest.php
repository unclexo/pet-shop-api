<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_provide_jwt_token_after_successful_login()
    {
        $email = 'admin@buckhill.co.uk';
        $password = 'admin';

        User::factory()->create([
            'email' => $email, 'password' => bcrypt($password)
        ]);

        $this->postJson(route('v1.admin.login'), [
            'email' => $email, 'password' => $password
        ])
        ->assertStatus(200)
        ->assertJsonPath(
            'data.token',
            fn (string $token) => str_starts_with($token, 'eyJ')
        );
    }

    public function test_it_can_raise_exception_for_invalid_credentials_given()
    {
        $email = 'admin@buckhill.co.uk';
        $password = 'admin';

        User::factory()->create([
            'email' => $email, 'password' => bcrypt($password)
        ]);

        $this->postJson(route('v1.admin.login'), [
            'email' => '', 'password' => ''
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);

        $this->postJson(route('v1.admin.login'), [
            'email' => 'invalid@example.com', 'password' => '123'
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }
}
