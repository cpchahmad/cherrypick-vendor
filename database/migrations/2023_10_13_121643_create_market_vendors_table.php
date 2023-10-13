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
        Schema::create('market_vendors', function (Blueprint $table) {
            $table->id();
            $table->longText('status')->nullable();
            $table->longText('type')->nullable();
            $table->double('value')->nullable();
            $table->bigInteger('market_id')->nullable();
            $table->bigInteger('vendor_id')->nullable();
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
        Schema::dropIfExists('market_vendors');
    }
};
