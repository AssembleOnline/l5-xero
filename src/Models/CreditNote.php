<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class CreditNote extends Eloquent {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_credit_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
      'CreditNoteID',
      'CreditNoteNumber',
      'Reference',
      'SentToContact',
      'CurrencyRate',
      'RemainingCredit',
      'BrandingThemeID',
      'HasAttachments',
      'Contact_id',
    ];


   	public function contact()
   	{
   		return $this->belongsTo('Assemble\l5xero\Models\Contact');
   	}

   	public function payment()
   	{
   		return $this->hasOne('Assemble\l5xero\Models\Payment');
   	}

    public function allocation()
    {
      return $this->belongsTo('Assemble\l5xero\Models\Allocation');
    }

}