@extends('mca-address::layouts.app')

@section('title', mca_addr('settings.title').' — '.($mcaAddrTitle ?? mca_addr('app.title')))

@section('content')
    <div class="mca-perm-toolbar">
        <div>
            <h1 class="mca-perm-toolbar__title">{{ mca_addr('settings.title') }}</h1>
            <p class="mca-perm-toolbar__subtitle">{{ mca_addr('settings.subtitle') }}</p>
        </div>
    </div>

    <div class="mca-perm-card mca-perm-card__body" style="max-width:40rem;">
        <dl class="mca-addr-settings">
            <dt>{{ mca_addr('settings.enabled') }}</dt>
            <dd>{{ ($uavt['enabled'] ?? false) ? mca_addr('common.yes') : mca_addr('common.no') }}</dd>
            <dt>{{ mca_addr('settings.driver') }}</dt>
            <dd class="mca-perm-mono">{{ $uavt['driver'] ?? 'none' }}</dd>
            <dt>{{ mca_addr('settings.api_url') }}</dt>
            <dd class="mca-perm-mono">{{ $uavt['api_url'] ?: '—' }}</dd>
            <dt>{{ mca_addr('settings.fallback') }}</dt>
            <dd>{{ ($uavt['fallback_local'] ?? true) ? mca_addr('common.yes') : mca_addr('common.no') }}</dd>
        </dl>
        <p class="mca-perm-help" style="margin-top:1rem;">{{ mca_addr('settings.env_hint') }}</p>
        <pre class="mca-perm-mono" style="margin-top:0.75rem;padding:0.75rem;background:#f8fafc;border-radius:0.5rem;font-size:0.8rem;">MCA_ADDRESS_UAVT_ENABLED=false
MCA_ADDRESS_UAVT_DRIVER=none
MCA_ADDRESS_UAVT_API_URL=
MCA_ADDRESS_UAVT_API_KEY=
MCA_ADDRESS_UAVT_FALLBACK_LOCAL=true</pre>
    </div>
@endsection
