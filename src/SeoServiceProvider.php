<?php

namespace Bale\Seo;

use Illuminate\Support\ServiceProvider;
use Bale\Seo\Commands\SeoInstallCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'seo');

        $this->commands([
            SeoInstallCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'seo');
        
        // Load routes manually to allow conditional loading based on config
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
        }

        $this->registerBladeComponents();
    }

    /**
     * Publish file agar bisa diubah oleh user.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/seo.php' => config_path('seo.php'),
        ], 'seo-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/seo'),
        ], 'seo-views');

        $this->publishes($this->getMigrations(), 'seo-migrations');
    }

    /**
     * Mengambil semua file migration dari direktori package.
     */
    protected function getMigrations(): array
    {
        $migrations = [];
        $sourcePath = __DIR__ . '/../database/migrations/';

        if (!is_dir($sourcePath)) {
            return $migrations;
        }

        foreach (glob($sourcePath . '*.{php,stub}', GLOB_BRACE) as $file) {
            $filename = basename($file);
            $targetFile = $this->getMigrationFileName($filename);
            $migrations[$file] = $targetFile;
        }

        return $migrations;
    }

    /**
     * Membuat nama file migration yang sesuai dengan timestamp laravel.
     */
    protected function getMigrationFileName(string $filename): string
    {
        $timestamp = date('Y_m_d_His');
        $migrationName = str_replace(['.php', '.stub'], '', $filename) . '.php';

        return database_path('migrations/' . $timestamp . '_' . $migrationName);
    }

    /**
     * Register Blade components automatically.
     */
    protected function registerBladeComponents(): void
    {
        $componentPath = __DIR__ . '/../resources/views/components';

        if (File::isDirectory($componentPath)) {
            foreach (File::allFiles($componentPath) as $file) {
                if ($file->getExtension() === 'blade') {
                    $componentName = str_replace('.blade.php', '', $file->getFilename());
                    // Register as <x-seo::component-name />
                    Blade::component('seo::' . $componentName, 'seo::' . $componentName);
                }
            }
        }
    }
}
