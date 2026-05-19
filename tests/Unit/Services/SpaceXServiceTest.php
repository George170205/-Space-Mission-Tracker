<?php

namespace Tests\Unit\Services;

use App\Services\SpaceXService;
use Tests\TestCase;

class SpaceXServiceTest extends TestCase
{
    protected SpaceXService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SpaceXService();
    }

    /** @test */
    public function status_label_returns_proximo_for_upcoming_launch()
    {
        $launch = ['upcoming' => true, 'success' => null];
        $this->assertEquals('Próximo', $this->service->statusLabel($launch));
    }

    /** @test */
    public function status_label_returns_exitoso_for_successful_launch()
    {
        $launch = ['upcoming' => false, 'success' => true];
        $this->assertEquals('Exitoso', $this->service->statusLabel($launch));
    }

    /** @test */
    public function status_label_returns_fallido_for_failed_launch()
    {
        $launch = ['upcoming' => false, 'success' => false];
        $this->assertEquals('Fallido', $this->service->statusLabel($launch));
    }

    /** @test */
    public function status_badge_returns_proper_css_class()
    {
        $this->assertEquals('badge-amber', $this->service->statusBadge(['upcoming' => true, 'success' => null]));
        $this->assertEquals('badge-green', $this->service->statusBadge(['upcoming' => false, 'success' => true]));
        $this->assertEquals('badge-red',   $this->service->statusBadge(['upcoming' => false, 'success' => false]));
    }

    /** @test */
    public function format_date_handles_null_and_valid_dates()
    {
        $this->assertEquals('—', $this->service->formatDate(null));
        $this->assertEquals('—', $this->service->formatDate(''));
        $this->assertMatchesRegularExpression(
            '/\d{2} \w{3} \d{4}/',
            $this->service->formatDate('2024-05-19T12:00:00Z')
        );
    }

    /** @test */
    public function cache_keys_returns_known_keys()
    {
        $keys = $this->service->cacheKeys();

        $this->assertIsArray($keys);
        $this->assertContains('spacex.launches.all', $keys);
        $this->assertContains('spacex.stats', $keys);
        $this->assertContains('spacex.rockets', $keys);
    }

    /** @test */
    public function benchmark_returns_a_positive_duration()
    {
        $ms = $this->service->benchmark(function () {
            usleep(1000); // 1 ms
        });

        $this->assertGreaterThan(0, $ms);
    }
}
