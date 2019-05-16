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

    /**
    *   attribute to track what type of save occured in events
    */
    public $save_event_type = 0;
    public $internal_original_attributes = [];
    
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $unique = [
      'AccountNumber',
      'Name'
    ];

    public function ContactGroups()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\ContactGroup', 'ContactGroup_id');
    }

   	public function ContactPersons()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\ContactPerson', 'Contact_id');
   	}
    // Possible pluralisation issues have been seen here, need to clarify versioning.
    public function ContactPeople()
    {
      return $this->hasMany('Assemble\l5xero\Models\ContactPerson', 'Contact_id');
    }

   	public function Addresses()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Address', 'Contact_id');
   	}

   	public function Phones()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Phone', 'Contact_id');
   	}

   	public function Invoices()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Invoice', 'Contact_id');
   	}

}