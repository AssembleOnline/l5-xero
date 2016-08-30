<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Item extends Eloquent {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_items';

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

    public function line_items()
    {
    	return $this->hasMany('Assemble\l5xero\Models\LineItem');
    }

    public function purchase_details()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\PurchaseDetail');
    }

    public function sales_details()
    {
    	return $this->belongsTo('Assemble\l5xero\Models\SalesDetail');
    }

}