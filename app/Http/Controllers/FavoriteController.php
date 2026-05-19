<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Services\SpaceXService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FavoriteController extends Controller
{
    public function __construct(protected SpaceXService $spacex) {}

    /**
     * Invalidar la caché de IDs de favoritos del usuario.
     */
    protected function bustFavoritesCache(int $userId): void
    {
        Cache::forget("user.{$userId}.favorite_ids");
    }

    public function index()
    {
        $favorites = Favorite::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

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

        $validated['user_id'] = auth()->id();

        Favorite::updateOrCreate(
            [
                'user_id'   => $validated['user_id'],
                'launch_id' => $validated['launch_id'],
            ],
            $validated
        );

        $this->bustFavoritesCache($validated['user_id']);

        return back()->with('success', '¡Misión guardada en favoritos!');
    }

    public function destroy(int $id)
    {
        $fav = Favorite::where('user_id', auth()->id())->findOrFail($id);
        $fav->delete();

        $this->bustFavoritesCache(auth()->id());

        return back()->with('success', 'Favorito eliminado.');
    }

    public function updateNotes(Request $request, int $id)
    {
        $fav = Favorite::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $fav->notes = $validated['notes'] ?? '';
        $fav->save();

        return back()->with('success', 'Notas actualizadas.');
    }
}
