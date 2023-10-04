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
        Schema::create('variant_changes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->nullable();
            $table->longText('sku')->nullable();
            $table->bigInteger('taxable')->default(0);
            $table->float('shipping_weight')->nullable();
            $table->float('price')->nullable();
            $table->float('base_price')->nullable();
            $table->float('price_usd')->nullable();
            $table->float('price_aud')->nullable();
            $table->float('price_cad')->nullable();
            $table->float('price_gbp')->nullable();
            $table->float('price_nld')->nullable();
            $table->float('price_irl')->nullable();
            $table->float('price_ger')->nullable();
            $table->integer('grams')->nullable();
            $table->integer('stock')->nullable();
            $table->longText('shelf_life')->nullable();
            $table->longText('temp_require')->nullable();
            $table->longText('dimensions')->nullable();
            $table->longText('varient_name')->nullable();
            $table->longText('varient_value')->nullable();
            $table->bigInteger('vendor_id')->nullable();
            $table->longText('inventory_item_id')->nullable();
            $table->longText('inventory_id')->nullable();
            $table->bigInteger('edit_status')->default(0);
            $table->bigInteger('new_add_status')->default(0);
            $table->bigInteger('price_status')->default(0);
            $table->bigInteger('inventory_status')->default(0);
            $table->bigInteger('product_discount')->default(0);
            $table->float('discounted_base_price')->nullable();
            $table->float('discounted_inr')->nullable();
            $table->float('discounted_usd')->nullable();
            $table->float('discounted_aud')->nullable();
            $table->float('discounted_cad')->nullable();
            $table->float('discounted_gbp')->nullable();
            $table->float('discounted_nld')->nullable();
            $table->float('discounted_irl')->nullable();
            $table->float('discounted_ger')->nullable();
            $table->bigInteger('price_conversion_update_status')->default(0);
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
        Schema::dropIfExists('variant_changes');
    }
};
