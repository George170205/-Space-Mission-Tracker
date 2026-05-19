<?php

namespace Tests\Feature\Favorites;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_store_a_favorite()
    {
        $user = User::factory()->create();

        $payload = [
            'launch_id'    => 'spx_xyz',
            'mission_name' => 'Crew-9',
            'rocket_name'  => 'Falcon 9',
            'launch_date'  => '01 Jan 2025',
            'launch_site'  => 'Cape Canaveral',
            'status_label' => 'Próximo',
        ];

        $this->actingAs($user)
            ->post(route('favorites.store'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id'      => $user->id,
            'launch_id'    => 'spx_xyz',
            'mission_name' => 'Crew-9',
        ]);
    }

    /** @test */
    public function storing_same_launch_twice_only_creates_one_record_per_user()
    {
        $user = User::factory()->create();
        $payload = ['launch_id' => 'spx_unique', 'mission_name' => 'Demo'];

        $this->actingAs($user)->post(route('favorites.store'), $payload);
        $this->actingAs($user)->post(route('favorites.store'), $payload);

        $this->assertEquals(1, Favorite::where('user_id', $user->id)->count());
    }

    /** @test */
    public function storing_fails_validation_when_required_fields_are_missing()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('favorites.store'), [])
            ->assertSessionHasErrors(['launch_id', 'mission_name']);
    }

    /** @test */
    public function user_can_delete_their_own_favorite()
    {
        $user = User::factory()->create();
        $fav  = Favorite::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('favorites.destroy', $fav->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('favorites', ['id' => $fav->id]);
    }

    /** @test */
    public function user_can_update_notes_on_a_favorite()
    {
        $user = User::factory()->create();
        $fav  = Favorite::factory()->for($user)->create(['notes' => '']);

        $this->actingAs($user)
            ->post(route('favorites.notes', $fav->id), [
                'notes' => 'Misión histórica de prueba',
            ])
            ->assertRedirect();

        $this->assertEquals('Misión histórica de prueba', $fav->fresh()->notes);
    }
}
