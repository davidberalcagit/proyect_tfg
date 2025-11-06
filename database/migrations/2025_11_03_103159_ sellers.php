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
        Schema::create('vendedores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_particular')->nullable()->constrained('particulares')->onDelete('cascade');
        $table->foreignId('id_empresa')->nullable()->constrained('empresas')->onDelete('cascade');
        $table->timestamps();
     });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendedores');

    }
};
