<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_types', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('listing_type');

            $table->foreignId('id_listing_type')->nullable()->constrained('listing_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['id_listing_type']);
            $table->dropColumn('id_listing_type');
            $table->string('listing_type')->default('sale');
        });

        Schema::dropIfExists('listing_types');
    }
};
