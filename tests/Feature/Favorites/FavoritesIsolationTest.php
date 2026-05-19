<?php

namespace Tests\Feature\Favorites;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_only_sees_their_own_favorites_in_the_index()
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();

        Favorite::factory()->for($alice)->create(['mission_name' => 'Mission Alice']);
        Favorite::factory()->for($bob)->create(['mission_name'   => 'Mission Bob']);

        $response = $this->actingAs($alice)->get(route('favorites.index'));

        $response->assertOk()
            ->assertSee('Mission Alice')
            ->assertDontSee('Mission Bob');
    }

    /** @test */
    public function a_user_cannot_delete_anothers_favorite()
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();

        $bobFav = Favorite::factory()->for($bob)->create();

        $this->actingAs($alice)
            ->delete(route('favorites.destroy', $bobFav->id))
            ->assertNotFound(); // Por scoping a user_id

        $this->assertDatabaseHas('favorites', ['id' => $bobFav->id]);
    }

    /** @test */
    public function a_user_cannot_update_notes_of_anothers_favorite()
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();

        $bobFav = Favorite::factory()->for($bob)->create(['notes' => 'original']);

        $this->actingAs($alice)
            ->post(route('favorites.notes', $bobFav->id), ['notes' => 'hacked'])
            ->assertNotFound();

        $this->assertEquals('original', $bobFav->fresh()->notes);
    }
}
