<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class PurchaseDetails extends Model {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_details';

    public function __construct()
    {
        $this->table = config('xero.prefix').$this->table;
    }

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
		'UnitPrice',
		'AccountCode',
		'COGSAccountCode',
		'UpdatedDateUTC',
		'TaxType',
    ];

    public function item()
    {
    	return $this->hasOne('Assemble\l5xero\Models\Item');
    }

}