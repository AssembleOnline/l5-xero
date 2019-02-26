<?php
namespace Assemble\l5xero\Middleware;

use Closure;

class XeroWebhookMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $xero = new \Assemble\l5xero\Xero(config('xero.application_type'));
        $webhook = new \XeroPHP\Webhook($xero, $request->getContent());

        // handle invalid action
        if (! $webhook->validate($request->headers->get('x-xero-signature'))) {
            return response("", 401);
        }

        return $next($request);
    }
}
