<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Contact extends Model {
    
	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contacts';

    public function __construct()
    {
        $this->table = config('xero.prefix').$this->table;
    }
    
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