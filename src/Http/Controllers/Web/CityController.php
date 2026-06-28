<?php

namespace Mca\Address\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mca\Address\Http\Controllers\McaAddressController;
use Mca\Address\Models\City;
use Mca\Address\Models\Country;
use Mca\Address\Services\CityService;
use Mca\Address\Services\CountryService;
use Mca\Address\Support\AddressSort;

class CityController extends McaAddressController
{
    public function __construct(
        private readonly CityService $cities,
        private readonly CountryService $countries,
    ) {}

    public function index(Request $request): View
    {
        $sort = $this->tableSort($request);
        $countryId = $request->integer('country_id') ?: null;
        $search = $request->string('q')->toString() ?: null;

        return $this->view('cities.index', [
            'cities' => $this->cities->paginated($countryId, $search, $sort['sort'], $sort['dir']),
            'countries' => AddressSort::byLabel(
                $this->countries->query()->where('is_active', true)->get(['id', 'title', 'iso_code_2']),
                fn (Country $country) => $country->title,
            ),
            'countryId' => $countryId,
            'search' => $request->string('q')->toString(),
            'sort' => $sort['sort'],
            'dir' => $sort['dir'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'country_id' => ['required', 'integer', 'exists:'.config('address.tables.countries').',id'],
            'title' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:120'],
            'uavt_code' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $this->cities->create($data);

        return redirect()->route($this->routePrefix().'cities.index', ['country_id' => $data['country_id']])
            ->with('mca_addr_status', mca_addr('flash.city_created'));
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $data = $request->validate([
            'country_id' => ['required', 'integer', 'exists:'.config('address.tables.countries').',id'],
            'title' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:120'],
            'uavt_code' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $this->cities->update($city, $data);

        return redirect()->route($this->routePrefix().'cities.index', ['country_id' => $data['country_id']])
            ->with('mca_addr_status', mca_addr('flash.city_updated'));
    }

    public function destroy(City $city): RedirectResponse
    {
        $countryId = $city->country_id;

        if (! $this->cities->delete($city)) {
            return back()->withErrors(['city' => mca_addr('errors.has_children')]);
        }

        return redirect()->route($this->routePrefix().'cities.index', ['country_id' => $countryId])
            ->with('mca_addr_status', mca_addr('flash.city_deleted'));
    }
}
