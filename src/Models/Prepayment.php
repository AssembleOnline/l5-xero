<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Prepayment extends Model {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prepayments';

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
   		return $this->belongsTo('Assemble\l5xero\Contact', 'Contact_id');
   	}

   	public function payment()
   	{
   		return $this->hasOne('Assemble\l5xero\Payment', 'Prepayment_id');
   	}

    public function allocation()
    {
      return $this->belongsTo('Assemble\l5xero\Allocation', 'Allocation_id');
    }

}