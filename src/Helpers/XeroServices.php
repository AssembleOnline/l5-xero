<?php
namespace Assemble\l5xero\Helpers;

use Closure;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Assemble\l5xero\Jobs\Job;
use Assemble\l5xero\Traits\XeroClassMap;
use Assemble\l5xero\Traits\XeroAPIRateLimited;
use Assemble\l5xero\Traits\UpdatesXeroModel;

use Assemble\l5xero\Xero;
use Illuminate\Support\Str;

use Log;
use DB;

use ReflectionClass;
use Exception;

class XeroServices
{
    use  SerializesModels, XeroClassMap, XeroAPIRateLimited,UpdatesXeroModel;
    protected $type;
    protected $model;
    protected $id;
    protected $prefix;
    protected $map;
    protected $data;
    protected $dirtyItems;


    protected $saved = 0;
    protected $updated = 0;
    protected $deleted = 0;

   public function __construct($type, $model, $id = null,$data = [],$dirtyItems = [])
   {
        $this->type = $type;
        $this->model = $model;
        $map = $this->getXeroClassMap();
        $this->map = $map[$model];
        $this->id = $id;
        $this->prefix = 'Assemble\\l5xero\\Models\\';
        $this->data = $data;
        $this->dirtyItems = $dirtyItems;
   }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function run()
    {       switch (strtolower($this->type)) {
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
                throw new Exception("Application type does not exist [$this->type]");
        }
        try
        {

            $class = '\\XeroPHP\\Models\\Accounting\\'.$this->model;
            $xeroApp = $xero->getApp();


            $model = $this->prefix.$this->model;
            $instance = (new $model);
            $fillable = $instance->getFillable();
            
            //stop here
            if(count($this->data) == 0)
            {
                $object = $instance->findOrFail($this->id);
                $data = $object->toArray();
            }else
            {
                $object = $instance->findOrFail($this->id);
                $data = $this->data;
            }

            // $item = new $class($xeroApp); 

            $item = $xero->loadByGUID($class,$object->{$this->map['GUID']});
            //exit;
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
            $item->fromStringArray($data,true);
            //$item->setDirty('_data');
            // T: set every key in data to dirty to ensure saving
            
            foreach ($this->dirtyItems as $dirtyItem) {
                $item->setDirty($dirtyItem);
            }
            //$itemRes = $item->save();
        $res = $xeroApp->save($item,true);    
        $updatedItem = $xero->loadByGUID($class,$object->{$this->map['GUID']});
        $this->processModel($this->model, $this->map, $updatedItem, null, null, true);


        }
        catch(\XeroPHP\Remote\Exception\UnauthorizedException $e)
        {
            \Log::info(["XeroPushException", $this->model, $this->id]);
            \Log::error($e);
            throw $e;
        }
        catch (\XeroPHP\Remote\Exception\BadRequestException $e) {
            \Log::error($e);
            throw $e;
        }
        catch (Exception $e) {
            \Log::info(["XeroPushException", $this->model, $this->id]);
            \Log::error($e);
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

 /**
     * Processes a retrieved record and sub relations therein
     *
     * @param String $sub_key
     * @param Array $map
     * @param Array $obj
     * @param String $parent_key
     * @param Mixed $parent_value
     * @param Boolean $shallow
     *
     * @return void
     */
    private function processModel($sub_key, $map, $obj, $parent_key = null, $parent_value = null, $shallow = false)
    {
        $model = $this->prefix.$sub_key;
        $instance = (new $model);
        $items = [];
        $fillable = $instance->getFillable();
        $sub = ( isset($map['SUB']) ? $map['SUB'] : null);
        $last_updated = 0;
        $last_saved = 0;
        $original = [];

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
                        $model_sub = $this->prefix.$key;
                        $guids = collect($sub_objs)->pluck($sub_item['GUID']);
                        Log::info("remove Relations");
                        $this->deleted += $this->removeOrphanedRelations($sub_item['GUID'],$model_sub,$guids,$sub_key.'_id', $saved->id);
                        foreach($sub_objs as $sub_obj) {
                            $saved_obj = $this->processModel($key, $sub_item, $sub_obj, $sub_key.'_id', $saved->id);
                            $original[$list_key][] = $saved_obj->internal_original_attributes;
                        }
                    }
                        
                }
            }
        }
        // stats
        $this->saved += ( $saved->save_event_type == 1 ? 1 : 0 ); // saved
        $this->updated += ( $saved->save_event_type == 2 ? 1 : 0 ); // updates
     
        return $saved;
    }
   
 
    public static function bulkUpdate($type, $model ,$bulkData = [],$dirtyItems = [])
    {
        switch (strtolower($type)) {
            case 'private':
            case 'public':
            case 'partner':
                $xero = new Xero($type);
            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }
            $service = new \Assemble\l5xero\Helpers\XeroServices('private',$model);
            $classMap = $service->getXeroClassMap();
            $map = $classMap[$model];
            $prefix = '\\Assemble\\l5xero\\Models\\';
            $class = '\\XeroPHP\\Models\\Accounting\\'.$model;
            $xeroApp = $xero->getApp();
            $items = [];

            foreach ($bulkData as $object)
            {
                $model = $prefix.$model;
                \Log::info($model);
                exit;
                $instance = (new $model);
                $fillable = $instance->getFillable();

                // $item = new $class($xeroApp); 
                $item = new $class($xeroApp); 
                $data = $object->toArray();
                $item->{$service->map['GUID']} = $data[$service->map['GUID']];

                foreach($data as $key => $value)
                {
                    if(is_array($value))
                    {
                        $data[Str::studly($key)] = $value;
                        unset($data[$key]);
                    }
                }

                $service->cleanFieldTypes($data, $item);
                $item->fromStringArray($data,true);
        

                foreach ($dirtyItems as $dirtyItem) {
                    $item->setDirty($dirtyItem);
                }
                $items[] = $item;
            }
            $response = $xeroApp->saveAll($items,true);
            $service = new \Assemble\l5xero\Helpers\XeroServices('private',"Invoice");
            $service->bulkModelProcess($response);
    }


    public static function addBulkPayments($type,$payments)
    {
        switch (strtolower($type)) {
            case 'private':
            case 'public':
            case 'partner':
                $xero = new Xero($type);
            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }

        try{
            $xeroPayments = [];
            foreach ($payments as $payment) 
            {
                $newPayment = new \XeroPHP\Models\Accounting\Payment();

                $invoice = new \XeroPHP\Models\Accounting\Invoice();
                $invoice->setInvoiceID($payment->invoice->InvoiceID);

                $account = new \XeroPHP\Models\Accounting\Account();
                $account->setAccountId($payment->AccountID);
                
                $xeroPayments[] = $newPayment
                    ->setInvoice($invoice)
                    ->setAccount($account)
                    ->setDate($payment->Date)
                    ->setAmount($payment->Amount)
                    ->setIsReconciled(true)
                    ->setReference($payment->Reference);
            }
            $response = $xero->saveAll($xeroPayments);
            
            $service = new \Assemble\l5xero\Helpers\XeroServices('private',"Payment");
            $service->bulkModelProcess($response);
           

        }catch(Exception $e){
            \Log::error($e);
            throw $e;
        }
    }

    public static function addBulkBankTransactions($type,$transactionDetailList) {

        switch (strtolower($type)) {
            case 'private':
            case 'public':
            case 'partner':
                $xero = new Xero($type);
            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }

        try{
            $xeroTransactions = [];
            foreach($transactionDetailList as $transactionDetails)
            {
                $newTransaction = new \XeroPHP\Models\Accounting\BankTransaction();

                $account = new \XeroPHP\Models\Accounting\BankTransaction\BankAccount();
                $account->setAccountID($transactionDetails['AccountID']);

                $contact = new \XeroPHP\Models\Accounting\Contact();
                $contact->setContactID($transactionDetails['ContactID']);

                $lineItem = new \XeroPHP\Models\Accounting\BankTransaction\LineItem();
                $lineItem->setQuantity(1)
                ->setUnitAmount($transactionDetails['amount'])
                ->setDescription('Overpayment for ref '.$transactionDetails['payment_reference']);

                $xeroTransactions[] =  $newTransaction
                    ->setType('RECEIVE-OVERPAYMENT')
                    ->setContact($contact) 
                    ->setDate($transactionDetails['date'])
                    ->setCurrencyCode($transactionDetails['currency_code'])
                    ->addLineItem($lineItem)
                    ->setBankAccount($account)
                    ->setIsReconciled(false);
            }
            return $xero->saveAll($xeroTransactions);

        }catch(Exception $e){
            \Log::error($e);
            throw $e;
        }
    }

    public function bulkModelProcess($response)
    {
        \Log::info(["bluck process",print_r($response->getElements(),true)]);

        foreach ($response->getElements() as $element) 
        {
            if(!array_key_exists("ValidationErrors", $element))
            {
                $classMap = $this->getXeroClassMap();
                $map = $classMap[$this->model];
                $this->processModel($this->model, $map, $element, null, null, true);  
            }else
            {
                \Log::error("error");
            }
        }
    }
}
