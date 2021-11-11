<?php

namespace HexideDigital\AdminConfigurations;

use HexideDigital\AdminConfigurations\Classes\Configuration;
use Illuminate\Support\ServiceProvider;

class AdminConfigurationsServiceProvider extends ServiceProvider
{

    /**
     * Boot the instance.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/admin_configurations.php' => config_path('admin_configurations.php'),
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
            __DIR__ . '/../database/seeders/' => database_path('seeders'),
        ], 'admin-configurations-publishes');

        if (config('admin_configurations.vendor_migrations', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin_configurations.php', 'admin_configurations');

        $this->app->bind('admin_configuration', Configuration::class);
    }

}
