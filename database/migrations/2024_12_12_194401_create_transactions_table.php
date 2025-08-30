<?php

use App\Constants\GlobalConst;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->string('trx_id')->comment('Transaction ID');
            $table->enum('user_type',[
                GlobalConst::USER,
                GlobalConst::ADMIN,
                GlobalConst::VENDOR,
            ])->nullable()->comment("transaction creator");
            $table->unsignedBigInteger('user_id')->nullable()->comment("transaction creator id");
            $table->unsignedBigInteger('vendor_id')->nullable()->comment("transaction creator id");
            $table->unsignedBigInteger('admin_id')->nullable()->comment("transaction creator admin id");
            $table->unsignedBigInteger('wallet_id')->nullable()->comment("transaction creator wallet it");
            $table->unsignedBigInteger('payment_gateway_currency_id')->nullable();
            $table->decimal('request_amount', 28, 8)->comment('add money: user wallet balance, money transfer: send amount, money out: withdraw wallet amount');
            $table->string('request_currency')->comment("In add money user wallet currency, money transfer receiver currency");
            $table->decimal('exchange_rate', 28, 8)->nullable();
            $table->decimal('percent_charge', 28, 8)->nullable();
            $table->decimal('fixed_charge', 28, 8)->nullable();
            $table->decimal('total_charge', 28, 8)->nullable();
            $table->decimal('total_payable', 28, 8)->nullable();
            $table->decimal('receive_amount', 28, 8)->nullable()->comment('add money: user wallet balance, money transfer: receiver amount, money out: user receive amount using manual info');
            $table->enum('receiver_type',[
                GlobalConst::USER,
                GlobalConst::ADMIN,
                GlobalConst::VENDOR,
            ])->nullable()->comment("Uses maybe money transfer, make payment");
            $table->unsignedBigInteger('receiver_id')->nullable()->comment("Uses maybe money transfer, make payment");

            $table->decimal('available_balance', 28, 8)->nullable();
            $table->string('payment_currency')->nullable()->comment('user payment currency (wallet/gateway)');
            $table->string('remark')->nullable();
            $table->text('details')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('reject_reason')->nullable();
            $table->text('callback_ref')->nullable();
            $table->string('booking_token')->nullable()->comment('Booking Token');
            $table->tinyInteger('refundable')->nullable();
            $table->timestamps();
            $table->foreign("wallet_id")->references("id")->on("vendor_wallets")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("payment_gateway_currency_id")->references("id")->on("payment_gateway_currencies")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('transactions');
    }
};
