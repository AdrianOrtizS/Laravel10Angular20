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
            $table->increments('id');

            $table->integer('id_customer')->unsigned();
            $table->foreign('id_customer')->references('id')->on('customers');

            $table->date('date');
            $table->decimal('total', 10, 2);
            // $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            
            ///////////////////////////////////////////////
            $table->string('establecimiento', 3)->default('001');
            $table->string('punto_emision', 3)->default('001');
            $table->unsignedInteger('secuencial');
            $table->string('numero_factura')->unique();
            ///////////////////////////////////////////////

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
