<?php

namespace Mca\Address\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mca\Address\Models\City;
use Mca\Address\Models\Country;
use Mca\Address\Models\District;
use Mca\Address\Models\Neighborhood;

final class AddressTableSort
{
    public const string ENTITY_COUNTRY = 'country';

    public const string ENTITY_CITY = 'city';

    public const string ENTITY_DISTRICT = 'district';

    public const string ENTITY_NEIGHBORHOOD = 'neighborhood';

    /** @return array{sort: ?string, dir: string} */
    public static function fromRequest(Request $request): array
    {
        $sort = trim($request->string('sort')->toString());

        return [
            'sort' => $sort !== '' ? $sort : null,
            'dir' => strtolower($request->string('dir')->toString()) === 'desc' ? 'desc' : 'asc',
        ];
    }

    public static function apply(Builder $query, ?string $sort, ?string $dir, string $entity): void
    {
        if ($sort === null || $sort === '') {
            $query->orderBy('sort_order')->orderBy('title');

            return;
        }

        $direction = $dir === 'desc' ? 'desc' : 'asc';
        $columns = self::columns($entity);

        if (! isset($columns[$sort])) {
            $query->orderBy('sort_order')->orderBy('title');

            return;
        }

        $handler = $columns[$sort];

        if (is_callable($handler)) {
            $handler($query, $direction);

            return;
        }

        $query->orderBy($handler, $direction);
    }

    /** @return array<string, string|callable(Builder, string): void> */
    private static function columns(string $entity): array
    {
        return match ($entity) {
            self::ENTITY_COUNTRY => self::countryColumns(),
            self::ENTITY_CITY => self::cityColumns(),
            self::ENTITY_DISTRICT => self::districtColumns(),
            self::ENTITY_NEIGHBORHOOD => self::neighborhoodColumns(),
            default => [],
        };
    }

    /** @return array<string, string|callable(Builder, string): void> */
    private static function countryColumns(): array
    {
        $table = (new Country)->getTable();

        return [
            'title' => "{$table}.title",
            'iso_code_2' => "{$table}.iso_code_2",
            'iso_code_3' => "{$table}.iso_code_3",
            'is_active' => "{$table}.is_active",
            'sort_order' => "{$table}.sort_order",
        ];
    }

    /** @return array<string, string|callable(Builder, string): void> */
    private static function cityColumns(): array
    {
        $table = (new City)->getTable();
        $countries = (new Country)->getTable();

        return [
            'title' => "{$table}.title",
            'code' => "{$table}.code",
            'sort_order' => "{$table}.sort_order",
            'is_active' => "{$table}.is_active",
            'country' => function (Builder $query, string $direction) use ($table, $countries): void {
                $query->leftJoin("{$countries} as addr_sort_countries", 'addr_sort_countries.id', '=', "{$table}.country_id")
                    ->orderBy('addr_sort_countries.title', $direction)
                    ->select("{$table}.*");
            },
        ];
    }

    /** @return array<string, string|callable(Builder, string): void> */
    private static function districtColumns(): array
    {
        $table = (new District)->getTable();
        $cities = (new City)->getTable();

        return [
            'title' => "{$table}.title",
            'sort_order' => "{$table}.sort_order",
            'is_active' => "{$table}.is_active",
            'city' => function (Builder $query, string $direction) use ($table, $cities): void {
                $query->leftJoin("{$cities} as addr_sort_cities", 'addr_sort_cities.id', '=', "{$table}.city_id")
                    ->orderBy('addr_sort_cities.title', $direction)
                    ->select("{$table}.*");
            },
        ];
    }

    /** @return array<string, string|callable(Builder, string): void> */
    private static function neighborhoodColumns(): array
    {
        $table = (new Neighborhood)->getTable();
        $districts = (new District)->getTable();
        $cities = (new City)->getTable();

        return [
            'title' => "{$table}.title",
            'postal_code' => "{$table}.postal_code",
            'sort_order' => "{$table}.sort_order",
            'is_active' => "{$table}.is_active",
            'district' => function (Builder $query, string $direction) use ($table, $districts, $cities): void {
                $query->leftJoin("{$districts} as addr_sort_districts", 'addr_sort_districts.id', '=', "{$table}.district_id")
                    ->leftJoin("{$cities} as addr_sort_cities", 'addr_sort_cities.id', '=', 'addr_sort_districts.city_id')
                    ->orderBy('addr_sort_cities.title', $direction)
                    ->orderBy('addr_sort_districts.title', $direction)
                    ->select("{$table}.*");
            },
        ];
    }
}
