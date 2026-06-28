<?php

namespace Mca\Address\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Mca\Address\Models\Country;
use Mca\Address\Support\AddressSlug;
use Mca\Address\Support\AddressTableSort;

class CountryService
{
    public function query(): Builder
    {
        return Country::query();
    }

    public function paginated(?string $search = null, ?string $sort = null, ?string $dir = null): LengthAwarePaginator
    {
        $query = $this->query();

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('iso_code_2', 'like', '%'.$search.'%')
                    ->orWhere('iso_code_3', 'like', '%'.$search.'%');
            });
        }

        AddressTableSort::apply($query, $sort, $dir, AddressTableSort::ENTITY_COUNTRY);

        return $query->paginate((int) config('address.ui.per_page', 20))->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): Country
    {
        $data['slug'] ??= AddressSlug::from((string) $data['title']);

        return Country::query()->create($data);
    }

    /** @param array<string, mixed> $data */
    public function update(Country $country, array $data): Country
    {
        if (array_key_exists('title', $data) && ! array_key_exists('slug', $data)) {
            $data['slug'] = AddressSlug::from((string) $data['title'], $country->slug);
        }

        $country->update($data);

        return $country->fresh();
    }

    public function delete(Country $country): bool
    {
        if ($country->cities()->exists()) {
            return false;
        }

        return (bool) $country->delete();
    }

    /** @return list<array{id: int, name: string}> */
    public function options(): array
    {
        return $this->query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (Country $row) => ['id' => $row->id, 'name' => $row->title])
            ->all();
    }
}
