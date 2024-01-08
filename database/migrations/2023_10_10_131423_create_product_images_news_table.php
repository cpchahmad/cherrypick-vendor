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
        Schema::create('product_images_news', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('variant_ids')->nullable();
            $table->longText('image')->nullable();
            $table->longText('image2')->nullable();
            $table->longText('image3')->nullable();
            $table->longText('image4')->nullable();
            $table->longText('image5')->nullable();
            $table->longText('alt_text')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->bigInteger('image_id')->nullable();
            $table->bigInteger('shopify_image_id')->nullable();
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
        Schema::dropIfExists('product_images_news');
    }
};
