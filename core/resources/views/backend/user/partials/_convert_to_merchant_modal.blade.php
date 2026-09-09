{{-- Convert User to Merchant Modal --}}
<div class="modal fade" id="convertToMerchantModal" tabindex="-1" aria-labelledby="convertToMerchantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow border-0">
            {{-- Modal Header --}}
            <div class="modal-header bg-primary text-white border-0 py-2">
                <h6 class="modal-title mb-0 d-flex align-items-center" id="convertToMerchantModalLabel">
                    <i class="fas fa-store me-2"></i>{{ __('Convert to Merchant') }}
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-coreui-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                {{-- User Profile Card --}}
                <div class="card border-0 bg-light mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            {{-- User Avatar --}}
                            <div class="flex-shrink-0 me-3">
                                <img src="{{ asset($user->avatar_alt) }}" alt="{{ $user->name }}"
                                     class="rounded-circle border border-2 border-primary convert-modal-avatar"
                                     width="50" height="50">
                            </div>
                            
                            {{-- User Details --}}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">{{ $user->name }}</h6>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-at text-muted me-1 convert-modal-user-icon"></i>
                                    <span class="text-muted small">{{ $user->username }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-muted me-1 convert-modal-user-icon"></i>
                                    <span class="text-muted small">{{ $user->email }}</span>
                                </div>
                            </div>
                            
                            {{-- User Type Badge --}}
                            <div class="flex-shrink-0">
                                <span class="badge bg-secondary bg-opacity-75 text-dark">
                                    <i class="fas fa-user me-1"></i>{{ __('User') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Merchant Benefits --}}
                <div class="bg-success bg-opacity-10 border-start border-success border-3 p-3 rounded-end mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <h6 class="mb-0 text-success fw-semibold">{{ __('Merchant Benefits') }}</h6>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-key text-primary me-2 convert-modal-benefit-icon"></i>
                                <small class="text-dark">{{ __('API Access') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-percentage text-success me-2 convert-modal-benefit-icon"></i>
                                <small class="text-dark">{{ __('Best Low Rates') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-qrcode text-info me-2 convert-modal-benefit-icon"></i>
                                <small class="text-dark">{{ __('QR Code Generate') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-link text-warning me-2 convert-modal-benefit-icon"></i>
                                <small class="text-dark">{{ __('Payment Link Generate') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top border-success border-opacity-25">
                        <small class="text-muted fst-italic">
                            <i class="fas fa-plus-circle me-1"></i>{{ __('+ More extra features enabled for merchant') }}
                        </small>
                    </div>
                </div>

                {{-- Warning --}}
                <div class="alert alert-warning py-2 mb-3">
                    <small><i class="fas fa-exclamation-triangle me-1"></i>{{ __('After conversion, merchant user can access merchant menu to create new merchant shop and will get API keys & API secret.') }}</small>
                </div>

                {{-- Username Confirmation --}}
                <div class="border border-danger rounded p-3">
                    <div class="mb-3">
                        <p class="mb-2">
                            @lang('Please type') <code class="bg-danger bg-opacity-10 text-danger px-2 py-1 rounded fw-bold">{{ $user->username }}</code> @lang('to confirm').
                        </p>
                        <p class="text-muted small mb-0">
                            @lang('This will permanently convert the user to merchant and cannot be undone.')
                        </p>
                    </div>
                    
                    <input type="text" class="form-control" id="confirmConvertUsername" 
                           placeholder="@lang('Type username here')" 
                           autocomplete="off" spellcheck="false"
                           data-expected-username="{{ $user->username }}">
                    
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle me-1"></i>@lang('Type the username exactly as shown above')
                    </div>
                    <div class="valid-feedback">
                        <i class="fas fa-check-circle me-1"></i>@lang('Username confirmed')
                    </div>
                </div>

                {{-- Hidden form --}}
                <form id="convertToMerchantForm" method="POST" action="{{ route('admin.user.convert-to-merchant', $user->id) }}">
                    @csrf
                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer bg-light border-0 p-3">
                <div class="d-flex w-100 justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">
                        <i class="fas fa-times me-1"></i>@lang('Cancel')
                    </button>
                    <button type="button" class="btn btn-danger text-white" id="confirmConvertBtn" disabled>
                        <i class="fas fa-exchange-alt me-1"></i>@lang('Convert to Merchant')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
