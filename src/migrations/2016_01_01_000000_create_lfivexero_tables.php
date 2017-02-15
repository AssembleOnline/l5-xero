<?php
// src/migrations/0000_00_00_000000_create_lfivezero_tables.php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLfivexeroTables extends Migration
{
    private $prefix = '';

    public function __construct()
    {
        $this->prefix = config('xero.prefix');
    }

    public function up()
    {

        $LineAmountTypes = ['Exclusive', 'Inclusive', 'NoTax'];
        
        $Status = ['DRAFT', 'SUBMITTED', 'AUTHORISED', 'DELETED', 'VOIDED', 'PAID'];
        
        $PaymentType = ['ACCRECPAYMENT', 'ACCPAYPAYMENT', 'ARCREDITPAYMENT', 'APCREDITPAYMENT', 'AROVERPAYMENTPAYMENT', 'ARPREPAYMENTPAYMENT', 'APPREPAYMENTPAYMENT', 'APOVERPAYMENTPAYMENT'];
        
        $PrePaymentType = ['RECEIVE-PREPAYMENT', 'SPEND-PREPAYMENT'];
        $PrePaymentStatus = ['AUTHORISED', 'PAID', 'VOIDED'];
        
        $OverPaymentType = ['RECEIVE-OVERPAYMENT', 'SPEND-OVERPAYMENT'];
        $OverPaymentStatus = ['AUTHORISED', 'PAID', 'VOIDED'];
        
        $CreditNoteType = ['ACCPAYCREDIT', 'ACCRECCREDIT'];
        $CreditNoteStatus = ['DRAFT', 'SUBMITTED', 'DELETED', 'AUTHORISED', 'PAID', 'VOIDED'];

        $ContactStatus = ['ACTIVE', 'ARCHIVED'];
        
        $AddressType = ['POBOX', 'STREET', 'DELIVERY'];
        $PhoneType = ['DEFAULT', 'DDI', 'MOBILE', 'FAX'];


        /*
        *   Setup The <PurchaseDetails> element to be used for <Item> in xero.
        */
        Schema::create($this->prefix.'purchase_details', function(Blueprint $t)
        {  
            $t->increments('id')->unsigned();
            $t->decimal('UnitPrice', 10, 4)->nullable();
            $t->string('AccountCode', 50)->nullable();
            $t->string('COGSAccountCode', 10)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();
            $t->string('TaxType', 50)->nullable();

            $t->integer('Item_id')->unsigned()->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <SalesDetails> element to be used for <Item> in xero.
        */
        Schema::create($this->prefix.'sales_details', function(Blueprint $t)
        {  
            $t->increments('id');
            $t->decimal('UnitPrice', 10, 4)->nullable();
            $t->string('AccountCode', 50)->nullable();
            $t->string('COGSAccountCode', 10)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();
            $t->string('TaxType', 50)->nullable();

            $t->integer('Item_id')->unsigned()->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <Item> element to be used for <LineItem> in xero.
        */
        Schema::create($this->prefix.'items', function(Blueprint $t)
        {  
            $t->increments('id')->unsigned();
            $t->string('ItemID', 50)->nullable();
            $t->string('Code', 50);
            $t->string('InventoryAssetAccountCode', 10)->nullable();
            $t->string('Name', 50)->nullable();
            $t->boolean('IsSold')->nullable();
            $t->boolean('IsPurchased')->nullable();
            $t->text('Description')->nullable();
            $t->text('PurchaseDescription')->nullable();
            $t->integer('PurchaseDetails_id')->unsigned()->nullable();
            $t->integer('SalesDetails_id')->unsigned()->nullable();
            $t->string('IsTrackedAsInventory', 100)->nullable();
            $t->string('TotalCostPool', 100)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <CreditNote> element
        */
        Schema::create($this->prefix.'credit_notes', function(Blueprint $t) use ($CreditNoteType, $CreditNoteStatus, $LineAmountTypes)
        {
            $t->increments('id')->unsigned();

            $t->enum('Type', $CreditNoteType)->nullable();
            $t->string('Date')->nullable();
            $t->enum('Status', $CreditNoteStatus)->nullable();
            $t->enum('LineAmountTypes', $LineAmountTypes)->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->timestamp('UpdatedDateUTC')->nullable();
            $t->string('CurrencyCode')->nullable();
            $t->string('FullyPaidOnDate')->nullable();
            $t->string('CreditNoteID')->nullable();
            $t->string('CreditNoteNumber')->nullable();
            $t->string('Reference')->nullable();
            $t->boolean('SentToContact')->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->string('RemainingCredit')->nullable();
            $t->string('BrandingThemeID')->nullable();
            $t->boolean('HasAttachments')->nullable();

            $t->integer('Contact_id')->unsigned()->nullable();
            // $t->string('Allocations')->nullable();
            // $t->string('LineItems')->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <Overpayment> element
        */
        Schema::create($this->prefix.'prepayments', function(Blueprint $t) use ($PrePaymentType, $PrePaymentStatus)
        {
            $t->increments('id')->unsigned();

            $t->string('Reference')->nullable();
            $t->string('PrepaymentID')->nullable();
            $t->enum('Type', $PrePaymentType)->nullable();
            $t->date('Date')->nullable();
            $t->enum('Status', $PrePaymentStatus)->nullable();
            $t->string('LineAmountTypes')->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->timestamp('UpdatedDateUTC')->nullable();
            $t->string('CurrencyCode')->nullable();
            $t->string('FullyPaidOnDate')->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->string('RemainingCredit', 255)->nullable();
            $t->boolean('HasAttachments')->nullable();
            
            //Many
            $t->integer('Contact_id')->unsigned()->nullable();
            // $t->string('LineItems')->nullable();
            // $t->string('Allocations')->nullable();
            // $t->string('Payments')->nullable();
            
            $t->timestamps();
        });

        /*
        *   Setup The <Overpayment> element
        */
        Schema::create($this->prefix.'overpayments', function(Blueprint $t) use ($OverPaymentType, $OverPaymentStatus)
        {
            $t->increments('id')->unsigned();

            $t->string('Reference')->nullable();
            $t->string('OverpaymentID')->nullable();
            $t->enum('Type', $OverPaymentType)->nullable();
            $t->date('Date')->nullable();
            $t->enum('Status', $OverPaymentStatus)->nullable();
            $t->string('LineAmountTypes')->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->timestamp('UpdatedDateUTC')->nullable();
            $t->string('CurrencyCode')->nullable();
            $t->string('FullyPaidOnDate')->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->string('RemainingCredit', 255)->nullable();
            $t->boolean('HasAttachments')->nullable();
            
            //Many
            $t->integer('Contact_id')->unsigned()->nullable();
            // $t->string('LineItems')->nullable();
            // $t->string('Allocations')->nullable();
            // $t->string('Payments')->nullable();
            
            $t->timestamps();
        });

        /*
        *   Setup The <Payment> element
        */
        Schema::create($this->prefix.'payments', function(Blueprint $t) use ($Status, $PaymentType)
        {
            $t->increments('id')->unsigned();

            $t->string('AccountID')->nullable();

            $t->string('Date')->nullable();
            $t->string('CurrencyRate')->nullable();
            $t->decimal('Amount', 10, 4);
            $t->string('Reference')->nullable();
            $t->string('IsReconciled')->nullable();
            $t->enum('Status', $Status)->nullable();
            $t->enum('PaymentType', $PaymentType)->nullable();
            $t->datetime('UpdatedDateUTC')->nullable();
            $t->string('PaymentID')->nullable();

            /* Invoice -- Many Relation */
            /* CreditNote -- Many Relation */
            /* Prepayment -- Many Relation */
            /* Overpayment -- Many Relation */

            $t->integer('Invoice_id')->unsigned()->nullable();
            $t->integer('CreditNote_id')->unsigned()->nullable();
            $t->integer('Prepayment_id')->unsigned()->nullable();
            $t->integer('Overpayment_id')->unsigned()->nullable();

            $t->timestamps();
        });

        /*
        *   Allocations
        */
        Schema::create($this->prefix.'allocations', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();

            $t->decimal('AppliedAmount', 10, 4)->nullable();
            $t->dateTime('Date')->nullable();

            $t->integer('Invoice_id')->unsigned();

        });
       
        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'invoices', function(Blueprint $t) use ($LineAmountTypes, $Status)
        {
            $t->increments('id')->unsigned();

            $t->string('Type', 50);
            $t->integer('Contact_id')->unsigned()->nullable();
            //LineItems
            $t->date('Date')->nullable();
            $t->date('DueDate')->nullable();
            $t->enum('LineAmountTypes', $LineAmountTypes)->nullable();
            $t->string('InvoiceNumber', 50)->nullable();
            $t->string('Reference', 50)->nullable();
            $t->string('BrandingThemeID', 50)->nullable();
            $t->string('Url', 50)->nullable();
            $t->string('CurrencyCode', 50)->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->enum('Status', $Status)->nullable();
            $t->boolean('SentToContact')->nullable();
            $t->string('ExpectedPaymentDate', 50)->nullable();
            $t->string('PlannedPaymentDate', 50)->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->decimal('TotalDiscount', 10, 4)->nullable();
            $t->string('InvoiceID', 50)->nullable();
            $t->boolean('HasAttachments')->nullable();
            $t->decimal('AmountDue', 10, 4)->nullable();
            $t->decimal('AmountPaid', 10, 4)->nullable();
            $t->dateTime('FullyPaidOnDate')->nullable();
            $t->decimal('AmountCredited', 10, 4)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();

            $t->integer('Payments_id')->unsigned()->nullable();
            $t->integer('Prepayments_id')->unsigned()->nullable();
            $t->integer('Overpayments_id')->unsigned()->nullable();
            $t->integer('CreditNotes_id')->unsigned()->nullable();


            $t->timestamps();
        });
        
        /*
        *   Setup The <LineItem> element to be used for <Invoice> in xero.
        */
        Schema::create($this->prefix.'line_items', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->string('Description', 50)->nullable();
            $t->string('Quantity', 50)->nullable();
            $t->decimal('UnitAmount', 10, 4)->nullable();
            $t->string('ItemCode', 50)->nullable();
            $t->string('AccountCode', 50)->nullable();
            $t->string('LineItemID', 50)->nullable();
            $t->string('TaxType', 50)->nullable();
            $t->decimal('TaxAmount', 10, 4)->nullable();
            $t->decimal('LineAmount', 10, 4)->nullable();
            // Tracking
            $t->string('DiscountRate', 50)->nullable();

            
            $t->integer('Invoice_id')->unsigned();


            $t->timestamps();
        });

        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'contact_persons', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->string('FirstName', 50)->nullable();
            $t->string('LastName', 50)->nullable();
            $t->string('EmailAddress', 50)->nullable();
            $t->boolean('IncludeInEmails', 50)->nullable();

            $t->integer('Contact_id')->unsigned()->nullable();


            $t->timestamps();
        });

        /*
        *   Setup The <Contact> element
        */
        Schema::create($this->prefix.'addresses', function(Blueprint $t) use ($AddressType)
        {
            $t->increments('id')->unsigned();
            $t->enum('AddressType', $AddressType)->nullable();
            $t->string('AddressLine1')->nullable();
            $t->string('AddressLine2')->nullable();
            $t->string('AddressLine3')->nullable();
            $t->string('AddressLine4')->nullable();
            $t->string('City')->nullable();
            $t->string('Region')->nullable();
            $t->string('PostalCode')->nullable();
            $t->string('Country')->nullable();
            $t->string('AttentionTo')->nullable();

            $t->integer('Contact_id')->unsigned()->nullable();


            $t->timestamps();
        });

        /*
        *   Setup The <Contact> element
        */
        Schema::create($this->prefix.'phones', function(Blueprint $t) use ($PhoneType)
        {
            $t->increments('id')->unsigned();
            $t->enum('PhoneType', $PhoneType)->nullable();
            $t->string('PhoneNumber')->nullable();
            $t->string('PhoneAreaCode')->nullable();
            $t->string('PhoneCountryCode')->nullable();

            $t->integer('Contact_id')->unsigned()->nullable();


            $t->timestamps();
        });

        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'contacts', function(Blueprint $t) use ($ContactStatus)
        {
            $t->increments('id')->unsigned();
            $t->string('ContactID', 50)->nullable();
            $t->string('ContactNumber', 50)->nullable();
            $t->string('AccountNumber', 50)->nullable();
            $t->enum('ContactStatus', $ContactStatus)->nullable();
            $t->string('Name', 50)->nullable();
            $t->string('FirstName', 50)->nullable();
            $t->string('LastName', 50)->nullable();
            $t->string('EmailAddress', 50)->nullable();
            $t->string('SkypeUserName', 50)->nullable();
            $t->string('BankAccountDetails', 50)->nullable();
            $t->string('TaxNumber', 50)->nullable();
            $t->string('AccountsReceivableTaxType', 50)->nullable();
            $t->string('AccountsPayableTaxType', 50)->nullable();
            $t->boolean('IsSupplier')->nullable();
            $t->boolean('IsCustomer')->nullable();
            $t->string('DefaultCurrency', 50)->nullable();
            $t->string('XeroNetworkKey', 50)->nullable();
            $t->string('SalesDefaultAccountCode', 50)->nullable();
            $t->string('PurchasesDefaultAccountCode', 50)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();
            $t->string('Website', 50)->nullable();
            $t->string('BatchPayments', 50)->nullable();
            $t->decimal('Discount', 10, 4)->nullable();
            $t->string('Balances', 50)->nullable();
            $t->boolean('HasAttachments')->nullable();

            $t->integer('BrandingTheme_id')->unsigned()->nullable();


            /* ContactPersons -- Many Relationship */
            /* Addresses -- Many Relationship */
            /* Phones -- Many Relationship */
            /* ContactGroups -- Many Relationship */

            /* SalesTrackingCategories -- Not Currently Required... add in future pull req */
            /* PurchasesTrackingCategories -- Not Currently Required... add in future pull req */
            // $t->integer('PaymentTerms_id')->unsigned()->nullable();

            //TODO link tables


            $t->timestamps();
        });

        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'contact_groups', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            
            $t->string('Name')->nullable();
            $t->string('Status')->nullable();
            $t->string('ContactGroupID')->nullable();
            
            $t->timestamps();
        });

        //Add One to Many Relations
        // $this->createLink('contacts', 'contact_persons');
        // $this->createLink('contacts', 'addresses');
        // $this->createLink('contacts', 'phones');

        //Add Many to Many Relations
        $this->createLink('contact_groups', 'contacts');
        

    }

    public function down()
    {
        //Add One to Many Relations
        // $this->dropLink('contacts', 'contact_persons');
        // $this->dropLink('contacts', 'addresses');
        // $this->dropLink('contacts', 'phones');

        //Add Many to Many Relations
        $this->dropLink('contact_groups', 'contacts');

        Schema::drop($this->prefix.'line_items');
        Schema::drop($this->prefix.'purchase_details');
        Schema::drop($this->prefix.'sales_details');
        Schema::drop($this->prefix.'items');
        Schema::drop($this->prefix.'credit_notes');
        Schema::drop($this->prefix.'prepayments');
        Schema::drop($this->prefix.'overpayments');
        Schema::drop($this->prefix.'payments');
        Schema::drop($this->prefix.'allocations');
        Schema::drop($this->prefix.'invoices');
        Schema::drop($this->prefix.'line_items');
        Schema::drop($this->prefix.'contact_persons');
        Schema::drop($this->prefix.'addresses');
        Schema::drop($this->prefix.'phones');
        Schema::drop($this->prefix.'contacts');
        Schema::drop($this->prefix.'contact_groups');



    }




    private function createLink($table, $table2)
    {
        Schema::create($this->prefix.$table.'_'.$table2, function($t) use ($table, $table2) 
        {
            $t->increments('id');
            $t->integer($table.'_id')->unsigned();
            $t->integer($table2.'_id')->unsigned();
            $t->timestamps();
        });
    }
    private function dropLink($table, $table2)
    {
        Schema::drop($this->prefix.$table.'_'.$table2);
    }

}