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
        Schema::table('product_master', function (Blueprint $table) {
            $table->longText('hex_code')->nullable();
            $table->longText('swatch_image')->nullable();
            $table->longText('additional_key_ingredients')->nullable();
            $table->longText('additional_how_to_use')->nullable();
            $table->longText('additional_who_can_use')->nullable();
            $table->longText('additional_why_mama_earth')->nullable();
            $table->longText('additional_different_shades')->nullable();
            $table->longText('additional_faqs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_master', function (Blueprint $table) {
            //
        });
    }
};
