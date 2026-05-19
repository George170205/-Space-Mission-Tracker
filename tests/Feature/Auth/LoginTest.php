<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_form_is_accessible_for_guests()
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'commander@spacex.com',
            'password' => Hash::make('rocket123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'commander@spacex.com',
            'password' => 'rocket123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_password()
    {
        User::factory()->create([
            'email'    => 'commander@spacex.com',
            'password' => Hash::make('rocket123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'commander@spacex.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_form_redirects_authenticated_users_to_home()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect();
    }
}
