@extends('frontend.layouts.user.index')
@section('title', __('Merchant API Configuration'))

@section('content')
    <div class="single-form-card">
        <div class="card-title d-flex flex-column flex-md-row justify-content-between">
            <h6 class="text-white mb-2 mb-md-0"> <i class="fas fa-cogs me-2"></i> {{ __('Merchant API Configuration') }}</h6>
            <div class="d-flex gap-2 flex-row">
                <a href="{{ route('user.merchant.index') }}" class="btn btn-light-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Merchants') }}
                </a>
            </div>
        </div>
        
        <div class="card-main">
            {{-- Environment Toggle Section --}}
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between border-bottom pb-3 mb-4">
                <div class="element-text-box">
                    <h6 class="main-title mb-1">
                        <i class="fas fa-toggle-on text-primary me-2"></i>{{ __('API Environment') }}
                    </h6>
                    <p class="mb-0 text-muted">
                        {{ __('Switch between sandbox (testing) and production environments') }}
                    </p>
                </div>
                <div class="environment-toggle mt-3 mt-md-0">
                    <div class="btn-group flex-wrap" role="group">
                        @foreach(\App\Enums\EnvironmentMode::cases() as $env)
                            <button type="button"
                                    class="btn btn-outline-{{ $env->colorClass() }} environment-btn {{ $merchant->current_mode === $env ? 'active' : '' }}"
                                    data-environment="{{ $env->value }}">
                                <i class="{{ $env->icon() }} me-1"></i> {{ $env->label() }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            
            
            {{-- Security Warning --}}
            <div class="text-muted bg-light-warning fw-500 border-warning-left-5 rounded text-white p-3 mb-4 d-flex align-items-center" role="alert" aria-live="assertive">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                {{ __('Keep your API credentials secure and do not share them with unauthorized persons.') }}
            </div>
            
            {{-- API Benefits --}}
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center border-bottom pb-3 mb-3 w-100 justify-content-between">
                <div class="element-text-box">
                    <h6 class="main-title mb-1">
                        <span class="me-2">🚀</span>{{ __('Benefits of :name API Integration', ['name' => setting('site_title')]) }}
                    </h6>
                    <p class="mb-0">
                        {{ __('Seamlessly integrate payments into any script and allow REEXPAY users to pay your company using their UGX wallets. A developer-friendly and flexible solution!') }}
                    </p>
                </div>
            </div>
            
            {{-- API Credentials Section --}}
            <div id="credentials-section">
                @foreach(\App\Enums\EnvironmentMode::cases() as $env)
                    <div class="credentials-container"
                         id="{{ $env->value }}-credentials"
                         style="{{ $merchant->current_mode === $env ? '' : 'display: none;' }}">
                        {{-- Environment Alert --}}
                        <div class="alert alert-{{ $env->isSandbox() ? 'info' : 'danger' }} mb-3">
                            <i class="{{ $env->icon() }} me-2"></i>
                            <strong>{{ $env->label() }} {{ __('Environment') }}</strong><br>
                            {{ $env->description() }}
                            @if($env->isProduction())
                                <br><strong>{{ __('Handle with extreme care!') }}</strong>
                            @endif
                        </div>
                        
                        {{-- Credentials Display --}}
                        @php
                            $credentials = [
                                'merchant_key' => [
                                    'label' => __('Merchant ID'),
                                    'icon' => 'fas fa-id-badge',
                                    'value' => $env->isSandbox() ? $merchant->test_merchant_key : $merchant->merchant_key,
                                    'copy_title' => $env->isSandbox() ? __('Copy Test Merchant Key') : __('Copy Merchant Key')
                                ],
                                'api_key' => [
                                    'label' => $env->isSandbox() ? __('Test API Key') : __('API Key'),
                                    'icon' => 'fas fa-key',
                                    'value' => $env->isSandbox() ? $merchant->test_api_key : $merchant->api_key,
                                    'copy_title' => $env->isSandbox() ? __('Copy Test API Key') : __('Copy API Key')
                                ],
                                'api_secret' => [
                                    'label' => $env->isSandbox() ? __('Test API Secret Key') : __('API Secret Key'),
                                    'icon' => 'fas fa-shield-alt',
                                    'value' => $env->isSandbox() ? $merchant->test_api_secret : $merchant->api_secret,
                                    'copy_title' => $env->isSandbox() ? __('Copy Test Secret Key') : __('Copy Secret Key')
                                ]
                            ];
                        @endphp
                        
                        @foreach($credentials as $credential)
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center border-bottom mb-4 w-100 justify-content-between">
                                <div class="element-text-box pe-0 pe-md-5 mb-2 mb-md-0 w-100">
                                    <h6 class="main-title style-small mb-1">
                                        <i class="{{ $credential['icon'] }} text-{{ $env->colorClass() }} me-2"></i>
                                        {{ $credential['label'] }}
                                    </h6>
                                    <p class="mb-0 text-muted text-break d-flex flex-column flex-md-row align-items-start align-items-md-center">
                                        <span class="me-2">{{ $credential['value'] }}</span>
                                        <i class="fa-solid fa-copy copy-icon copyNow ms-0 ms-md-2"
                                           data-clipboard-text="{{ $credential['value'] }}"
                                           title="{{ $credential['copy_title'] }}"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"></i>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            
            {{-- API Documentation --}}
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center pt-3 mt-3 w-100 justify-content-between">
                <div class="mb-3 mb-md-0">
                    <h6 class="mb-1"><i class="fas fa-book me-2 text-info"></i> {{ __('API Implementation Guide') }}</h6>
                    <p class="mb-0">{{ __('Refer to the API documentation for integration details.') }}</p>
                </div>
                <a href="{{ route('api-docs.index') }}" class="btn btn-light-primary btn-sm">
                    <i class="fas fa-external-link-alt"></i> {{ __('View Documentation') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        "use strict";
        
        $(document).ready(function() {
            // Environment toggle functionality
            $('.environment-btn').on('click', function() {
                const environment = $(this).data('environment');
                const merchantId = {{ $merchant->id }};
                
                // Visual feedback
                $('.environment-btn').removeClass('active');
                $(this).addClass('active');
                
                // Show loading state
                $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ __("Switching...") }}').prop('disabled', true);
                
                // AJAX call to switch environment
                $.ajax({
                    url: '{{ route("user.merchant.switch-environment") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        merchant_id: merchantId,
                        environment: environment
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI
                            toggleCredentialsDisplay(environment);
                            updateCurrentModeAlert(environment);
                            
                            // Show success message
                            notifyEvs('success', response.message);
                        } else {
                            notifyEvs('error', response.message || '{{ __("Failed to switch environment") }}');
                        }
                    },
                    error: function() {
                        notifyEvs('error', '{{ __("An error occurred while switching environment") }}');
                    },
                    complete: function() {
                        // Restore button state based on environment
                        const buttonText = environment === 'sandbox' ?
                            '<i class="fas fa-flask me-1"></i> {{ __("Sandbox") }}' :
                            '<i class="fas fa-rocket me-1"></i> {{ __("Production") }}';
                        
                        $('.environment-btn[data-environment="' + environment + '"]')
                            .html(buttonText)
                            .prop('disabled', false);
                    }
                });
            });
            
            function toggleCredentialsDisplay(environment) {
                // Hide all credential containers
                $('.credentials-container').hide();
                // Show selected environment credentials
                $('#' + environment + '-credentials').show();
            }
            
            function updateCurrentModeAlert(environment) {
                const alertBox = $('.alert').first();
                const colorClass = environment === 'sandbox' ? 'alert-warning' : 'alert-success';
                const icon = environment === 'sandbox' ? 'fas fa-flask' : 'fas fa-rocket';
                const label = environment === 'sandbox' ? '{{ __("Sandbox") }}' : '{{ __("Production") }}';
                const description = environment === 'sandbox' ?
                    '{{ __("Test environment - No real money transactions. Perfect for integration testing.") }}' :
                    '{{ __("Live environment - Real money transactions. Use with caution.") }}';
                
                alertBox.removeClass('alert-warning alert-success').addClass(colorClass);
                alertBox.html(`
            <i class="${icon} me-2"></i>
            <div>
                <strong>{{ __('Current Mode') }}: ${label}</strong><br>
                <small>${description}</small>
            </div>
        `);
            }
        });
    </script>
@endsection