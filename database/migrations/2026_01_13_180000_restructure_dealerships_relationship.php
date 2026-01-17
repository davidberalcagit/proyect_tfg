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
        // 1. Añadir dealership_id a customers
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('dealership_id')->nullable()->after('id_entidad');
            $table->foreign('dealership_id')->references('id')->on('dealerships')->onDelete('set null');
        });

        // 2. Eliminar id_cliente de dealerships (ya no es 1 a 1)
        Schema::table('dealerships', function (Blueprint $table) {
            // Primero eliminamos la clave foránea si existe
            $table->dropForeign(['id_cliente']);
            // Luego eliminamos la columna
            $table->dropColumn('id_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios
        Schema::table('dealerships', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->foreign('id_cliente')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['dealership_id']);
            $table->dropColumn('dealership_id');
        });
    }
};
