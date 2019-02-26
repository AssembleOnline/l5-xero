<?php
namespace Assemble\l5xero\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Assemble\l5xero\Jobs\Job;
use Assemble\l5xero\Traits\XeroClassMap;
use Assemble\l5xero\Traits\XeroAPIRateLimited;
use Assemble\l5xero\Traits\UpdatesXeroModel;

use Assemble\l5xero\Xero;

use Log;
use DB;
use ReflectionClass;
use Cache;
use Carbon\Carbon;

class XeroPull extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, XeroClassMap, XeroAPIRateLimited, UpdatesXeroModel;


    protected $xero;
    protected $prefix;
    protected $model;
    protected $map;
    protected $page;
    protected $callback;

    protected $saved = 0;
    protected $updated = 0;
    protected $deleted = 0;

    protected $xeroInstance;
    private $since;

    /**
     * Create a new job instance.
     *
     * @param  String $model
     * @return void
     */
    public function __construct($xero, $model, $page = null, $callback = null, $since = null)
    {
        $this->xero = $xero;
        $this->prefix = 'Assemble\\l5xero\\Models\\';
        
        $map = $this->getXeroClassMap();
        $this->map = $map[$model];
        $this->model = $model;
        $this->page = ( $page == null ? $page = 1 : $page );

        $this->callback = $callback;
        $class = $this->prefix.$this->model;


        if($since != null) {
            $this->since = $since;
        } 
        elseif((new $class)->hasUpdateField()) {
            // set our own
            $this->since = new Carbon((new $class)->max('UpdatedDateUTC'));
            // get latest date +1 second
            $this->since->addSeconds(1);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->rateLimit_canRun();
        switch (strtolower($this->xero)) {
            case 'private':
                $this->xeroInstance = new Xero($this->xero);
            break;
            case 'public':
                $this->xeroInstance = new Xero($this->xero);
            break;
            case 'partner':
                $this->xeroInstance = new Xero($this->xero);
            break;
            default:
                throw new \Assemble\l5xero\Exceptions\InvalidTypeException();
        } try {
            Log::info("Running XeroPull For ".$this->model);

            $object = $this->xeroInstance->load($this->map['MODEL']);

            // Only get recently updated records
            $class = $this->prefix.$this->model;
            if(method_exists($object, 'modifiedAfter') && (new $class)->hasUpdateField() && $this->since != null) {
                $since = new Carbon($this->since);
                Log::info("Getting Updates Since: ".$since);
                $object = $object->modifiedAfter($since);
            }
            $pageable = call_user_func('XeroPHP\\Models\\'.$this->map['MODEL'].'::isPageable');
            $objects = ( $pageable ? $object->page($this->page)->execute() : $object->execute() );

            //Check page count if need more, queue them at front...
            if($pageable == true && count($objects) == 100 && $this->page != null) {
                $this->page++;
                dispatch(new XeroPull($this->xero, $this->model, $this->page, $this->callback, $this->since));
                Log::info("ADDED NEXT PAGE { ".$this->page." } TO QUEUE FOR ".$this->model."(s)\n");
            }
            
            Log::info("FOUND [".count($objects)."] ".$this->model."(s)\n");
                
            $this->processModel($this->model, $this->map, $objects, null, null, true);

            Log::info("SAVED [".$this->saved."] UPDATED [".$this->updated."] DELETED [".$this->deleted."] ".$this->model."(s) & related Object(s)\n");
            

        }
        catch(\XeroPHP\Remote\Exception\UnauthorizedException $e)
        {
            Log::error($e);
            echo 'ERROR: Xero Authentication Error. Check logs for more details.'.PHP_EOL;
            throw $e;
        }
    }

    /**
     * dispatces a callback job provided
     *
     * @param String $object
     * @param String $status
     *
     * @return void
     */
    private function queueCallback($object, $status, $original)
    {
            $job = (new ReflectionClass($this->callback))->newInstanceArgs([$object, $status, $original]);
            dispatch($job);
    }

    /**
     * Queries Xero by XeroID field on records to test for a 404 response
     *
     * @param String $model
     * @param String $GUID
     *
     * @return Boolean
     */
    private function testForXeroExistence($model, $GUID) {
        try {
            $this->xeroInstance->loadByGUID($model, $GUID);
            return true;
        } catch (\XeroPHP\Remote\Exception\NotFoundException $e) {
            return false;
        }
    }

    /**
     * Retrieves an existing record by unique fields
     *
     * @param String $model
     * @param Array $uniques
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function getUniqueOffendingRow($model, $uniques) {
        $item = (new $model);
        foreach($uniques as $key => $value) {
            $item = $item->orWhere($key, $value);
        }
        return $item->first();
    }

    /**
     * Processes a retrieved record and sub relations therein
     *
     * @param String $sub_key
     * @param Array $map
     * @param Array $object_data
     * @param String $parent_key
     * @param Mixed $parent_value
     * @param Boolean $shallow
     *
     * @return void
     */
    private function processModel($sub_key, $map, $object_data, $parent_key = null, $parent_value = null, $shallow = false)
    {
        $model = $this->prefix.$sub_key;
        $instance = (new $model);
        $items = [];
        $fillable = $instance->getFillable();
        $sub = $map['SUB'];

        $last_updated = 0;
        $last_saved = 0;

        $saved_models = [];

        foreach($object_data as $obj)
        {
            \Log::info("XeroPull processing record");
            //DO SAVE!
            try {
                $saved = $this->saveToModel($map['GUID'], $obj, $model, $fillable, $parent_key, $parent_value);
                $original = $saved->internal_original_attributes;
            } catch (\Illuminate\Database\QueryException $e) {
                // if its a unique constraint scenario ie: someone deleted a record and updated another one with the same unique fields
                if($e->getCode() == 23000) {
                    \Log::info("Duplicate Record On Update: ".$e);
                    $uniques = $this->getModelUniques($model, $obj);
                    $offendingRow = $this->getUniqueOffendingRow($model, $uniques);
                    if($offendingRow) {
                        $exists = $this->testForXeroExistence($map['MODEL'], $offendingRow->getAttributeValue($map['GUID']));
                        if(!$exists) {
                            try {
                                $offendingRow->delete();
                                $this->deleted++;
                                $saved = $this->saveToModel($map['GUID'], $obj, $model, $fillable, $parent_key, $parent_value);
                            } catch (Excepton $e) {
                                \Log::error("Unable to handle delete-update condition: ".$e);
                                return;
                            }
                        } else {
                            \Log::error("Duplicate Record On Update - Cannot Be Resolved: ".$e);
                            return;
                        }
                    }
                    return;
                } else {
                    \Log::error("Failed To Store \"".$model."\" Level 1 - Query Exception");
                    \Log::error($e);
                    return;
                }
            } catch (Exception $e) {
                \Log::error("Failed To Store \"".$model."\" Level 1");
                \Log::error($e);
                return;
            }
            \Log::info("XeroPull processing relations");
            /*
            *   Run for collection of sub elements
            */
            if($sub != null && count($sub) > 0) {
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
                                try {
                                    $saved_sub = $this->saveToModel($sub_item['GUID'], $obj[$key], $model_sub, $fillable_sub, $sub_key.'_id', $saved->id);
                                } catch (Exception $e) {
                                    \Log::error("Failed To Store \"".$model."\" Level 2");
                                    \Log::error($e);
                                    continue;
                                }
                            }
                            elseif($sub_item['SINGLE'] == 'BELONGS')
                            {
                                try {
                                    $saved_sub = $this->saveToModel($sub_item['GUID'], $obj[$key], $model_sub, $fillable_sub);
                                } catch (Exception $e) {
                                    \Log::error("Failed To Store \"".$model."\"  Level 3");
                                    \Log::error($e);
                                    continue;
                                }

                                $saved->{$key.'_id'} = $saved_sub->id;
                                $saved->save();
                                $original[$key] = $saved_sub->internal_original_attributes;
                            }

                        }
                        else // otherwise process the sub objects as one-many relations
                        {
                            $list_key = ( isset($obj[$key.'s']) ? $key.'s' : $key );
                            $sub_objs = $obj[$list_key];
                            $saved->{$list_key} = [];
                            $original[$list_key] = [];
                            $saved_objs = $this->processModel($key, $sub_item, $sub_objs, $sub_key.'_id', $saved->id);
                            foreach($saved_objs as $saved_obj) {
                                $original[$list_key][] = $saved_obj->internal_original_attributes;
                            }
                        }
                            
                    }
                }
            }

            // stats
            $this->saved += ( $saved->save_event_type == 1 ? 1 : 0 ); // saved
            $this->updated += ( $saved->save_event_type == 2 ? 1 : 0 ); // updates


            \Log::info("XeroPull Testing for callback execution...");
            if($shallow == true && $this->callback != null && isset($this->callback) )
            {
                \Log::info("XeroPull Callback declared: ".$this->callback);
                if($saved->save_event_type == 1)
                {
                    \Log::info("XeroPull Callback running for [create]");
                    $this->queueCallback($saved, 'create', $original);
                }
                elseif($saved->save_event_type == 2)
                {
                    \Log::info("XeroPull Callback running for [update]");
                    $this->queueCallback($saved, 'update', $original);
                }
            }
            $saved_models[] = $saved;
        }
        return $saved_models;
    }
}