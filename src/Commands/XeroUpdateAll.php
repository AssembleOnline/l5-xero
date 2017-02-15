<?php

namespace Assemble\l5xero\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use Assemble\l5xero\Jobs\XeroPull;
use Assemble\l5xero\Jobs\XeroPush;
use Assemble\l5xero\Xero;

class XeroUpdateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xero:sync {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send update items to the queue to be run.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $xero = $this->argument('type');


        $this->queuePushForType($xero, 'ContactGroup', \Assemble\l5xero\Models\ContactGroup::where('ContactGroupID', null)->get());
        $this->queuePushForType($xero, 'Contact', \Assemble\l5xero\Models\Contact::where('ContactID', null)->get());
        $this->queuePushForType($xero, 'Item', \Assemble\l5xero\Models\Item::where('ItemID', null)->get());
        $this->queuePushForType($xero, 'Invoice', \Assemble\l5xero\Models\Invoice::where('InvoiceID', null)->get());
        $this->queuePushForType($xero, 'Payment', \Assemble\l5xero\Models\Payment::where('PaymentID', null)->get());
        $this->queuePushForType($xero, 'Overpayment', \Assemble\l5xero\Models\Overpayment::where('OverpaymentID', null)->get());
        $this->queuePushForType($xero, 'Prepayment', \Assemble\l5xero\Models\Prepayment::where('PrepaymentID', null)->get());


        dispatch(new XeroPull($xero, 'ContactGroup'));
        dispatch(new XeroPull($xero, 'Contact'));
        dispatch(new XeroPull($xero, 'Item'));
        dispatch(new XeroPull($xero, 'Invoice'));
        dispatch(new XeroPull($xero, 'Payment'));
        dispatch(new XeroPull($xero, 'Overpayment'));
        dispatch(new XeroPull($xero, 'Prepayment'));

        // $contact = new \Assemble\l5xero\Models\Contact;
        // $contact->Name = str_random(5);
        // $contact->save();

        // $invoice = new \Assemble\l5xero\Models\Invoice;
        // $invoice->Type = 'ACCREC';
        // $invoice->Date = date('Y-m-dTH:i:s');
        // $invoice->DueDate = date('Y-m-dTH:i:s');
        // $invoice->LineAmountTypes = 'Inclusive';
        // $invoice->Contact_id = $contact->id;
        // $invoice->save();

        // $lineItem = new \Assemble\l5xero\Models\LineItem;
        // $lineItem->Description = 'Application Fee';
        // $lineItem->Quantity = 1;
        // $lineItem->UnitAmount = 100.00;
        // $lineItem->AccountCode = 200;
        // $lineItem->Invoice_id = $invoice->id;
        // $lineItem->save();

        // $contact = \Assemble\l5xero\Models\Contact::first();
        // $invoice = \Assemble\l5xero\Models\Invoice::first();
        // $lineItem = \Assemble\l5xero\Models\LineItem::first();



        // dispatch(new XeroPush($xero, 'Invoice', 2 ));
    }

    private function queuePushForType(&$xero, $type, $objects_to_push)
    {
        if(count($objects_to_push) > 0)
        foreach($objects_to_push as $object)
        {
            dispatch(new XeroPush($xero, $type, $object->id));
        }
    }
}
