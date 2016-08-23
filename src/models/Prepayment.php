<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Prepayment extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'prepayments';

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
   		return $this->belongsTo('\Assemble\l5xero\Contact');
   	}

   	public function payment()
   	{
   		return $this->hasOne('\Assemble\l5xero\Payment');
   	}

    public function allocation()
    {
      return $this->belongsTo('\Assemble\l5xero\Allocation')
    }

}