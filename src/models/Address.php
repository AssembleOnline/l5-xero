<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Address extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'AddressType',
		'AddressLine1',
		'AddressLine2',
		'AddressLine3',
		'AddressLine4',
		'City',
		'Region',
		'PostalCode',
		'Country',
		'AttentionTo',
    ];


   	public function contact()
   	{
   		return $this->hasOne('\Assemble\l5xero\Models\Contact');
   	}

}