<?php

namespace Tests\Unit\Models;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_the_expected_fillable_fields()
    {
        $fav = new Favorite();
        $expected = [
            'user_id', 'launch_id', 'mission_name', 'rocket_name',
            'launch_date', 'launch_site', 'success', 'status_label', 'notes',
        ];

        $this->assertEquals($expected, $fav->getFillable());
    }

    /** @test */
    public function it_casts_success_to_boolean()
    {
        $user = User::factory()->create();

        $fav = Favorite::factory()->create([
            'user_id' => $user->id,
            'success' => 1,
        ]);

        $this->assertIsBool($fav->fresh()->success);
        $this->assertTrue($fav->fresh()->success);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $fav  = Favorite::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $fav->user);
        $this->assertEquals($user->id, $fav->user->id);
    }

    /** @test */
    public function it_can_be_filtered_by_user_id()
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();

        Favorite::factory()->count(3)->for($alice)->create();
        Favorite::factory()->count(2)->for($bob)->create();

        $this->assertCount(3, Favorite::where('user_id', $alice->id)->get());
        $this->assertCount(2, Favorite::where('user_id', $bob->id)->get());
    }
}
