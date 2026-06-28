@php
    $name = $name ?? 'filter';
    $selected = $selected ?? null;
    $options = $options ?? [];
    $autoSubmit = $autoSubmit ?? true;
    $placeholder = $placeholder ?? mca_addr('common.all');
    $searchPlaceholder = $searchPlaceholder ?? mca_addr('common.filter_search');
    $selectedValue = $selected !== null && $selected !== '' ? (string) $selected : '';
    $selectedLabel = $placeholder;

    foreach ($options as $option) {
        if ((string) ($option['value'] ?? '') === $selectedValue) {
            $selectedLabel = (string) ($option['label'] ?? '');
            break;
        }
    }
@endphp

<div
    class="mca-addr-filter-select"
    data-mca-addr-filter-select
    @if ($autoSubmit) data-auto-submit @endif
>
    <select name="{{ $name }}" class="mca-addr-filter-select__native" tabindex="-1" aria-hidden="true">
        <option value="" @selected($selectedValue === '')>{{ $placeholder }}</option>
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}" @selected($selectedValue === (string) $option['value'])>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>

    <div class="mca-addr-filter-select__widget">
        <button
            type="button"
            class="mca-addr-filter-select__trigger mca-perm-input"
            aria-haspopup="listbox"
            aria-expanded="false"
        >
            <span class="mca-addr-filter-select__value">{{ $selectedLabel }}</span>
            <svg class="mca-addr-filter-select__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="mca-addr-filter-select__dropdown" hidden>
            <input
                type="search"
                class="mca-addr-filter-select__search mca-perm-input"
                placeholder="{{ $searchPlaceholder }}"
                autocomplete="off"
                spellcheck="false"
            >
            <ul class="mca-addr-filter-select__list" role="listbox" tabindex="-1"></ul>
        </div>
    </div>
</div>
