@extends('frontend.layouts.user.index')
@section('title', __('My QR Codes'))
@section('content')
    <div class="single-form-card">
        {{-- Header --}}
        <div class="card-title mb-0 d-flex flex-column flex-md-row justify-content-between">
            <h6 class="text-white mb-2 mb-md-0">{{ __('Generated QR Codes') }}</h6>
            <div class="d-flex gap-2 flex-row">
                <a class="btn btn-light-primary btn-sm" href="{{ route('user.merchant.index') }}"><i class="fas fa-shop"></i>{{ __('Merchants Manage') }}</a>
            </div>
        </div>
        
        {{-- Cards Grid --}}
        <div class="card-main p-3">
            <div class="row g-4">
                @forelse($qrTransactions as $transaction)
                    @php
                        $status = $transaction->status;
						if ($status instanceof \App\Enums\TrxStatus) {
						  $label = $status->label();
						  $color = $status->color();
						  $icon  = $status->icon();
						} else {
						  $label = __(ucfirst($status));
						  $color = $status==='completed'?'success':($status==='pending'?'warning text-dark':'danger');
						  $icon  = $status==='completed'?'fa-solid fa-check':($status==='pending'?'fa-regular fa-clock':'fa-solid fa-circle-exclamation');
						}
                    @endphp
                    <div class="col-12 col-md-6 col-lg-6 col-xl-4 col-xxl-4 d-flex">
                        <div class="qr-card">
                            <div class="qr-card-main-row">
                                <div class="qr-preview-wrapper" id="qrPrintArea-{{ $transaction->trx_token }}">
                                    <div class="qr-svg">{!! $transaction->qr_code_svg !!}</div>
                                </div>
                                <div>
                                    <div class="amount-label">
                                        {{ $transaction->payable_amount }} {{ $transaction->payable_currency }}
                                    </div>
                                    <div class="expires-label">
                                        {{ __('Expires At') }}:
                                        @if($transaction->isExpired())
                                            <span class="expired">{{ __('Expired') }}</span>
                                        @else
                                            <span class="not-expired">{{ $transaction->formattedExpiresAt() ?? __('Never') }}</span>
                                        @endif
                                    </div>
                                    <span class="qr-status-badge bg-{{ $color }} text-white">
                                        <i class="{{ $icon }}"></i>{{ $label }}
                                     </span>
                                </div>
                            </div>
                            
                            <div class="qr-link-row">
                                <i class="fas fa-link"></i>
                                <a href="{{ route('payment.pay', ['merchant'=>Str::slug($transaction->trx_data['merchant_name']),'token'=> $transaction->trx_token]) }}" target="_blank">
                                    {{ url('/pay/' . Str::slug($transaction->trx_data['merchant_name']) . '/...') }}
                                </a>
                            </div>
                            
                            <div class="card-actions">
                                <button class="btn btn-outline-primary btn-sm copyNow"
                                        data-clipboard-text="{{ route('payment.pay', [
                              'merchant'=>Str::slug($transaction->trx_data['merchant_name']),
                              'token'=> $transaction->trx_token
                            ]) }}"
                                        title="{{ __('Copy Payment Link') }}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top">
                                    <i class="fas fa-copy"></i> {{ __('Copy') }}
                                </button>
                                <button class="btn btn-outline-dark btn-sm"
                                        onclick="printQrCode('qrPrintArea-{{ $transaction->trx_token }}')">
                                    <i class="fas fa-print"></i> {{ __('Print') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center small mb-0">
                            {{ __('No QR codes found.') }}
                        </div>
                    </div>
                @endforelse
            </div>
            
            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $qrTransactions->links() }}
            </div>
        </div>
    </div>

@endsection
