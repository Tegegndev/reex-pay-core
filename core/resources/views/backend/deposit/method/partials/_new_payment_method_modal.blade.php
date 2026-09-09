@php use App\Enums\FixPctType; @endphp
<div class="modal fade" id="new_payment_method_modal" aria-hidden="true" aria-labelledby="logoutmodal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New').' '. $type->label().' ' . __('Payment Method') }}</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body payment-method-form">
                <form action="{{ route('admin.deposit.method.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="icon">{{ __('Logo') }}</label>
                            <x-img name="logo"/>
                        </div>
                    </div>
                    @if($type === App\Enums\MethodType::AUTOMATIC)
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-12">
                                <label class="form-label" for="role">{{ __('Payment Gateway') }}</label>
                                <select class="form-select" id="select-payment-gateway" name="payment_gateway_id"
                                        required>
                                    <option selected disabled>{{ __('Select Payment Gateway') }}</option>
                                    @foreach($paymentGateways as $gateway)
                                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
                                <label class="form-label" for="currency">{{ __('Supported Currency') }}</label>
                                <select class="form-select" id="currency-list" name="currency" required>
                                    <option selected disabled>{{ __('Select First Payment Gateway') }}</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="name">{{ __('Name') }}</label>
                            <input class="form-control" type="text" name="name" placeholder="Name" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
                            <label class="form-label" for="currency_symbol">{{ __('Currency Symbol') }}</label>
                            <input class="form-control" type="text" name="currency_symbol" id="currency-symbol"
                                   placeholder="Ex: $, BTC"
                                   required>
                        </div>
                    </div>
                    @if($type == App\Enums\MethodType::MANUAL)
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-12">
                                <label class="form-label" for="code">{{ __('Method Code') }}</label>
                                <input class="form-control" type="text" name="method_code"
                                       placeholder="Ex: paypal-usd, custom-name-btc" required>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
                                <label class="form-label" for="currency">{{ __('Currency') }}</label>
                                <input class="form-control" type="text" name="currency" id="custom_currency"
                                       placeholder="Ex: USD, BTC,etc.."
                                       required>
                            </div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label" for="conversion_rate">{{ __('Conversion Rate:') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ siteCurrency() }}</span>
                                <input type="text" oninput="this.value = validateDouble(this.value)"
                                       name="conversion_rate" id="conversion_rate" class="form-control"
                                       aria-label="Amount (to the nearest dollar)">
                                @if($type == App\Enums\MethodType::AUTOMATIC)
                                    <span class="input-group-text">
                                        <div class="form-check form-switch">
                                          <input type="hidden" name="conversion_rate_live" value="0">
                                          <input class="form-check-input" id="conversion_rate_live" type="checkbox"
                                                 name="conversion_rate_live" value="1">
                                          <label class="form-check-label text-danger" for="conversion_rate_live">
                                            {{ __('Live') }}
                                          </label>
                                        </div>
                                    </span>
                                @endif
                                <span class="input-group-text" id="currency-selected"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
                            <label class="form-label" for="min_deposit">{{ __('Minimum Deposit:') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="min_deposit"
                                       oninput="this.value = validateDouble(this.value)"
                                       aria-label="Amount (to the nearest dollar)">
                                <span class="input-group-text">{{ siteCurrency() }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="max_deposit">{{ __('Maximum Deposit:') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="max_deposit"
                                       oninput="this.value = validateDouble(this.value)"
                                       aria-label="Amount (to the nearest dollar)">
                                <span class="input-group-text">{{ siteCurrency() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charge Configuration Section -->
                    <div class="card bg-light shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calculator text-primary me-2"></i>
                                <h6 class="fw-semibold mb-0">{{ __('Charge Configuration') }}</h6>
                            </div>
                            
                            <div class="alert alert-info d-flex align-items-start mb-3">
                                <i class="fas fa-lightbulb me-2 mt-1"></i>
                                <div>
                                    <strong>{{ __('Business Logic:') }}</strong>
                                    {{ __('Set different deposit charges for regular users and merchant users. Regular users typically have standard rates while merchants may have preferential rates based on volume or partnership agreements.') }}
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <!-- Regular User Charge -->
                                <div class="col-lg-6 col-md-12">
                                    <div class="border rounded p-3 user-charge-border">
                                        <label class="form-label fw-semibold text-primary">
                                            <i class="fas fa-user me-2"></i>{{ __('Regular User Charge') }}
                                        </label>
                                        <div class="input-group">
                                            <input class="form-control" type="text"
                                                   oninput="this.value = validateDouble(this.value)"
                                                   name="user_charge" placeholder="Enter user charge">
                                            <select name="user_charge_type" class="form-select input-group-select">
                                                @foreach(FixPctType::options() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="text-muted">{{ __('Charge applied to regular user deposits') }}</small>
                                    </div>
                                </div>
                                
                                <!-- Merchant User Charge -->
                                <div class="col-lg-6 col-md-12">
                                    <div class="border rounded p-3 merchant-charge-border">
                                        <label class="form-label fw-semibold text-success">
                                            <i class="fas fa-store me-2"></i>{{ __('Merchant User Charge') }}
                                        </label>
                                        <div class="input-group">
                                            <input class="form-control" type="text"
                                                   oninput="this.value = validateDouble(this.value)"
                                                   name="merchant_charge" placeholder="Enter merchant charge">
                                            <select name="merchant_charge_type" class="form-select input-group-select">
                                                @foreach(FixPctType::options() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="text-muted">{{ __('Charge applied to merchant user deposits') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($type == App\Enums\MethodType::MANUAL)
                        <div class="mb-3 mt-4">
                            <div class="col-xl-3 mb-3">
                                <a href="javascript:void(0)" id="add-new-field"
                                   class="btn btn-primary">
                                    <x-icon name="add" height="20"/>{{ __('Add New Field') }}</a>
                            </div>
                            <div class="append-new-field mb-3">
                            </div>
                            <div class="col-xl-12">
                                <div class="site-input-groups fw-normal">
                                    <label for="" class="form-label">{{ __('Receive Payment Details:') }}</label>
                                    <div class="site-editor">
                                        <textarea class="summernote form-control"
                                                  name="receive_payment_details"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-lg-4 col-md-4 col-4">
                            <label class="form-label" for="status">{{ __('Status') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input coevs-switch" type="checkbox" name="status" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary" type="submit">
                            <x-icon name="check" height="20"/> {{ __('Create Payment Method') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>