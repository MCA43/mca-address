@extends('mca-address::layouts.app')

@section('title', mca_addr('countries.title').' — '.($mcaAddrTitle ?? mca_addr('app.title')))

@section('content')
    @php $np = config('address.routes.name_prefix'); @endphp

    <div class="mca-perm-toolbar">
        <div>
            <h1 class="mca-perm-toolbar__title">{{ mca_addr('countries.title') }}</h1>
            <p class="mca-perm-toolbar__subtitle">{{ mca_addr('countries.subtitle') }}</p>
        </div>
        <form method="GET" class="mca-addr-filter-bar">
            @include('mca-address::partials.sort-fields')
            <input type="search" name="q" value="{{ $search }}" class="mca-perm-input" placeholder="{{ mca_addr('common.search') }}">
        </form>
    </div>

    <div
        class="mca-addr-layout-split"
        data-mca-addr-crud-root
        data-store-url="{{ route($np.'countries.store') }}"
        data-i18n-new="{{ mca_addr('countries.new') }}"
        data-i18n-edit="{{ mca_addr('countries.edit_title') }}"
        data-i18n-hint="{{ mca_addr('countries.form_hint') }}"
        data-i18n-add="{{ mca_addr('common.create') }}"
        data-i18n-save="{{ mca_addr('common.save') }}"
        data-i18n-cancel="{{ mca_addr('countries.new') }}"
        data-i18n-editing="{{ mca_addr('common.editing_name', ['name' => '__NAME__']) }}"
    >
        <div class="mca-perm-card mca-perm-card__body mca-addr-crud-form-card" data-mca-addr-crud-form-card>
            <h2 class="mca-ui-card__title" data-mca-addr-crud-title>{{ mca_addr('countries.new') }}</h2>
            <p class="mca-perm-help" data-mca-addr-crud-hint style="margin-bottom:0.75rem;">{{ mca_addr('countries.form_hint') }}</p>
            <p class="mca-perm-mono mca-perm-form-editing-name" data-mca-addr-crud-editing hidden></p>

            <form method="POST" action="{{ route($np.'countries.store') }}" data-mca-addr-crud-form>
                @csrf
                <input type="hidden" name="_method" value="PUT" data-mca-addr-crud-method disabled>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('common.name') }} *</label>
                    <input name="title" class="mca-perm-input" required value="{{ old('title') }}" placeholder="Türkiye">
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('countries.iso2') }}</label>
                    <input name="iso_code_2" class="mca-perm-input mca-perm-mono" maxlength="2" required value="{{ old('iso_code_2') }}">
                </div>
                <div class="mca-perm-field">
                    <label class="mca-perm-label">{{ mca_addr('countries.iso3') }}</label>
                    <input name="iso_code_3" class="mca-perm-input mca-perm-mono" maxlength="3" required value="{{ old('iso_code_3') }}">
                </div>
                <label class="mca-perm-checkbox-label">
                    <input type="hidden" name="postcode_required" value="0">
                    <input type="checkbox" name="postcode_required" value="1" @checked(old('postcode_required'))> {{ mca_addr('countries.postcode') }}
                </label>
                <label class="mca-perm-checkbox-label" style="display:block;margin-top:0.5rem;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked> {{ mca_addr('common.active') }}
                </label>
                <div class="mca-perm-form-actions">
                    <button type="submit" class="mca-perm-btn mca-perm-btn--primary mca-ui-btn--block" data-mca-addr-crud-submit>
                        @include('mca-address::partials.icon', ['name' => 'plus', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                        <span data-mca-addr-crud-submit-label>{{ mca_addr('common.create') }}</span>
                    </button>
                    <button type="button" class="mca-perm-btn mca-perm-btn--secondary mca-ui-btn--block" data-mca-addr-crud-cancel hidden>
                        {{ mca_addr('countries.new') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="mca-perm-card mca-perm-card__body mca-addr-table-wrap">
            <table class="mca-addr-table" data-mca-addr-crud-table>
                <thead>
                    <tr>
                        @include('mca-address::partials.sortable-th', ['column' => 'title', 'label' => mca_addr('countries.title'), 'sort' => $sort, 'dir' => $dir])
                        @include('mca-address::partials.sortable-th', ['column' => 'iso_code_2', 'label' => mca_addr('countries.iso2'), 'sort' => $sort, 'dir' => $dir])
                        @include('mca-address::partials.sortable-th', ['column' => 'iso_code_3', 'label' => mca_addr('countries.iso3'), 'sort' => $sort, 'dir' => $dir])
                        @include('mca-address::partials.sortable-th', ['column' => 'is_active', 'label' => mca_addr('common.active'), 'sort' => $sort, 'dir' => $dir])
                        <th scope="col">{{ mca_addr('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($countries as $country)
                        <tr>
                            <td><strong>{{ $country->title }}</strong></td>
                            <td class="mca-perm-mono">{{ $country->iso_code_2 }}</td>
                            <td class="mca-perm-mono">{{ $country->iso_code_3 }}</td>
                            <td>{{ $country->is_active ? mca_addr('common.yes') : mca_addr('common.no') }}</td>
                            <td class="mca-addr-table__actions">
                                <div class="mca-ui-list-card__actions mca-ui-list-card__actions--tight">
                                    <button
                                        type="button"
                                        class="mca-perm-btn mca-perm-btn--secondary mca-perm-btn--icon"
                                        data-mca-addr-edit-row
                                        title="{{ mca_addr('common.edit') }}"
                                        aria-label="{{ mca_addr('common.edit') }}"
                                        data-update-url="{{ route($np.'countries.update', $country) }}"
                                        data-label="{{ $country->title }}"
                                        data-field-title="{{ $country->title }}"
                                        data-field-iso_code_2="{{ $country->iso_code_2 }}"
                                        data-field-iso_code_3="{{ $country->iso_code_3 }}"
                                        data-field-postcode_required="{{ $country->postcode_required ? '1' : '0' }}"
                                        data-field-is_active="{{ $country->is_active ? '1' : '0' }}"
                                    >
                                        @include('mca-address::partials.icon', ['name' => 'pencil', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                    </button>
                                    <form method="POST" action="{{ route($np.'countries.destroy', $country) }}" data-mca-ui-confirm="{{ mca_addr('modal.delete_title') }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mca-perm-btn mca-perm-btn--danger mca-perm-btn--icon" title="{{ mca_addr('common.delete') }}" aria-label="{{ mca_addr('common.delete') }}">
                                            @include('mca-address::partials.icon', ['name' => 'trash', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                        </button>
                                    </form>
                                    <a href="{{ route($np.'cities.index', ['country_id' => $country->id]) }}" class="mca-perm-btn mca-perm-btn--secondary mca-perm-btn--icon" title="{{ mca_addr('nav.cities') }}" aria-label="{{ mca_addr('nav.cities') }}">
                                        @include('mca-address::partials.icon', ['name' => 'map-pin', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">{{ mca_addr('common.all') }} —</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($countries->hasPages())
                {{ $countries->links('mca-address::partials.pagination') }}
            @endif
        </div>
    </div>
@endsection
