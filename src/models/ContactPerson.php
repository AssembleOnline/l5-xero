<?php
namespace \Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class ContactPerson extends Eloquent {
    
    private $prefix = 'lfivexero_';

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = $prefix.'contact_persons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'FirstName',
		'LastName',
		'EmailAddress',
		'IncludeInEmails',
    ];


   	public function contact()
   	{
   		return $this->belongsTo('\Assemble\l5xero\Models\Contact');
   	}

}