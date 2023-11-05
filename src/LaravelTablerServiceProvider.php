<?php

namespace MimisK13\LaravelTabler;

use Illuminate\Support\ServiceProvider;

class LaravelTablerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mimisk13');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mimisk13');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        /*
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        */
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        //$this->mergeConfigFrom(__DIR__.'/../config/laravel-tabler.php', 'laravel-tabler');

        // Register the service the package provides.
        /**
        $this->app->singleton('laravel-tabler', function ($app) {
            return new LaravelTabler;
        });
        */
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        //return ['laravel-tabler'];
        return [Console\InstallCommand::class];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        /*
        $this->publishes([
            __DIR__.'/../config/laravel-tabler.php' => config_path('laravel-tabler.php'),
        ], 'laravel-tabler.config');
        */

        // Publishing Main Layout
        $this->publishes([
            __DIR__.'/../resources/views/layouts' => base_path('resources/views/vendor/mimisk13'),
            __DIR__.'/../resources/views/errors' => base_path('resources/views/vendor/mimisk13'),
        ], 'tabler.views');

        // Components
        $this->publishes([
            __DIR__.'/../resources/views/components' => base_path('resources/views/vendor/mimisk13'),
        ], 'tabler.components');

        $this->publishes([
            __DIR__.'/../vite.config.js' => base_path(),
        ], 'tabler.vite-config');

        // Publishing assets.
//        $this->publishes([
//            __DIR__.'/../resources/assets' => public_path('vendor/mimisk13'),
//        ], 'laravel-tabler.assets');


        // Registering package commands.
        // $this->commands([]);
    }
}
