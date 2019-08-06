<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Payment extends Model {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';
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
		// 'Account_id',
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
	
	protected $dates = [
		'Date'
	];


   	public function account()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\Account');
   	}

   	public function invoice()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\Invoice', 'Invoice_id');
   	}

   	public function credit_note()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\CreditNote', 'CreditNote_id');
   	}

   	public function prepayment()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\Prepayment', 'Prepayment_id');
   	}

   	public function overpayment()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\Overpayment', 'Overpayment_id');
   	}

}