<?php

namespace Mca\Address\Support;

use Illuminate\Support\Str;

final class AddressSlug
{
    public static function from(string $title, ?string $fallback = null): string
    {
        $slug = Str::slug($title);

        return $slug !== '' ? $slug : ($fallback ?? Str::random(8));
    }
}
