<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedBigInteger('id_marca')->nullable()->change();
            $table->unsignedBigInteger('id_modelo')->nullable()->change();

            $table->string('temp_brand')->nullable()->after('id_modelo');
            $table->string('temp_model')->nullable()->after('temp_brand');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['temp_brand', 'temp_model']);
        });
    }
};
