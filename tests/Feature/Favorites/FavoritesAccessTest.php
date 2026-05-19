<?php

namespace Tests\Feature\Favorites;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_are_redirected_to_login_when_accessing_favorites_index()
    {
        $this->get(route('favorites.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function guests_cannot_store_favorites()
    {
        $this->post(route('favorites.store'), [
            'launch_id'    => 'spx_demo',
            'mission_name' => 'Demo Mission',
        ])->assertRedirect(route('login'));

        $this->assertEquals(0, Favorite::count());
    }

    /** @test */
    public function guests_cannot_delete_favorites()
    {
        $user = User::factory()->create();
        $fav  = Favorite::factory()->for($user)->create();

        $this->delete(route('favorites.destroy', $fav->id))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('favorites', ['id' => $fav->id]);
    }

    /** @test */
    public function authenticated_user_can_access_favorites_index()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('Mis Favoritos');
    }
}
