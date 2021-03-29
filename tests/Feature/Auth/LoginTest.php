<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_login_with_valid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'iPhone de ' . $user->name
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            "The plain text token is not valid"
        );
    }

    /** @test */
    function cannot_login_with_invalid_credentials()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'emaya@gmail.com',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de ernesto'
        ])->assertJsonValidationErrors('email');
    }


    public function cannot_login_twice()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.v1.login'))
            ->assertStatus(204);
    }



    /** @test */
    function email_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => '',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de ernesto'
        ])->assertSee(__('validation.required', ['attribute' => 'email']))
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    function email_must_be_valid()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'invalid-email',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de ernesto'
        ])->assertSee(__('validation.email', ['attribute' => 'email']))
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    function password_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'jorge@aprendible.com',
            'password' => '',
            'device_name' => 'iPhone de ernesto'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    function device_name_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'emaya@gmail.com',
            'password' => 'password',
            'device_name' => ''
        ])->assertJsonValidationErrors('device_name');
    }
}
