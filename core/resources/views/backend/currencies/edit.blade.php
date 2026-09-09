@php use App\Constants\CurrencyType; @endphp
@php use App\Constants\CurrencyRole; @endphp
@php use App\Constants\FixPctType; @endphp
<form action="{{ route('admin.currency.update', ['currency' => $currency->id]) }}" method="post"
      enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="row">
        {{-- Flag --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('Flag') }}</label>
            <x-img name="flag" old="{{ $currency->flag }}" :ref="'coevs-currency-flag'"/>
        </div>
    </div>
    <div class="row mt-2 g-2">
        {{-- Currency Type and Name --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('Currency Type') }}</label>
            <select name="type" class="form-select" required id="site_currency_type">
                <option selected disabled>{{ __('Select Currency Type') }}</option>
                @foreach(CurrencyType::getTypes() as $type)
                    <option value="{{ $type }}" @selected($currency->type === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Currency Name') }}</label>
            <select name="name" class="form-select" id="site_currency">
                @foreach(getJsonData('currencies')[$currency['type']] as $value)
                    <option value="{{ $value['name'] }}" @selected($currency->name == $value['name'])>{{ $value['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Code and Symbol --}}
        <div class="col-md-6">
            <label class="form-label">{{ __('Code') }}</label>
            <input type="text" name="code" value="{{ old('code', $currency->code) }}" id="currency_code"
                   class="form-control" placeholder="{{ __('Code') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Symbol') }}</label>
            <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" id="currency_symbol"
                   class="form-control" placeholder="{{ __('Symbol') }}" required>
        </div>

        {{-- Conversion Rate --}}
        <div class="col-lg-6 col-md-12">
            <label class="form-label">{{ __('Conversion Rate') }}</label>
            <a class="badge bg-secondary text-decoration-none"
               href="{{ route('admin.settings.plugin_type','exchange_rate') }}">{{ __('Manage Exchange') }}</a>
            <div class="input-group">
                <span class="input-group-text">1 {{ siteCurrency() }} =</span>
                <input type="text" oninput="this.value = validateDouble(this.value)" name="exchange_rate"
                       value="{{ old('exchange_rate', $currency->exchange_rate) }}" id="conversion_rate"
                       class="form-control">
                <span class="input-group-text">
                    <div class="form-check form-switch m-0">
                        <input type="hidden" name="rate_live" value="0">
                        <input class="form-check-input" id="rate_live" type="checkbox" name="rate_live" value="1" @checked($currency->rate_live)>
                        <label class="form-check-label text-danger" for="rate_live">{{ __('Live') }}</label>
                    </div>
                </span>
                <span class="input-group-text" id="currency-selected">{{ old('name', $currency->code) }}</span>
            </div>
        </div>

        {{-- Default and Status Toggles --}}
        @if(!$currency->default)
            <div class="col-md-6">
                <label class="form-label" for="default">
                    {{ __('Auto Wallet') }}
                    <span class="modal-tooltip" data-coreui-toggle="tooltip" data-coreui-placement="top"
                          title="{{ __('If enabled, when a user registers, a wallet will be created automatically for this currency.') }}">
                    <x-icon name="info" height="18"/>
                </span>
                </label>
                <div class="form-check form-switch p-2 border rounded d-flex align-items-center">
                    <label class="form-check-label flex-grow-1" for="default">{{ __('Enable') }}</label>
                    <input class="form-check-input me-2" type="checkbox" role="switch" name="auto_wallet" value="1"
                           id="default" @checked($currency->auto_wallet)>
                </div>
            </div>
        @endif


        <div class="col-md-{{ $currency->default ? '3' : '6' }}">
            <label class="form-label" for="default">{{ __('Default') }}</label>
            <div class="form-check form-switch p-2 border rounded d-flex align-items-center">
                <label class="form-check-label flex-grow-1" for="default">{{ __('Enable') }}</label>
                <input class="form-check-input me-2" type="checkbox" role="switch" name="default" value="1"
                       id="default" @checked($currency->default)>
            </div>
        </div>
        <div class="col-md-{{ $currency->default ? '3' : '6' }}">
            <label class="form-label" for="default">{{ __('Status') }}</label>
            <div class="form-check form-switch p-2 border rounded d-flex align-items-center">
                <label class="form-check-label flex-grow-1" for="status">{{ __('Enable') }}</label>
                <input class="form-check-input me-2" type="checkbox" role="switch" name="status" value="1"
                       id="status" @checked($currency->status)>
            </div>
        </div>

        {{-- Roles Accordion --}}
        <div class="col-12">
            <p class="lead text-muted fw-bold mt-2">
                #{{ __('Role with Fee and Limit Management') }}
            </p>
            <div class="accordion" id="accordionExample">
                @foreach($currency->roles as $role)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingRole{{ $role->id }}">
                            <button class="accordion-button collapsed" type="button"
                                    data-coreui-toggle="collapse" data-coreui-target="#collapseRole{{ $role->id }}"
                                    aria-expanded="false" aria-controls="collapseRole{{ $role->id }}">
                                <x-icon :name="$role->role_name" height="20" width="20"
                                        class="me-2"/> {{ Str::upper($role->role_name) }}
                            </button>
                        </h2>
                        <div id="collapseRole{{ $role->id }}" class="accordion-collapse collapse"
                             aria-labelledby="headingRole{{ $role->id }}" data-coreui-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    @if($role->role_name !== CurrencyRole::WITHDRAW)
                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('Fee') }}</label>
                                            <div class="input-group  evs-input-group">
                                                <input type="text" class="form-control"
                                                       name="roles[{{ $role->id }}][fee]"
                                                       value="{{ $role->fee }}" placeholder="{{ __('Fee') }}"
                                                       oninput="this.value = validateDouble(this.value)">
                                                <select name="roles[{{ $role->id }}][fee_type]"
                                                        class="form-select input-group-select">
                                                    @foreach(FixPctType::getTypeWithSymbol() as $key => $value)
                                                        <option value="{{ $key }}" @selected($key == $role->fee_type)>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('Minimum Amount') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                       name="roles[{{ $role->id }}][min_limit]"
                                                       value="{{ $role->min_limit }}"
                                                       oninput="this.value = validateDouble(this.value)">
                                                <span class="input-group-text">{{ siteCurrency() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('Maximum Amount') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                       name="roles[{{ $role->id }}][max_limit]"
                                                       value="{{ $role->max_limit }}"
                                                       oninput="this.value = validateDouble(this.value)">
                                                <span class="input-group-text">{{ siteCurrency() }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted"> {{ __('Withdrawal fee & limit only applicable for withdraw payment method') }}</span>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="roles[{{ $role->id }}][status]" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   name="roles[{{ $role->id }}][status]" value="1"
                                                   id="role_status_{{ $role->id }}" @checked($role->is_active)>
                                            <label class="form-check-label"
                                                   for="role_status_{{ $role->id }}">{{ __('Status') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="text-end mt-4">
        <button class="btn btn-primary" type="submit">
            <x-icon name="check" height="20"/> {{ __('Update Now') }}
        </button>
    </div>
</form>
