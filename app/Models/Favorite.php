<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
