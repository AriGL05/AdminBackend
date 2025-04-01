<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ViteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Create a custom @vite directive that does nothing in development
        // This allows you to keep the @vite directives in your code
        // but they won't cause errors if the manifest.json doesn't exist
        Blade::directive('vite', function ($expression) {
            if (app()->environment('local')) {
                return '';
            }
        });
    }
}
