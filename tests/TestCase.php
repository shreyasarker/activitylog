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
        // Activity log config
        $app['config']->set('activitylog.table', 'activity_logs');
        $app['config']->set('activitylog.capture_request', false);
        $app['config']->set('activitylog.silent_failures', false); // Throw exceptions in tests

        // Database config for MySQL
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'test_activitylog'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create database if it doesn't exist (MySQL only)
        $this->createDatabaseIfNotExists();

        // Run our test migration (test_users)
        $this->runTestUsersMigration();

        // Run the package migration stub (activity_logs)
        $this->runPackageMigrationStub();
    }

    private function createDatabaseIfNotExists(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        // Only for MySQL/MariaDB
        if ($driver === 'mysql') {
            $database = config("database.connections.{$connection}.database");
            $host = config("database.connections.{$connection}.host");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");

            try {
                // Connect without database name to create it
                $pdo = new \PDO(
                    "mysql:host={$host}",
                    $username,
                    $password
                );
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}`");
            } catch (\PDOException $e) {
                // Database might already exist or connection failed
                // Continue anyway - migrations will handle it
            }
        }
    }

    private function runTestUsersMigration(): void
    {
        $path = __DIR__ . '/database/migrations/2025_01_01_000000_create_test_users_table.php';

        if (! file_exists($path)) {
            $this->fail('Test users migration not found at: ' . $path);
        }

        $migration = require $path;
        // Drop table if exists before creating (for MySQL)
        \Illuminate\Support\Facades\Schema::dropIfExists('test_users');
        $migration->up();
    }

    private function runPackageMigrationStub(): void
    {
        $migrationPath = __DIR__ . '/../database/migrations/create_activity_logs_table.php';

        if (! file_exists($migrationPath)) {
            $this->fail('Migration file not found at: ' . $migrationPath);
        }

        $migration = require $migrationPath;
        // Drop table if exists before creating (for MySQL)
        $tableName = config('activitylog.table', 'activity_logs');
        \Illuminate\Support\Facades\Schema::dropIfExists($tableName);
        $migration->up();
    }
}
