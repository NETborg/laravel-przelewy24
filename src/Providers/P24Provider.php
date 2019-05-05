<?php
declare(strict_types=1);

namespace NetborgTeam\P24\Providers;

use Illuminate\Support\ServiceProvider;
use NetborgTeam\P24\Observers\P24TransactionConfirmationObserver;
use NetborgTeam\P24\Observers\P24TransactionObserver;
use NetborgTeam\P24\P24Transaction;
use NetborgTeam\P24\P24TransactionConfirmation;
use NetborgTeam\P24\Services\P24Manager;
use NetborgTeam\P24\Services\P24Signer;
use NetborgTeam\P24\Services\P24TransactionConfirmationValidator;
use NetborgTeam\P24\Services\P24WebServicesManager;

class P24Provider extends ServiceProvider
{
    public function register()
    {
        // register P24Signer as singleton
        $this->app->singleton(P24Signer::class, function ($app) {
            $crc = config('p24.crc', null);
            return new P24Signer($crc);
        });

        // register P24TransactionConfirmationValidator
        $this->app->singleton(P24TransactionConfirmationValidator::class, function ($app) {
            return new P24TransactionConfirmationValidator($app->make(P24Signer::class));
        });

        // register P24Manager as singleton
        $this->app->singleton(P24Manager::class, function ($app) {
            $config = config('p24', []);

            return new P24Manager(
                $config,
                $app->make(P24Signer::class),
                $app->make(P24TransactionConfirmationValidator::class)
            );
        });

        // register P24WebServicesManager as singleton
        $this->app->singleton(P24WebServicesManager::class, function ($app) {
            $config = config('p24', []);
            return new P24WebServicesManager($config);
        });
    }
    

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // publish P24 config
            $this->publishes([
                __DIR__.'/../config/p24.php' => config_path('p24.php'),
            ], 'p24-config');

            // load P24 migrations
            $this->loadMigrationsFrom(__DIR__.'/../migrations');

            // publish migrations
            $this->addPublishGroup('p24-migrations', [
                __DIR__.'/../migrations' => database_path('migrations')
            ]);
        }
        
        // load P24 routes
        $this->loadRoutesFrom(__DIR__.'/../routes/p24.php');

        // register observers
        $this->registerObservers();
    }


    private function registerObservers(): void
    {
        P24Transaction::observe(P24TransactionObserver::class);
        P24TransactionConfirmation::observe(P24TransactionConfirmationObserver::class);
    }
}
