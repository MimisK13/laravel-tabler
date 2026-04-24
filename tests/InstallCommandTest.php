<?php

namespace MimisK13\LaravelTabler\Tests;

use MimisK13\LaravelTabler\Console\InstallCommand;

class InstallCommandTest extends TestCase
{
    public function test_update_node_packages_writes_development_dependencies(): void
    {
        $packageJsonPath = base_path('package.json');
        $originalContent = file_exists($packageJsonPath)
            ? file_get_contents($packageJsonPath)
            : null;

        try {
            file_put_contents($packageJsonPath, json_encode([
                'name' => 'example/app',
                'devDependencies' => [
                    'z-package' => '^1.0.0',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL);

            FakeInstallCommand::callUpdateNodePackages(function (array $packages): array {
                return [
                    'a-package' => '^2.0.0',
                ] + $packages;
            });

            $updatedJson = json_decode((string) file_get_contents($packageJsonPath), true);

            $this->assertArrayHasKey('devDependencies', $updatedJson);
            $this->assertSame([
                'a-package' => '^2.0.0',
                'z-package' => '^1.0.0',
            ], $updatedJson['devDependencies']);
        } finally {
            if ($originalContent === null) {
                @unlink($packageJsonPath);
            } else {
                file_put_contents($packageJsonPath, $originalContent);
            }
        }
    }
}

class FakeInstallCommand extends InstallCommand
{
    public static function callUpdateNodePackages(callable $callback): void
    {
        parent::updateNodePackages($callback);
    }
}
