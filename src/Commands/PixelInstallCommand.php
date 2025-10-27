<?php

namespace Ideacrafters\PixelManager\Commands;

use Illuminate\Console\Command;

class PixelInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pixel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and setup the Laravel Pixel Manager';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Welcome to Laravel Pixel Manager Setup!');
        $this->newLine();

        // Publish config
        $this->info('Publishing configuration file...');
        $this->call('vendor:publish', [
            '--tag' => 'pixels-manager-config',
        ]);
        $this->info('✓ Configuration published');

        // Publish migrations
        $this->info('Publishing migrations...');
        $this->call('vendor:publish', [
            '--tag' => 'pixels-manager-migrations',
        ]);
        $this->info('✓ Migrations published');

        // Run migrations
        if ($this->confirm('Would you like to run the migrations now?')) {
            $this->call('migrate');
            $this->info('✓ Database tables created');
        }

        $this->newLine();
        $this->info('Installation complete! 🎉');
        $this->newLine();
        $this->info('Next steps:');
        $this->line('  1. Configure your pixels: <comment>php artisan pixel:add</comment>');
        $this->line('  2. Add @pixels directive to your Blade layouts');
        $this->line('  3. Start tracking events in your application');

        return self::SUCCESS;
    }
}
