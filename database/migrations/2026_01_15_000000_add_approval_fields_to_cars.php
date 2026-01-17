<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Hacer nullable las claves foráneas existentes
            // Nota: En algunos drivers SQL, modificar columnas con FK es complejo.
            // Lo más seguro es desactivar checks, modificar y reactivar.

            $table->unsignedBigInteger('id_marca')->nullable()->change();
            $table->unsignedBigInteger('id_modelo')->nullable()->change();

            // Campos temporales para cuando el usuario selecciona "Otro"
            $table->string('temp_brand')->nullable()->after('id_modelo');
            $table->string('temp_model')->nullable()->after('temp_brand');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['temp_brand', 'temp_model']);
            // No revertimos el nullable para evitar conflictos de datos
        });
    }
};
