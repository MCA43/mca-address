@extends('mca-address::layouts.app')

@section('title', mca_addr('cities.title').' — '.($mcaAddrTitle ?? mca_addr('app.title')))

@section('content')
    @php $np = config('address.routes.name_prefix'); @endphp

    <div class="mca-perm-toolbar">
        <div>
            <h1 class="mca-perm-toolbar__title">{{ mca_addr('cities.title') }}</h1>
            <p class="mca-perm-toolbar__subtitle">{{ mca_addr('cities.subtitle') }}</p>
        </div>
        <form method="GET" class="mca-perm-field mca-addr-filter-bar">
            @include('mca-address::partials.sort-fields')
            @include('mca-address::partials.filter-select', [
                'name' => 'country_id',
                'selected' => $countryId,
                'options' => $countries->map(fn ($country) => [
                    'value' => $country->id,
                    'label' => $country->title,
                ])->all(),
            ])
            <input type="search" name="q" value="{{ $search }}" class="mca-perm-input" placeholder="{{ mca_addr('common.search') }}">
        </form>
    </div>

    <div
        class="mca-addr-layout-split"
        data-mca-addr-crud-root
        data-store-url="{{ route($np.'cities.store') }}"
        data-i18n-new="{{ mca_addr('cities.new') }}"
        data-i18n-edit="{{ mca_addr('cities.edit_title') }}"
        data-i18n-hint="{{ mca_addr('cities.form_hint') }}"
        data-i18n-add="{{ mca_addr('common.create') }}"
        data-i18n-save="{{ mca_addr('common.save') }}"
        data-i18n-cancel="{{ mca_addr('cities.new') }}"
        data-i18n-editing="{{ mca_addr('common.editing_name', ['name' => '__NAME__']) }}"
    >
        <div class="mca-perm-card mca-perm-card__body mca-addr-crud-form-card" data-mca-addr-crud-form-card>
            <h2 class="mca-ui-card__title" data-mca-addr-crud-title>{{ mca_addr('cities.new') }}</h2>
            <p class="mca-perm-help" data-mca-addr-crud-hint style="margin-bottom:0.75rem;">{{ mca_addr('cities.form_hint') }}</p>
            <p class="mca-perm-mono mca-perm-form-editing-name" data-mca-addr-crud-editing hidden></p>

            <form method="POST" action="{{ route($np.'cities.store') }}" data-mca-addr-crud-form>
                @csrf
                <input type="hidden" name="_method" value="PUT" data-mca-addr-crud-method disabled>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('cities.country') }}</label>
                    <select name="country_id" class="mca-perm-input" required>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" @selected(old('country_id', $countryId) == $country->id)>{{ $country->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('common.name') }}</label>
                    <input name="title" class="mca-perm-input" required value="{{ old('title') }}">
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('cities.code') }}</label>
                    <input name="code" class="mca-perm-input mca-perm-mono" value="{{ old('code') }}">
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('cities.uavt') }}</label>
                    <input name="uavt_code" class="mca-perm-input mca-perm-mono" value="{{ old('uavt_code') }}">
                </div>
                <label class="mca-perm-checkbox-label">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked> {{ mca_addr('common.active') }}
                </label>
                <div class="mca-perm-form-actions">
                    <button type="submit" class="mca-perm-btn mca-perm-btn--primary mca-ui-btn--block" data-mca-addr-crud-submit>
                        @include('mca-address::partials.icon', ['name' => 'plus', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                        <span data-mca-addr-crud-submit-label>{{ mca_addr('common.create') }}</span>
                    </button>
                    <button type="button" class="mca-perm-btn mca-perm-btn--secondary mca-ui-btn--block" data-mca-addr-crud-cancel hidden>
                        {{ mca_addr('cities.new') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="mca-perm-card mca-perm-card__body mca-addr-table-wrap">
            <table class="mca-addr-table" data-mca-addr-crud-table>
                <thead>
                    <tr>
                        @include('mca-address::partials.sortable-th', ['column' => 'title', 'label' => 'Ad', 'sort' => $sort, 'dir' => $dir])
                        @include('mca-address::partials.sortable-th', ['column' => 'country', 'label' => mca_addr('cities.country'), 'sort' => $sort, 'dir' => $dir])
                        @include('mca-address::partials.sortable-th', ['column' => 'code', 'label' => mca_addr('cities.code'), 'sort' => $sort, 'dir' => $dir])
                        <th scope="col">{{ mca_addr('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cities as $city)
                        <tr>
                            <td><strong>{{ $city->title }}</strong></td>
                            <td>{{ $city->country?->title }}</td>
                            <td class="mca-perm-mono">{{ $city->code }}</td>
                            <td class="mca-addr-table__actions">
                                <div class="mca-ui-list-card__actions mca-ui-list-card__actions--tight">
                                    <button
                                        type="button"
                                        class="mca-perm-btn mca-perm-btn--secondary mca-perm-btn--icon"
                                        data-mca-addr-edit-row
                                        title="{{ mca_addr('common.edit') }}"
                                        aria-label="{{ mca_addr('common.edit') }}"
                                        data-update-url="{{ route($np.'cities.update', $city) }}"
                                        data-label="{{ $city->title }}"
                                        data-field-country_id="{{ $city->country_id }}"
                                        data-field-title="{{ $city->title }}"
                                        data-field-code="{{ $city->code }}"
                                        data-field-uavt_code="{{ $city->uavt_code }}"
                                        data-field-is_active="{{ $city->is_active ? '1' : '0' }}"
                                    >
                                        @include('mca-address::partials.icon', ['name' => 'pencil', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                    </button>
                                    <form method="POST" action="{{ route($np.'cities.destroy', $city) }}" data-mca-ui-confirm="{{ mca_addr('modal.delete_title') }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mca-perm-btn mca-perm-btn--danger mca-perm-btn--icon" title="{{ mca_addr('common.delete') }}" aria-label="{{ mca_addr('common.delete') }}">
                                            @include('mca-address::partials.icon', ['name' => 'trash', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                        </button>
                                    </form>
                                    <a href="{{ route($np.'districts.index', ['city_id' => $city->id]) }}" class="mca-perm-btn mca-perm-btn--secondary mca-perm-btn--icon" title="{{ mca_addr('nav.districts') }}" aria-label="{{ mca_addr('nav.districts') }}">
                                        @include('mca-address::partials.icon', ['name' => 'layers', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">—</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($cities->hasPages())
                {{ $cities->links('mca-address::partials.pagination') }}
            @endif
        </div>
    </div>
@endsection
