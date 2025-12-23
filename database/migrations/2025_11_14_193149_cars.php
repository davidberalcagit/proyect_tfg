<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vendedor')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('id_marca')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('id_modelo')->constrained('car_models')->cascadeOnDelete();
            $table->foreignId('id_marcha')->constrained('gears')->cascadeOnDelete();
            $table->foreignId('id_combustible')->constrained('fuels')->cascadeOnDelete();
            $table->string('matricula');
            $table->year('anyo_matri');
            $table->string('color');
            $table->integer('km');
            $table->integer('precio');
            $table->text('descripcion');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
