<?php

namespace Infinety\BackupManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class BackupManagerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'infinety.backupmanager'
        );

        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'backupmanager');

        $this->loadTranslationsFrom(realpath(__DIR__.'/resources/lang/'), 'backup');

        /*
         * Publish default views
         */
        $this->publishes([
            realpath(__DIR__.'/resources/views') => $this->app->basePath().'/resources/views/vendor/infinety/backupmanager',
        ], 'views');

        /*
         * Publishes Lang files
         */
        $this->publishes([
            realpath(__DIR__.'/resources/lang') => $this->app->basePath().'/resources/lang',
        ], 'lang');

        // use this if your package needs a config file
        $this->publishes([
                __DIR__.'/config/config.php' => config_path('backupmanager.php'),
        ], 'config');
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Infinety\BackupManager\Http\Controllers'], function ($router) {
            require __DIR__.'/Http/routes.php';
        });
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        $this->registerBackupManager();
        $this->setupRoutes($this->app->router);

        // use this if your package has a config file
        config([
                'config/config.php',
        ]);
    }

    private function registerBackupManager()
    {
        $this->app->bind('backupmanager', function ($app) {
            return new BackupManager($app);
        });
    }
}
