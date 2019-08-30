<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLfivexeroBankTransactionsTable extends Migration
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
        $type = ['RECEIVE','RECEIVE-OVERPAYMENT','RECEIVE-PREPAYMENT','SPEND','SPEND-OVERPAYMENT','SPEND-PREPAYMENT','RECEIVE-TRANSFER','SPEND-TRANSFER'];
        $status = ['AUTHORISED','DELETED'];
        $line_amount_types = ["Exclusive","Inclusive","NoTax"];
        
        Schema::create($this->prefix.'bank_transactions', function(Blueprint $table) use ($type, $status, $line_amount_types) {
            $table->increments('id')->unsigned();
            $table->integer('Contact_id')->unsigned();
            $table->boolean('IsReconciled')->nullable();
            $table->date('Date')->nullable();
            $table->string('Reference')->nullable();
            $table->string('CurrencyCode')->nullable();
            $table->decimal('CurrencyRate', 10, 4)->nullable();
            $table->string('Url')->nullable();
            $table->decimal('SubTotal', 10, 4)->nullable();
            $table->decimal('TotalTax', 10, 4)->nullable();
            $table->decimal('Total', 10, 4)->nullable();
            $table->string('BankTransactionID')->nullable();
            $table->string('PrepaymentID')->nullable();
            $table->string('OverpaymentID')->nullable();
            $table->timestamp('UpdatedDateUTC')->nullable();
            $table->boolean('HasAttachments')->nullable();

            $table->enum('Type', $type)->nullable();
            $table->enum('Status', $status)->nullable();
            $table->enum('LineAmountTypes', $line_amount_types)->nullable();
            
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
        Schema::dropIfExists($this->prefix.'bank_transactions');
    }
}
