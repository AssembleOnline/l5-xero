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