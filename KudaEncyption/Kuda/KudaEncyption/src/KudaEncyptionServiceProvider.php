<?php

namespace Kuda\KudaEncyption;

use Illuminate\Support\ServiceProvider;

class KudaEncyptionServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var  bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Migration
        $this->loadMigrationsFrom(__DIR__.'/migrations');

    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('kuda-encyption', function()
        {
            return new KudaEncyption;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['kuda-encyption'];
    }

}