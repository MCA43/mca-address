<?php

namespace Mca\Address\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'country_code',
        'title',
        'slug',
        'code',
        'uavt_code',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'uavt_code' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return (string) config('address.tables.cities', 'mca_cities');
    }

    /** @return BelongsTo<Country, $this> */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /** @return HasMany<District, $this> */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
