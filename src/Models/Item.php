<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Item extends Model {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'items';

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
		'ItemID',
		'Code',
		'InventoryAssetAccountCode',
		'Name',
		'IsSold',
		'IsPurchased',
		'Description',
		'PurchaseDescription',
		'PurchaseDetails_id',
		'SalesDetails_id',
		'IsTrackedAsInventory',
		'TotalCostPool',
		'UpdatedDateUTC',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $unique = [
      'Code'
    ];

    public function line_items()
    {
    	return $this->hasMany('Assemble\l5xero\Models\LineItem');
    }

    public function purchase_details()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\PurchaseDetails', 'Item_id');
    }

    public function sales_details()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\SalesDetails', 'Item_id');
    }

}