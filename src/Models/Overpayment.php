<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Overpayment extends Eloquent {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_overpayments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'Reference',
		'OverpaymentID',
		'Type',
		'Date',
		'Status',
		'LineAmountTypes',
		'SubTotal',
		'TotalTax',
		'Total',
		'UpdatedDateUTC',
		'CurrencyCode',
		'FullyPaidOnDate',
		'CurrencyRate',
		'RemainingCredit',
		'HasAttachments',
		'Contact_id',
    ];


   	public function contact()
   	{
   		return $this->belongsTo('Assemble\l5xero\Contact');
   	}

   	public function payment()
   	{
   		return $this->hasOne('Assemble\l5xero\Payment');
   	}

}