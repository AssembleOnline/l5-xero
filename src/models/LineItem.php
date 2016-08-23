<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class LineItem extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'line_items';

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
		'Item_id',
    ];

    public function Item()
    {
    	return $this->belongsTo('\Assemble\l5xero\Models\Item');
    }


}