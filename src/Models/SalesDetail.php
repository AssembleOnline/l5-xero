<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class SalesDetail extends Eloquent {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lfivexero_sales_details';

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