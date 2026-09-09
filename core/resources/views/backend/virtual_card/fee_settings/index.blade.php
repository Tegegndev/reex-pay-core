@extends('backend.virtual_card.index')
@section('title', __('Virtual Card Fee Settings'))

@section('virtual_card_header')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start">
            {{ __('Virtual Card Fee Settings') }}
        </div>
        <div class="float-end">
            <a href="#new_fee_setting_modal" data-coreui-toggle="modal" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> {{ __('Add New') }}
            </a>
        </div>
    </div>
@endsection

@section('virtual_card_content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table align-middle border mb-0">
            <thead class="table-light fw-semibold">
            <tr class="align-middle text-nowrap">
                <th>@lang('Provider') | @lang('Currency')</th>
                <th>@lang('Threshold') | @lang('Fee Amount')</th>
                <th>@lang('Min') | @lang('Max Amount')</th>
                <th>@lang('Status')</th>
                <th class="text-center">@lang('Actions')</th>
            </tr>
            </thead>
            <tbody>
            @forelse($feeSettings as $setting)
                @php
                    $op = $setting->operation;
                    $siteSymbol = siteCurrency('symbol');
                @endphp
                <tr class="align-middle">
                    <td>
                        <div>
                            {{ $setting->provider->name ?? '-' }}
                            <span class="badge badge-sm text-bg-secondary ">
                                 {{ $setting->currency->code ?? '-' }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <span class="badge text-white text-bg-{{ $op?->cssClass() }}">
                                 <i class="{{ $op?->icon() }}"></i>
                                {{ $op?->label() }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div>{{ $setting->approval_threshold ? $siteSymbol . number_format($setting->approval_threshold, 2) : '-' }}</div>
                        <div class="small text-muted">
                            @if($setting->fee_type->value === 'percent')
                            {{ number_format($setting->fee_amount, 2) }}%
                            @else
                                {{ $siteSymbol . number_format($setting->fee_amount, 2) }}
                            @endif
                        </div>
                    </td>
                    <td>
                        <div>{{ $siteSymbol . number_format($setting->min_amount, 2) }}</div>
                        <div class="small text-muted">{{ $setting->max_amount ? $siteSymbol . number_format($setting->max_amount, 2) : '-' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $setting->active ? 'success' : 'secondary' }} text-uppercase">
                            {{ $setting->active ? __('Active') : __('Inactive') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="javascript:void(0)" type="button" data-edit-url="{{ route('admin.virtual-card.fee-settings.edit', $setting) }}" class="btn btn-primary edit-modal">
                                <x-icon name="edit" height="20" width="20"/> {{ __('Edit') }}
                            </a>
                            <a href="javascript:void(0)" class="btn btn-danger delete text-white"
                               data-url="{{ route('admin.virtual-card.fee-settings.destroy', $setting) }}">
                                <x-icon name="delete-3" height="20" width="20"/> {{ __('Delete') }}
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <x-icon name="no-data-found" height="120"/>
                        <div class="mt-2">@lang('No fee settings found.')</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-end mt-3">
            {{ $feeSettings->links() }}
        </div>
    </div>
</div>
@include('backend.virtual_card.fee_settings.partials._new_fee_setting_modal')
@include('backend.virtual_card.fee_settings.partials._edit_fee_setting_modal')
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        editFormByModal('edit_fee_setting_modal', 'edit_fee_setting_data', true, true);
    });
</script>
@endpush
