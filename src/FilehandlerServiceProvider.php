<?php

namespace Abianbiya\Filehandler;

use Illuminate\Support\ServiceProvider;

class FilehandlerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'Filehandler');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Filehandler');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/fileroute.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filehandler.php', 'filehandler');

        $this->app->make('Abianbiya\Filehandler\Controllers\FileHandlerController');

        // Register the service the package provides.
        $this->app->singleton('filehandler', function ($app) {
            return new Filehandler;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['filehandler'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/filehandler.php' => config_path('filehandler.php'),
        ], 'filehandler.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/dsiunnes'),
        ], 'filehandler.views');*/

        // Publishing assets.
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('build/vendor/filehandler'),
        ], 'filehandler.assets');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/dsiunnes'),
        ], 'filehandler.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
