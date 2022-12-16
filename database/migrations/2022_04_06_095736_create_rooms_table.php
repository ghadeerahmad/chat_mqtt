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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('description');
            $table->integer('max_users');
            $table->string('password')->nullable();
            $table->string('identifier')->unique();
            $table->foreignId('user_id');
            $table->foreignId('country_id');
            $table->string('topic')->nullable();
            $table->string('pinned_message')->nullable();
            $table->enum('is_deleted', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('rooms');
    }
};
