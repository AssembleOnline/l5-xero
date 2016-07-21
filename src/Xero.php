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
            default:
                throw new Exception("Application type does not exist [$type]");
        }

        $arr = [];
        foreach(get_class_methods($this->app) as $method)
        {
            if($method[0] !== '_')
            {
                $ref = new ReflectionMethod(get_class($this->app), $method);

                $this->$method = function($args) use ($ref) {
                    return $ref->invokeArgs($this->app, $args);
                };
            }
        }
    }

    public function __call($method, $args)
    {   
        if (isset($this->$method)) {
            return call_user_func($this->{$method}->bindTo($this),$args);
        }
    }

    public function methods()
    {
        $callable = get_object_vars($this);
        return array_keys($callable);
    }

    public function process($data)
    {
        switch (get_class($data)){
            case 'XeroPHP\\Remote\\Query':
                $data = $data;
            break;
            case 'XeroPHP\\Remote\\Collection':
                $data = collect($data);
            break;
            default:
                $data = $data;
        }
        return $data;
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




    /*
    *
    *   Shortcut Functions For Use In Quick load calls
    *
    */
    public function invoice($guid = null, $page = null)
    {
        if($guid == null)
        {
            if($page == null)
                return $this->process($this->app->load('Accounting\\Invoice')->execute());
            else
                return $this->process($this->app->load('Accounting\\Invoice')->page($page)->execute()); 
        }
        return $this->process($this->app->loadByGUID('Accounting\\Invoice', $guid));
    }

    public function contact($guid = null, $page = null)
    {
        if($guid == null)
        {
            if($page == null)
                return $this->process($this->app->load('Accounting\\Contact')->execute());
            else
                return $this->process($this->app->load('Accounting\\Contact')->page($page)->execute()); 
        }
        return $this->process($this->app->loadByGUID('Accounting\\Contact', $guid));
    }

    public function item($guid = null)
    {
        if($guid == null)
        {
            return $this->process($this->app->load('Accounting\\Item')->execute()); 
        }
        return $this->process($this->app->loadByGUID('Accounting\\Item', $guid));
    }

    public function attachment($guid = null)
    {
        if($guid == null)
        {
            return $this->process($this->app->load('Accounting\\Attachment')->execute()); 
        }
        return $this->process($this->app->loadByGUID('Accounting\\Attachment', $guid));
    }

}