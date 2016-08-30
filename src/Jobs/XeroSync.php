<?php
namespace Assemble\l5xero\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Assemble\l5xero\Jobs\Job;

use Assemble\l5xero\Xero;

use Log;
use DB;

class XeroSync extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    protected $classMap = [
    	'ContactGroup' => [
    		'GUID' 		=> 'ContactGroup',
    		'MODEL' 	=> 'Accounting\\ContactGroup',
    		'SUB'		=> null,
    	],
    	'Contact' => [
    		'GUID' 		=> 'ContactID',
    		'MODEL'		=> 'Accounting\\Contact',
    		'SUB'		=> [
    			'Address' => [
    				'GUID'		=> null,
    				'MODEL'		=> 'Accounting\\Address',
    				'SUB'		=> null,
    			],
    			'Phone' => [
    				'GUID'		=> null,
    				'MODEL'		=> 'Accounting\\Phone',
    				'SUB'		=> null,
    			],
    			'ContactPerson' => [
    				'GUID' 		=> null,
    				'MODEL'		=> 'Accounting\\Contact\\ContactPerson',
    				'SUB'		=> null,
    			],
    		]
    	],
    	'Item' => [
    		'GUID'		=> 'ItemID',
    		'MODEL'		=> 'Accounting\\Item',
    		'SUB'		=> [
    			'PurchaseDetail' => [
    				'SINGLE'	=> true,
    				'GUID'		=> null,
    				'MODEL'		=> 'Accounting\\Item\\Purchase',
    				'SUB'		=> null,
    			],
    			'SalesDetail' => [
    				'SINGLE'	=> true,
    				'GUID'		=> null,
    				'MODEL'		=> 'Accounting\\Item\\Sale',
    				'SUB'		=> null,
    			],
    		],
    	],
    	'Invoice' => [
    		'GUID'		=> 'InvoiceID',
    		'MODEL'	=> 'Accounting\\Invoice',
    		'SUB'		=> [
    			'LineItem' => [ 
    				'GUID'		=> null,
    				'MODEL'	=> 'Accounting\\Invoice\\LineItem',
    				'SUB'		=> null,
    			],
    			'Payment' => [
    				'GUID'		=> 'PaymentID',
    				'MODEL'	=> 'Accounting\\Payment',
    				'SUB'		=> null,
    			],
    			'CreditNote' => [
    				'GUID'		=> 'CreditNoteID',
    				'MODEL'	=> 'Accounting\\CreditNote',
    				'SUB'		=> null,
    			],
    		]

    	],
    	'Payment' => [
			'GUID'		=> 'PaymentID',
			'MODEL'	=> 'Accounting\\Payment',
			'SUB'		=> null,
		],
    	'Overpayment' => [
    		'GUID'		=> 'PrepaymentID',
    		'MODEL'	=> 'Accounting\\Overpayment',
    		'SUB'		=> [
    			'LineItem' => [
    				'GUID'		=> null,
    				'MODEL'	=> 'Accounting\\Overpayment\\LineItem',
    				'SUB'		=> null,
    			],
    			'Allocation' => [
    				'GUID'		=> null,
    				'MODEL'	=> 'Accounting\\Overpayment\\Allocation',
    				'SUB'		=> null,
    			],
    		],
    	],
    	'Prepayment' => [
    		'GUID'		=> 'PrepaymentID',
    		'MODEL'	=> 'Accounting\\Prepayment',
    		'SUB'		=> [
    			'LineItem' => [
    				'GUID'		=> null,
    				'MODEL'	=> 'Accounting\\Prepayment\\LineItem',
    				'SUB'		=> null,
    			],
    			'Allocation' => [
    				'GUID'		=> null,
    				'MODEL'	=> 'Accounting\\Prepayment\\Allocation',
    				'SUB'		=> null,
    			],
    		],
    	],
    ];

    protected $prefix;
    protected $model;
    protected $map;

    /**
     * Create a new job instance.
     *
     * @param  String $model
     * @return void
     */
    public function __construct($model)
    {
    	$this->prefix = 'Assemble\\l5xero\\Models\\';
    	$this->map = $this->classMap[$model];
    	$this->model = $model;
    	$tmp = $this->prefix.$model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $xero = Xero::privateApp();
        $object = $xero->load($this->map['MODEL']);
        $pageable = call_user_func('XeroPHP\\Models\\'.$this->map['MODEL'].'::isPageable');
        $objects = ( $pageable ? $object->page(1)->execute() : $object->execute() );
        echo "FOUND [".count($objects)."] ".$this->model."(s)\n";
    	//TODO
    	//Check page count if need more, queue them at front...
    	
    		
    	$this->doShit($this->model, $this->map['SUB'], $objects);

    }

    private function saveToModel($obj, $model, $fillable, $parent_key, $parent_value)
    {
    	/*
		*	set to string array if XeroPHP collection
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
		
		$new['created_at'] = date('Y-m-d H:i:s');
		$new['updated_at'] = date('Y-m-d H:i:s');

		return call_user_func($model.'::create', $new);
    }

    private function doShit($sub_key, $sub, $withStuff, $parent_key = null, $parent_value = null)
    {
    	$model = $this->prefix.$sub_key;
    	$instance = (new $model);
    	$items = [];
    	$fillable = $instance->getFillable();

    	foreach($withStuff as $obj)
    	{
    		
    		//DO SAVE!
    		$saved = $this->saveToModel($obj, $model, $fillable, $parent_key, $parent_value);

    		/*
    		*	Run for collection of sub elements
    		*/
    		if($sub != null && count($sub) > 0)
    		foreach($sub as $key => $sub_item)
    		{
    			if(isset($obj[$key.'s']))
				{
					echo "FOUND [".count($obj[$key.'s'])."] ".$key."(s)\n";
					// echo print_r($obj[$key.'s'],true);
					if( isset($sub_item['SINGLE']) && $sub_item['SINGLE'] == true)
			    	{
			    		$model_sub = $this->prefix.$key;
    					$instance_sub = (new $model_sub);
			    		$fillable_sub = $instance_sub->getFillable();
			    		$saved = $this->saveToModel($obj[$key.'s'], $model_sub, $fillable_sub, $sub_key.'_id', $saved->id);
			    	}
			    	else
			    	{
						$this->doShit($key, $sub_item['SUB'], $obj[$key.'s'], $sub_key.'_id', $saved->id);
			    	}
			    		
    			}
    		}
    	}
//        return DB::table($instance->getTable())->insert($items);//->raw('ON DUPLICATE KEY UPDATE');
    }
}