<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountidRelationToPayments extends Migration
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
        //
        Schema::table($this->prefix.'payments', function ($table) {
            $table->integer('Account_id')->unsigned()->nullable();
            
            $table->foreign('Account_id')->references('id')->on($this->prefix.'accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
