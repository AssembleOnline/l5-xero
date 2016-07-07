<?php

namespace Assemble\XeroIntegration;

use Illuminate\Support\ServiceProvider;

class XeroIntegrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }


    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/xero.php');

        $this->publishes([ $source => config_path('xero.php') ]);

        $this->mergeConfigFrom($source, 'xero');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('xero', function () {
            return new Xero;
        });

       
    }


}
