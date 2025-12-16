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
        Schema::create('sales', function (Blueprint $table) {
           $table->id();
           $table->foreignId('id_vendedor')->nullable()->references('id')->on('customers')->onDelete('cascade');
           $table->foreignId('id_comprador')->nullable()->references('id')->on('customers')->onDelete('cascade');
           $table->foreignId('id_vehiculo')->nullable()->constrained('cars')->onDelete('cascade');
           $table->integer('precio');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
