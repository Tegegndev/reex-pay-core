@php use App\Enums\MethodType; @endphp
@extends('general.merchant.index')
@section('favicon', asset($data['business_logo']))
@section('title', __('Payment Checkout'))
@section('merchant_content')
    <div class="gateway-section">
        {{-- REEXPAY branded checkout header --}}
        <div class="checkout-header bg-gradient-to-r from-indigo-600 to-purple-600 text-white d-flex align-items-center justify-content-between mb-4 px-4 py-4 rounded-xl shadow-lg">
            {{-- Merchant Info --}}
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <img src="{{ asset($data['business_logo'] ?? 'default-logo.png') }}"
                         alt="{{ $data['business_name'] ?? 'Business' }} Logo"
                         class="merchant-logo rounded-lg border-2 border-white/30">
                </div>
                <div>
                    <h6 class="mb-0 text-white font-semibold">
                        {{ $data['business_name'] }}
                        @if($data['is_sandbox'] ?? false)
                            <small class="badge bg-yellow-400 text-yellow-900 ms-2 font-bold">@lang('TEST MODE')</small>
                        @endif
                    </h6>
                    <small class="text-indigo-100 merchant-description">{{ $data['description'] }}</small>
                </div>
            </div>
            {{-- Payment Amount --}}
            <div class="text-end">
                <small class="text-indigo-200 d-block">@lang('Total Amount')</small>
                <h4 class="mb-0 fw-bold text-white text-2xl">{{ $data['payment_amount'] }}</h4>
            </div>
        </div>

        {{-- Test Payment Information (Sandbox Only) --}}
        @if($data['is_sandbox'] ?? false)
            <div class="alert  border-0 mb-4 test-payment-info">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-info-circle me-2"></i>@lang('Test Payment Info')
                        </h6>
                        <p class="small text-muted mb-0">@lang('Use these credentials for testing')</p>
                    </div>
                    <div class="col-md-9">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="bg-white rounded p-2 border">
                                    <strong class="text-primary small">@lang('Wallet ID:')</strong>
                                    <code class="d-block bg-light px-2 py-1 rounded mt-1">123456789</code>
                                    <small class="text-muted">@lang('Password:'): <code>demo123</code></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white rounded p-2 border">
                                    <strong class="text-success small">@lang('Voucher Code:')</strong>
                                    <code class="d-block bg-light px-2 py-1 rounded mt-1">TESTVOUCHER</code>
                                    <small class="text-muted">@lang('For testing only')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white rounded p-2 border">
                                    <strong class="text-info small">@lang('Gateway:')</strong>
                                    <code class="d-block bg-light px-2 py-1 rounded mt-1">@lang('Auto Success')</code>
                                    <small class="text-muted">@lang('Instant completion')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- MarzPay Payment Methods Grid --}}
        <section aria-label="@lang('Payment Methods')" class="bg-white rounded-xl p-6 shadow-sm border border-indigo-100">
            <h6 class="text-uppercase text-indigo-600 font-bold mb-4 flex items-center">
                <i class="fas fa-credit-card me-2"></i>
                @lang('Payment Methods')
            </h6>

            <form id="paymentForm" action="{{ route('payment.process') }}" method="post">
                @csrf
                <input type="hidden" name="selected_method" id="selectedMethod">
                <input type="hidden" name="trx_id" value="{{ $trxId }}">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="payment-logo-card bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-4 text-center transition-all duration-300 hover:shadow-lg hover:border-indigo-400"
                             role="button"
                             tabindex="0"
                             aria-pressed="false"
                             data-method="{{ MethodType::SYSTEM->value }}">
                            <img src="{{ asset(setting('logo')) }}" class="payment-logo h-12 mx-auto mb-2"
                                 alt="{{ setting('site_title') }} Logo">
                            <p class="payment-name font-semibold text-indigo-900">REEXPAY Wallet</p>
                        </div>
                    </div>

                    @foreach($paymentMethods as $method)
                        <div class="col-6 col-md-3">
                            <div class="payment-logo-card bg-white border-2 border-gray-200 rounded-xl p-4 text-center transition-all duration-300 hover:shadow-lg hover:border-indigo-400"
                                 role="button"
                                 tabindex="0"
                                 aria-pressed="false"
                                 data-method="{{ $method->method_code }}">
                                <img src="{{ asset($method->logo_alt) }}" class="payment-logo h-12 mx-auto mb-2"
                                     alt="{{ $method->name }} Logo">
                                <p class="payment-name font-semibold text-gray-700">{{ $method->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- REEXPAY payment action --}}
                <div class="d-grid mt-5">
                    <button id="payButton" type="submit" class="btn btn-reexpay btn-lg fw-bold text-white py-3 rounded-xl" disabled>
                        <i class="fas fa-lock me-2"></i>
                        @lang('Complete Payment :amount', ['amount' => $data['payment_amount']])
                    </button>
                </div>
            </form>
        </section>

        {{-- REEXPAY security footer --}}
        <div class="text-center mt-4 small text-indigo-600 bg-indigo-50 py-3 rounded-lg">
            <i class="fas fa-shield-alt me-2"></i>
            @lang('REEXPAY secured • 256-bit SSL encryption • PCI DSS compliant')
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        "use strict";
        
        document.addEventListener('DOMContentLoaded', function() {
            const isSandbox = {{ ($data['is_sandbox'] ?? false) ? 'true' : 'false' }};
            
            // Add sandbox logging
            if (isSandbox) {
                console.log('%c🧪 SANDBOX MODE ACTIVE', 'color: #007bff; font-weight: bold; font-size: 14px;');
                console.log('Transaction ID:', '{{ $data['sandbox_transaction_id'] ?? $trxId }}');
                console.log('Environment:', '{{ $data['environment'] ?? 'sandbox' }}');
            }
            
            // Payment method selection logic
            const paymentCards = document.querySelectorAll('.payment-logo-card');
            const payButton = document.getElementById('payButton');
            const selectedMethodInput = document.getElementById('selectedMethod');
            const paymentForm = document.getElementById('paymentForm');
            
            paymentCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove active class from all cards
                    paymentCards.forEach(c => {
                        c.classList.remove('active', 'border-indigo-500', 'bg-indigo-100');
                        c.classList.add('border-gray-200', 'bg-white');
                        c.setAttribute('aria-pressed', 'false');
                    });
                    
                    // Add active class to clicked card with MarzPay styling
                    this.classList.remove('border-gray-200', 'bg-white');
                    this.classList.add('active', 'border-indigo-500', 'bg-indigo-100');
                    this.setAttribute('aria-pressed', 'true');
                    
                    // Set selected method and enable button
                    const selectedMethod = this.dataset.method;
                    selectedMethodInput.value = selectedMethod;
                    payButton.disabled = false;
                    
                    // Sandbox auto-payment for gateway methods
                    if (isSandbox && selectedMethod !== '{{ MethodType::SYSTEM->value }}') {
                        console.log('🧪 Auto-completing sandbox gateway payment:', selectedMethod);
                        
                        // Show processing state
                        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>@lang("Auto Processing...")';
                        payButton.disabled = true;
                        
                        // Auto-submit after short delay for better UX
                        setTimeout(() => {
                            paymentForm.submit();
                        }, 1000);
                    }
                    
                    // Sandbox feedback
                    if (isSandbox) {
                        console.log('🧪 Payment method selected:', selectedMethod);
                    }
                });
                
                // Keyboard accessibility
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
            
            // Form submission handling
            paymentForm.addEventListener('submit', function(e) {
                if (isSandbox) {
                    console.log('🧪 Submitting sandbox payment form');
                }
                
                // Disable button to prevent double submission
                if (!payButton.innerHTML.includes('Auto Processing')) {
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>@lang("Processing...")';
                }
            });
        });
    </script>
@endpush
