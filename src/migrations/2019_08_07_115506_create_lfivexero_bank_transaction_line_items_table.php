<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLfivexeroBankTransactionLineItemsTable extends Migration
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
        $tax_type = ["CAPEXINPUT","CAPEXINPUT2","EXEMPTOUTPUT","GSTONCAPIMPORTS","IMINPUT","INPUT","INPUT2","INPUT3","INPUT4","NONE","OUTPUT","OUTPUT2","OUTPUT3","OUTPUT4","SROUTPUT","SROUTPUT2","ZERORATED","ZERORATEDOUTPUT"];

        Schema::create($this->prefix.'bank_transaction_line_items', function(Blueprint $table) use ($tax_type) {  
            $table->increments('id')->unsigned();
            $table->integer('BankTransaction_id')->unsigned()->nullable();
            $table->integer('Tracking_id')->unsigned()->nullable();
            $table->string('Description')->nullable();
            $table->string('Quantity')->nullable();
            $table->string('AccountCode')->nullable();
            $table->string('ItemCode')->nullable();
            $table->string('LineItemID')->nullable();
            $table->decimal('UnitAmount', 10, 4)->nullable();
            $table->decimal('LineAmount', 10, 4)->nullable();

            $table->enum('TaxType', $tax_type)->nullable();

            $table->timestamps();

            $table->foreign('BankTransaction_id', 'bt_bt_line_item')->references('id')->on($this->prefix.'bank_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix.'bank_transaction_line_items');
    }
}
