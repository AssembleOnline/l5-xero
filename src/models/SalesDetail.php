<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class PurchaseDetail extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'sales_details';

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
    	return $this->hasOne('\Assemble\l5xero\Models\Item');
    }

}