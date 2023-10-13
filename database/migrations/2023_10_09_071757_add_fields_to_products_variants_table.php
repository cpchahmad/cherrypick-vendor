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
        Schema::table('products_variants', function (Blueprint $table) {
            $table->longText('dimensions_text')->nullable();
            $table->double('volume')->nullable();
            $table->longText('varient1_name')->nullable();
            $table->longText('varient1_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_variants', function (Blueprint $table) {
            //
        });
    }
};
