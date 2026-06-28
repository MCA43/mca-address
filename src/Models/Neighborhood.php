<?php

namespace Mca\Address\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Neighborhood extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'district_id',
        'title',
        'slug',
        'postal_code',
        'uavt_code',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'district_id' => 'integer',
            'uavt_code' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return (string) config('address.tables.neighborhoods', 'mca_neighborhoods');
    }

    /** @return BelongsTo<District, $this> */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
