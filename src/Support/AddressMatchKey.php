<?php

namespace Mca\Address\Support;

final class AddressMatchKey
{
    public static function district(string $cityCode, string $districtTitle): string
    {
        $plate = str_pad((string) $cityCode, 2, '0', STR_PAD_LEFT);

        return $plate.'|'.self::normalize($districtTitle);
    }

    public static function normalize(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (class_exists(\Normalizer::class)) {
            $value = \Normalizer::normalize($value, \Normalizer::FORM_C) ?: $value;
        }

        return mb_strtolower($value, 'UTF-8');
    }
}
