<?php

namespace HexideDigital\AdminConfigurations;

use HexideDigital\AdminConfigurations\Classes\Configuration;
use Illuminate\Support\ServiceProvider;

class AdminConfigurationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/admin-configurations.php' => config_path('admin-configurations.php'),
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
            __DIR__ . '/../database/seeders/' => database_path('seeders'),
        ], 'admin-configurations-publishes');

        if (config('admin-configurations.vendor_migrations', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin-configurations.php', 'admin-configurations');

        $this->app->bind('admin_configuration', Configuration::class);
    }
}
