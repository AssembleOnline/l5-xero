<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class Phone extends Model {

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'phones';

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
      'PhoneType',
      'PhoneNumber',
      'PhoneAreaCode',
      'PhoneCountryCode',
      'Contact_id',
    ];


   	public function contact()
   	{
   		return $this->hasOne('Assemble\l5xero\Contact', 'Contact_id');
   	}

}