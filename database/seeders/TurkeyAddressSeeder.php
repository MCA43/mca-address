<?php

namespace Mca\Address\Database\Seeders;

use Illuminate\Database\Seeder;
use Mca\Address\Models\City;
use Mca\Address\Models\Country;
use Mca\Address\Models\District;
use Mca\Address\Support\AddressSlug;

class TurkeyAddressSeeder extends Seeder
{
    public function run(): void
    {
        $cities = require dirname(__DIR__).'/data/turkey-cities.php';
        $districtsByPlate = require dirname(__DIR__).'/data/turkey-districts.php';

        if ($cities === [] || ! is_array($cities)) {
            $this->command?->warn('turkey-cities.php boş — database/data dosyalarını kontrol edin.');

            return;
        }

        $turkey = Country::query()->updateOrCreate(
            ['iso_code_2' => 'TR'],
            [
                'title' => 'Türkiye',
                'slug' => 'turkiye',
                'iso_code_3' => 'TUR',
                'postcode_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        foreach ($cities as $index => $cityRow) {
            $plate = str_pad((string) ($cityRow['plate'] ?? ''), 2, '0', STR_PAD_LEFT);
            $name = (string) ($cityRow['name'] ?? '');

            if ($plate === '' || $name === '') {
                continue;
            }

            $city = City::query()->updateOrCreate(
                ['country_id' => $turkey->id, 'code' => $plate],
                [
                    'country_code' => 'TR',
                    'title' => $name,
                    'slug' => AddressSlug::from($name),
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );

            $districtNames = $districtsByPlate[$plate] ?? $districtsByPlate[ltrim($plate, '0')] ?? [];

            foreach ($districtNames as $dIndex => $districtName) {
                District::query()->updateOrCreate(
                    ['city_id' => $city->id, 'title' => $districtName],
                    [
                        'city_code' => $plate,
                        'slug' => AddressSlug::from($districtName),
                        'is_active' => true,
                        'sort_order' => $dIndex + 1,
                    ],
                );
            }
        }
    }
}
