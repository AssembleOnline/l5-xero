<?php

namespace Assemble\l5xero;

use Validator;
use Config;
use Illuminate\Support\MessageBag;
use Log;
use Exception;
use ReflectionMethod;
/**
 * This is the xero class.
 *
 * @author Alex Blake <alex@assemble.co.za>
 */
class Xero
{

    /**
     * The class entities in the system.
     */
    protected $app;
    protected $item;
    protected $config;

    /**
     * Create a new Searcher instance.
     */    
    public function __construct($type = null, $item = null)
    {
        $this->config = Config::get('xero');

        switch (strtolower($type)) {
            case 'private':
                // $this->app = new \XeroPHP\Application\PrivateApplication($this->config);
                $xero = new \XeroPHP\Application($accessToken, $tenantId);

            break;
            case 'public':
                // $this->app = new \XeroPHP\Application\PublicApplication($this->config);
                $xero = new \XeroPHP\Application($accessToken, $tenantId);

            break;
            case 'partner':
                // $this->app = new \XeroPHP\Application\PartnerApplication($this->config);
                $xero = new \XeroPHP\Application($accessToken, $tenantId);

            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }
        switch (strtolower($item)) {
            case 'invoice':
                $this->item = new \XeroPHP\Models\Accounting\Invoice($this->app);
            break;
            case 'attachment':
                $this->item = new \XeroPHP\Models\Accounting\Attachment($this->app);
            break;
            case 'lineItem':
                $this->item = new \XeroPHP\Models\Accounting\LineItem($this->app);
            break;
            case 'contact':
                $this->item = new \XeroPHP\Models\Accounting\Contact($this->app);
            break;
            case 'brandingTheme':
                $this->item = new \XeroPHP\Models\Accounting\BrandingTheme($this->app);
            break;
            default:
                $this->item = $this->app;
        }

        $arr = [];
        foreach(get_class_methods($this->item) as $method)
        {
            if($method[0] !== '_')
            {
                $ref = new ReflectionMethod(get_class($this->item), $method);

                $this->$method = function() use ($ref) {
                    $params = func_get_args();
                    array_unshift($params, $this->item);
                    return call_user_func_array([$ref, 'invoke'], $params);
                };
            }
        }
    }

    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }

    public function methods()
    {
        $callable = get_object_vars($this);
        return array_keys($callable);
    }

    public function getApp()
    {
        return $this->app;
    }


    //Static constructrs for ease.
    public static function privateApp($item = null)
    {
        return new Xero('private', $item);
    }
    public static function publicApp($item = null)
    {
        return new Xero('public', $item);
    }
    public static function partnerApp($item = null)
    {
        return new Xero('partner', $item);
    }
    // public static function invoice()
    // {
    //     return new Xero('invoice');
    // }
    // public static function attachment()
    // {
    //     return new Xero('attachment');
    // }
    // public static function lineItem()
    // {
    //     return new Xero('lineItem');
    // }
    // public static function contact()
    // {
    //     return new Xero('contact');
    // }
    // public static function brandingTheme()
    // {
    //     return new Xero('brandingTheme');
    // }

}