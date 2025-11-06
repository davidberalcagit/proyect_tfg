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
        Schema::create('ventas', function (Blueprint $table) {
           $table->id();
           $table->foreignId('id_vendedor')->nullable()->constrained('vendedores')->onDelete('cascade');
           $table->foreignId('id_comprador')->nullable()->constrained('compradores')->onDelete('cascade');
           $table->foreignId('id_vehiculo')->nullable()->constrained('vehiculos')->onDelete('cascade');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
