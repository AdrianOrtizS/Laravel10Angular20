<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            // $table->string('surname');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('uniqid')->nullable();
            $table->string('code_verified')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            // $table->rememberToken();
            
        });

        DB::table('users')->insert(
                            array( 'id'     =>'1',      
                                   'name'   =>'Adrian Ortiz', 
                                   'email'  =>'adrian-2222@hotmail.com',
                                   'password'=> Hash::make('1718348053'),
                                   'email_verified_at'=> now(),
                                   'uniqid' => uniqid(),
                                   'role'   =>'admin',
                                   'created_at' => now(),
                                   'updated_at' => now() ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
