<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class PurchaseDetail extends Model {

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