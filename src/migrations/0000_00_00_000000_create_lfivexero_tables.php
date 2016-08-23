<?php
// src/migrations/0000_00_00_000000_create_lfivezero_tables.php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXeroTables extends Migration
{
    private $prefix = 'lfivexero_';

    public function up()
    {

        $LineAmountTypes = ['Exclusive', 'Inclusive', 'NoTax'];
        
        $Status = ['DRAFT', 'SUBMITTED', 'AUTHORISED', 'DELETED'];
        
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
            $t->varchar('TaxType', 50)->nullable();

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
            $t->varchar('TaxType', 50)->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <Item> element to be used for <LineItem> in xero.
        */
        Schema::create($this->prefix.'items', function(Blueprint $t)
        {  
            $t->increments('id')->unsigned();
            $t->varchar('ItemID', 50)->nullable();
            $t->varchar('Code', 50);
            $t->varchar('InventoryAssetAccountCode', 10)->nullable();
            $t->varchar('Name', 50)->nullable();
            $t->boolean('IsSold')->nullable();
            $t->boolean('IsPurchased')->nullable();
            $t->text('Description')->nullable();
            $t->text('PurchaseDescription')->nullable();
            $t->int('PurchaseDetails_id')->unsigned()->nullable();
            $t->int('SalesDetails_id')->unsigned()->nullable();
            $t->varchar('IsTrackedAsInventory', 100)->nullable();
            $t->varchar('TotalCostPool', 100)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <CreditNote> element
        */
        Schema::create($this->prefix.'credit_notes', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();

            $t->enum('Type', $CreditNoteType)->nullable();
            $t->varchar('Date')->nullable();
            $t->enum('Status', $CreditNoteStatus)->nullable();
            $t->enum('LineAmountTypes', $LineAmountTypes)->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->timestamp('UpdatedDateUTC')->nullable();
            $t->varchar('CurrencyCode')->nullable();
            $t->varchar('FullyPaidOnDate')->nullable();
            $t->varchar('CreditNoteID')->nullable();
            $t->varchar('CreditNoteNumber')->nullable();
            $t->varchar('Reference')->nullable();
            $t->boolean('SentToContact')->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->varchar('RemainingCredit')->nullable();
            $t->varchar('BrandingThemeID')->nullable();
            $t->boolean('HasAttachments')->nullable();

            $t->int('Contact_id')->unsigned()->nullable();
            // $t->varchar('Allocations')->nullable();
            // $t->varchar('LineItems')->nullable();

            $t->timestamps();
        });

        /*
        *   Setup The <Overpayment> element
        */
        Schema::create($this->prefix.'prepayments', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();

            $t->varchar('Reference')->nullable();
            $t->varchar('OverpaymentID')->nullable();
            $t->enum('Type', $PrePaymentType)->nullable();
            $t->date('Date')->nullable();
            $t->enum('Status', $PrePaymentStatus)->nullable();
            $t->varchar('LineAmountTypes')->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->timestamp('UpdatedDateUTC')->nullable();
            $t->varchar('CurrencyCode')->nullable();
            $t->varchar('FullyPaidOnDate')->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->varchar('RemainingCredit', 255)->nullable();
            $t->boolean('HasAttachments')->nullable();
            
            //Many
            $t->int('Contact_id')->unsigned()->nullable();
            // $t->varchar('LineItems')->nullable();
            // $t->varchar('Allocations')->nullable();
            // $t->varchar('Payments')->nullable();
            
            $t->timestamps();
        });

        /*
        *   Setup The <Overpayment> element
        */
        Schema::create($this->prefix.'overpayments', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();

            $t->varchar('Reference')->nullable();
            $t->varchar('OverpaymentID')->nullable();
            $t->enum('Type', $OverPaymentType)->nullable();
            $t->date('Date')->nullable();
            $t->enum('Status', $OverPaymentStatus)->nullable();
            $t->varchar('LineAmountTypes')->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->timestamp('UpdatedDateUTC')->nullable();
            $t->varchar('CurrencyCode')->nullable();
            $t->varchar('FullyPaidOnDate')->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->varchar('RemainingCredit', 255)->nullable();
            $t->boolean('HasAttachments')->nullable();
            
            //Many
            $t->int('Contact_id')->unsigned()->nullable();
            // $t->varchar('LineItems')->nullable();
            // $t->varchar('Allocations')->nullable();
            // $t->varchar('Payments')->nullable();
            
            $t->timestamps();
        });

        /*
        *   Setup The <Payment> element
        */
        Schema::create($this->prefix.'payments', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();

            $t->int('Account_id')->unsigned()->nullable();

            $t->varchar('Date')->nullable();
            $t->varchar('CurrencyRate')->nullable();
            $t->decimal('Amount', 10, 4);
            $t->varchar('Reference')->nullable();
            $t->varchar('IsReconciled')->nullable();
            $t->enum('Status', $Status)->nullable();
            $t->enum('PaymentType', $PaymentType);
            $t->datetime('UpdatedDateUTC')->nullable();
            $t->varchar('PaymentID')->nullable();

            /* Invoice -- Many Relation */
            /* CreditNote -- Many Relation */
            /* Prepayment -- Many Relation */
            /* Overpayment -- Many Relation */

            $t->int('Invoice_id')->unsigned()->nullable();
            $t->int('CreditNote_id')->unsigned()->nullable();
            $t->int('Prepayment_id')->unsigned()->nullable();
            $t->int('Overpayment_id')->unsigned()->nullable();

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
        Schema::create($this->prefix.'invoices', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();

            $t->varchar('Type', 50);
            $t->varchar('Contact_id', 50);
            //LineItems
            $t->date('Date')->nullable();
            $t->date('DueDate')->nullable();
            $t->enum('LineAmountTypes', $LineAmountTypes)->nullable();
            $t->varchar('InvoiceNumber', 50)->nullable();
            $t->varchar('Reference', 50)->nullable();
            $t->varchar('BrandingThemeID', 50)->nullable();
            $t->varchar('Url', 50)->nullable();
            $t->varchar('CurrencyCode', 50)->nullable();
            $t->decimal('CurrencyRate', 10, 4)->nullable();
            $t->enum('Status', $Status)->nullable();
            $t->boolean('SentToContact')->nullable();
            $t->varchar('ExpectedPaymentDate', 50)->nullable();
            $t->varchar('PlannedPaymentDate', 50)->nullable();
            $t->decimal('SubTotal', 10, 4)->nullable();
            $t->decimal('TotalTax', 10, 4)->nullable();
            $t->decimal('Total', 10, 4)->nullable();
            $t->decimal('TotalDiscount', 10, 4)->nullable();
            $t->varchar('InvoiceID', 50)->nullable();
            $t->boolean('HasAttachments')->nullable();
            $t->decimal('AmountDue', 10, 4)->nullable();
            $t->decimal('AmountPaid', 10, 4)->nullable();
            $t->dateTime('FullyPaidOnDate')->nullable();
            $t->decimal('AmountCredited', 10, 4)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();

            $t->int('Payments_id')->unsigned()->nullable();
            $t->int('Prepayments_id')->unsigned()->nullable();
            $t->int('Overpayments_id')->unsigned()->nullable();
            $t->int('CreditNotes_id')->unsigned()->nullable();


            $t->timestamps();
        });
        
        /*
        *   Setup The <LineItem> element to be used for <Invoice> in xero.
        */
        Schema::create($this->prefix.'line_items', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->varchar('Description', 50)->nullable();
            $t->varchar('Quantity', 50)->nullable();
            $t->decimal('UnitAmount', 10, 4)->nullable();
            $t->varchar('ItemCode', 50)->nullable();
            $t->varchar('AccountCode', 50)->nullable();
            $t->varchar('LineItemID', 50)->nullable();
            $t->varchar('TaxType', 50)->nullable();
            $t->decimal('TaxAmount', 10, 4)->nullable();
            $t->decimal('LineAmount', 10, 4)->nullable();
            // Tracking
            $t->varchar('DiscountRate', 50)->nullable();

            
            $t->int('Invoice_id')->unsigned();
            $t->int('Item_id')->unsigned();


            $t->timestamps();
        });

        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'contact_persons', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->varchar('FirstName', 50)->nullable();
            $t->varchar('LastName', 50)->nullable();
            $t->varchar('EmailAddress', 50)->nullable();
            $t->boolean('IncludeInEmails', 50)->nullable();


            $t->timestamps();
        });

        /*
        *   Setup The <Contact> element
        */
        Schema::create($this->prefix.'addresses', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->enum('AddressType', $AddressType)->nullable();
            $t->varchar('AddressLine1')->nullable();
            $t->varchar('AddressLine2')->nullable();
            $t->varchar('AddressLine3')->nullable();
            $t->varchar('AddressLine4')->nullable();
            $t->varchar('City')->nullable();
            $t->varchar('Region')->nullable();
            $t->varchar('PostalCode')->nullable();
            $t->varchar('Country')->nullable();
            $t->varchar('AttentionTo')->nullable();


            $t->timestamps();
        });

        /*
        *   Setup The <Contact> element
        */
        Schema::create($this->prefix.'phones', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->enum('PhoneType', $PhoneType)->nullable();
            $t->varchar('PhoneNumber')->nullable();
            $t->varchar('PhoneAreaCode')->nullable();
            $t->varchar('PhoneCountryCode')->nullable();


            $t->timestamps();
        });

        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'contacts', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            $t->varchar('ContactID', 50)->nullable();
            $t->varchar('ContactNumber', 50)->nullable();
            $t->varchar('AccountNumber', 50)->nullable();
            $t->enum('ContactStatus', $ContactStatus)->nullable();
            $t->varchar('Name', 50)->nullable();
            $t->varchar('FirstName', 50)->nullable();
            $t->varchar('LastName', 50)->nullable();
            $t->varchar('EmailAddress', 50)->nullable();
            $t->varchar('SkypeUserName', 50)->nullable();
            $t->varchar('BankAccountDetails', 50)->nullable();
            $t->varchar('TaxNumber', 50)->nullable();
            $t->varchar('AccountsReceivableTaxType', 50)->nullable();
            $t->varchar('AccountsPayableTaxType', 50)->nullable();
            $t->boolean('IsSupplier')->nullable();
            $t->boolean('IsCustomer')->nullable();
            $t->varchar('DefaultCurrency', 50)->nullable();
            $t->varchar('XeroNetworkKey', 50)->nullable();
            $t->varchar('SalesDefaultAccountCode', 50)->nullable();
            $t->varchar('PurchasesDefaultAccountCode', 50)->nullable();
            $t->dateTime('UpdatedDateUTC')->nullable();
            $t->varchar('Website', 50)->nullable();
            $t->varchar('BatchPayments', 50)->nullable();
            $t->decimal('Discount', 10, 4)->nullable();
            $t->varchar('Balances', 50)->nullable();
            $t->boolean('HasAttachments')->nullable();

            $t->int('BrandingTheme_id')->unsigned()->nullable();

            /* ContactPersons -- Many Relationship */
            /* Addresses -- Many Relationship */
            /* Phones -- Many Relationship */
            /* ContactGroups -- Many Relationship */

            /* SalesTrackingCategories -- Not Currently Required... add in future pull req */
            /* PurchasesTrackingCategories -- Not Currently Required... add in future pull req */
            // $t->int('PaymentTerms_id')->unsigned()->nullable();

            //TODO link tables


            $t->timestamps();
        });

        /*
        *   Setup The <Invoice> element
        */
        Schema::create($this->prefix.'contact_groups', function(Blueprint $t)
        {
            $t->increments('id')->unsigned();
            
            $t->varchar('Name')->nullable();
            $t->varchar('Status')->nullable();
            $t->varchar('ContactGroupID')->nullable();
            
            $t->timestamps();
        });

        //Add One to Many Relations
        $this->createLink('contacts', 'contact_persons');
        $this->createLink('contacts', 'addresses');
        $this->createLink('contacts', 'phones');

        //Add Many to Many Relations
        $this->createLink('contact_groups', 'contacts');
        

    }

    public function down()
    {
        Schema::drop($this->prefix.'line_items');
    }

    private function createLink($table, $table2)
    {
        Schema::create($this->prefix.$table.'_'.$table2, function($table) {
            $table->increments('id');
            $table->integer($table.'_id');
            $table->integer($table2.'_id');
            $table->timestamps();
        });
    }

}