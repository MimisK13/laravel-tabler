<?php

namespace MimisK13\LaravelTabler\Tests;

use Illuminate\Contracts\Console\Kernel;
use MimisK13\LaravelTabler\LaravelTablerServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_is_loaded(): void
    {
        $this->assertTrue($this->app->providerIsLoaded(LaravelTablerServiceProvider::class));
    }

    public function test_tabler_install_command_is_registered(): void
    {
        $commands = $this->app->make(Kernel::class)->all();

        $this->assertArrayHasKey('tabler:install', $commands);
    }
}
