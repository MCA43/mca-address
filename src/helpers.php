<?php

if (! function_exists('mca_addr')) {
    function mca_addr(string $key, array $replace = []): string
    {
        return (string) __('mca-address::address.'.$key, $replace);
    }
}

if (! function_exists('mca_address_default_country_id')) {
    function mca_address_default_country_id(): ?int
    {
        $configured = config('address.api.default_country_id');

        if ($configured !== null && $configured !== '') {
            return (int) $configured;
        }

        if (! class_exists(\Mca\Address\Models\Country::class)) {
            return null;
        }

        return \Mca\Address\Models\Country::query()
            ->where('iso_code_2', 'TR')
            ->value('id');
    }
}
