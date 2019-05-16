<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class CreditNote extends Model {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'credit_notes';

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
   		return $this->belongsTo('Assemble\l5xero\Models\Contact', 'Contact_id');
   	}

   	public function payment()
   	{
   		return $this->hasOne('Assemble\l5xero\Models\Payment', 'CreditNote_id');
   	}

    public function allocation()
    {
      return $this->belongsTo('Assemble\l5xero\Models\Allocation');
    }

}