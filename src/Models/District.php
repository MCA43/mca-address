<?php

namespace Mca\Address\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'city_id',
        'city_code',
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
            'city_id' => 'integer',
            'uavt_code' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return (string) config('address.tables.districts', 'mca_districts');
    }

    /** @return BelongsTo<City, $this> */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /** @return HasMany<Neighborhood, $this> */
    public function neighborhoods(): HasMany
    {
        return $this->hasMany(Neighborhood::class);
    }
}
