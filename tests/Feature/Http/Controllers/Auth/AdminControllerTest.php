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
            'email' => $email, 'password' => $password
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
            'email' => $email, 'password' => $password
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

    public function test_an_admin_user_can_register_with_valid_data()
    {
        $this
            ->withHeaders($this->authorizationHeader())
            ->postJson(route('v1.admin.registration'), [
                'first_name' => fake()->firstName,
                'last_name' => fake()->lastName,
                'email' => fake()->unique()->safeEmail,
                'password' => 'password',
                'password_confirmation' => 'password',
                'avatar' => fake()->uuid,
                'address' => fake()->address,
                'phone_number' => fake()->phoneNumber,
                'is_marketing' => 0,
            ])
            ->assertStatus(201)
            ->assertJsonPath(
                'data.token',
                fn (string $token) => str_starts_with($token, 'eyJ')
            );

        $this->assertDatabaseHas('users', ['is_admin' => 1]);
    }

    public function test_an_admin_user_can_not_register_with_invalid_data()
    {
        $firstName = fake()->firstName;

        $this
            ->withHeaders($this->authorizationHeader())
            ->postJson(route('v1.admin.registration'), [
                'first_name' => $firstName,
                'last_name' => '',
                'email' => fake()->unique()->safeEmail,
                'password' => 'password',
                'password_confirmation' => 'passwor',
                'address' => fake()->address,
                'phone_number' => fake()->phoneNumber,
                'is_marketing' => 0,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['last_name', 'password', 'avatar']);

        $this->assertDatabaseMissing(
            'users', ['first_name' => $firstName]
        );
    }

    public function test_a_token_can_not_be_used_to_authorize_after_logout()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $user->tokenize('User creation');

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->postJson(route('v1.admin.logout'))
            ->assertStatus(200)
            ->assertJsonStructure(['data' => []]);

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->postJson(route('v1.admin.logout'))
            ->assertStatus(401);
    }

    public function test_listing_all_non_admin_users()
    {
        User::factory(11)->create();

        $user = User::find(1);
        $user->is_admin = 1;
        $user->save();

        $user->tokenize('User creation');

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->getJson(route('v1.admin.user_listing'))
            ->assertStatus(200)
            ->assertJsonCount(10, 'data');

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->getJson(route('v1.admin.user_listing', [
                'page' => 4,
                'limit' => 3
            ]))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    private function authorizationHeader()
    {
        $user = User::factory()->create([
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
            'is_admin' => 1,
        ]);

        $user->tokenize('User creation');

        return [
            'Authorization' => 'Bearer '.$user->token,
        ];
    }
}
