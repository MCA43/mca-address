<?php

namespace Mca\Address\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Mca\Address\Models\City;
use Mca\Address\Models\Country;
use Mca\Address\Support\AddressSlug;
use Mca\Address\Support\AddressTableSort;

class CityService
{
    public function query(?int $countryId = null): Builder
    {
        $query = City::query()->with('country');

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return $query;
    }

    public function paginated(?int $countryId = null, ?string $search = null, ?string $sort = null, ?string $dir = null): LengthAwarePaginator
    {
        $query = $this->query($countryId);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            });
        }

        AddressTableSort::apply($query, $sort, $dir, AddressTableSort::ENTITY_CITY);

        return $query->paginate((int) config('address.ui.per_page', 20))->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): City
    {
        $country = Country::query()->findOrFail($data['country_id']);
        $data['country_code'] = $country->iso_code_2;
        $data['slug'] ??= AddressSlug::from((string) $data['title']);

        return City::query()->create($data);
    }

    /** @param array<string, mixed> $data */
    public function update(City $city, array $data): City
    {
        if (isset($data['country_id'])) {
            $country = Country::query()->findOrFail($data['country_id']);
            $data['country_code'] = $country->iso_code_2;
        }

        if (array_key_exists('title', $data) && ! array_key_exists('slug', $data)) {
            $data['slug'] = AddressSlug::from((string) $data['title'], $city->slug);
        }

        $city->update($data);

        return $city->fresh();
    }

    public function delete(City $city): bool
    {
        if ($city->districts()->exists()) {
            return false;
        }

        return (bool) $city->delete();
    }

    /** @return list<array{id: int, name: string}> */
    public function options(?int $countryId = null): array
    {
        $countryId ??= mca_address_default_country_id();

        $query = City::query()
            ->where('is_active', true)
            ->orderBy('title');

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return $query->get(['id', 'title'])
            ->map(fn (City $row) => ['id' => $row->id, 'name' => $row->title])
            ->all();
    }
}
