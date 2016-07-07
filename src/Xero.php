<?php

namespace Assemble\l5xero;

use Validator;
use Config;
use Illuminate\Support\MessageBag;
use Log;
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
    private $config

    /**
     * Create a new Searcher instance.
     */
    public function __construct()
    {
        $this->config = Config::get('xero');
    }

    public function private()
    {
        return new \XeroPHP\Application\PrivateApplication($this->config);
    }
    public function Public()
    {
        return new \XeroPHP\Application\PublicApplication($this->config);
    }
    public function Partner()
    {
        return new \XeroPHP\Application\PartnerApplication($this->config);
    }
    public function Invoice()
    {
        return new \XeroPHP\Application\Invoice();
    }
    public function Attachment()
    {
        return new \XeroPHP\Application\Attachment();
    }
    public function lineItem()
    {
        return new \XeroPHP\Application\LineItem();
    }
    public function Contact()
    {
        return new \XeroPHP\Application\Contact();
    }
    public function BrandingTheme()
    {
        return new \XeroPHP\Application\BrandingTheme();
    }

}