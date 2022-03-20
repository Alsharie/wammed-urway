<?php

namespace Wameed\UrwayPaymentGateway;

use Illuminate\Support\ServiceProvider;

class  UrwayServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config file
        $this->publishes([
            __DIR__ . '/../config/urway.php' => config_path('urway.php'),
        ]);

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/urway.php', 'urway');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Urway::class, function () {
            return new Urway();
        });
    }
}