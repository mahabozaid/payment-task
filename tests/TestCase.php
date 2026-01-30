<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected $app;
    /**
     * Setup for all tests
     */
    protected function setUp(): void
    {
        $this->app = $this->createApplication();
        // Load all module migrations for in-memory SQLite before refreshing database
        $this->loadModuleMigrations();

        parent::setUp();

        // Load all module factories automatically
        $this->loadModuleFactories();
    }

    /**
     * Load factories from each module
     */
    protected function loadModuleFactories(): void
    {
        foreach (app('modules')->all() as $module) {
            $factoriesPath = $module->getPath() . '/Database/Factories';
            if (is_dir($factoriesPath)) {
                $this->app->withFactories($factoriesPath);
            }
        }
    }

    /**
     * Load migrations from each module
     */
    protected function loadModuleMigrations(): void
    {
        foreach (app('modules')->all() as $module) {
            $migrationsPath = $module->getPath() . '/Database/Migrations';
            if (is_dir($migrationsPath)) {
                $this->app->loadMigrationsFrom($migrationsPath);
            }
        }
    }


     public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }
    
}
