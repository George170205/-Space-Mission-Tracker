<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Services\SpaceXService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(protected SpaceXService $spacex) {}

    public function index()
    {
        $stats    = $this->spacex->getDashboardStats();
        $recent   = $this->spacex->getRecentLaunches(3);
        $upcoming = $this->spacex->getUpcomingLaunches();
        $next     = $upcoming[0] ?? null;

        // Countdown to next launch
        $countdown = null;
        if ($next && !empty($next['date_utc'])) {
            $diff = Carbon::parse($next['date_utc'])->diff(Carbon::now());
            if (Carbon::parse($next['date_utc'])->isFuture()) {
                $countdown = [
                    'days'    => $diff->days,
                    'hours'   => $diff->h,
                    'minutes' => $diff->i,
                    'seconds' => $diff->s,
                ];
            }
        }

        $successRate = $stats['total'] > 0
            ? round(($stats['successful'] / $stats['total']) * 100, 1)
            : 0;

        return view('dashboard', compact('stats', 'recent', 'next', 'countdown', 'successRate'));
    }
}
