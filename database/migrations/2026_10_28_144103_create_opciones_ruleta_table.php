<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('opciones_ruleta', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('color', 7);
            $table->unsignedSmallInteger('tamano')->default(1);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        $ahora = now();

        DB::table('opciones_ruleta')->insert([
            ['nombre' => 'Opción 1', 'color' => '#f59e0b', 'tamano' => 1, 'orden' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Opción 2', 'color' => '#d946ef', 'tamano' => 1, 'orden' => 2, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Opción 3', 'color' => '#10b981', 'tamano' => 1, 'orden' => 3, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opciones_ruleta');
    }
};
