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
        Schema::create('vendor_categories', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->longText('category_image')->nullable();
            $table->longText('tags')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('level')->nullable();
            $table->boolean('is_active')->nullable();
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
        Schema::dropIfExists('vendor_categories');
    }
};
