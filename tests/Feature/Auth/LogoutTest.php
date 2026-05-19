<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('dashboard'));

        $this->assertGuest();
    }

    /** @test */
    public function guest_cannot_call_logout()
    {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    }
}
