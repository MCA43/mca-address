<?php

namespace Mca\Address\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mca\Address\Services\CityService;
use Mca\Address\Services\CountryService;
use Mca\Address\Services\DistrictService;
use Mca\Address\Services\NeighborhoodService;

class AddressApiController extends Controller
{
    public function __construct(
        private readonly CountryService $countries,
        private readonly CityService $cities,
        private readonly DistrictService $districts,
        private readonly NeighborhoodService $neighborhoods,
    ) {}

    public function countries(): JsonResponse
    {
        return response()->json(['data' => $this->countries->options()]);
    }

    public function cities(Request $request): JsonResponse
    {
        $countryId = $request->integer('country_id') ?: mca_address_default_country_id();

        return response()->json(['data' => $this->cities->options($countryId)]);
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json([
            'data' => $this->districts->options($request->integer('city_id')),
        ]);
    }

    public function neighborhoods(Request $request): JsonResponse
    {
        $request->validate([
            'district_id' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json([
            'data' => $this->neighborhoods->options($request->integer('district_id')),
        ]);
    }
}
