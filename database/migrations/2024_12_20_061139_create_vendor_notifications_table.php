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
        Schema::create('vendor_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("vendor_id")->nullable();
            $table->string("type",100)->nullable();
            $table->text('message',1000);
            $table->timestamps();

            $table->foreign("vendor_id")->references("id")->on("vendors")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_notifications');
    }
};
