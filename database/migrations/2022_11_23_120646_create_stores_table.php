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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('email')->unique();
            $table->enum('role', ['0','SuperAdmin', 'Vendor','Other'])->default('0');
            $table->string('logo')->default('ccccccc');
            $table->string('about_store')->nullable();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->string('password');
            $table->string('username');
            $table->string('store_carry')->nullable();
            $table->string('about')->nullable();
            $table->string('company')->nullable();
            $table->string('job')->nullable();
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->string('profile_picture')->nullable();
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
        Schema::dropIfExists('stores');
    }
};
