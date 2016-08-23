<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Invoice extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'Type',
		'Contact_id',
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
		'Payments_id',
		'Prepayments_id',
		'Overpayments_id',
		'CreditNotes_id',
    ];

    public function payments()
    {
    	return $this->hasMany('\Assemble\l5xero\Models\Payment');
    }

    public function allocations()
    {
    	return $this->hasMany('\Assemble\l5xero\Models\Allocation');
    }

    public function line_items()
    {
    	return $this->hasMany('\Assemble\l5xero\Models\LineItem');
    }

}