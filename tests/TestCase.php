<?php

namespace ShreyaSarker\Activitylog\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ShreyaSarker\Activitylog\ActivitylogServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ActivitylogServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('activitylog.table', 'activity_logs');
        $app['config']->set('activitylog.capture_request', false);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Run our test migration (test_users)
        $this->runTestUsersMigration();

        // Run the package migration stub (activity_logs)
        $this->runPackageMigrationStub();
    }

    private function runTestUsersMigration(): void
    {
        $path = __DIR__ . '/database/migrations/2025_01_01_000000_create_test_users_table.php';

        if (! file_exists($path)) {
            $this->fail('Test users migration not found at: ' . $path);
        }

        $migration = require $path;
        $migration->up();
    }

    private function runPackageMigrationStub(): void
    {
        $stubPath = __DIR__ . '/../database/migrations/create_activity_logs_table.php.stub';

        if (! file_exists($stubPath)) {
            $this->fail('Migration stub not found at: ' . $stubPath);
        }

        $migration = require $stubPath;
        $migration->up();
    }
}
