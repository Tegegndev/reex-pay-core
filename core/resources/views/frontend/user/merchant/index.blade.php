@extends('frontend.layouts.user.index')
@section('title', __('Merchants'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card single-form-card">
                {{-- Card Header --}}
                <div class="card-title mb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h6 class="text-white mb-2 mb-md-0 text-center text-md-start">{{ __('Merchants') }}</h6>
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-center justify-content-md-end">
                        <a class="btn btn-light-success btn-sm flex-fill flex-md-grow-0"
                           href="{{ route('user.transaction.index', ['type' => \App\Enums\TrxType::RECEIVE_PAYMENT]) }}">
                            <i class="fas fa-list me-1"></i> 
                            <span class="d-none d-sm-inline">{{ __('Payments') }}</span>
                            <span class="d-sm-none">{{ __('Pay') }}</span>
                        </a>
                        <a class="btn btn-light-primary btn-sm flex-fill flex-md-grow-0" href="{{ route('user.merchant.create') }}">
                            <i class="fas fa-university me-1"></i> 
                            <span class="d-none d-sm-inline">{{ __('Create') }}</span>
                            <span class="d-sm-none">{{ __('New') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body">
                    {{-- Merchants List --}}
                    <div class="transaction-list mt-4">
                        @forelse($merchants as $merchant)
                            <div class="transaction-item border rounded mb-3 overflow-hidden">
                                {{-- Mobile Layout (Stack) --}}
                                <div class="d-block d-lg-none">
                                    {{-- Header: Logo + Name + Status --}}
                                    <div class="d-flex align-items-center p-3 border-bottom bg-light">
                                        <div class="transaction-icon rounded me-3 flex-shrink-0">
                                            <img src="{{ asset($merchant->business_logo) }}" alt="Logo" class="img-fluid">
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-bold text-dark text-truncate">{{ $merchant->business_name }}</div>
                                            <div class="small text-muted text-truncate">{{ $merchant->site_url }}</div>
                                        </div>
                                        <div class="ms-2">
                                            <x-badge :status="$merchant->status"/>
                                        </div>
                                    </div>
                                    
                                    {{-- Body: Info --}}
                                    <div class="p-3">
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">{{ __('Currency') }}</small>
                                                <span class="fw-bold">{{ $merchant->currency->code }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">{{ __('Total Payments') }}</small>
                                                <span class="fw-bold">00 {{ $merchant->currency->symbol }}</span>
                                            </div>
                                        </div>
                                        
                                        {{-- Action Buttons --}}
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('user.merchant.qr-payment', $merchant->id) }}" 
                                               class="btn btn-outline-primary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                                <i class="fas fa-qrcode me-1"></i>
                                                <span class="small">{{ __('QR') }}</span>
                                            </a>
                                            <a href="{{ route('user.merchant.config', $merchant->id) }}" 
                                               class="btn btn-outline-info btn-sm flex-fill d-flex align-items-center justify-content-center">
                                                <i class="fas fa-key me-1"></i>
                                                <span class="small">{{ __('Config') }}</span>
                                            </a>
                                            <a href="{{ route('user.merchant.edit', $merchant->id) }}" 
                                               class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                                <i class="fas fa-edit me-1"></i>
                                                <span class="small">{{ __('Edit') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Desktop Layout (Horizontal) --}}
                                <div class="d-none d-lg-flex align-items-center justify-content-between p-3">
                                    {{-- Left: Icon + Merchant Info --}}
                                    <div class="d-flex align-items-center me-3">
                                        <div class="transaction-icon rounded me-3">
                                            <img src="{{ asset($merchant->business_logo) }}" alt="Logo">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $merchant->business_name }}</div>
                                            <div class="small text-uppercase text-muted fw-bold">
                                                {{ __('Currency : :currency', ['currency' => $merchant->currency->code]) }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $merchant->site_url }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Right: Payment Info + Buttons --}}
                                    <div class="text-end ms-auto">
                                        <div class="fw-bold mb-1">
                                            {{ __('Total Payments:') }} 00 {{ $merchant->currency->symbol }}
                                        </div>
                                        
                                        <x-badge :status="$merchant->status"/>
                                        
                                        <div class="d-flex gap-2 mt-2 justify-content-end">
                                            <a href="{{ route('user.merchant.qr-payment', $merchant->id) }}" class="btn-qr">
                                                <i class="fas fa-qrcode"></i> {{ __('QR Payment') }}
                                            </a>
                                            <a href="{{ route('user.merchant.config', $merchant->id) }}" class="btn-config">
                                                <i class="fas fa-key"></i> {{ __('Config') }}
                                            </a>
                                            <a href="{{ route('user.merchant.edit', $merchant->id) }}" class="btn-edit">
                                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-store fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted mb-2">{{ __('No merchants found') }}</h6>
                                <p class="small text-muted mb-3">{{ __('Create your first merchant to start accepting payments') }}</p>
                                <a href="{{ route('user.merchant.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> {{ __('Create Merchant') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection