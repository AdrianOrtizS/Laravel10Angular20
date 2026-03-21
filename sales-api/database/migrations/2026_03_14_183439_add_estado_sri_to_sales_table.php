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
        Schema::table('sales', function (Blueprint $table) {
            // PENDIENTE | AUTORIZADO | NO AUTORIZADO | ERROR
            $table->string('estado_sri', 20)->default('PENDIENTE')->after('clave_acceso');
            $table->string('numero_autorizacion', 50)->nullable()->after('estado_sri');
            $table->timestamp('fecha_autorizacion_sri')->nullable()->after('numero_autorizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['estado_sri', 'numero_autorizacion', 'fecha_autorizacion_sri']);
        });
    }
};
