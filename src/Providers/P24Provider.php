<?php

namespace NetborgTeam\P24\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use NetborgTeam\P24\Services\P24Manager;

class P24Provider extends ServiceProvider
{
   
    
    
    public function register()
    {
        // register P24Manager as singleton
        $this->app->singleton(P24Manager::class, function($app) {
            return new P24Manager();
        });
    }
    

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {        
        // publish P24 config
        $this->publishes([
            __DIR__.'/../config/p24.php' => config_path('p24.php'),
        ]);
        
        // load P24 routes
        $this->loadRoutesFrom(__DIR__.'/../routes/p24.php');
        
        // load P24 migrations
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
