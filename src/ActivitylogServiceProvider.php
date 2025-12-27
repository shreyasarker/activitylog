<?php

namespace ShreyaSarker\Activitylog;

use Illuminate\Support\ServiceProvider;

class ActivitylogServiceProvider extends ServiceProvider
{
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

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/activitylog.php' => config_path('activitylog.php'),
        ], 'activitylog-config');

        // Publish migration (stub -> timestamped migration file)
        $this->publishes([
            __DIR__ . '/../database/migrations/create_activity_logs_table.php.stub' =>
                database_path('migrations/' . date('Y_m_d_His') . '_create_activity_logs_table.php'),
        ], 'activitylog-migrations');
    }
}
