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
        Schema::create('product_changes', function (Blueprint $table) {
            $table->id();
            $table->longText('title')->nullable();
            $table->longText('handle')->nullable();
            $table->longText('body_html')->nullable();
            $table->longText('vendor')->nullable();
            $table->longText('product_type')->nullable();
            $table->longText('tags')->nullable();
            $table->bigInteger('status')->default(0);
            $table->bigInteger('is_variants')->default(0);
            $table->longText('category')->nullable();
            $table->bigInteger('shopify_id')->nullable();
            $table->bigInteger('edit_status')->default(0);
            $table->date('approve_date')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->bigInteger('json_shopify_id')->nullable();
            $table->bigInteger('product_id')->nullable();
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
        Schema::dropIfExists('product_changes');
    }
};
