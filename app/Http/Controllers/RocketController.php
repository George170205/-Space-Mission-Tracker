<?php

namespace App\Http\Controllers;

use App\Services\SpaceXService;

class RocketController extends Controller
{
    public function __construct(protected SpaceXService $spacex) {}

    public function index()
    {
        $rockets = $this->spacex->getRockets();
        return view('rockets.index', compact('rockets'));
    }
}
