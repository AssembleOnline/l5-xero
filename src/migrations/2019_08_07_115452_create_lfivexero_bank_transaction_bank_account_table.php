<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLfivexeroBankTransactionBankAccountTable extends Migration
{
    private $prefix = '';

    public function __construct()
    {
        $this->prefix = config('xero.prefix');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->prefix.'bank_transaction_bank_account', function(Blueprint $table) {  
            $table->increments('id')->unsigned();
            $table->integer('BankTransaction_id')->unsigned()->nullable();
            $table->string('Code')->nullable();
            $table->string('AccountID')->nullable();

            $table->timestamps();

            $table->foreign('BankTransaction_id','bt_bt_bank_acc')->references('id')->on($this->prefix.'bank_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix.'bank_transaction_bank_account');
    }
}
