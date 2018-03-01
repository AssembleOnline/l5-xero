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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'Name',
    'Status',
    'ContactGroupID',
    ];

    /**
     * The attributes that are required.
     *
     * @var array
     */

    public function __construct()
    {
        $this->table = config('xero.prefix').$this->table;
    }

   	public function contacts()
   	{
   		return $this->hasMany('Assemble\l5xero\Models\Contact');
   	}

   	

}