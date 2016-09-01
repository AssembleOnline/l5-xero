<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Address extends Model {
    
	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'addresses';

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
    'Contact_id'
    ];


   	public function contact()
   	{
   		return $this->hasOne('Assemble\l5xero\Models\Contact');
   	}

}