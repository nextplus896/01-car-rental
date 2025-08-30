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
        Schema::create('car_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("car_id");
            $table->unsignedBigInteger("user_id")->nullable();
            $table->string('slug');
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('trip_id')->nullable();
            $table->string('location');
            $table->string('destination');
            $table->string('payment_type');
            $table->string('trx_id')->comment('Transaction ID');
            $table->decimal('amount',28,8)->default(0);
            $table->decimal('charges',28,8)->default(0);
            $table->decimal('distance',28,8)->default(0);
            $table->time('pickup_time');
            $table->time('round_pickup_time')->nullable();
            $table->date('pickup_date');
            $table->date('round_pickup_date')->nullable();
            $table->text('message')->nullable();
            $table->tinyInteger("status")->default(0)->comment("0: Default, 1: Booked, 2: OnGoing, 3: Completed, 4: Reject");
            $table->timestamps();
            $table->foreign("car_id")->references("id")->on("cars")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_bookings');
    }
};
