<?php
namespace Assemble\l5xero\Models;

use Assemble\l5xero\Models\Model as Model;

class ContactGroup extends Model {
    
	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contact_groups';
    
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
    'Name',
    'Status',
    'ContactGroupID',
    ];

   	public function contacts()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Contact');
   	}

   	

}