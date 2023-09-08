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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('home_desktop_banner')->nullable();
            $table->string('home_mobile_banner')->nullable();
            $table->string('store_desktop_banner')->nullable();
            $table->string('store_mobile_banner')->nullable();
            $table->unSignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('stores');
            $table->enum('live_status', ['Active', 'InActive',])->default('InActive');
            $table->enum('approve_status', ['Disable', 'Pending','Approved','Reject',])->default('Disable');

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
        Schema::dropIfExists('banners');
    }
};
