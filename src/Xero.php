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
    protected $config;

    /**
     * Create a new Searcher instance.
     */    
    public function __construct($type = null)
    {
        $this->config = Config::get('xero');

        switch (strtolower($type)) {
            case 'private':
                $this->app = new \XeroPHP\Application\PrivateApplication($this->config);
            break;
            case 'public':
                $this->app = new \XeroPHP\Application\PublicApplication($this->config);
            break;
            case 'partner':
                $this->app = new \XeroPHP\Application\PartnerApplication($this->config);
            break;
            case 'invoice':
                $this->app = new \XeroPHP\Models\Accounting\Invoice();
            break;
            case 'attachment':
                $this->app = new \XeroPHP\Models\Accounting\Attachment();
            break;
            case 'lineItem':
                $this->app = new \XeroPHP\Models\Accounting\LineItem();
            break;
            case 'contact':
                $this->app = new \XeroPHP\Models\Accounting\Contact();
            break;
            case 'brandingTheme':
                $this->app = new \XeroPHP\Models\Accounting\BrandingTheme();
            break;
            default:
                throw new Exception("Application type does not exist [$type]");
        }

        $arr = [];
        foreach(get_class_methods($this->app) as $method)
        {
            if($method[0] !== '_')
            {
                $ref = new ReflectionMethod(get_class($this->app), $method);

                $this->$method = function() use ($ref) {
                    $params = func_get_args();
                    array_unshift($params, $this->app);
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


    //Static constructrs for ease.
    public static function privateApp()
    {
        return new Xero('private');
    }
    public static function publicApp()
    {
        return new Xero('public');
    }
    public static function partnerApp()
    {
        return new Xero('partner');
    }
    public static function invoice()
    {
        return new Xero('invoice');
    }
    public static function attachment()
    {
        return new Xero('attachment');
    }
    public static function lineItem()
    {
        return new Xero('lineItem');
    }
    public static function contact()
    {
        return new Xero('contact');
    }
    public static function brandingTheme()
    {
        return new Xero('brandingTheme');
    }

}