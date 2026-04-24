<?php

namespace MimisK13\LaravelTabler\Tests;

use MimisK13\LaravelTabler\LaravelTablerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelTablerServiceProvider::class,
        ];
    }
}
