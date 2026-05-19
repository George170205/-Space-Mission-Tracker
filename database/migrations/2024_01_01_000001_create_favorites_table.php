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
            $table->string('launch_id')->unique();         // ID de SpaceX
            $table->string('mission_name');
            $table->string('rocket_name')->nullable();
            $table->string('launch_date')->nullable();
            $table->string('launch_site')->nullable();
            $table->boolean('success')->nullable();
            $table->string('status_label')->nullable();    // "Exitoso", "Fallido", "Próximo"
            $table->text('notes')->nullable();             // Notas personales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
