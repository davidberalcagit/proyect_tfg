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
            $table->foreignId('id_vendedor')->constrained('customers')->onDelete('cascade');
            $table->decimal('cantidad', 10, 2);
            $table->string('estado')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
