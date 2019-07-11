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
    protected $signature = 'xero:sync {type} {since}';

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
        $since = $this->argument('since');
        if($since) {
            $since = new \Carbon\Carbon($since);
        }


        $this->queuePushForType($xero, 'ContactGroup', \Assemble\l5xero\Models\ContactGroup::where('ContactGroupID', null)->get());
        $this->queuePushForType($xero, 'Contact', \Assemble\l5xero\Models\Contact::where('ContactID', null)->get());
        $this->queuePushForType($xero, 'Item', \Assemble\l5xero\Models\Item::where('ItemID', null)->get());
        $this->queuePushForType($xero, 'Invoice', \Assemble\l5xero\Models\Invoice::where('InvoiceID', null)->get());
        $this->queuePushForType($xero, 'Payment', \Assemble\l5xero\Models\Payment::where('PaymentID', null)->get());
        $this->queuePushForType($xero, 'Overpayment', \Assemble\l5xero\Models\Overpayment::where('OverpaymentID', null)->get());
        $this->queuePushForType($xero, 'Prepayment', \Assemble\l5xero\Models\Prepayment::where('PrepaymentID', null)->get());


        dispatch(new XeroPull($xero, 'ContactGroup', 1, null, $since));
        dispatch(new XeroPull($xero, 'Contact', 1, null, $since));
        dispatch(new XeroPull($xero, 'Item', 1, null, $since));
        dispatch(new XeroPull($xero, 'Invoice', 1, null, $since));
        dispatch(new XeroPull($xero, 'Payment', 1, null, $since));
        dispatch(new XeroPull($xero, 'Overpayment', 1, null, $since));
        dispatch(new XeroPull($xero, 'Prepayment', 1, null, $since));

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
