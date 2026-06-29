<?php

namespace Mca\Address\Support;

final class AddressDataPaths
{
    public static function packageRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function turkeyNeighborhoodsJson(): string
    {
        $configured = config('address.import.turkey.neighbourhoods_file');

        if (is_string($configured) && $configured !== '' && is_file($configured)) {
            return $configured;
        }

        return self::packageRoot().'/database/data/turkey-neighbourhoods.json';
    }
}
