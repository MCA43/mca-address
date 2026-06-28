<?php

namespace Mca\Address\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mca\Address\Http\Controllers\McaAddressController;
use Mca\Address\Models\District;
use Mca\Address\Models\Neighborhood;
use Mca\Address\Services\DistrictService;
use Mca\Address\Services\NeighborhoodService;
use Mca\Address\Support\AddressSort;

class NeighborhoodController extends McaAddressController
{
    public function __construct(
        private readonly NeighborhoodService $neighborhoods,
        private readonly DistrictService $districts,
    ) {}

    public function index(Request $request): View
    {
        $sort = $this->tableSort($request);
        $districtId = $request->integer('district_id') ?: null;
        $search = $request->string('q')->toString() ?: null;

        return $this->view('neighborhoods.index', [
            'neighborhoods' => $this->neighborhoods->paginated($districtId, $search, $sort['sort'], $sort['dir']),
            'districts' => AddressSort::byLabel(
                $this->districts->query()->where('is_active', true)->with('city:id,title')->get(['id', 'title', 'city_id']),
                fn (District $district) => ($district->city?->title ?? '').' / '.$district->title,
            ),
            'districtId' => $districtId,
            'search' => $request->string('q')->toString(),
            'sort' => $sort['sort'],
            'dir' => $sort['dir'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'district_id' => ['required', 'integer', 'exists:'.config('address.tables.districts').',id'],
            'title' => ['required', 'string', 'max:160'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:160'],
            'uavt_code' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $this->neighborhoods->create($data);

        return redirect()->route($this->routePrefix().'neighborhoods.index', ['district_id' => $data['district_id']])
            ->with('mca_addr_status', mca_addr('flash.neighborhood_created'));
    }

    public function update(Request $request, Neighborhood $neighborhood): RedirectResponse
    {
        $data = $request->validate([
            'district_id' => ['required', 'integer', 'exists:'.config('address.tables.districts').',id'],
            'title' => ['required', 'string', 'max:160'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:160'],
            'uavt_code' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $this->neighborhoods->update($neighborhood, $data);

        return redirect()->route($this->routePrefix().'neighborhoods.index', ['district_id' => $data['district_id']])
            ->with('mca_addr_status', mca_addr('flash.neighborhood_updated'));
    }

    public function destroy(Neighborhood $neighborhood): RedirectResponse
    {
        $districtId = $neighborhood->district_id;
        $this->neighborhoods->delete($neighborhood);

        return redirect()->route($this->routePrefix().'neighborhoods.index', ['district_id' => $districtId])
            ->with('mca_addr_status', mca_addr('flash.neighborhood_deleted'));
    }
}
