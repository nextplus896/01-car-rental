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
        Schema::create('area_has_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("car_area_id");
            $table->unsignedBigInteger("car_type_id");
            $table->foreign("car_area_id")->references("id")->on("car_areas")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("car_type_id")->references("id")->on("car_types")->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('area_has_types');
    }
};
