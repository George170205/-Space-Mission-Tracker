<?php

namespace Tests\Feature\SpaceX;

use App\Services\SpaceXService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Pruebas de SpaceXService usando Http::fake() — sin tocar la API real.
 * Cubre el requisito de "mínimo 2 pruebas con Http::fake()".
 */
class SpaceXHttpFakeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // Aislar cada test
    }

    /** @test */
    public function get_all_launches_parses_the_docs_field_from_a_faked_response()
    {
        Http::fake([
            'api.spacexdata.com/v4/launches/query' => Http::response([
                'docs' => [
                    ['id' => 'a1', 'name' => 'Demo-1', 'success' => true,  'upcoming' => false],
                    ['id' => 'a2', 'name' => 'Demo-2', 'success' => false, 'upcoming' => false],
                ],
                'totalDocs' => 2,
            ], 200),
        ]);

        $service  = new SpaceXService();
        $launches = $service->getAllLaunches();

        $this->assertCount(2, $launches);
        $this->assertEquals('Demo-1', $launches[0]['name']);
        $this->assertEquals('Demo-2', $launches[1]['name']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/launches/query');
        });
    }

    /** @test */
    public function get_rockets_returns_an_empty_array_when_api_fails()
    {
        Http::fake([
            'api.spacexdata.com/v4/rockets' => Http::response('error', 500),
        ]);

        $service = new SpaceXService();
        $rockets = $service->getRockets();

        $this->assertIsArray($rockets);
        $this->assertEmpty($rockets);

        Http::assertSentCount(1);
    }

    /** @test */
    public function get_upcoming_launches_handles_a_successful_faked_response()
    {
        Http::fake([
            'api.spacexdata.com/v4/launches/query' => Http::response([
                'docs' => [
                    ['id' => 'up1', 'name' => 'Future-1', 'upcoming' => true],
                ],
            ], 200),
        ]);

        $service  = new SpaceXService();
        $upcoming = $service->getUpcomingLaunches();

        $this->assertCount(1, $upcoming);
        $this->assertEquals('Future-1', $upcoming[0]['name']);
        $this->assertTrue($upcoming[0]['upcoming']);
    }

    /** @test */
    public function get_launch_by_id_returns_null_for_non_existent_id_via_fake()
    {
        Http::fake([
            'api.spacexdata.com/v4/launches/*' => Http::response('not found', 404),
        ]);

        $service = new SpaceXService();
        $launch  = $service->getLaunchById('does-not-exist');

        $this->assertNull($launch);
    }
}
