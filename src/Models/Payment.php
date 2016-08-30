<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Payment extends Eloquent {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'Account_id',
		'Date',
		'CurrencyRate',
		'Amount',
		'Reference',
		'IsReconciled',
		'Status',
		'PaymentType',
		'UpdatedDateUTC',
		'PaymentID',
		'Invoice_id',
		'CreditNote_id',
		'Prepayment_id',
		'Overpayment_id',
    ];


   	public function account()
   	{
   		return $this->belongsTo('Assemble\l5xero\Account');
   	}

   	public function invoice()
   	{
   		return $this->belongsTo('Assemble\l5xero\Invoice');
   	}

   	public function credit_note()
   	{
   		return $this->belongsTo('Assemble\l5xero\CreditNote');
   	}

   	public function prepayment()
   	{
   		return $this->belongsTo('Assemble\l5xero\Prepayment');
   	}

   	public function overpayment()
   	{
   		return $this->belongsTo('Assemble\l5xero\Overpayment');
   	}

}