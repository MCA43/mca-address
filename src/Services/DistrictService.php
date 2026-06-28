<?php

namespace Mca\Address\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Mca\Address\Models\City;
use Mca\Address\Models\District;
use Mca\Address\Support\AddressSlug;
use Mca\Address\Support\AddressTableSort;

class DistrictService
{
    public function query(?int $cityId = null): Builder
    {
        $query = District::query()->with('city');

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        return $query;
    }

    public function paginated(?int $cityId = null, ?string $search = null, ?string $sort = null, ?string $dir = null): LengthAwarePaginator
    {
        $query = $this->query($cityId);

        if ($search) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        AddressTableSort::apply($query, $sort, $dir, AddressTableSort::ENTITY_DISTRICT);

        return $query->paginate((int) config('address.ui.per_page', 20))->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): District
    {
        $city = City::query()->findOrFail($data['city_id']);
        $data['city_code'] = $city->code;
        $data['slug'] ??= AddressSlug::from((string) $data['title']);

        return District::query()->create($data);
    }

    /** @param array<string, mixed> $data */
    public function update(District $district, array $data): District
    {
        if (isset($data['city_id'])) {
            $city = City::query()->findOrFail($data['city_id']);
            $data['city_code'] = $city->code;
        }

        if (array_key_exists('title', $data) && ! array_key_exists('slug', $data)) {
            $data['slug'] = AddressSlug::from((string) $data['title'], $district->slug);
        }

        $district->update($data);

        return $district->fresh();
    }

    public function delete(District $district): bool
    {
        if ($district->neighborhoods()->exists()) {
            return false;
        }

        return (bool) $district->delete();
    }

    /** @return list<array{id: int, name: string}> */
    public function options(int $cityId): array
    {
        return District::query()
            ->where('city_id', $cityId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (District $row) => ['id' => $row->id, 'name' => $row->title])
            ->all();
    }
}
