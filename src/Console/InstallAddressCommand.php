<?php

namespace Mca\Address\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Mca\Address\Database\Seeders\TurkeyAddressSeeder;
use Mca\Address\Support\McaAddressLocale;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mca:address:install')]
class InstallAddressCommand extends Command
{
    protected $signature = 'mca:address:install
                            {--no-assets : Skip CSS publish}
                            {--no-seed : Skip Turkey demo seed}';

    protected $description = 'Install MCA Address (migration, assets, demo data)';

    public function handle(): int
    {
        McaAddressLocale::apply();

        $this->components->info(mca_addr('console.install.start'));

        if (! file_exists(config_path('address.php'))) {
            $this->callSilent('vendor:publish', ['--tag' => 'mca-address-config']);
        }
        $this->components->task(mca_addr('console.install.config_ready'), fn () => true);

        if (! $this->option('no-assets')) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'mca-address-assets',
                '--force' => true,
            ]);
            $this->components->task(mca_addr('console.install.assets_published'), fn () => true);
        }

        Artisan::call('migrate', ['--force' => true]);
        $this->output->write(Artisan::output());
        $this->components->task(mca_addr('console.install.migration_done'), fn () => true);

        if (! $this->option('no-seed')) {
            $this->callSilent('db:seed', ['--class' => TurkeyAddressSeeder::class, '--force' => true]);
            $this->components->task(mca_addr('console.install.seed_done'), fn () => true);
        }

        $this->newLine();
        $this->components->info(mca_addr('console.install.done'));
        $this->line('  '.mca_addr('console.install.web_ui', [
            'prefix' => config('address.routes.web.prefix', 'mca/address'),
        ]));

        return self::SUCCESS;
    }
}
