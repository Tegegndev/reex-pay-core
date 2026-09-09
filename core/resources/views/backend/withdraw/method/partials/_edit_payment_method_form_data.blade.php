@php use App\Enums\FixPctType; @endphp
@php use App\Constants\TimeUnits; @endphp
<form action="{{ route('admin.withdraw.method.update', $paymentMethod->id) }}" method="post"
      enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <input type="hidden" name="type" value="{{ $paymentMethod->type }}">
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="icon">{{ __('Logo') }}</label>
            <x-img name="logo" old="{{ $paymentMethod->logo_alt }}" :ref="'coevs-payment-method-logo'"/>
        </div>
    </div>
    @if($paymentMethod->type === App\Enums\MethodType::AUTOMATIC)
        <div class="row mb-3">
            <div class="col-lg-6 col-md-6 col-12">
                <label class="form-label" for="role">{{ __('Payment Gateway') }}</label>
                <select class="form-select" id="select-payment-gateway" name="payment_gateway_id" required>
                    <option selected disabled>{{ __('Select Payment Gateway') }}</option>
                    @foreach($paymentGateways as $gateway)
                        <option value="{{ $gateway->id }}" @selected($paymentMethod->payment_gateway_id === $gateway->id)>{{ $gateway->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
                <label class="form-label" for="currency">{{ __('Supported Currency') }}</label>
                <select class="form-select" id="currency-list" name="currency" required>
                    @foreach($paymentMethod->paymentGateway->currencies as $paymentCurrency)
                        <option value="{{ $paymentCurrency }}" @selected($paymentMethod->currency === $paymentCurrency)>{{ $paymentCurrency }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="name">{{ __('Name') }}</label>
            <input class="form-control" type="text" value="{{ $paymentMethod->name }}" name="name" placeholder="Name"
                   required>
        </div>
        <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
            <label class="form-label" for="currency_symbol">{{ __('Currency Symbol') }}</label>
            <input class="form-control" type="text" name="currency_symbol" value="{{ $paymentMethod->currency_symbol }}"
                   id="currency-symbol" placeholder="Ex: $, BTC"
                   required>
        </div>
    </div>
    @if($paymentMethod->type == App\Enums\MethodType::MANUAL)
        <div class="row mb-3">
            <div class="col-lg-6 col-md-6 col-12">
                <label class="form-label" for="code">{{ __('Method Code') }}</label>
                <input class="form-control" type="text" name="method_code" value="{{ $paymentMethod->method_code }}"
                       placeholder="Ex: paypal-usd, custom-name-btc" required>
            </div>

            <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
                <label class="form-label" for="currency">{{ __('Currency') }}</label>
                <input class="form-control" type="text" name="currency" id="custom_currency"
                       value="{{ $paymentMethod->currency }}"
                       placeholder="Ex: USD, BTC,etc.."
                       required>
            </div>
        </div>
    @endif
    <div class="row mb-3">
        <div class="col-md-12">
            <label class="form-label" for="conversion_rate">{{ __('Conversion Rate:') }}</label>
            @if($paymentMethod->type == App\Enums\MethodType::AUTOMATIC)
                <a class="badge text-bg-secondary text-decoration-none"
                   href="{{ route('admin.settings.plugin_type','exchange_rate') }}"> {{ __('Manage Exchange') }}</a>
            @endif

            <div class="input-group">
                <span class="input-group-text">1 {{ siteCurrency() }} =</span>
                <input type="text" oninput="this.value = validateDouble(this.value)"
                       name="conversion_rate" value="{{ $paymentMethod->conversion_rate }}" id="conversion_rate"
                       class="form-control"
                       aria-label="Amount (to the nearest dollar)">
                @if($paymentMethod->type == App\Enums\MethodType::AUTOMATIC)
                    <span class="input-group-text">
                        <div class="form-check form-switch">
                          <input type="hidden" name="conversion_rate_live" value="0">
                          <input class="form-check-input" id="conversion_rate_live" type="checkbox"
                                 @checked($paymentMethod->conversion_rate_live)
                                 name="conversion_rate_live" value="1">
                          <label class="form-check-label text-danger" for="conversion_rate_live">
                            {{ __('Live') }}
                          </label>
                        </div>
                    </span>
                @endif
                <span class="input-group-text" id="currency-selected">{{ $paymentMethod->currency }}</span>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="currency_symbol">{{ __('Minimum withdraw:') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="min_withdraw" value="{{ $paymentMethod->min_withdraw }}"
                       oninput="this.value = validateDouble(this.value)"
                       aria-label="Amount (to the nearest dollar)">
                <span class="input-group-text">{{ siteCurrency() }}</span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12 mt-md-0 mt-3">
            <label class="form-label" for="currency_symbol">{{ __('Maximum withdraw:') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="max_withdraw" value="{{ $paymentMethod->max_withdraw }}"
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
                    {{ __('Set different withdrawal charges for regular users and merchant users. Regular users typically have standard rates while merchants may have preferential rates based on volume or partnership agreements.') }}
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
                                   name="user_charge" value="{{ $paymentMethod->user_charge }}" placeholder="Enter user charge">
                            <select name="user_charge_type" class="form-select input-group-select">
                                @foreach(FixPctType::options() as $key => $value)
                                    <option value="{{ $key }}" @selected($key == $paymentMethod->user_charge_type?->value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">{{ __('Charge applied to regular user withdrawals') }}</small>
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
                                   name="merchant_charge" value="{{ $paymentMethod->merchant_charge }}" placeholder="Enter merchant charge">
                            <select name="merchant_charge_type" class="form-select input-group-select">
                                @foreach(FixPctType::options() as $key => $value)
                                    <option value="{{ $key }}" @selected($key == $paymentMethod->merchant_charge_type?->value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">{{ __('Charge applied to merchant user withdrawals') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($paymentMethod->type == App\Enums\MethodType::MANUAL)
        <div class="row">
            {{-- Process Time Input Group --}}
            <div class="col-lg-6 col-md-6 col-12">
                <label class="form-label" for="process_time_value">{{ __('Process Time:') }}</label>
                <div class="input-group">
                    <input
                            class="form-control"
                            type="text"
                            id="process_time_value"
                            oninput="this.value = validateNumber(this.value)"
                            name="process_time_value"
                            placeholder="{{ __('Process Time') }}"
                            value="{{ $paymentMethod->process_time_value }}"
                            required>
                    <select name="process_time_unit" class="form-select input-group-select">
                        @foreach(TimeUnits::getAll() as $unit)
                            <option value="{{ $unit }}" @selected($unit == $paymentMethod->process_time_unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Add New Field Button --}}
            <div class="col-lg-6 col-md-6 col-12 d-flex align-items-end mt-md-0 mt-3">
                <a href="javascript:void(0)" id="add-new-field" class="btn btn-primary">
                    <x-icon name="add" height="20" class="me-2"/>
                    {{ __('Add New Field') }}
                </a>
            </div>
        </div>

        <div class="row mt-4">
            {{-- Dynamically Appended Fields --}}
            <div class="col-12 append-new-field-edit">
                @foreach($paymentMethod->fields as $key => $value)
                    @include('backend.withdraw.method.partials._method_append_form_field', ['key' => $key, 'field' => $value])
                @endforeach
            </div>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-lg-4 col-md-4 col-4">
            <label class="form-label" for="status">{{ __('Status') }}</label>
            <div class="form-check form-switch">
                <input class="form-check-input coevs-switch" type="checkbox" name="status"
                       @checked($paymentMethod->status) value="1">
            </div>
        </div>
    </div>
    <div class="text-end">
        <button class="btn btn-primary" type="submit">
            <x-icon name="check" height="20"/>
            {{ __('Update Payment Method') }}
        </button>
    </div>
</form>