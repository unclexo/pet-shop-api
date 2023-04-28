<?php

namespace Tests\Feature\Http\Controllers\Auth;


use App\Models\User;
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

    public function test_it_can_provide_jwt_token_after_successful_login()
    {
        $email = 'user@example.com';
        $password = 'userpassword';

        User::factory()->create([
            'email' => $email, 'password' => $password
        ]);

        $this->postJson(route('v1.user.login'), [
            'email' => $email, 'password' => $password
        ])->assertStatus(200)->assertJsonPath(
            'data.token',
            fn (string $token) => str($token)->startsWith('eyJ')
        );
    }

    public function test_a_user_can_not_login_with_invalid_credentials()
    {
        $email = 'user@example.com';
        $password = 'userpassword';

        User::factory()->create([
            'email' => $email, 'password' => $password
        ]);

        $this->postJson(route('v1.user.login'), [
            'email' => '', 'password' => ''
        ])->assertStatus(422)->assertJsonValidationErrors([
            'email', 'password'
        ]);

        $this->postJson(route('v1.user.login'), [
            'email' => 'invalid@example.com', 'password' => '123'
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_a_token_is_not_allowed_for_authorization_after_logout()
    {
        $user = User::factory()->create();
        $user->tokenize('User creation');

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->postJson(route('v1.user.logout'))
            ->assertStatus(200)
            ->assertJsonStructure(['data' => []]);

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->postJson(route('v1.user.logout'))
            ->assertStatus(401);
    }

    public function test_a_user_can_be_updated()
    {
        $data = $this->userData();

        $user = User::factory()->create();
        $user->tokenize('User creation');

        $this
            ->withHeaders(['Authorization' => 'Bearer '.$user->token])
            ->putJson(route('v1.user.edit', ['uuid' => $user->uuid]), [
                'first_name' => $data->firstName,
                'last_name' => $data->lastName,
                'email' => $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->passwordConfirmation,
                'address' => $data->address,
                'phone_number' => $data->phoneNumber,
            ])->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'first_name' => $data->firstName,
            'email' => $data->email,
        ]);
    }

    private function userData()
    {
        return (object) [
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'email' => 'auniqueemail@example.com',
            'password' => 'password',
            'passwordConfirmation' => 'password',
            'address' => 'address',
            'phoneNumber' => '+8801712345678',
            'isMarketing' => 0,
        ];
    }
}
