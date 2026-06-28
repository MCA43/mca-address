@extends('mca-address::layouts.app')

@section('title', mca_addr('districts.title').' — '.($mcaAddrTitle ?? mca_addr('app.title')))

@section('content')
    @php $np = config('address.routes.name_prefix'); @endphp

    <div class="mca-perm-toolbar">
        <div>
            <h1 class="mca-perm-toolbar__title">{{ mca_addr('districts.title') }}</h1>
            <p class="mca-perm-toolbar__subtitle">{{ mca_addr('districts.subtitle') }}</p>
        </div>
        <form method="GET" class="mca-perm-field mca-addr-filter-bar">
            @include('mca-address::partials.sort-fields')
            @include('mca-address::partials.filter-select', [
                'name' => 'city_id',
                'selected' => $cityId,
                'options' => $cities->map(fn ($city) => [
                    'value' => $city->id,
                    'label' => $city->title.($city->code ? ' ('.$city->code.')' : ''),
                ])->all(),
            ])
            <input type="search" name="q" value="{{ $search }}" class="mca-perm-input" placeholder="{{ mca_addr('common.search') }}">
        </form>
    </div>

    <div
        class="mca-addr-layout-split"
        data-mca-addr-crud-root
        data-store-url="{{ route($np.'districts.store') }}"
        data-i18n-new="{{ mca_addr('districts.new') }}"
        data-i18n-edit="{{ mca_addr('districts.edit_title') }}"
        data-i18n-hint="{{ mca_addr('districts.form_hint') }}"
        data-i18n-add="{{ mca_addr('common.create') }}"
        data-i18n-save="{{ mca_addr('common.save') }}"
        data-i18n-cancel="{{ mca_addr('districts.new') }}"
        data-i18n-editing="{{ mca_addr('common.editing_name', ['name' => '__NAME__']) }}"
    >
        <div class="mca-perm-card mca-perm-card__body mca-addr-crud-form-card" data-mca-addr-crud-form-card>
            <h2 class="mca-ui-card__title" data-mca-addr-crud-title>{{ mca_addr('districts.new') }}</h2>
            <p class="mca-perm-help" data-mca-addr-crud-hint style="margin-bottom:0.75rem;">{{ mca_addr('districts.form_hint') }}</p>
            <p class="mca-perm-mono mca-perm-form-editing-name" data-mca-addr-crud-editing hidden></p>

            <form method="POST" action="{{ route($np.'districts.store') }}" data-mca-addr-crud-form>
                @csrf
                <input type="hidden" name="_method" value="PUT" data-mca-addr-crud-method disabled>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('districts.city') }}</label>
                    <select name="city_id" class="mca-perm-input" required>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @selected(old('city_id', $cityId) == $city->id)>{{ $city->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('common.name') }}</label>
                    <input name="title" class="mca-perm-input" required placeholder="İlçe adı" value="{{ old('title') }}">
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('districts.uavt') }}</label>
                    <input name="uavt_code" class="mca-perm-input" placeholder="{{ mca_addr('districts.uavt') }}" value="{{ old('uavt_code') }}">
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
                        {{ mca_addr('districts.new') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="mca-perm-card mca-perm-card__body mca-addr-table-wrap">
            <table class="mca-addr-table" data-mca-addr-crud-table>
                <thead>
                    <tr>
                        @include('mca-address::partials.sortable-th', ['column' => 'title', 'label' => 'Ad', 'sort' => $sort, 'dir' => $dir])
                        @include('mca-address::partials.sortable-th', ['column' => 'city', 'label' => mca_addr('districts.city'), 'sort' => $sort, 'dir' => $dir])
                        <th scope="col">{{ mca_addr('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($districts as $district)
                        <tr>
                            <td><strong>{{ $district->title }}</strong></td>
                            <td>{{ $district->city?->title }}</td>
                            <td class="mca-addr-table__actions">
                                <div class="mca-ui-list-card__actions mca-ui-list-card__actions--tight">
                                    <button
                                        type="button"
                                        class="mca-perm-btn mca-perm-btn--secondary mca-perm-btn--icon"
                                        data-mca-addr-edit-row
                                        title="{{ mca_addr('common.edit') }}"
                                        aria-label="{{ mca_addr('common.edit') }}"
                                        data-update-url="{{ route($np.'districts.update', $district) }}"
                                        data-label="{{ $district->title }}"
                                        data-field-city_id="{{ $district->city_id }}"
                                        data-field-title="{{ $district->title }}"
                                        data-field-uavt_code="{{ $district->uavt_code }}"
                                        data-field-is_active="{{ $district->is_active ? '1' : '0' }}"
                                    >
                                        @include('mca-address::partials.icon', ['name' => 'pencil', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                    </button>
                                    <form method="POST" action="{{ route($np.'districts.destroy', $district) }}" data-mca-ui-confirm="{{ mca_addr('modal.delete_title') }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mca-perm-btn mca-perm-btn--danger mca-perm-btn--icon" title="{{ mca_addr('common.delete') }}" aria-label="{{ mca_addr('common.delete') }}">
                                            @include('mca-address::partials.icon', ['name' => 'trash', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                        </button>
                                    </form>
                                    <a href="{{ route($np.'neighborhoods.index', ['district_id' => $district->id]) }}" class="mca-perm-btn mca-perm-btn--secondary mca-perm-btn--icon" title="{{ mca_addr('nav.neighborhoods') }}" aria-label="{{ mca_addr('nav.neighborhoods') }}">
                                        @include('mca-address::partials.icon', ['name' => 'home', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">—</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($districts->hasPages())
                {{ $districts->links('mca-address::partials.pagination') }}
            @endif
        </div>
    </div>
@endsection
