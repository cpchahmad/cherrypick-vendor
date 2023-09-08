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
        Schema::create('product_infos', function (Blueprint $table) {
            $table->id();
            $table->unSignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('varient_name');
            $table->string('varient_value');
            $table->string('price');
            $table->string('sku');
            $table->string('weight');
            $table->string('quantity');
            $table->string('dimensions');
            $table->string('shell_life');
            $table->string('temp');
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
        Schema::dropIfExists('product_infos');
    }
};
