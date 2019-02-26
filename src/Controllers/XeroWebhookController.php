<?php
namespace Assemble\l5xero\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class XeroWebhookController extends Controller
{
    /**
     * Handle incoming webhooks
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        // test for events
        $events = $request->get('events');
        if($events && count($events) > 0) {
            foreach($events as $event) {
                $this->handleEvent($event);
            }
        }
        // valid
        return response("", 200);
    }


    private function handleEvent($event) {
        switch($event['eventCategory']) {
            case "INVOICE":
                dispatch(new \Assemble\l5xero\Jobs\XeroPullSingle('private', 'Invoice', $event['resourceId']));
            break;
            case "CONTACT":
                dispatch(new \Assemble\l5xero\Jobs\XeroPullSingle('private', 'Contact', $event['resourceId']));
            break;
        }
    }
}