@extends('backend.deposit.index')
@section('title', $type->label().' '. __('Methods'))
@section('deposit_header')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-3">
            <div class="mb-3 mb-lg-0">
                <h1 class="h3 fw-bold mb-1">
                    <x-icon name="deposit" height="28" width="28" class="me-1 text-primary align-middle"/>
                    {{ __(':type Deposit Methods', ['type' => $type->label()]) }}
                </h1>
                <div class="text-muted small lh-sm">
                    <span class="d-block">{{ __('Easily manage, create, or update all :type deposit methods for your platform from this page.', ['type' => $type->label()]) }}</span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @can('deposit-method-manage')
                    @if($type->isAutomatic())
                        <a href="{{ route('admin.payment.gateway.index') }}" class="btn btn-outline-dark d-flex align-items-center px-4 shadow-sm">
                            <x-icon name="payment" height="20" width="20" class="me-2"/> <span>{{ __('Payment Gateways') }}</span>
                        </a>
                    @endif
                    <a href="#new_payment_method_modal" data-coreui-toggle="modal" class="btn btn-primary d-flex align-items-center px-4 shadow-sm">
                        <x-icon name="add" height="20" width="20" class="me-2"/> <span class="fw-semibold">{{ __('Create').' '. $type->label().' ' . __('Method') }}</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endSection

@section('deposit_content')
    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive rounded">
                <table class="table mb-0 caption-top">
                    <thead>
                    <tr>
                        <th>{{ __('Logo') }}</th>
                        <th>{{ __('Name|Cu                //
rrency') }}</th>
                        <th>{{ __('Min|Max Deposit') }}</th>
                        <th>{{ __('Rate Type|Rate') }}</th>
                        <th>{{ __('Charges') }}</th>
                        <th>{{ __('Status') }}</th>
                        @can('deposit-method-manage')
                            <th>{{ __('Action') }}</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($paymentMethods as $paymentMethod)
                        <tr class="align-middle">
                            <td>
                                <div class="position-relative me-3">
                                    <img src="{{ asset($paymentMethod->logo_alt) }}" height="20" alt="">
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap"><strong
                                            class="text-muted">{{ title($paymentMethod->name) }}</strong></div>
                                <div class="small text-body-secondary text-nowrap">
                                    <span class="badge text-bg-secondary">
                                        {{ $paymentMethod->currency }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap"><strong
                                            class="text-muted"> {{ $paymentMethod->currency_symbol }}{{ $paymentMethod->min_deposit }} </strong>
                                </div>
                                <div class="small text-body-secondary text-nowrap"><strong
                                            class="text-muted"> {{ $paymentMethod->currency_symbol }}{{ $paymentMethod->max_deposit }} </strong>
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    @if($paymentMethod->conversion_rate_live)
                                        <strong class="text-danger">{{ __('LIVE') }}</strong>
                                    @else
                                        <strong class="text-primary">{{ __('LOCAL') }}</strong>
                                    @endif
                                </div>
                                <div class="small text-body-secondary text-nowrap ">
                                    <strong>1 {{ siteCurrency() }}
                                        = {{ $paymentMethod->conversion_rate }} {{ $paymentMethod->currency}}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    <!-- User Charge -->
                                    <div class="mb-1">
                                        <small class="text-primary fw-semibold">
                                            <i class="fas fa-user me-1"></i>User:
                                        </small>
                                        <strong class="text-muted">
                                            @if($paymentMethod->user_charge_type && $paymentMethod->user_charge_type->isPercent())
                                                {{ $paymentMethod->user_charge ?? 0 }}%
                                            @else
                                                {{ $paymentMethod->currency_symbol }}{{ $paymentMethod->user_charge ?? 0 }}
                                            @endif
                                        </strong>
                                    </div>
                                    <!-- Merchant Charge -->
                                    <div>
                                        <small class="text-success fw-semibold">
                                            <i class="fas fa-store me-1"></i>Merchant:
                                        </small>
                                        <strong class="text-muted">
                                            @if($paymentMethod->merchant_charge_type && $paymentMethod->merchant_charge_type->isPercent())
                                                {{ $paymentMethod->merchant_charge ?? 0 }}%
                                            @else
                                                {{ $paymentMethod->currency_symbol }}{{ $paymentMethod->merchant_charge ?? 0 }}
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap text-uppercase">
                                <span class="badge {{ $paymentMethod->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $paymentMethod->status ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            @can('deposit-method-manage')
                                <td>
                                    @if( (isset($paymentMethod->paymentGateway) && $paymentMethod->paymentGateway->status) || $paymentMethod->type == \App\Enums\MethodType::MANUAL )
                                        <button type="button"
                                                data-edit-url="{{ route('admin.deposit.method.edit', $paymentMethod->id) }}"
                                                class="btn btn-primary d-flex align-items-center edit-modal">
                                            <x-icon name="manage" height="20"/>
                                            {{ __('Manage') }}
                                        </button>
                                    @else
                                        <button disabled class="btn  btn-warning">
                                            <i class="fa-solid fa-lock me-1"></i> {{ __('Disabled by Gateway') }}
                                        </button>
                                    @endif
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    {{	$paymentMethods->links() }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @can('deposit-method-manage')
        @include('backend.deposit.method.partials._new_payment_method_modal')    
        @include('backend.deposit.method.partials._edit_payment_method_modal')    
        @php
        $fields = $paymentMethod->fields;
        $fieldCount = $fields ? count($fields) : 0;
    @endphp
    @endcan
@endsection

@push('scripts')
    @can('deposit-method-manage') 
        @include('backend.deposit.method.partials._scripts')
    @endcan
@endpush