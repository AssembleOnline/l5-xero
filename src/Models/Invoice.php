<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Invoice extends Model {
	
	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices';

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
    	'Type',
		'Date',
		'DueDate',
		'LineAmountTypes',
		'InvoiceNumber',
		'Reference',
		'BrandingThemeID',
		'Url',
		'CurrencyCode',
		'CurrencyRate',
		'Status',
		'SentToContact',
		'ExpectedPaymentDate',
		'PlannedPaymentDate',
		'SubTotal',
		'TotalTax',
		'Total',
		'TotalDiscount',
		'InvoiceID',
		'HasAttachments',
		'AmountDue',
		'AmountPaid',
		'FullyPaidOnDate',
		'AmountCredited',
		'UpdatedDateUTC',
        'Contact_id',
    ];

    protected $with = [
        'Contact',
        'LineItems',
        'Payments',
    ];

    public function Contact()
    {
        return $this->belongsTo('Assemble\l5xero\Models\Contact', 'Contact_id', 'id');
    }

    public function Payments()
    {
    	return $this->hasMany('Assemble\l5xero\Models\Payment', 'Invoice_id');
    }

    public function Allocations()
    {
    	return $this->hasMany('Assemble\l5xero\Models\Allocation');
    }

    public function LineItems()
    {
    	return $this->hasMany('Assemble\l5xero\Models\LineItem', 'Invoice_id', 'id');
    }

}