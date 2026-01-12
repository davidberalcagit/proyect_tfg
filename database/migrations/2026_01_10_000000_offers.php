<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vehiculo')->constrained('cars')->onDelete('cascade');
            $table->foreignId('id_comprador')->constrained('customers')->onDelete('cascade');
            $table->foreignId('id_vendedor')->constrained('customers')->onDelete('cascade'); // Added id_vendedor
            $table->decimal('cantidad', 10, 2); // El precio ofertado (normalmente el precio del coche)
            $table->string('estado')->default('pending'); // pending, accepted, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
