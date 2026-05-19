<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'launch_id',
        'mission_name',
        'rocket_name',
        'launch_date',
        'launch_site',
        'success',
        'status_label',
        'notes',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    /**
     * Cada favorito pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
