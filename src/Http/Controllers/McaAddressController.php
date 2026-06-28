<?php

namespace Mca\Address\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mca\Address\Support\AddressTableSort;
use Mca\Address\Support\McaAddressView;

abstract class McaAddressController extends Controller
{
    protected function view(string $name, array $data = [])
    {
        return McaAddressView::render($name, $data);
    }

    protected function routePrefix(): string
    {
        return (string) config('address.routes.name_prefix', 'mca.address.');
    }

    /** @return array{sort: ?string, dir: string} */
    protected function tableSort(Request $request): array
    {
        return AddressTableSort::fromRequest($request);
    }
}
