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
        // dispatch(new XeroPull('ContactGroup'));
        // dispatch(new XeroPull($xero, 'Contact'));
        // dispatch(new XeroPull('Item'));
        // dispatch(new XeroPull('Invoice'));
        // dispatch(new XeroPull('Payment'));
        // dispatch(new XeroPull('Overpayment'));
        // dispatch(new XeroPull('Prepayment'));

        dispatch(new XeroPush($xero, 'Contact', 1));
    }
}
