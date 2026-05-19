<?php

namespace Tests\Feature\Launches;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Pruebas de vistas públicas (Launches) — accesibles sin login.
 */
class LaunchesViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        // Fakear todas las llamadas a la API de SpaceX para no depender de red
        Http::fake([
            'api.spacexdata.com/v4/launches/query' => Http::response([
                'docs' => [
                    [
                        'id'            => 'spx_demo',
                        'name'          => 'Demo Launch',
                        'date_utc'      => '2024-05-19T12:00:00Z',
                        'date_unix'     => 1716120000,
                        'success'       => true,
                        'upcoming'      => false,
                        'flight_number' => 1,
                        'rocket'        => ['id' => 'r1', 'name' => 'Falcon 9'],
                        'launchpad'     => ['name' => 'KSC'],
                    ],
                ],
                'totalDocs' => 1,
            ], 200),
            'api.spacexdata.com/v4/rockets'   => Http::response([], 200),
            'api.spacexdata.com/v4/rockets/*' => Http::response([], 200),
            'api.spacexdata.com/v4/launches/spx_demo' => Http::response([
                'id'       => 'spx_demo',
                'name'     => 'Demo Launch',
                'date_utc' => '2024-05-19T12:00:00Z',
                'success'  => true,
                'upcoming' => false,
                'rocket'   => null,
                'launchpad'=> null,
            ], 200),
            'api.spacexdata.com/v4/launches/missing' => Http::response('nope', 404),
        ]);
    }

    /** @test */
    public function dashboard_is_accessible_for_guests()
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Space');
    }

    /** @test */
    public function launches_index_is_accessible_for_guests()
    {
        $this->get(route('launches.index'))
            ->assertOk()
            ->assertSee('Demo Launch');
    }

    /** @test */
    public function launches_show_displays_a_launch_detail()
    {
        $this->get(route('launches.show', 'spx_demo'))
            ->assertOk()
            ->assertSee('Demo Launch');
    }

    /** @test */
    public function launches_show_returns_404_for_missing_launch()
    {
        $this->get(route('launches.show', 'missing'))
            ->assertNotFound();
    }

    /** @test */
    public function rockets_index_is_accessible_for_guests()
    {
        $this->get(route('rockets.index'))->assertOk();
    }
}
