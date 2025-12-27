<?php

namespace ShreyaSarker\Activitylog;

use Illuminate\Support\ServiceProvider;

class ActivitylogServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Merge package config so config('activitylog.*') works even without publishing
        $this->mergeConfigFrom(
            __DIR__ . '/../config/activitylog.php',
            'activitylog'
        );

        // Bind logger into container
        $this->app->singleton(ActivityLogger::class, function () {
            return new ActivityLogger();
        });

        // Optional alias for facade-style resolution
        $this->app->alias(ActivityLogger::class, 'activitylog');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/activitylog.php' => config_path('activitylog.php'),
        ], 'activitylog-config');

        // Publish migration
        // Users should manually rename it with a timestamp after publishing
        $this->publishes([
            __DIR__ . '/../database/migrations/create_activity_logs_table.php' =>
                database_path('migrations/create_activity_logs_table.php'),
        ], 'activitylog-migrations');
    }
}
