<?php

namespace Codificar\Panic;

use Illuminate\Support\ServiceProvider;

class PanicServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'views');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'panic');
    }
    public function register()
    {
    }
}
