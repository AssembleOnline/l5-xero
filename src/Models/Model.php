<?php
namespace Assemble\l5xero\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent {
    
    /**
     * initial constructor to prepend table names with prefix
     */
    public function __construct()
    {
        $this->table = config('xero.prefix').$this->table;
    }

    /**
    *   attribute to track what type of save occured in events
    */
    public $save_event_type = 0;
    public $internal_original_attributes = [];
    
    /**
     * The attributes that are required.
     *
     * @var array
     */
    protected $required = [];

    /**
     * Get the required attributes for the model.
     *
     * @return array
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set the required attributes for the model.
     *
     * @param  array  $required
     * @return $this
     */
    public function required(array $required)
    {
        $this->required = $required;

        return $this;
    }

    
    public function hasUpdateField() {
        return in_array('UpdatedDateUTC', $this->fillable);
    }

}