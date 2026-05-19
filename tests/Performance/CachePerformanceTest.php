<?php

namespace Tests\Performance;

use App\Services\SpaceXService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Mide el impacto del caché en SpaceXService.
 * Compara: "sin caché" (primer hit que va a HTTP) vs "con caché" (segundo hit).
 *
 * Resultado documentado en docs/PERFORMANCE.md
 */
class CachePerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        // Simulamos latencia de la red (~50 ms por request) con Http::fake
        Http::fake([
            'api.spacexdata.com/*' => function () {
                usleep(50_000); // 50 ms simulando red
                return Http::response([
                    'docs'      => [['id' => 'a', 'name' => 'X', 'success' => true, 'upcoming' => false]],
                    'totalDocs' => 1,
                ], 200);
            },
        ]);
    }

    /** @test */
    public function cache_is_faster_than_a_cold_call()
    {
        $service = new SpaceXService();

        // Cold (sin caché) — golpea el HTTP fake
        $cold = $service->benchmark(fn () => $service->getAllLaunches());

        // Warm (con caché) — debería devolver del array driver
        $warm = $service->benchmark(fn () => $service->getAllLaunches());

        // El warm debe ser significativamente más rápido que el cold
        $this->assertLessThan($cold, $warm);

        // Y el warm debe ser sub-milisegundo (mucho menos que los 50 ms del cold)
        $this->assertLessThan(10, $warm, "Warm cache tomó {$warm}ms, esperábamos <10ms");

        // Solo 1 request HTTP en total (la segunda fue cache hit)
        Http::assertSentCount(1);
    }

    /** @test */
    public function flushing_cache_forces_a_new_http_call()
    {
        $service = new SpaceXService();

        $service->getAllLaunches();          // 1ª (HTTP)
        $service->getAllLaunches();          // cache hit
        $service->flushCache();
        $service->getAllLaunches();          // 2ª (HTTP)

        Http::assertSentCount(2);
    }

    /** @test */
    public function multiple_endpoints_are_cached_independently()
    {
        $service = new SpaceXService();

        $service->getAllLaunches();
        $service->getRockets();
        $service->getAllLaunches();   // cache hit
        $service->getRockets();       // cache hit

        // Solo 2 HTTPs aunque hubo 4 llamadas
        Http::assertSentCount(2);
    }
}
