<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition()
    {
        return [
            'user_id'      => User::factory(),
            'launch_id'    => 'spx_' . Str::random(10),
            'mission_name' => fake()->words(3, true),
            'rocket_name'  => fake()->randomElement(['Falcon 9', 'Falcon Heavy', 'Starship']),
            'launch_date'  => fake()->date('d M Y'),
            'launch_site'  => fake()->randomElement(['Cape Canaveral', 'Vandenberg', 'Starbase']),
            'success'      => fake()->boolean(80),
            'status_label' => fake()->randomElement(['Exitoso', 'Fallido', 'Próximo']),
            'notes'        => fake()->optional()->sentence(),
        ];
    }
}
