<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use NetborgTeam\P24\P24TransactionConfirmation;

class CreateP24TransactionConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p24_transaction_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('p24_transaction_id')->unsigned();
            $table->integer('p24_merchant_id')->unsigned()->default(0);
            $table->integer('p24_pos_id')->unsigned()->default(0);
            $table->string('p24_session_id', 100);
            $table->integer('p24_amount')->unsigned();
            $table->string('p24_currency', 3)->default('PLN');
            $table->integer('p24_order_id')->unsigned();
            $table->integer('p24_method')->unsigned()->nullable();
            $table->string('p24_statement')->nullable();
            $table->string('p24_sign');
            $table->string('verification_status')->default(P24TransactionConfirmation::STATUS_NEW);
            $table->string('verification_sign')->nullable();
            $table->dateTimeTz('verified_at')->nullable();
            $table->timestampsTz();
            
            $table->foreign('p24_transaction_id')
                ->references('id')
                ->on('p24_transactions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p24_transaction_confirmations');
    }
}
