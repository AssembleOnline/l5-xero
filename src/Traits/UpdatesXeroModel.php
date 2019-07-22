<?php
namespace Assemble\l5xero\Traits;


trait UpdatesXeroModel {

    /**
     * Save recieved data to model in database
     *
     * @param String $GUID
     * @param Array $obj
     * @param String $model
     * @param Array $fillable
     * @param Mixed $parent_key
     * @param Mixed $parent_value
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\QueryException when unable to save
     */
    private function saveToModel($GUID, $obj, $model, $fillable, $parent_key = null, $parent_value = null)
    {
    
        /*
        *   set to string array if XeroPHP collection
        */
        if(!is_array($obj))
        {
            $obj = $obj->toStringArray();
        }

        // Find existing Entry
        $returned = (new $model);
        if(isset($obj[$GUID])) {

            // Test for existence based on XeroID GUID
            $saved = $returned->where($GUID, '=', $obj[$GUID])->first();

            // Test for existence based on Xero Model Unique Field(s)
            $uniques = $this->getModelUniques($model);
            if($saved == null && $uniques) {
                $returned = (new $model);
                foreach($uniques as $unique) {
                    if(isset($obj[$unique])) {
                        $returned = $returned->orWhere($unique, '=', $obj[$unique]);
                    }
                }
                $saved = $returned->first();
                
            }
        } else {
            $saved = null;
        }

        //create new object instance and save to DB
        $new = [];
        foreach($fillable as $item)
        {
            $new[$item] = ( isset($obj[$item]) ? $obj[$item] : null );
        }

        //Add id to new item if created in upper parent
        if($parent_key != null && $parent_value != null)
        {
            $new[$parent_key] = $parent_value;
        }
        
        $new['updated_at'] = date('Y-m-d H:i:s');

        if($saved == null)
        {
            $new['created_at'] = date('Y-m-d H:i:s');

            $returned = (new $model);
            $returned->fill($new);

            $original_attributes = $returned->getOriginal(); // track original
            
            $done = $returned->save();
            
            if(!$done) {
                \Log::error("L5XERO - ERROR: Failed To Save [".$model."] Relation [".$parent_key.":".$parent_value."] Reason [Failed To Save Child]");
            }

            $returned->save_event_type = 1;
            $returned->internal_original_attributes = $original_attributes;
            return $returned;
        }
        else
        {
            $saved->fill($new);
            
            $original_attributes = $saved->getOriginal(); // track original

            $done = $saved->save();

            
            if(!$done) {
                \Log::error("L5XERO - ERROR: Failed To Save [".$model."] Relation [".$parent_key.":".$parent_value."] Reason [Failed To Save Child]");
            }

            $saved->save_event_type = 2;
            $saved->internal_original_attributes = $original_attributes;
            return $saved;
        }
    }

    /**
     * Remove all the relations that exist on the local database but dont exist on xero side
     * @param String $GUID 
     * @param String $model
     * @param Array $guids
     * @param Mixed $parent_key
     * @param Mixed $parent_value
     *
     * @return int
     */
    public function removeOrphanedRelations($GUID,$model,$guids,$parent_key, $parent_value)
    {
        return $model::where($parent_key,$parent_value)
                ->whereNotin($GUID,$guids)
                ->delete();    
    }


    /**
     * Get unique fields from model definition
     * when $obj set it will return field values otherwise will return field names
     *
     * @param String $model
     * @param Array $obj - optional
     *
     * @return Array
     * @return Boolean - when no fields defined
     */
    private function getModelUniques($model, $obj = null) {
        if(property_exists($model, 'unique') && sizeof((new $model)->unique) > 0) {
            if($obj) {
                $fields = [];
                $item = ( $obj );
                foreach((new $model)->unique as $field) {
                    $fields[$field] = $obj[$field];
                } 
                return $fields;
            }
            return (new $model)->unique;
        }
        return false;
    }

}