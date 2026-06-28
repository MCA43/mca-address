<?php

namespace Mca\Address\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mca\Address\Http\Controllers\McaAddressController;
use Mca\Address\Models\City;
use Mca\Address\Models\District;
use Mca\Address\Services\CityService;
use Mca\Address\Services\DistrictService;
use Mca\Address\Support\AddressSort;

class DistrictController extends McaAddressController
{
    public function __construct(
        private readonly DistrictService $districts,
        private readonly CityService $cities,
    ) {}

    public function index(Request $request): View
    {
        $sort = $this->tableSort($request);
        $cityId = $request->integer('city_id') ?: null;
        $search = $request->string('q')->toString() ?: null;

        return $this->view('districts.index', [
            'districts' => $this->districts->paginated($cityId, $search, $sort['sort'], $sort['dir']),
            'cities' => AddressSort::byLabel(
                $this->cities->query()->where('is_active', true)->get(['id', 'title', 'code', 'country_id']),
                fn (City $city) => $city->title,
            ),
            'cityId' => $cityId,
            'search' => $request->string('q')->toString(),
            'sort' => $sort['sort'],
            'dir' => $sort['dir'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'city_id' => ['required', 'integer', 'exists:'.config('address.tables.cities').',id'],
            'title' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:20'],
            'slug' => ['nullable', 'string', 'max:120'],
            'uavt_code' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $this->districts->create($data);

        return redirect()->route($this->routePrefix().'districts.index', ['city_id' => $data['city_id']])
            ->with('mca_addr_status', mca_addr('flash.district_created'));
    }

    public function update(Request $request, District $district): RedirectResponse
    {
        $data = $request->validate([
            'city_id' => ['required', 'integer', 'exists:'.config('address.tables.cities').',id'],
            'title' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:20'],
            'slug' => ['nullable', 'string', 'max:120'],
            'uavt_code' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $this->districts->update($district, $data);

        return redirect()->route($this->routePrefix().'districts.index', ['city_id' => $data['city_id']])
            ->with('mca_addr_status', mca_addr('flash.district_updated'));
    }

    public function destroy(District $district): RedirectResponse
    {
        $cityId = $district->city_id;

        if (! $this->districts->delete($district)) {
            return back()->withErrors(['district' => mca_addr('errors.has_children')]);
        }

        return redirect()->route($this->routePrefix().'districts.index', ['city_id' => $cityId])
            ->with('mca_addr_status', mca_addr('flash.district_deleted'));
    }
}
