<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpaceXService
{
    protected string $baseUrl = 'https://api.spacexdata.com/v4';
    protected int $cacheTtl = 1800; // 30 minutos

    // ---------- LAUNCHES ----------

    public function getAllLaunches(): array
    {
        return Cache::remember('spacex.launches.all', $this->cacheTtl, function () {
            try {
                $resp = Http::timeout(30)->post("{$this->baseUrl}/launches/query", [
                    'query' => [],
                    'options' => [
                        'sort'   => ['date_unix' => 'desc'],
                        'limit'  => 200,
                        'select' => ['id','name','date_utc','date_unix','success','upcoming','flight_number'],
                        'populate' => [
                            ['path' => 'rocket',    'select' => ['name','id']],
                            ['path' => 'launchpad', 'select' => ['name','full_name']],
                        ],
                    ],
                ]);
                if (!$resp->successful()) {
                    // Fallback: simple GET endpoint
                    $r2 = Http::timeout(30)->get("{$this->baseUrl}/launches");
                    return $r2->successful() ? array_reverse($r2->json() ?? []) : [];
                }
                return $resp->json('docs') ?? [];
            } catch (\Exception $e) {
                Log::error('SpaceX getAllLaunches: ' . $e->getMessage());
                return [];
            }
        });
    }

    public function getLaunchById(string $id): ?array
    {
        return Cache::remember("spacex.launch.{$id}", $this->cacheTtl, function () use ($id) {
            try {
                $resp = Http::timeout(15)->get("{$this->baseUrl}/launches/{$id}");
                if (!$resp->successful()) return null;
                $launch = $resp->json();

                // Enrich with rocket details
                if (!empty($launch['rocket'])) {
                    $rocket = Http::timeout(15)->get("{$this->baseUrl}/rockets/{$launch['rocket']}")->json();
                    $launch['rocket_data'] = $rocket;
                }
                // Enrich with launchpad details
                if (!empty($launch['launchpad'])) {
                    $pad = Http::timeout(15)->get("{$this->baseUrl}/launchpads/{$launch['launchpad']}")->json();
                    $launch['launchpad_data'] = $pad;
                }
                return $launch;
            } catch (\Exception $e) {
                Log::error("SpaceX getLaunchById({$id}): " . $e->getMessage());
                return null;
            }
        });
    }

    public function getUpcomingLaunches(): array
    {
        return Cache::remember('spacex.launches.upcoming', $this->cacheTtl, function () {
            try {
                $resp = Http::timeout(15)->post("{$this->baseUrl}/launches/query", [
                    'query' => ['upcoming' => true],
                    'options' => [
                        'sort'  => ['date_unix' => 'asc'],
                        'limit' => 5,
                        'populate' => [
                            ['path' => 'rocket', 'select' => ['name']],
                            ['path' => 'launchpad', 'select' => ['name']],
                        ],
                    ],
                ]);
                return $resp->successful() ? ($resp->json('docs') ?? []) : [];
            } catch (\Exception $e) {
                Log::error('SpaceX getUpcomingLaunches: ' . $e->getMessage());
                return [];
            }
        });
    }

    public function getRecentLaunches(int $limit = 6): array
    {
        return Cache::remember("spacex.launches.recent.{$limit}", $this->cacheTtl, function () use ($limit) {
            try {
                $resp = Http::timeout(15)->post("{$this->baseUrl}/launches/query", [
                    'query' => ['upcoming' => false],
                    'options' => [
                        'sort'  => ['date_unix' => 'desc'],
                        'limit' => $limit,
                        'populate' => [
                            ['path' => 'rocket', 'select' => ['name']],
                            ['path' => 'launchpad', 'select' => ['name']],
                        ],
                    ],
                ]);
                return $resp->successful() ? ($resp->json('docs') ?? []) : [];
            } catch (\Exception $e) {
                Log::error('SpaceX getRecentLaunches: ' . $e->getMessage());
                return [];
            }
        });
    }

    // ---------- ROCKETS ----------

    public function getRockets(): array
    {
        return Cache::remember('spacex.rockets', $this->cacheTtl * 4, function () {
            try {
                $resp = Http::timeout(15)->get("{$this->baseUrl}/rockets");
                return $resp->successful() ? $resp->json() : [];
            } catch (\Exception $e) {
                Log::error('SpaceX getRockets: ' . $e->getMessage());
                return [];
            }
        });
    }

    public function getRocketById(string $id): ?array
    {
        return Cache::remember("spacex.rocket.{$id}", $this->cacheTtl * 4, function () use ($id) {
            try {
                $resp = Http::timeout(15)->get("{$this->baseUrl}/rockets/{$id}");
                return $resp->successful() ? $resp->json() : null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    // ---------- STATS ----------

    public function getDashboardStats(): array
    {
        return Cache::remember('spacex.stats', $this->cacheTtl, function () {
            try {
                // Total
                $allResp = Http::timeout(20)->post("{$this->baseUrl}/launches/query", [
                    'query' => [],
                    'options' => ['limit' => 1, 'select' => ['id']],
                ]);
                $total = $allResp->json('totalDocs') ?? 0;

                // Successful
                $succResp = Http::timeout(20)->post("{$this->baseUrl}/launches/query", [
                    'query' => ['success' => true],
                    'options' => ['limit' => 1, 'select' => ['id']],
                ]);
                $successful = $succResp->json('totalDocs') ?? 0;

                // Upcoming
                $upResp = Http::timeout(20)->post("{$this->baseUrl}/launches/query", [
                    'query' => ['upcoming' => true],
                    'options' => ['limit' => 1, 'select' => ['id']],
                ]);
                $upcoming = $upResp->json('totalDocs') ?? 0;

                $failed = max(0, $total - $successful - $upcoming);

                return compact('total', 'successful', 'failed', 'upcoming');
            } catch (\Exception $e) {
                return ['total' => 0, 'successful' => 0, 'failed' => 0, 'upcoming' => 0];
            }
        });
    }

    // ---------- HELPERS ----------

    public function statusLabel(array $launch): string
    {
        if ($launch['upcoming'] ?? false) return 'Próximo';
        if ($launch['success'] === true) return 'Exitoso';
        if ($launch['success'] === false) return 'Fallido';
        return 'Parcial';
    }

    public function statusBadge(array $launch): string
    {
        if ($launch['upcoming'] ?? false) return 'badge-amber';
        if ($launch['success'] === true) return 'badge-green';
        if ($launch['success'] === false) return 'badge-red';
        return 'badge-amber';
    }

    public function formatDate(?string $dateUtc): string
    {
        if (!$dateUtc) return '—';
        try {
            return \Carbon\Carbon::parse($dateUtc)->format('d M Y');
        } catch (\Exception $e) {
            return $dateUtc;
        }
    }

    // ---------- PERFORMANCE / CACHE MANAGEMENT ----------

    /**
     * Lista de todas las claves cacheadas por este servicio.
     *
     * @return string[]
     */
    public function cacheKeys(): array
    {
        return [
            'spacex.launches.all',
            'spacex.launches.upcoming',
            'spacex.launches.recent.3',
            'spacex.launches.recent.6',
            'spacex.rockets',
            'spacex.stats',
        ];
    }

    /**
     * Borrar todas las claves del servicio.
     * Útil para invalidar (cron, deploy, tests de rendimiento).
     */
    public function flushCache(): void
    {
        foreach ($this->cacheKeys() as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Medir el tiempo (ms) de una sola ejecución de un callable.
     */
    public function benchmark(callable $callable): float
    {
        $start = microtime(true);
        $callable();
        return (microtime(true) - $start) * 1000;
    }
}
