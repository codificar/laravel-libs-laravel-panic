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

        $this->publishes([
            __DIR__.'/../public/js' => public_path('vendor/codificar/panic'),
        ], 'public_vuejs_libs');
    }
    public function register()
    {
    }
}
