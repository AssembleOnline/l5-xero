<?php

namespace Assemble\l5xero;

use Illuminate\Support\ServiceProvider;

class XeroServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Assemble\l5xero\Commands\XeroUpdateAll',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
        $this->setupMigrations();
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

        $this->commands($this->commands);

       
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
    *   Setup the migrations
    *   
    *   @return void
    */
    protected function setupMigrations()
    {
        $source = realpath(__DIR__.'/migrations');

        $this->publishes([ $source => $this->app->databasePath().'/migrations' ]);
    }
    



}
