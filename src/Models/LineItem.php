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

    public function Item()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\Item');
    }


}