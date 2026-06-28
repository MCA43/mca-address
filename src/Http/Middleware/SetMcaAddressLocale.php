<?php

namespace Mca\Address\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mca\Address\Support\McaAddressLocale;
use Symfony\Component\HttpFoundation\Response;

class SetMcaAddressLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        McaAddressLocale::apply();

        return $next($request);
    }
}
