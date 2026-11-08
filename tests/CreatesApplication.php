<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Config may be cached with the local MySQL connection. Force tests
        // onto an isolated, disposable database before database test traits
        // such as RefreshDatabase are allowed to run.
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['db']->purge();

        if (
            $app['config']->get('database.default') !== 'sqlite' ||
            $app['config']->get('database.connections.sqlite.database') !== ':memory:'
        ) {
            throw new \RuntimeException('Tests must use the isolated in-memory SQLite database.');
        }

        return $app;
    }
}
