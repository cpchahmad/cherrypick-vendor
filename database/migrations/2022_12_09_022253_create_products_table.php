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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('sku')->nullable();
            $table->string('price')->nullable();
            $table->string('weight')->nullable();
            $table->string('temp_requirements')->nullable();
            $table->string('compare_price')->nullable();
            $table->string('tags')->nullable();
            $table->string('shelf_life')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('quantity')->nullable();
            $table->string('category')->nullable();
            $table->enum('is_multi_variants', ['yes', 'no'])->default('no');
            $table->enum('status', [ 'active','inactive'])->default('inactive');
             $table->string('vendor_id')->nullable();

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
        Schema::dropIfExists('products');
    }
};
