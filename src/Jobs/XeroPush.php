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

class XeroPush extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $type;
    protected $model;
    protected $id;
    protected $callback;
    protected $prefix;

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
        
        $class = '\\XeroPHP\\Models\\Accounting\\'.$this->model;
        $item = new $class($xero->getApp());
        $model = $this->prefix.$this->model;
        $object = (new $model)->findOrFail($this->id);

        $item->fromStringArray($object->toArray(), true);
        $item->setDirty('id');
        $item->save();

        $object->fill($item->toStringArray());

        $done = $object->save();

        if($done && $callback != null)
        {
        	call_user_func_array($this->callback[0], $this->callback[1]);
        }
    }

}