<?php

namespace Mca\Address\Support;

final class McaAddressLocale
{
    public static function apply(): void
    {
        $locale = config('address.locale');

        if (is_string($locale) && $locale !== '') {
            app()->setLocale($locale);
        }
    }

    public static function resolve(): string
    {
        $locale = config('address.locale');

        if (is_string($locale) && $locale !== '') {
            return $locale;
        }

        return app()->getLocale();
    }
}
