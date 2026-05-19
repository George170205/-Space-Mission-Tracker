<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Services\SpaceXService;

class LaunchController extends Controller
{
    public function __construct(protected SpaceXService $spacex) {}

    public function index()
    {
        $allLaunches = $this->spacex->getAllLaunches();

        // Filter
        $filter = request('filter', 'all');
        $search = trim(request('search', ''));

        $launches = $allLaunches;

        if ($search !== '') {
            $searchLower = strtolower($search);
            $launches = array_filter($launches, function ($l) use ($searchLower) {
                $name   = strtolower($l['name'] ?? '');
                $rocket = strtolower($l['rocket']['name'] ?? ($l['rocket'] ?? ''));
                return str_contains($name, $searchLower) || str_contains($rocket, $searchLower);
            });
        }

        if ($filter === 'exitoso') {
            $launches = array_filter($launches, fn($l) => $l['success'] === true);
        } elseif ($filter === 'fallido') {
            $launches = array_filter($launches, fn($l) => $l['success'] === false && !($l['upcoming'] ?? false));
        } elseif ($filter === 'proximo') {
            $launches = array_filter($launches, fn($l) => $l['upcoming'] ?? false);
        } elseif (in_array($filter, ['falcon9', 'falconheavy', 'starship'])) {
            $rocketMap = [
                'falcon9'     => 'falcon 9',
                'falconheavy' => 'falcon heavy',
                'starship'    => 'starship',
            ];
            $rName = $rocketMap[$filter];
            $launches = array_filter($launches, function ($l) use ($rName) {
                $name = strtolower($l['rocket']['name'] ?? ($l['rocket'] ?? ''));
                return str_contains($name, $rName);
            });
        }

        $launches = array_values($launches);

        // Paginate manually
        $perPage = 15;
        $page    = max(1, (int) request('page', 1));
        $total   = count($launches);
        $pages   = max(1, (int) ceil($total / $perPage));
        $page    = min($page, $pages);
        $paginated = array_slice($launches, ($page - 1) * $perPage, $perPage);

        $favoriteIds = Favorite::pluck('launch_id')->toArray();

        return view('launches.index', compact(
            'paginated', 'filter', 'search', 'total', 'page', 'pages', 'favoriteIds'
        ));
    }

    public function show(string $id)
    {
        $launch = $this->spacex->getLaunchById($id);

        if (!$launch) {
            abort(404, 'Lanzamiento no encontrado');
        }

        $isFavorite = Favorite::where('launch_id', $id)->exists();

        return view('launches.show', compact('launch', 'isFavorite'));
    }
}
