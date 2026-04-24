<?php

namespace MimisK13\LaravelTabler\Tests;

use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use MimisK13\LaravelTabler\Console\InstallCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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

    public function test_install_blade_copies_stubs_updates_package_json_and_selects_npm_commands(): void
    {
        $files = new Filesystem;
        $backupRoot = base_path('storage/framework/testing/tabler-backup-'.uniqid('', true));
        $files->ensureDirectoryExists($backupRoot);

        $packageJsonPath = base_path('package.json');
        $routesPath = base_path('routes/web.php');
        $vitePath = base_path('vite.config.js');
        $viewsPath = resource_path('views');
        $pnpmLockPath = base_path('pnpm-lock.yaml');
        $yarnLockPath = base_path('yarn.lock');

        $this->backupFile($files, $packageJsonPath, $backupRoot.'/package.json');
        $this->backupFile($files, $routesPath, $backupRoot.'/routes_web.php');
        $this->backupFile($files, $vitePath, $backupRoot.'/vite.config.js');
        $this->backupFile($files, $pnpmLockPath, $backupRoot.'/pnpm-lock.yaml');
        $this->backupFile($files, $yarnLockPath, $backupRoot.'/yarn.lock');
        $this->backupDirectory($files, $viewsPath, $backupRoot.'/views');

        try {
            file_put_contents($packageJsonPath, json_encode([
                'name' => 'example/app',
                'devDependencies' => [],
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL);

            if ($files->exists($pnpmLockPath)) {
                $files->delete($pnpmLockPath);
            }

            if ($files->exists($yarnLockPath)) {
                $files->delete($yarnLockPath);
            }

            $files->deleteDirectory($viewsPath);
            $files->ensureDirectoryExists($viewsPath);

            $command = new FakeInstallCommand;
            $command->setLaravel($this->app);
            $command->callInstallBlade();

            $this->assertFileExists(resource_path('views/layouts/tabler.blade.php'));
            $this->assertFileExists(resource_path('views/dashboard.blade.php'));
            $this->assertFileExists(resource_path('views/components/button/save.blade.php'));
            $this->assertFileExists(base_path('routes/web.php'));
            $this->assertFileExists(base_path('vite.config.js'));

            $updatedJson = json_decode((string) file_get_contents($packageJsonPath), true);
            $this->assertArrayHasKey('devDependencies', $updatedJson);
            $this->assertSame('^1.0', $updatedJson['devDependencies']['@tabler/core']);
            $this->assertSame('^4.0', $updatedJson['devDependencies']['vite-plugin-static-copy']);

            $this->assertSame([
                ['npm install', 'npm run build'],
            ], $command->recordedCommands());
        } finally {
            $this->restoreFile($files, $backupRoot.'/package.json', $packageJsonPath);
            $this->restoreFile($files, $backupRoot.'/routes_web.php', $routesPath);
            $this->restoreFile($files, $backupRoot.'/vite.config.js', $vitePath);
            $this->restoreFile($files, $backupRoot.'/pnpm-lock.yaml', $pnpmLockPath);
            $this->restoreFile($files, $backupRoot.'/yarn.lock', $yarnLockPath);
            $this->restoreDirectory($files, $backupRoot.'/views', $viewsPath);
            $files->deleteDirectory($backupRoot);
        }
    }

    public function test_installed_stub_views_compile_and_routes_register_without_errors(): void
    {
        $files = new Filesystem;
        $backupRoot = base_path('storage/framework/testing/tabler-compile-'.uniqid('', true));
        $files->ensureDirectoryExists($backupRoot);

        $packageJsonPath = base_path('package.json');
        $routesPath = base_path('routes/web.php');
        $vitePath = base_path('vite.config.js');
        $viewsPath = resource_path('views');

        $this->backupFile($files, $packageJsonPath, $backupRoot.'/package.json');
        $this->backupFile($files, $routesPath, $backupRoot.'/routes_web.php');
        $this->backupFile($files, $vitePath, $backupRoot.'/vite.config.js');
        $this->backupDirectory($files, $viewsPath, $backupRoot.'/views');

        try {
            file_put_contents($packageJsonPath, json_encode([
                'name' => 'example/app',
                'devDependencies' => [],
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL);

            $files->deleteDirectory($viewsPath);
            $files->ensureDirectoryExists($viewsPath);

            $command = new FakeInstallCommand;
            $command->setLaravel($this->app);
            $command->callInstallBlade();

            $viewFiles = $files->allFiles($viewsPath);
            $this->assertNotEmpty($viewFiles);

            foreach ($viewFiles as $viewFile) {
                if ($viewFile->getExtension() !== 'php' || ! str_ends_with($viewFile->getFilename(), '.blade.php')) {
                    continue;
                }

                $source = $viewFile->getContents();
                $compiled = Blade::compileString($source);
                $this->assertIsString($compiled);
                $this->assertNotSame('', trim($compiled), 'Compiled blade output should not be empty for '.$viewFile->getPathname());
            }

            $routeContents = (string) file_get_contents(base_path('routes/web.php'));
            $this->assertStringContainsString("Route::get('/', function () {", $routeContents);
            $this->assertStringContainsString("Route::get('empty/', function () {", $routeContents);
            $this->assertStringContainsString("->name('empty');", $routeContents);
            $this->assertStringContainsString("->name('license');", $routeContents);
        } finally {
            $this->restoreFile($files, $backupRoot.'/package.json', $packageJsonPath);
            $this->restoreFile($files, $backupRoot.'/routes_web.php', $routesPath);
            $this->restoreFile($files, $backupRoot.'/vite.config.js', $vitePath);
            $this->restoreDirectory($files, $backupRoot.'/views', $viewsPath);
            $files->deleteDirectory($backupRoot);
        }
    }

    private function backupFile(Filesystem $files, string $source, string $backup): void
    {
        if (! $files->exists($source)) {
            return;
        }

        $files->ensureDirectoryExists(dirname($backup));
        $files->copy($source, $backup);
    }

    private function restoreFile(Filesystem $files, string $backup, string $target): void
    {
        if (! $files->exists($backup)) {
            if ($files->exists($target)) {
                $files->delete($target);
            }

            return;
        }

        $files->ensureDirectoryExists(dirname($target));
        $files->copy($backup, $target);
    }

    private function backupDirectory(Filesystem $files, string $source, string $backup): void
    {
        if (! $files->isDirectory($source)) {
            return;
        }

        $files->ensureDirectoryExists(dirname($backup));
        $files->copyDirectory($source, $backup);
    }

    private function restoreDirectory(Filesystem $files, string $backup, string $target): void
    {
        if ($files->isDirectory($target)) {
            $files->deleteDirectory($target);
        }

        if (! $files->isDirectory($backup)) {
            $files->ensureDirectoryExists($target);

            return;
        }

        $files->copyDirectory($backup, $target);
    }
}

class FakeInstallCommand extends InstallCommand
{
    /**
     * @var list<list<string>>
     */
    private array $commands = [];

    public static function callUpdateNodePackages(callable $callback): void
    {
        parent::updateNodePackages($callback);
    }

    public function callInstallBlade(): void
    {
        $output = new OutputStyle(new ArrayInput([]), new BufferedOutput);
        $this->setOutput($output);
        $this->components = new ComponentsFactory($this->output);

        $this->installBlade();
    }

    /**
     * @param  array<int, string>  $commands
     */
    protected function runCommands($commands)
    {
        $this->commands[] = array_values($commands);
    }

    /**
     * @return list<list<string>>
     */
    public function recordedCommands(): array
    {
        return $this->commands;
    }
}
