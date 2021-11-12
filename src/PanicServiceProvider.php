<?php

namespace Codificar\Panic;

use Illuminate\Support\ServiceProvider;

class PanicServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'laravel-panic');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'panic');

        //to be able to run the migration to run the seeder automatically, you need to publish it to the main app first then run the migration
        $this->publishes([
            __DIR__ . '/database/seeds' => database_path('seeds'),
        ], 'public_vuejs_libs');
    }
    public function register()
    {
    }
}
