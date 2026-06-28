<?php

namespace Mca\Address\Support;

use Illuminate\Contracts\View\View;

final class McaAddressView
{
    public static function layout(): string
    {
        return (string) config('address.views.layout', 'mca-address::layouts.app');
    }

    public static function render(string $view, array $data = []): View
    {
        McaAddressLocale::apply();

        $namespace = config('address.views.namespace', 'mca-address');

        return view($namespace.'::'.$view, array_merge([
            'mcaAddrTitle' => config('address.ui.title') ?: mca_addr('app.title'),
        ], $data));
    }

    public static function uiCssUrl(): string
    {
        return asset((string) config('address.ui.assets.ui', 'vendor/mca-permission/mca-ui.css'));
    }

    public static function uiJsUrl(): string
    {
        return asset((string) config('address.ui.assets.ui_js', 'vendor/mca-permission/mca-ui.js'));
    }

    public static function permCssUrl(): string
    {
        return asset((string) config('address.ui.assets.perm', 'vendor/mca-permission/mca-permission.css'));
    }

    public static function cssUrl(): string
    {
        return asset((string) config('address.ui.assets.css', 'vendor/mca-address/mca-address.css'));
    }

    public static function jsUrl(): string
    {
        return asset((string) config('address.ui.assets.js', 'vendor/mca-address/mca-address.js'));
    }
}
