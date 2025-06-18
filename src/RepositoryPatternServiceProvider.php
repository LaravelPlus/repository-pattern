<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern;

use Illuminate\Support\ServiceProvider;

final class RepositoryPatternServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'repository-pattern');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'repository-pattern');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('repository-pattern.php'),
                __DIR__.'/../stubs/repository.stub' => base_path('stubs/repository.stub'),
                __DIR__.'/../stubs/repository.simple.stub' => base_path('stubs/repository.simple.stub'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/repository-pattern'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/repository-pattern'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/repository-pattern'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([
            //     MakeRepositoryCommand::class,
            // ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'repository-pattern');

        // Register the main class to use with the facade
        $this->app->singleton('repository-pattern', fn () => new RepositoryPattern());
    }
}
