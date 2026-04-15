<?php

namespace Bale\Seo\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SeoInstallCommand extends Command
{
    public $signature = 'seo:install';

    public $description = 'Install Bale SEO package';

    public function handle(): int
    {
        $this->info('Installing Bale SEO...');

        // 1. Publish Config
        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => 'Bale\Seo\SeoServiceProvider',
            '--tag' => 'seo-config',
        ]);

        // 2. Publish Migration (Optional)
        $publishMigration = $this->choice(
            'Do you want to publish the SEO migrations?',
            ['No', 'Yes'],
            1 // Default to Yes
        );

        if ($publishMigration === 'Yes') {
            $this->info('Publishing migrations...');
            $this->call('vendor:publish', [
                '--provider' => 'Bale\Seo\SeoServiceProvider',
                '--tag' => 'seo-migrations',
            ]);
        }

        // 3. Ask about routes
        $useRoutes = $this->choice(
            'Do you want to enable SEO routes (sitemap.xml and robots.txt)?',
            ['No', 'Yes'],
            0 // Default to No
        );

        if ($useRoutes === 'Yes') {
            $this->updateConfig('use_routes', true);
            $this->info('SEO routes enabled.');
        } else {
            $this->updateConfig('use_routes', false);
            $this->info('SEO routes disabled.');
        }

        $this->info('Bale SEO installed successfully.');

        return self::SUCCESS;
    }

    /**
     * Update the config file or .env
     */
    protected function updateConfig(string $key, bool $value): void
    {
        $path = base_path('.env');
        $envKey = 'SEO_USE_ROUTES';
        $envValue = $value ? 'true' : 'false';

        if (File::exists($path)) {
            $content = File::get($path);
            if (str_contains($content, $envKey)) {
                $content = preg_replace("/{$envKey}=.*/", "{$envKey}={$envValue}", $content);
            } else {
                $content .= "\n{$envKey}={$envValue}\n";
            }
            File::put($path, $content);
        }
    }
}
