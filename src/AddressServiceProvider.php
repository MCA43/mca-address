<?php

namespace Mca\Address;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Mca\Address\Console\ImportTurkeyCommand;
use Mca\Address\Console\InstallAddressCommand;
use Mca\Address\Http\Middleware\EnsureMcaAddressRoot;
use Mca\Address\Http\Middleware\SetMcaAddressLocale;
use Mca\Address\Services\CityService;
use Mca\Address\Services\CountryService;
use Mca\Address\Services\DistrictService;
use Mca\Address\Services\NeighborhoodService;
use Mca\Address\Services\TurkeyNeighborhoodImporter;

class AddressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/address.php', 'address');

        $this->app->singleton(CountryService::class);
        $this->app->singleton(CityService::class);
        $this->app->singleton(DistrictService::class);
        $this->app->singleton(NeighborhoodService::class);
        $this->app->singleton(TurkeyNeighborhoodImporter::class);
    }

    public function boot(): void
    {
        if (! config('address.enabled', true)) {
            return;
        }

        $this->registerPublishing();
        $this->registerMiddleware();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'mca-address');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mca-address');
        $this->registerRoutes();
        $this->registerHub();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallAddressCommand::class,
                ImportTurkeyCommand::class,
            ]);
        }
    }

    protected function registerHub(): void
    {
        if (! function_exists('mca_hub_register')) {
            return;
        }

        mca_hub_register('address', [
            'enabled' => fn () => (bool) config('address.enabled', true)
                && (bool) config('address.routes.web.enabled', true),
        ]);
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/address.php' => config_path('address.php'),
        ], 'mca-address-config');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mca-address'),
        ], 'mca-address-assets');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'mca-address-migrations');
    }

    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('mca.address.root', EnsureMcaAddressRoot::class);
        $router->aliasMiddleware('mca.address.locale', SetMcaAddressLocale::class);
    }

    protected function registerRoutes(): void
    {
        if (! config('address.routes.load_package_routes', true)) {
            return;
        }

        if (config('address.routes.web.enabled', true) || config('address.routes.api.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }
}
