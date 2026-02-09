<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('estado');
            $table->foreignId('id_estado')->default(1)->constrained('rental_statuses');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['id_estado']);
            $table->dropColumn('id_estado');
            $table->string('estado')->default('active');
        });

        Schema::dropIfExists('rental_statuses');
    }
};
