@php use App\Enums\TrxType; @endphp
<form action="{{ route('admin.ranking.update', $userRank->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row mb-3">
        <div class="col-12">
            <label class="form-label" for="icon">{{ __('Icon') }}</label>
            <x-img name="icon" :value="$userRank->icon" :old="$userRank->icon" :ref="'coevs-user-rank-icon'"/>
        </div>
    </div>

    <div class="mb-3">
        <label for="rank-name" class="form-label">{{ __('Name') }}</label>
        <input type="text" class="form-control" id="rank-name" name="name" value="{{ $userRank->name }}" required
               placeholder="{{ __('Enter rank name') }}">
    </div>

    <div class="mb-3">
        <label for="rank-description" class="form-label">{{ __('Description') }}</label>
        <input class="form-control" id="rank-description" name="description" value="{{ $userRank->description }}"
               required placeholder="{{ __('Enter rank description') }}">
    </div>

    <div class="row mb-3">
        <div class="col-6">
            <label for="trx-amount" class="form-label">
                {{ __('Transaction Amount') }}
                @if($userRank->is_default)
                    <span class="modal-tooltip" data-coreui-toggle="tooltip" data-coreui-placement="top"
                          title="{{ __('Default Rank Not Allowed To Change Transaction Amount') }}">
                        <i class="fa-solid fa-circle-info ms-1"></i>
                    </span>
                @endif
            </label>
            <div class="input-group">
                <input type="text" class="form-control"
                       name="transaction_amount" oninput="this.value = validateDouble(this.value)"
                       @if($userRank->is_default)
                           disabled
                       @endif
                       value="{{ $userRank->transaction_amount }}"
                       aria-label="Amount (to the nearest dollar)">
                <span class="input-group-text">{{ siteCurrency() }}</span>
            </div>
        </div>
        <div class="col-6">
            <label for="trx-amount" class="form-label">{{ __('Rank Reward') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="reward"
                       oninput="this.value = validateDouble(this.value)" value="{{ $userRank->reward }}"
                       aria-label="Amount (to the nearest dollar)">
                <span class="input-group-text">{{ siteCurrency() }}</span>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Allow Transaction Type') }}</label>
        <div>
            @foreach(TrxType::userRankSupport() as $trxType)
                @php($trxType = $trxType->value)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="transaction_types[]"
                           id="trx-type-{{ $trxType }}"
                           value="{{ $trxType }}" @checked(in_array($trxType, $userRank->transaction_types))>
                    <label class="form-check-label"
                           for="trx-type-{{ $trxType }}">{{ title($trxType) }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-6">
            <label for="trx-amount" class="form-label">{{ __('Max Wallet Create') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="features[wallet_create]"
                       oninput="this.value = validateDouble(this.value)"
                       value="{{ $userRank->features['wallet_create'] }}"
                       aria-label="Wallet Create">
                <span class="input-group-text">{{ __('Wallet') }}</span>
            </div>
        </div>
        <div class="col-6">
            <label for="trx-amount" class="form-label">{{ __('Max Referral Level') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="features[referral_level]"
                       value="{{ $userRank->features['referral_level']  }}"
                       oninput="this.value = validateDouble(this.value)"
                       aria-label="Referral Level">
                <span class="input-group-text">{{ __('Level') }}</span>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">
            {{ __('Status') }}
            @if($userRank->is_default)
                <span class="modal-tooltip" data-coreui-toggle="tooltip" data-coreui-placement="top"
                      title="{{ __('Default Rank Status Cannot Be Changed - Always Active') }}">
                    <i class="fa-solid fa-circle-info ms-1"></i>
                </span>
            @endif
        </label>
        <div class="form-check form-switch ">
            <input class="form-check-input coevs-switch" type="checkbox" id="is-active" name="is_active" value="1"
                   @checked($userRank->is_active)
                   @if($userRank->is_default) disabled @endif>
        </div>
    </div>

    {{-- Modal Footer --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-coreui-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</form>