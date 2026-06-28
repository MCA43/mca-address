<?php

namespace Mca\Address\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Mca\Address\Models\Neighborhood;
use Mca\Address\Support\AddressSlug;
use Mca\Address\Support\AddressTableSort;

class NeighborhoodService
{
    public function query(?int $districtId = null): Builder
    {
        $query = Neighborhood::query()->with('district.city');

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        return $query;
    }

    public function paginated(?int $districtId = null, ?string $search = null, ?string $sort = null, ?string $dir = null): LengthAwarePaginator
    {
        $query = $this->query($districtId);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('postal_code', 'like', '%'.$search.'%');
            });
        }

        AddressTableSort::apply($query, $sort, $dir, AddressTableSort::ENTITY_NEIGHBORHOOD);

        return $query->paginate((int) config('address.ui.per_page', 20))->withQueryString();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): Neighborhood
    {
        $data['slug'] ??= AddressSlug::from((string) $data['title']);

        return Neighborhood::query()->create($data);
    }

    /** @param array<string, mixed> $data */
    public function update(Neighborhood $neighborhood, array $data): Neighborhood
    {
        if (array_key_exists('title', $data) && ! array_key_exists('slug', $data)) {
            $data['slug'] = AddressSlug::from((string) $data['title'], $neighborhood->slug);
        }

        $neighborhood->update($data);

        return $neighborhood->fresh();
    }

    public function delete(Neighborhood $neighborhood): bool
    {
        return (bool) $neighborhood->delete();
    }

    /** @return list<array{id: int, name: string}> */
    public function options(int $districtId): array
    {
        return Neighborhood::query()
            ->where('district_id', $districtId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (Neighborhood $row) => ['id' => $row->id, 'name' => $row->title])
            ->all();
    }
}
