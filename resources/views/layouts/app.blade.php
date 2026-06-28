<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $mcaAddrTitle ?? mca_addr('app.title'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ \Mca\Address\Support\McaAddressView::uiCssUrl() }}">
    <link rel="stylesheet" href="{{ \Mca\Address\Support\McaAddressView::permCssUrl() }}">
    <link rel="stylesheet" href="{{ \Mca\Address\Support\McaAddressView::cssUrl() }}">
</head>
<body class="mca-ui-root mca-perm-root mca-addr-root">
    @include('mca-address::partials.header')

    <main class="mca-ui-main mca-perm-main mca-addr-main">
        @include('mca-address::partials.flash')
        @yield('content')
    </main>

    @php
        $mcaUiI18n = [
            'ok' => mca_addr('modal.ok'),
            'confirm' => mca_addr('modal.confirm'),
            'cancel' => mca_addr('modal.cancel'),
            'close' => mca_addr('modal.close'),
            'alert_title' => mca_addr('modal.alert_title'),
            'confirm_title' => mca_addr('modal.confirm_title'),
            'delete_title' => mca_addr('modal.delete_title'),
        ];
        $mcaAddrI18n = [
            'no_results' => mca_addr('common.no_results'),
        ];
    @endphp
    <script>
        window.McaUiI18n = @json($mcaUiI18n);
        window.McaAddrI18n = @json($mcaAddrI18n);
    </script>
    <script src="{{ \Mca\Address\Support\McaAddressView::uiJsUrl() }}" defer></script>
    <script src="{{ \Mca\Address\Support\McaAddressView::jsUrl() }}" defer></script>
</body>
</html>
