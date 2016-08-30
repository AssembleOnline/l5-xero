<?php

namespace Assemble\l5xero\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

use Assemble\l5xero\Xero;

use Assemble\l5xero\Models\ContactGroup;
use Assemble\l5xero\Models\Contact;
use Assemble\l5xero\Models\ContactPerson;
use Assemble\l5xero\Models\Address;
use Assemble\l5xero\Models\CreditNote;
use Assemble\l5xero\Models\Invoice;
use Assemble\l5xero\Models\Item;
use Assemble\l5xero\Models\LineItem;
use Assemble\l5xero\Models\Payment;
use Assemble\l5xero\Models\Overpayment;
use Assemble\l5xero\Models\Prepayment;
use Assemble\l5xero\Models\Allocation;
use Assemble\l5xero\Models\Phone;
use Assemble\l5xero\Models\PurchaseDetail;
use Assemble\l5xero\Models\SalesDetail;

class XeroSync implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
    				'GUID'		=> null,
    				'MODEL'		=> 'Accounting\\Item\\Purchase',
    				'SUB'		=> null,
    			],
    			'SalesDetail' => [
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

    protected $model;
    protected $modelName;

    /**
     * Create a new job instance.
     *
     * @param  String $model
     * @return void
     */
    public function __construct($model)
    {
    	$this->modelName = $model;
    	$this->model = $this->classMap[$model];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $xero = Xero::privateApp();
        $object = $xero->load($this->model['MODEL']);
        $pageable = $object->isPageable();
        $objects = ( $pageable ? $object->page(1)->execute() : $object->execute() );

    	//TODO
    	//Check page count if need more, queue them at front...


    	// foreach($objects as $object)
    	// {

    	// }



    }

    private function saveToModel($model, $data, $GUID)
    {
    	$check = (new $model)->where($GUID, $data[$GUID]);
    	if(count($check) == 0)
    	{
    		$obj = (new $model)->create($data);
    		return $obj->save();
    	}
    	return true;
    }
}