<?php

namespace Dick\BackupManager;

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
     *
     * @return void
     */
    public function boot()
    {
        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'dick.backupmanager'
        );

        // use this if your package has views
        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'backupmanager');

        // use this if your package needs a config file
        $this->publishes([
                __DIR__.'/config/config.php' => config_path('dick/backupmanager.php'),
        ], 'config');
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Dick\BackupManager\Http\Controllers'], function($router)
        {
            require __DIR__.'/Http/routes.php';
        });
    }

    /**
     * Register any package services.
     *
     * @return void
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
        $this->app->bind('backupmanager',function($app){
            return new BackupManager($app);
        });
    }
}