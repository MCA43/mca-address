<?php

namespace Mca\Address\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mca\Address\Http\Controllers\McaAddressController;
use Mca\Address\Models\Country;
use Mca\Address\Services\CountryService;

class CountryController extends McaAddressController
{
    public function __construct(
        private readonly CountryService $countries,
    ) {}

    public function index(Request $request): View
    {
        $sort = $this->tableSort($request);
        $search = $request->string('q')->toString() ?: null;

        return $this->view('countries.index', [
            'countries' => $this->countries->paginated($search, $sort['sort'], $sort['dir']),
            'search' => $request->string('q')->toString(),
            'sort' => $sort['sort'],
            'dir' => $sort['dir'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'iso_code_2' => ['required', 'string', 'size:2', 'unique:'.config('address.tables.countries').',iso_code_2'],
            'iso_code_3' => ['required', 'string', 'size:3', 'unique:'.config('address.tables.countries').',iso_code_3'],
            'slug' => ['nullable', 'string', 'max:120'],
            'postcode_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['postcode_required'] = $request->boolean('postcode_required');
        $data['is_active'] = $request->boolean('is_active', true);

        $this->countries->create($data);

        return redirect()->route($this->routePrefix().'countries.index')
            ->with('mca_addr_status', mca_addr('flash.country_created'));
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'iso_code_2' => ['required', 'string', 'size:2', 'unique:'.config('address.tables.countries').',iso_code_2,'.$country->id],
            'iso_code_3' => ['required', 'string', 'size:3', 'unique:'.config('address.tables.countries').',iso_code_3,'.$country->id],
            'slug' => ['nullable', 'string', 'max:120'],
            'postcode_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['postcode_required'] = $request->boolean('postcode_required');
        $data['is_active'] = $request->boolean('is_active');

        $this->countries->update($country, $data);

        return redirect()->route($this->routePrefix().'countries.index')
            ->with('mca_addr_status', mca_addr('flash.country_updated'));
    }

    public function destroy(Country $country): RedirectResponse
    {
        if (! $this->countries->delete($country)) {
            return back()->withErrors(['country' => mca_addr('errors.has_children')]);
        }

        return redirect()->route($this->routePrefix().'countries.index')
            ->with('mca_addr_status', mca_addr('flash.country_deleted'));
    }
}
