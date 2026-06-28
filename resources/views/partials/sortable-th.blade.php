@props([
    'column',
    'label',
    'sort' => null,
    'dir' => 'asc',
])

@php
    $active = ($sort ?? null) === $column;
    $currentDir = $dir ?? 'asc';
    $nextDir = $active && $currentDir === 'asc' ? 'desc' : 'asc';
    $params = array_merge(request()->except(['page', 'sort', 'dir']), [
        'sort' => $column,
        'dir' => $nextDir,
    ]);
    $ariaLabel = $active
        ? ($nextDir === 'desc' ? mca_addr('common.sort_desc', ['column' => $label]) : mca_addr('common.sort_asc', ['column' => $label]))
        : mca_addr('common.sort_by', ['column' => $label]);
@endphp

<th
    scope="col"
    @class([
        'mca-addr-table__sort',
        'is-active' => $active,
        'is-asc' => $active && $currentDir === 'asc',
        'is-desc' => $active && $currentDir === 'desc',
    ])
>
    <a href="{{ request()->url().'?'.http_build_query($params) }}" class="mca-addr-table__sort-link" aria-label="{{ $ariaLabel }}">
        <span>{{ $label }}</span>
        <svg class="mca-addr-table__sort-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 4.5 6 9h8L10 4.5zm0 11L14 11H6l4 4.5z"/>
        </svg>
    </a>
</th>
