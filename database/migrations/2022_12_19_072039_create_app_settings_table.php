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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('version',50)->nullable();
            $table->string('splash_screen_image',255)->nullable();
            $table->string('url_title')->nullable();
            $table->string('android_url',255)->nullable();
            $table->string('iso_url')->nullable();
            $table->string('vendor_version',50)->nullable();
            $table->string('vendor_splash_screen_image',255)->nullable();
            $table->string('vendor_url_title')->nullable();
            $table->string('vendor_android_url',255)->nullable();
            $table->string('vendor_iso_url')->nullable();
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
        Schema::dropIfExists('app_settings');
    }
};
