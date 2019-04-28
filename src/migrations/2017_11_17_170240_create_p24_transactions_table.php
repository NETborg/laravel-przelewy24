<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateP24TransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p24_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('p24_session_id', 100);
            $table->integer('p24_amount')->unsigned()->default(0);
            $table->string('p24_currency', 3)->default('PLN');
            $table->text('p24_description')->nullable();
            $table->string('p24_email', 50);
            $table->string('p24_client', 50)->nullable();
            $table->string('p24_address', 80)->nullable();
            $table->string('p24_zip', 10)->nullable();
            $table->string('p24_city', 50)->nullable();
            $table->string('p24_country', 2)->default('PL');
            $table->string('p24_phone', 12)->nullable();
            $table->string('p24_language', 2)->default(config('app.locale'));
            $table->integer('p24_method')->unsigned()->nullable();
            $table->string('p24_url_return');
            $table->string('p24_url_status')->nullable();
            $table->integer('p24_time_limit')->default(0);
            $table->integer('p24_wait_for_result')->default(0);
            $table->integer('p24_channel')->nullable();
            $table->integer('p24_shipping')->unsigned()->default(0);
            $table->string('p24_transfer_label', 20)->nullable();
            $table->string('p24_sign', 100)->nullable();
            $table->string('p24_encoding', 15)->default('UTF-8');
            $table->integer('p24_order_id')->nullable();
            $table->string('p24_statement')->nullable();
            $table->string('token')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p24_transactions');
    }
}
