<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingLfivexeroAccountFields extends Migration
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
        $status = ['ACTIVE','ARCHIVED'];
        $taxType = ["CAPEXINPUT","CAPEXINPUT2","EXEMPTOUTPUT","GSTONCAPIMPORTS","IMINPUT","INPUT","INPUT2","INPUT3","INPUT4","NONE","OUTPUT","OUTPUT2","OUTPUT3","OUTPUT4","SROUTPUT","SROUTPUT2","ZERORATED","ZERORATEDOUTPUT"];
        $systemAccount = ['DEBTORS','CREDITORS','BANKCURRENCYGAIN','GST','GSTONIMPORTS','HISTORICAL','REALISEDCURRENCYGAIN','RETAINEDEARNINGS','ROUNDING','TRACKINGTRANSFERS','UNPAIDEXPCLM','UNREALISEDCURRENCYGAIN','WAGEPAYABLES'];

        Schema::table($this->prefix.'accounts', function ($table) use ($status,$taxType,$systemAccount) {
            $table->enum('Status', $status )->nullable();
            $table->enum('TaxType', $taxType )->nullable();
            $table->enum('SystemAccount', $systemAccount )->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->prefix.'accounts', function ($table) {
            $table->dropColumn(['Status','TaxType','SystemAccount']);
        });
    }
}
