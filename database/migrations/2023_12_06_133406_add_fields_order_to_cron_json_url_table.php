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
        Schema::table('cron_json_url', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->longText('api_link')->nullable();
            $table->longText('authorization_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_json_url', function (Blueprint $table) {
            //
        });
    }
};
