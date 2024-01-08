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
        Schema::create('vendor_urls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_id')->nullable();
            $table->longText('vendor_name')->nullable();
            $table->longText('url')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->bigInteger('total_products')->nullable();
            $table->longText('product_ids')->nullable();
            $table->bigInteger('log_id')->nullable();
            $table->longText('failed_reason')->nullable();
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
        Schema::dropIfExists('vendor_urls');
    }
};
