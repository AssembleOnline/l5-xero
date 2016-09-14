<?php
namespace Assemble\l5xero\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Assemble\l5xero\Jobs\Job;
use Assemble\l5xero\Traits\XeroClassMap;

use Assemble\l5xero\Xero;

use Log;
use DB;
use ReflectionClass;

class XeroPull extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, XeroClassMap;


    protected $xero;
    protected $prefix;
    protected $model;
    protected $map;
    protected $page;
    protected $saved = 0;
    protected $updated = 0;
    protected $callback;

    /**
     * Create a new job instance.
     *
     * @param  String $model
     * @return void
     */
    public function __construct($xero, $model, $page = null, $callback = null)
    {
        $this->xero = $xero;
    	$this->prefix = 'Assemble\\l5xero\\Models\\';
    	$map = $this->getXeroClassMap();
        $this->map = $map[$model];
    	$this->model = $model;
        $this->page = ( $page == null ? $page = 1 : $page );

        $this->callback = $callback;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch (strtolower($this->xero)) {
            case 'private':
                $xero = new Xero($this->xero);
            break;
            case 'public':
                $xero = new Xero($this->xero);
            break;
            case 'partner':
                $xero = new Xero($this->xero);
            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }
        $object = $xero->load($this->map['MODEL']);
        $pageable = call_user_func('XeroPHP\\Models\\'.$this->map['MODEL'].'::isPageable');
        $objects = ( $pageable ? $object->page($this->page)->execute() : $object->execute() );

        echo "FOUND [".count($objects)."] ".$this->model."(s)\n";
            
        $this->processModel($this->model, $this->map, $objects, null, null, true);

        echo "SAVED [".$this->saved."] UPDATED [".$this->updated."] ".$this->model."(s) & related Object(s)\n";
    	
        //Check page count if need more, queue them at front...
    	if($pageable == true && count($objects) == 100 && $this->page != null)
        {
            $this->page++;
            dispatch(new XeroPull($this->xero, $this->model, $this->page, $this->callback));
            echo "ADDED NEXT PAGE { ".$this->page." } TO QUEUE FOR ".$this->model."(s)\n";
        }

    }

    private function saveToModel($GUID, $obj, $model, $fillable, $parent_key = null, $parent_value = null)
    {
        $returned = (new $model);
        $saved = $returned->where($GUID, $obj[$GUID])->first();
    	
        /*
        *   set to string array if XeroPHP collection
        */
        if(!is_array($obj))
        {
            $obj = $obj->toStringArray();
        }


        //create new object instance and save to DB
        $new = [];
        foreach($fillable as $item)
        {
            $new[$item] = ( isset($obj[$item]) ? $obj[$item] : null );
        }



		// echo $parent_key.' -- '.$parent_value;
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
            $done = $returned->save();
            $this->saved++;

            return $returned;
        }
        else
        {
            $saved->fill($new);
            $done = $saved->save();
            $this->updated++;

            return $saved;
        }
    }

    private function queueCallback($object, $status)
    {
            $job = (new ReflectionClass($this->callback))->newInstanceArgs([$object, $status]);
            dispatch($job);
    }

    private function processModel($sub_key, $map, $withStuff, $parent_key = null, $parent_value = null, $shallow = false)
    {
    	$model = $this->prefix.$sub_key;
    	$instance = (new $model);
    	$items = [];
    	$fillable = $instance->getFillable();
        $sub = $map['SUB'];

        $last_updated = 0;
        $last_saved = 0;
        foreach($withStuff as $obj)
        {
    		//DO SAVE!
            $saved = $this->saveToModel($map['GUID'], $obj, $model, $fillable, $parent_key, $parent_value);


    		/*
    		*	Run for collection of sub elements
    		*/
    		if($sub != null && count($sub) > 0)
    		foreach($sub as $key => $sub_item)
    		{
    			if(isset($obj[$key.'s']) || isset($obj[$key]))
				{
                    //If the sub item kas the tag SINGLE then its a one-one relation so save directly
					if( isset($sub_item['SINGLE']))
			    	{
			    		$model_sub = $this->prefix.$key;
    					$instance_sub = (new $model_sub);
			    		$fillable_sub = $instance_sub->getFillable();
                        if($sub_item['SINGLE'] == 'HAS')
                        {
			    		   $saved_sub = $this->saveToModel($sub_item['GUID'], $obj[$key], $model_sub, $fillable_sub, $sub_key.'_id', $saved->id);
                        }
                        elseif($sub_item['SINGLE'] == 'BELONGS')
                        {
                           $saved_sub = $this->saveToModel($sub_item['GUID'], $obj[$key], $model_sub, $fillable_sub);
                           $saved->{$key.'_id'} = $saved_sub->id;
                           $saved->save();
                        }

			    	}
			    	else // otherwise process the sub objects as one-many relations
			    	{
						$this->processModel($key, $sub_item, $obj[str_plural($key)], $sub_key.'_id', $saved->id);
			    	}
			    		
    			}
    		}

            if($shallow == true && $this->callback != null && isset($this->callback) )
            {
                if($this->saved > $last_saved)
                {
                    $this->queueCallback($saved, 'create');
                }
                else
                {
                    $this->queueCallback($saved, 'update');
                }
            }
            $last_saved = $this->saved;
            $last_updated = $this->updated;
    	}
    }
}