<?php

namespace Mca\Address\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'iso_code_2',
        'iso_code_3',
        'postcode_required',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'postcode_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return (string) config('address.tables.countries', 'mca_countries');
    }

    /** @return HasMany<City, $this> */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
