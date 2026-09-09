@php
    $avatarData = getUserAvatarDetails($user->first_name, $user->last_name);
@endphp
<div class="col-md-12 col-lg-4">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            {{-- Centered Avatar --}}
            <div class="d-flex justify-content-center">
                @if($user->avatar)
                    <img class="avatar-xxl rounded-circle mx-auto d-block" src="{{ asset($user->avatar) }}"
                         alt="User Avatar">
                @else
                    <div class="rounded-circle {{ $avatarData['class'] }} text-white d-flex align-items-center justify-content-center avatar-xxl">
                        {{ $avatarData['initials'] }}
                    </div>
                @endif
            </div>

            <h5 class="mt-3 mb-1">{{ $user->first_name . ' ' . $user->last_name }}</h5>
            <p class="text-muted mb-2">{{  $user->country ?? 'Unknown'  }}</p>
            <small class="text-muted d-block">
                {{ __('Last Login: :value', [
                        'value' =>  optional($user->latestLoginActivity)->login_at?->format('Y-m-d H:i') ?? '--'
                    ]) }}
            </small>
            <small class="text-muted d-block">{{ __('Browser: :value', [
                'value' => optional($user->latestLoginActivity)?->browser ?? '--'
            ]) }}</small>

            {{-- Centered Action Icons --}}
            <div class="d-flex justify-content-center gap-2 mt-4">
                @can('custom-notify-users')
                    <a href="#" class="btn btn-warning  d-flex align-items-center justify-content-center text-white notify-user"
                       data-coreui-toggle="tooltip" title="{{ __('Notify User') }}">
                        <i class="fa fa-bell"></i>
                    </a>
                @endcan

                @can('user-balance-manage')
                    <a href="#" class="btn btn-success  d-flex align-items-center justify-content-center text-white add-money"
                       data-coreui-toggle="tooltip" title="{{ __('Manage Funds') }}">
                        <i class="fa fa-wallet"></i>
                    </a>
                @endcan

                @can('user-login-as')
                    <a href="{{ route('admin.user.login', $user->id) }}" target="_blank" class="btn btn-dark  d-flex align-items-center justify-content-center text-white"
                       data-coreui-toggle="tooltip" title="{{ __('Login as User') }}">
                        <i class="fa fa-user-shield"></i>
                    </a>
                @endcan
                
                @can('user-manage')
                    @if($user->isUser())
                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center text-white"
                                data-coreui-toggle="modal" data-coreui-target="#convertToMerchantModal"
                                data-user-id="{{ $user->id }}" data-username="{{ $user->username }}"
                                data-fullname="{{ $user->first_name . ' ' . $user->last_name }}"
                                data-user-email="{{ $user->email }}"
                                title="{{ __('Convert to Merchant') }}">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    @endif
                @endcan
                

                @can('user-delete')
                    <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center text-white"
                            data-coreui-toggle="modal" data-coreui-target="#deleteUserModal"
                            data-user-id="{{ $user->id }}" data-username="{{ $user->username }}"
                            data-fullname="{{ $user->first_name . ' ' . $user->last_name }}"
                            data-is-merchant="{{ $user->isMerchant() }}"
                            title="{{ __('Delete User') }}">
                        <i class="fa fa-user-times"></i>
                    </button>
                @endcan

            </div>

            {{-- Wallet List --}}
            <ul class="list-group list-group-flush mt-4">
                @foreach($user->activeWallets() as $wallet)
                    @php
                        $amountColor = $wallet->latestTransaction?->amount_flow->color($wallet->latestTransaction->status);
                        $amountSign = $wallet->latestTransaction?->amount_flow->sign($wallet->latestTransaction->status);
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center border rounded mt-2 bg-light">
                        <div class="d-flex align-items-center text-start">
                            {{-- Wallet Icon --}}
                            <img src="{{ asset($wallet->currency->flag) }}"
                                 alt="{{ $wallet->currency->code }}"
                                 class="me-2 avatar avatar-md rounded">

                            {{-- Wallet Details --}}
                            <div>
                                <strong class="text-uppercase">{{ __(':value WALLET', ['value' => $wallet->currency->code]) }}
                                    @if($wallet->currency->default) <span class="badge badge-sm bg-success">{{ __('Default') }}</span> @endif
                                </strong>
                                <div class="text-muted small">
                                    @if($wallet->latestTransaction)
                                        <p class="small mb-0">
                                            <span class="text-muted">{{ __('Recent:') }}</span>
                                            <span class="fw-bold {{ $amountColor }}">
                                                {{ $amountSign . getSymbol($wallet->currency->code) . number_format($wallet->latestTransaction->amount, 2) }}
                                            </span>
                                            <span class="text-muted">{{ __('via') }}</span>
                                            <span class="fw-bold text-{{ $wallet->latestTransaction->trx_type->badgeColor() }}">
                                                {{ $wallet->latestTransaction->trx_type->label() }}
                                            </span>
                                        </p>
                                    @else
                                        <p class="small mb-0 text-muted">{{ __('No recent activity.') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Wallet Balance --}}
                        <div class="text-end">
                            <div class="fw-bold">{{  $wallet->currency->symbol.number_format($wallet->balance, 4) }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- User Control Section --}}
            @can('user-features-manage')
                @include('backend.user.partials._user_control')
            @endcan


        </div>
    </div>
</div>