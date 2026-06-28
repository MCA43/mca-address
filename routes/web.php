<?php

use Illuminate\Support\Facades\Route;
use Mca\Address\Http\Controllers\Api\AddressApiController;
use Mca\Address\Http\Controllers\Web\CityController;
use Mca\Address\Http\Controllers\Web\CountryController;
use Mca\Address\Http\Controllers\Web\DistrictController;
use Mca\Address\Http\Controllers\Web\NeighborhoodController;
use Mca\Address\Http\Controllers\Web\UavtSettingsController;

$web = config('address.routes.web', []);
$prefix = $web['prefix'] ?? 'mca/address';
$middleware = $web['middleware'] ?? ['web', 'auth', 'mca.address.root', 'mca.address.locale'];
$namePrefix = config('address.routes.name_prefix', 'mca.address.');

Route::prefix($prefix)
    ->middleware($middleware)
    ->name($namePrefix)
    ->group(function () use ($prefix, $namePrefix) {
        Route::get('/', fn () => redirect()->route($namePrefix.'countries.index'))->name('home');

        Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
        Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
        Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
        Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');

        Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
        Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
        Route::put('/cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');

        Route::get('/districts', [DistrictController::class, 'index'])->name('districts.index');
        Route::post('/districts', [DistrictController::class, 'store'])->name('districts.store');
        Route::put('/districts/{district}', [DistrictController::class, 'update'])->name('districts.update');
        Route::delete('/districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy');

        Route::get('/neighborhoods', [NeighborhoodController::class, 'index'])->name('neighborhoods.index');
        Route::post('/neighborhoods', [NeighborhoodController::class, 'store'])->name('neighborhoods.store');
        Route::put('/neighborhoods/{neighborhood}', [NeighborhoodController::class, 'update'])->name('neighborhoods.update');
        Route::delete('/neighborhoods/{neighborhood}', [NeighborhoodController::class, 'destroy'])->name('neighborhoods.destroy');

        Route::get('/settings', [UavtSettingsController::class, 'index'])->name('settings.index');
    });

$api = config('address.routes.api', []);
$apiPrefix = $api['prefix'] ?? 'mca/address/api';
$apiMiddleware = $api['middleware'] ?? ['web'];

Route::prefix($apiPrefix)
    ->middleware($apiMiddleware)
    ->name($namePrefix.'api.')
    ->group(function () {
        Route::get('/countries', [AddressApiController::class, 'countries'])->name('countries');
        Route::get('/cities', [AddressApiController::class, 'cities'])->name('cities');
        Route::get('/districts', [AddressApiController::class, 'districts'])->name('districts');
        Route::get('/neighborhoods', [AddressApiController::class, 'neighborhoods'])->name('neighborhoods');
    });
