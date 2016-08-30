<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Allocation extends Eloquent {
    

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_allocations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'AppliedAmount',
		'Date',
		'Invoice_id',
    ];


   	public function invoice()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\Invoice');
   	}

   	public function overpayments()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Overpayment');
   	}
   	public function prepayments()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Prepayment');
   	}
   	public function credit_notes()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\CreditNote');
   	}

}