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
    ];

    public function payments()
    {
    	return $this->hasMany('Assemble\l5xero\Models\Payment');
    }

    public function allocations()
    {
    	return $this->hasMany('Assemble\l5xero\Models\Allocation');
    }

    public function line_items()
    {
    	return $this->hasMany('Assemble\l5xero\Models\LineItem');
    }

}