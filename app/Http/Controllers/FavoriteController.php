<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Services\SpaceXService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(protected SpaceXService $spacex) {}

    public function index()
    {
        $favorites = Favorite::orderByDesc('created_at')->get();
        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'launch_id'    => 'required|string',
            'mission_name' => 'required|string|max:255',
            'rocket_name'  => 'nullable|string|max:255',
            'launch_date'  => 'nullable|string|max:100',
            'launch_site'  => 'nullable|string|max:255',
            'success'      => 'nullable|boolean',
            'status_label' => 'nullable|string|max:50',
            'notes'        => 'nullable|string|max:1000',
        ]);

        Favorite::updateOrCreate(
            ['launch_id' => $validated['launch_id']],
            $validated
        );

        return back()->with('success', '¡Misión guardada en favoritos!');
    }

    public function destroy(int $id)
    {
        Favorite::findOrFail($id)->delete();
        return back()->with('success', 'Favorito eliminado.');
    }

    public function updateNotes(Request $request, int $id)
    {
        $fav = Favorite::findOrFail($id);
        $fav->notes = $request->input('notes', '');
        $fav->save();
        return back()->with('success', 'Notas actualizadas.');
    }
}
