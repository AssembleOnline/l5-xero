<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Contact extends Eloquent {
    
	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'ContactID',
		'ContactNumber',
		'AccountNumber',
		'ContactStatus',
		'Name',
		'FirstName',
		'LastName',
		'EmailAddress',
		'SkypeUserName',
		'BankAccountDetails',
		'TaxNumber',
		'AccountsReceivableTaxType',
		'AccountsPayableTaxType',
		'IsSupplier',
		'IsCustomer',
		'DefaultCurrency',
		'XeroNetworkKey',
		'SalesDefaultAccountCode',
		'PurchasesDefaultAccountCode',
		'UpdatedDateUTC',
		'Website',
		'BatchPayments',
		'Discount',
		'Balances',
		'HasAttachments',
		'BrandingTheme_id',
    ];

    public function contact_group()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\ContactGroup');
    }

   	public function contact_persons()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\ContactPerson');
   	}

   	public function addresses()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Address');
   	}

   	public function phones()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Phones');
   	}

   	public function invoices()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Invoice');
   	}

}