@php
    $np = config('address.routes.name_prefix', 'mca.address.');
    $nav = [
        ['route' => $np.'countries.index', 'icon' => 'globe', 'label' => mca_addr('nav.countries'), 'active' => request()->routeIs($np.'countries.*')],
        ['route' => $np.'cities.index', 'icon' => 'map-pin', 'label' => mca_addr('nav.cities'), 'active' => request()->routeIs($np.'cities.*')],
        ['route' => $np.'districts.index', 'icon' => 'layers', 'label' => mca_addr('nav.districts'), 'active' => request()->routeIs($np.'districts.*')],
        ['route' => $np.'neighborhoods.index', 'icon' => 'home', 'label' => mca_addr('nav.neighborhoods'), 'active' => request()->routeIs($np.'neighborhoods.*')],
        ['route' => $np.'settings.index', 'icon' => 'cog', 'label' => mca_addr('nav.settings'), 'active' => request()->routeIs($np.'settings.*')],
    ];
@endphp
<header class="mca-ui-shell" id="mcaUiShell">
    <div class="mca-ui-shell__wrap">
        <div class="mca-ui-shell__inner">
            <a href="{{ route($np.'countries.index') }}" class="mca-ui-brand">
                <span class="mca-ui-brand__mark" aria-hidden="true">
                    @include('mca-address::partials.icon', ['name' => 'map-pin'])
                </span>
                <span>{{ $mcaAddrTitle ?? mca_addr('app.brand') }}</span>
            </a>

            <button type="button"
                    class="mca-ui-menu-btn"
                    id="mcaUiMenuBtn"
                    aria-expanded="false"
                    aria-controls="mcaUiNav"
                    aria-label="{{ mca_addr('app.nav_aria') }}">
                @include('mca-address::partials.icon', ['name' => 'menu'])
            </button>
        </div>

        <nav class="mca-ui-nav" id="mcaUiNav" aria-label="{{ mca_addr('app.nav_aria') }}">
            @if(Route::has('mca.hub.index'))
                <a href="{{ route('mca.hub.index') }}" class="mca-ui-nav__link">
                    @include('mca-address::partials.icon', ['name' => 'grid', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                    {{ mca_addr('nav.hub') }}
                </a>
            @endif

            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="mca-ui-nav__link {{ $item['active'] ? 'mca-ui-nav__link--active' : '' }}">
                    @include('mca-address::partials.icon', ['name' => $item['icon'], 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
