<?php
// src/migrations/0000_00_00_000000_create_xero_keys.php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLfivexeroKeys extends Migration
{
    private $prefix = 'lfivexero_';

    public function up()
    {
        
        /*
        *   Contact Link Tables
        */
        //Contact -> Contact Persons
        Schema::table($this->prefix.'contacts_contact_persons', function($table) {
           $table->foreign('contacts_id')->references('id')->on($this->prefix.'contacts');
           $table->foreign('contact_persons_id')->references('id')->on($this->prefix.'contact_persons');
        });
        //Contact -> Addresses
        Schema::table($this->prefix.'contacts_addresses', function($table) {
           $table->foreign('contacts_id')->references('id')->on($this->prefix.'contacts');
           $table->foreign('addresses_id')->references('id')->on($this->prefix.'addresses');
        });
        //Contact -> Phones
        Schema::table($this->prefix.'contacts_phones', function($table) {
           $table->foreign('contacts_id')->references('id')->on($this->prefix.'contacts');
           $table->foreign('phones_id')->references('id')->on($this->prefix.'phones');
        });
        //Contact Groups -> Contacts
        Schema::table($this->prefix.'contact_groups_contacts', function($table) {
           $table->foreign('contact_groups_id')->references('id')->on($this->prefix.'contact_groups');
           $table->foreign('contacts_id')->references('id')->on($this->prefix.'contacts');
        });


        /*
        *   Invoices Foreign Keys
        */
        Schema::table($this->prefix.'invoices', function($table) {
            $table->foreign('Contact_id')->references('id')->on($this->prefix.'contacts');
            $table->foreign('Payments_id')->references('id')->on($this->prefix.'invoices');
            $table->foreign('Prepayments_id')->references('id')->on($this->prefix.'prepayments');
            $table->foreign('Overpayments_id')->references('id')->on($this->prefix.'overpayments');
            $table->foreign('CreditNotes_id')->references('id')->on($this->prefix.'credit_notes');
        });
        //Invoice Line Items
        Schema::table($this->prefix.'line_items', function($table) {
           $table->foreign('Invoice_id')->references('id')->on($this->prefix.'invoices');
           $table->foreign('Item_id')->references('id')->on($this->prefix.'items');
        });
        //Allocations
        Schema::table($this->prefix.'allocations', function($table) {
           $table->foreign('Invoice_id')->references('id')->on($this->prefix.'invoices');
        });


        /*
        *   Payments Foreign Keys
        */
        Schema::table($this->prefix.'payments', function($table) {
            // $table->foreign('Account_id')->references('id')->on($this->prefix.'accounts'); // adding account tracking in future
            $table->foreign('Invoice_id')->references('id')->on($this->prefix.'invoices');
            $table->foreign('CreditNote_id')->references('id')->on($this->prefix.'items');
            $table->foreign('Prepayment_id')->references('id')->on($this->prefix.'prepayments');
            $table->foreign('Overpayment_id')->references('id')->on($this->prefix.'overpayments');
        });
        //Credit Notes Foreign Keys
        Schema::table($this->prefix.'credit_notes', function($table) {
            $table->foreign('Contact_id')->references('id')->on($this->prefix.'contacts');
        });
        //Prepayments Foreign Keys
        Schema::table($this->prefix.'prepayments', function($table) {
            $table->foreign('Contact_id')->references('id')->on($this->prefix.'contacts');
        });
        //Prepayments Foreign Keys
        Schema::table($this->prefix.'overpayments', function($table) {
            $table->foreign('Contact_id')->references('id')->on($this->prefix.'contacts');
        });



    }

    public function down()
    {

    }

    
}