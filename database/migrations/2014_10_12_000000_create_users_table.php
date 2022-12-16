<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('identifier')->unique();
            $table->string('userId')->unique();
            $table->foreignId('reserved_id_id')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->enum('status', ['ACTIVE', 'DISABLED'])->default('ACTIVE');
            $table->enum('connection_status', ['ONLINE', 'OFFLINE'])->default('OFFLINE');
            $table->foreignId('level_id')->default(1);
            $table->double('points')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->text('google_id')->nullable();
            $table->text('avatar')->nullable();
            $table->text('token')->nullable();
            $table->enum('local', ['en', 'ar'])->default('ar');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
