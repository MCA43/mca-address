<?php

namespace Mca\Address\Support;

use Illuminate\Support\Collection;

final class AddressSort
{
    /**
     * @template T
     *
     * @param  iterable<T>  $items
     * @return Collection<int, T>
     */
    public static function byLabel(iterable $items, callable $label): Collection
    {
        $collection = $items instanceof Collection ? $items : collect($items);
        $locale = app()->getLocale();

        /** @var array<string, \Collator> $collators */
        static $collators = [];

        return $collection->sort(function ($a, $b) use ($label, $locale, &$collators) {
            $left = (string) $label($a);
            $right = (string) $label($b);

            if (class_exists(\Collator::class)) {
                $collators[$locale] ??= new \Collator($locale);

                return $collators[$locale]->compare($left, $right);
            }

            return strnatcasecmp($left, $right);
        })->values();
    }
}
