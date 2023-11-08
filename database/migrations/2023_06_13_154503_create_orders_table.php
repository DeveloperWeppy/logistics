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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('wc_order_id');
            $table->string('wc_status');
            $table->string('billing');
            $table->string('shipping');
            $table->string('line_items');
            $table->string('total_amount');
            $table->string('siigo_invoice');
            $table->string('payment_method');
            $table->unsignedBigInteger('create_user_id');
            $table->unsignedBigInteger('finalized_user_id');
            $table->integer('status');
            $table->timestamps();
            $table->foreign('create_user_id')->references('id')->on('users');
            $table->foreign('finalized_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
