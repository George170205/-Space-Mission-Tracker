<?php

namespace Tests\Unit\Models;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_hides_sensitive_fields_when_serialized()
    {
        $user = User::factory()->create();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function it_has_many_favorites()
    {
        $user = User::factory()->create();
        Favorite::factory()->count(4)->for($user)->create();

        $this->assertCount(4, $user->favorites);
        $this->assertInstanceOf(Favorite::class, $user->favorites->first());
    }

    /** @test */
    public function it_deletes_favorites_on_cascade()
    {
        $user = User::factory()->create();
        Favorite::factory()->count(2)->for($user)->create();

        $this->assertEquals(2, Favorite::count());

        $user->delete();

        // Por la FK constrained con cascadeOnDelete
        $this->assertEquals(0, Favorite::count());
    }
}
