<?php
namespace Assemble\l5xero\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Assemble\l5xero\Jobs\Job;
use Assemble\l5xero\Traits\XeroClassMap;
use Assemble\l5xero\Traits\XeroAPIRateLimited;

use Assemble\l5xero\Xero;
use Illuminate\Support\Str;

use Log;
use DB;

use ReflectionClass;

class XeroPush extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, XeroClassMap, XeroAPIRateLimited;

    protected $type;
    protected $model;
    protected $id;
    protected $callback;
    protected $prefix;
    protected $map;

    /**
     * Create a new job instance.
     *
     * @param  String $model
     * @return void
     */
    public function __construct($type, $model, $id, $callback = null)
    {
    	$this->type = $type;
    	$this->model = $model;
    	$map = $this->getXeroClassMap();
        $this->map = $map[$model];
    	$this->id = $id;
    	$this->callback = $callback;
    	$this->prefix = 'Assemble\\l5xero\\Models\\';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->rateLimit_canRun();
    	switch (strtolower($this->type)) {
            case 'private':
                $xero = new Xero($this->type);
            break;
            case 'public':
                $xero = new Xero($this->type);
            break;
            case 'partner':
                $xero = new Xero($this->type);
            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }
        try
        {

            echo "Running XeroPush For ".$this->model.PHP_EOL;

            $class = '\\XeroPHP\\Models\\Accounting\\'.$this->model;
            $xeroApp = $xero->getApp();
            $item = new $class($xeroApp);
            $model = $this->prefix.$this->model;
            $instance = (new $model);
            $fillable = $instance->getFillable();
            $object = $instance->findOrFail($this->id);

            $data = $object->toArray();

            foreach($data as $key => $value)
            {
            	if(is_array($value))
            	{
            		$data[Str::studly($key)] = $value;
            		unset($data[$key]);
            	}
            }

            $this->cleanFieldTypes($data, $item);
            // dd($data);

            $item->fromStringArray($data);
            $item->setDirty('_data');

            // $pre_existing = $xeroApp->load($this->map['MODEL'])->where($this->map, $data['Name'])->execute();
            // if(count($pre_existing) > 0)
            // {
            //     foreach($pre_existing as $itemsub)
            //     {
            //         $toSave = $itemsub->toStringArray();
            //         break;
            //     }
            // }
            // else
            // {
            $res = $xeroApp->save($item);                

            // Log::info(print_r($res));
            $toSave = $res->getElements();
            $toSave = $toSave[0];
            // }

            // print_r($toSave);
            //Debug
            // Log::info(print_r($toSave));
            $save = array_replace_recursive($data, $toSave);

            $done = $object->update($save);
            foreach($this->map['SUB'] as $key => $data)
            {
                if(isset($data['SINGLE']) )
            	{
            		if(isset($save[$key]))
                    {
            		    $this->saveToSub($object, $key, $save[$key], $data);
                    }
            	}
            	else
            	{
            		if(isset($save[str_plural($key)]))
            		$this->saveToSub($object, $key, $save[str_plural($key)], $data);
            	}
            	
            }

            if($done && $this->callback != null && ( isset($this->callback[0]) && isset($this->callback[1]) ) )
            {
            	$job = (new ReflectionClass($this->callback[0]))->newInstanceArgs($this->callback[1]);
            	dispatch($job);
            }
        }
        catch(\XeroPHP\Remote\Exception\UnauthorizedException $e)
        {
            Log::error($e);
            echo 'ERROR: Xero Authentication Error. Check logs for more details.';
            throw $e;
        }
        catch (\XeroPHP\Remote\Exception\BadRequestException $e) {
            Log::error($e);
            echo 'ERROR: Xero Request Error.';
            throw $e;
        }
        catch (Exception $e) {
            Log::error($e);
            echo 'ERROR: Unexpected error occured.';
            throw $e;    
        }
    }

    private function cleanFieldTypes(&$arr, $class)
    {
        $props = $class::getProperties();
        foreach($arr as $key => &$val)
        {
            if( isset($props[$key][1]) )
            {
                switch($props[$key][1]) {
                    case "bool":
                        $val = (boolean) $val;
                    break;
                }
                if($val === null)
                {  
                    unset($arr[$key]);
                }
                
            }
        }
    }

    private function saveToSub(&$object, $relation, $data, $sub)
    {
    	//check if its single.
		if( isset($sub['SINGLE']) )
        {
        	//get instance
        	$rel = $object->{$relation};
	    	
	    	if($rel == null) // save new
	    	{
	 			$object->{$relation}()->create($data)->save();
    		}
	    	else // save existing
	    	{
	    		$rel->update($data);
	    	}
	    	if($sub['SUB'] != null)
	    	foreach($sub['SUB'] as $key => $data_sub)
	        {
	        	if(isset($data_sub['SINGLE']) )
	        	{
	        		if(isset($data[$key]))
	        		$this->saveToSub($rel, $key, $data[$key], $data_sub);
	        	}
	        	else
	        	{
	        		if(isset($data[str_plural($key)]))
	        		$this->saveToSub($rel, $key, $data[str_plural($key)], $data_sub);
	        	}

	        }
        }
    	else // if its a collection
    	{
    		//first get the collection
    		$rels = $object->{str_plural($relation)};
            if($rels)
    		foreach($rels as $rel)
    		{
	    		if($rel == null)
		    	{
		 			$object->{str_plural($relation)}()->create($data)->save();
		    	}
		    	else
		    	{
                    $key = array_search($rel->id, array_column($data, 'id'));
                    if($key !== false)
                    {
		    		    $saved = $rel->update($data[$key]);
                    }
                    //this shouldnt happen Oo.
		    	}
		    	if($sub['SUB'] != null)
		    	foreach($sub['SUB'] as $key => $data_sub)
		        {
		        	if(isset($data_sub['SINGLE']) )
		        	{
		        		if(isset($data[$key]))
		        		$this->saveToSub($rel, $key, $data[$key], $data_sub);
		        	}
		        	else
		        	{
		        		if(isset($data[str_plural($key)]))
		        		$this->saveToSub($rel, $key, $data[str_plural($key)], $data_sub);
		        	}

		        }
		    }
    	}
    	
        
    }

}