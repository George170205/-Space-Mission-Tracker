<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            // Cada favorito pertenece a un usuario (los favoritos son personales)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('launch_id');                   // ID de SpaceX
            $table->string('mission_name');
            $table->string('rocket_name')->nullable();
            $table->string('launch_date')->nullable();
            $table->string('launch_site')->nullable();
            $table->boolean('success')->nullable();
            $table->string('status_label')->nullable();    // "Exitoso", "Fallido", "Próximo"
            $table->text('notes')->nullable();             // Notas personales
            $table->timestamps();

            // Un mismo usuario no puede guardar el mismo launch dos veces
            $table->unique(['user_id', 'launch_id'], 'favorites_user_launch_unique');
            $table->index('launch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
