<?php

namespace Mca\Address\Http\Controllers\Web;

use Illuminate\View\View;
use Mca\Address\Http\Controllers\McaAddressController;

class UavtSettingsController extends McaAddressController
{
    public function index(): View
    {
        return $this->view('settings.index', [
            'uavt' => config('address.uavt', []),
        ]);
    }
}
