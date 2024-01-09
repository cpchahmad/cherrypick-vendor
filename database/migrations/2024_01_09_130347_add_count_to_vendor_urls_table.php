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
        Schema::table('vendor_urls', function (Blueprint $table) {
            $table->bigInteger('total_draft_products')->default(0);
            $table->bigInteger('total_update_products')->default(0);
            $table->bigInteger('fetch_from_api')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_urls', function (Blueprint $table) {
            //
        });
    }
};
