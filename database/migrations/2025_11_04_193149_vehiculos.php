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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vendedor')->constrained('vendedores')->onDelete('cascade');
            $table->string('marca');
            $table->string('modelo');
            $table->string('matricula');
            $table->year('año_matri');
            $table->string('motor');
            $table->string('combustible');
            $table->string('cambio');
            $table->string('color');
            $table->integer('km');
            $table->string('precio');
            $table->boolean('moto');
            $table->string('foto')->nullable();
            $table->text('descripcion');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
