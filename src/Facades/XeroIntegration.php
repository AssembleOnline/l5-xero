<?php

namespace Assemble\XeroIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the authorizer facade class.
 *
 * @author Alex Blake <alex@assemble.co.za>
 */
class XeroIntegration extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'xerointegration.searcher';
    }
}
