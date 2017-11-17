<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateP24TransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p24_transaction_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('p24_transaction_id')->unsigned();
            $table->string('p24_name', 127);
            $table->string('p24_description', 127)->nullable();
            $table->integer('p24_quantity')->default(1);
            $table->integer('p24_price')->default(0);
            $table->integer('p24_number')->nullable();
            
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
        Schema::dropIfExists('p24_transaction_items');
    }
}
