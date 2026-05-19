<?php

namespace Tests\Performance;

use App\Http\Controllers\LaunchController;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Verifica que la caché de IDs de favoritos por usuario evita consultas
 * repetidas a la base de datos en el flujo de listado de lanzamientos.
 */
class FavoritesCachePerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function favorite_ids_cache_avoids_extra_db_queries()
    {
        $user = User::factory()->create();
        Favorite::factory()->count(5)->for($user)->create();

        Cache::flush();

        // 1ª llamada — hace SELECT
        DB::flushQueryLog();
        DB::enableQueryLog();
        LaunchController::favoriteIdsFor($user->id);
        $firstQueries = count(DB::getQueryLog());
        DB::flushQueryLog();

        // 2ª llamada — debería leer de caché y NO ejecutar SELECT
        LaunchController::favoriteIdsFor($user->id);
        $secondQueries = count(DB::getQueryLog());

        $this->assertGreaterThan(0, $firstQueries, 'La primera llamada debería ejecutar al menos 1 SELECT');
        $this->assertEquals(0, $secondQueries, 'La segunda llamada NO debería ejecutar SELECTs (cache hit)');
    }

    /** @test */
    public function cache_is_invalidated_when_a_favorite_is_created()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Cache::flush();
        LaunchController::favoriteIdsFor($user->id); // warm-up
        $this->assertTrue(Cache::has("user.{$user->id}.favorite_ids"));

        // Crear favorito vía endpoint — debe invalidar caché
        $this->post(route('favorites.store'), [
            'launch_id'    => 'invalidates_cache',
            'mission_name' => 'Test',
        ]);

        $this->assertFalse(
            Cache::has("user.{$user->id}.favorite_ids"),
            'La caché de favoritos debió haberse invalidado tras crear uno'
        );
    }

    /** @test */
    public function cache_is_invalidated_when_a_favorite_is_deleted()
    {
        $user = User::factory()->create();
        $fav  = Favorite::factory()->for($user)->create();

        Cache::flush();
        LaunchController::favoriteIdsFor($user->id);
        $this->assertTrue(Cache::has("user.{$user->id}.favorite_ids"));

        $this->actingAs($user)->delete(route('favorites.destroy', $fav->id));

        $this->assertFalse(Cache::has("user.{$user->id}.favorite_ids"));
    }
}
