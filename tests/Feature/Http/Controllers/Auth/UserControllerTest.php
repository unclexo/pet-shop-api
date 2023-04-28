<?php

namespace Tests\Feature\Http\Controllers\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_be_registered_with_valid_data()
    {
        $this->withoutExceptionHandling();

        $email = fake()->unique()->safeEmail;

        $this->postJson(route('v1.user.registration'), [
                'first_name' => fake()->firstName,
                'last_name' => fake()->lastName,
                'email' => $email,
                'password' => 'password',
                'password_confirmation' => 'password',
                'address' => fake()->address,
                'phone_number' => fake()->phoneNumber,
                'is_marketing' => 0,
        ])->assertStatus(201)->assertJsonPath(
            'data.token',
            fn (string $token) => str($token)->contains('eyJ')
        );

        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    public function test_a_user_can_not_be_registered_with_invalid_data()
    {
        $firstName = fake()->firstName;

        $this->postJson(route('v1.user.registration'), [
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
            ->assertJsonValidationErrors(['last_name', 'password',]);

        $this->assertDatabaseMissing(
            'users', ['first_name' => $firstName]
        );
    }
}
