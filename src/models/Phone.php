<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Phone extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'phones';

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
    ];


   	public function contact()
   	{
   		return $this->hasOne('\Assemble\l5xero\Contact');
   	}

}