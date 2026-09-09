<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    {{ __('Confirm User Deletion') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                {{-- Warning Alert --}}
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading">
                        <i class="fa fa-warning me-2"></i>
                        {{ __('WARNING: This action is irreversible!') }}
                    </h6>
                    <p class="mb-0">{{ __('Deleting this user will permanently remove all associated data from the system.') }}</p>
                </div>

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-md-6">
                        {{-- User Information --}}
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fa fa-user me-2"></i>
                                    {{ __('User Information') }}
                                </h6>
                                <p class="mb-1"><strong>{{ __('Full Name:') }}</strong> <span id="modal-user-fullname"></span></p>
                                <p class="mb-1"><strong>{{ __('Username:') }}</strong> <span id="modal-username" class="text-primary fw-bold"></span></p>
                                <p class="mb-0"><strong>{{ __('User Type:') }}</strong> <span id="modal-user-type"></span></p>
                            </div>
                        </div>
                        
                        {{-- Transaction Statistics --}}
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fa fa-chart-line me-2"></i>
                                    {{ __('Transaction Statistics') }}
                                </h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="fw-bold fs-4" id="modal-successful-transactions">-</div>
                                        <small>{{ __('Success Count') }}</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold fs-4" id="modal-successful-amount">-</div>
                                        <small>{{ __('Success Amount') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Right Column --}}
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="fa fa-times-circle me-2"></i>
                                    {{ __('Data to be Deleted') }}
                                </h6>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('All Transactions') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('KYC Documents') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Support Tickets') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Deposit Methods') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Withdrawal Methods') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Notifications') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Referral Data') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Virtual Cards & Info') }}</li>
                                    <li class="mb-1"><i class="fa fa-times text-danger me-2"></i> {{ __('Wallet Data') }}</li>
                                    <li id="merchant-data-list" class="d-none mb-1">
                                        <i class="fa fa-times text-warning me-2"></i>
                                        <span class="text-warning">{{ __('Merchant Shop & Data') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Username Confirmation --}}
                <div class="mt-4">
                    <div class="border rounded p-4 bg-light">
                        <div class="mb-3">
                            <h6 class="mb-2 text-dark">
                                {{ __('Please type') }}
                                <code class="bg-danger text-white px-2 py-1 rounded" id="confirm-username-display">username</code>
                                {{ __('to confirm.') }}
                            </h6>
                            <p class="text-muted small mb-0">
                                {{ __('This will permanently delete the user and cannot be undone.') }}
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <input type="text"
                                   class="form-control border-2"
                                   id="confirmUsername"
                                   placeholder="{{ __('Type username here') }}"
                                   autocomplete="off">
                            <div class="invalid-feedback" id="username-error">
                                {{ __('Username does not match. Please type the exact username.') }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fa fa-info-circle me-2"></i>{{ __('Type the username exactly as shown above') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">
                    <i class="fa fa-times me-1"></i>
                    {{ __('Cancel') }}
                </button>
                <form id="deleteUserForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger text-white" id="confirmDeleteBtn" disabled>
                        <i class="fa fa-trash me-1"></i>
                        {{ __('Delete User Permanently') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
