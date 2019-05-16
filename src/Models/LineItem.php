<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class LineItem extends Model {
    

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'line_items';

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
		'Description',
		'Quantity',
		'UnitAmount',
		'ItemCode',
		'AccountCode',
		'LineItemID',
		'TaxType',
		'TaxAmount',
		'LineAmount',
		'DiscountRate',
		'Invoice_id',
    ];

    public function Invoice()
    {
        return $this->belongsTo('Assemble\l5xero\Models\Invoice', 'Invoice_id');
    }

    public function Item()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\Item', 'ItemCode', 'Code');
    }


}