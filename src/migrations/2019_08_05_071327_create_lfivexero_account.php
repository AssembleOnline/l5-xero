<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLfivexeroAccount extends Migration
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

        $type = ['BANK','CURRENT','CURRLIAB','DEPRECIATN','DIRECTCOSTS','EQUITY','EXPENSE','FIXED','INVENTORY','LIABILITY','NONCURRENT','OTHERINCOME','OVERHEADS','PREPAYMENT','REVENUE','SALES','TERMLIAB','PAYGLIABILITY','SUPERANNUATIONEXPENSE','SUPERANNUATIONLIABILITY','WAGESEXPENSE','WAGESPAYABLELIABILITY'];
        $bankAccountType = ['BANK','CREDITCARD','PAYPAL'];
        $class = ['ASSET','EQUITY','EXPENSE','LIABILITY','REVENUE'];
        
        Schema::create($this->prefix.'accounts', function(Blueprint $t) use ($type, $bankAccountType, $class)
        {  
            $t->increments('id')->unsigned();

            $t->string('Code');
            $t->string('Name');
            $t->string('BankAccountNumber');
            $t->string('Description');
            $t->string('CurrencyCode');
            $t->boolean('EnablePaymentsToAccount');
            $t->boolean('ShowInExpenseClaims');
            $t->string('AccountID');
            $t->string('ReportingCode');
            $t->string('ReportingCodeName');
            $t->boolean('HasAttachments');
            $t->timestamp('UpdatedDateUTC');


            $t->enum('Type', $type )->nullable();
            $t->enum('BankAccountType', $bankAccountType )->nullable();
            $t->enum('Class', $class )->nullable();

            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix.'accounts');
    }
}
