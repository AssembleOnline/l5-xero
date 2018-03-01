<?php
namespace Assemble\l5xero\Traits;


trait XeroClassMap {
 
	public function getXeroClassMap()
	{
		return [
	        'ContactGroup' => [
	            'MANY'      => true,
	            'GUID'      => 'ContactGroupID',
	            'MODEL'     => 'Accounting\\ContactGroup',
	            'SUB'       => null,
	        ],
	        'Contact' => [
	            'GUID'      => 'ContactID',
	            'MODEL'     => 'Accounting\\Contact',
	            'SUB'       => [
	                'Address' => [
	                    'GUID'      => 'Contact_id',
	                    'MODEL'     => 'Accounting\\Address',
	                    'SUB'       => null,
	                ],
	                'Phone' => [
	                    'GUID'      => 'Contact_id',
	                    'MODEL'     => 'Accounting\\Phone',
	                    'SUB'       => null,
	                ],
	                'ContactPerson' => [
	                    'GUID'      => 'Contact_id',
	                    'MODEL'     => 'Accounting\\Contact\\ContactPerson',
	                    'SUB'       => null,
	                ],
	            ]
	        ],
	        'Item' => [
	            'GUID'      => 'ItemID',
	            'MODEL'     => 'Accounting\\Item',
	            'SUB'       => [
	                'PurchaseDetails' => [
	                    // 'SINGLE'    => 'HAS',
	                    'GUID'      => 'Item_id',
	                    'MODEL'     => 'Accounting\\Item\\Purchase',
	                    'SUB'       => null,
	                ],
	                'SalesDetails' => [
	                    // 'SINGLE'    => 'HAS',
	                    'GUID'      => 'Item_id',
	                    'MODEL'     => 'Accounting\\Item\\Sale',
	                    'SUB'       => null,
	                ],
	            ],
	        ],
	        'Invoice' => [
	            'GUID'      => 'InvoiceID',
	            'MODEL' => 'Accounting\\Invoice',
	            'SUB'       => [
	            	'Contact' => [
	            		'SINGLE'	=> 'BELONGS',
			            'GUID'      => 'ContactID',
			            'MODEL'     => 'Accounting\\Contact',
			            'SUB'       => [
			                'Address' => [
			                    'GUID'      => 'Contact_id',
			                    'MODEL'     => 'Accounting\\Address',
			                    'SUB'       => null,
			                ],
			                'Phone' => [
			                    'GUID'      => 'Contact_id',
			                    'MODEL'     => 'Accounting\\Phone',
			                    'SUB'       => null,
			                ],
			                'ContactPerson' => [
			                    'GUID'      => 'Contact_id',
			                    'MODEL'     => 'Accounting\\Contact\\ContactPerson',
			                    'SUB'       => null,
			                ],
			            ]
			        ],
	                'LineItem' => [ 
	                    'GUID'      => 'LineItemID',
	                    'MODEL' => 'Accounting\\Invoice\\LineItem',
	                    'SUB'       => null,
	                ],
	                'Payment' => [
	                    'GUID'      => 'PaymentID',
	                    'MODEL' => 'Accounting\\Payment',
	                    'SUB'       => null,
	                ],
	                'CreditNote' => [
	                    'GUID'      => 'CreditNoteID',
	                    'MODEL' => 'Accounting\\CreditNote',
	                    'SUB'       => null,
	                ],
	            ]

	        ],
	        'Payment' => [
	            'GUID'      => 'PaymentID',
	            'MODEL' => 'Accounting\\Payment',
	            'SUB'       => null,
	        ],
	        'Overpayment' => [
	            'GUID'      => 'PrepaymentID',
	            'MODEL' => 'Accounting\\Overpayment',
	            'SUB'       => [
	                'LineItem' => [
	                    'GUID'      => 'LineItemID',
	                    'MODEL' => 'Accounting\\Overpayment\\LineItem',
	                    'SUB'       => null,
	                ],
	                // 'Allocation' => [
	                //  'GUID'      => null,
	                //  'MODEL' => 'Accounting\\Overpayment\\Allocation',
	                //  'SUB'       => null,
	                // ],
	            ],
	        ],
	        'Prepayment' => [
	            'GUID'      => 'PrepaymentID',
	            'MODEL' => 'Accounting\\Prepayment',
	            'SUB'       => [
	                'LineItem' => [
	                    'GUID'      => 'LineItemID',
	                    'MODEL' => 'Accounting\\Prepayment\\LineItem',
	                    'SUB'       => null,
	                ],
	                // 'Allocation' => [
	                //  'GUID'      => null,
	                //  'MODEL' => 'Accounting\\Prepayment\\Allocation',
	                //  'SUB'       => null,
	                // ],
	            ],
	        ],
	    ];
	}

    
 
}