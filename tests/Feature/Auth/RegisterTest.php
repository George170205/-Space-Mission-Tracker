<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_form_is_accessible_for_guests()
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Crear cuenta');
    }

    /** @test */
    public function a_user_can_register_with_valid_data()
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Cmdr. Abel',
            'email'                 => 'abel@spacex.com',
            'password'              => 'rocket1234',
            'password_confirmation' => 'rocket1234',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'name'  => 'Cmdr. Abel',
            'email' => 'abel@spacex.com',
        ]);

        $user = User::where('email', 'abel@spacex.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('rocket1234', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function registration_fails_when_email_is_already_taken()
    {
        User::factory()->create(['email' => 'busy@spacex.com']);

        $response = $this->post(route('register'), [
            'name'                  => 'Otro',
            'email'                 => 'busy@spacex.com',
            'password'              => 'rocket1234',
            'password_confirmation' => 'rocket1234',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::where('email', 'busy@spacex.com')->count());
    }

    /** @test */
    public function registration_fails_when_passwords_do_not_match()
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Cmdr. Abel',
            'email'                 => 'abel@spacex.com',
            'password'              => 'rocket1234',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'abel@spacex.com']);
        $this->assertGuest();
    }
}
