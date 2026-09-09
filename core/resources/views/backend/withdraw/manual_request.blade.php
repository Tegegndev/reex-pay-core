@php use App\Enums\TrxType; @endphp
@php use App\Enums\TrxStatus; @endphp
@extends('backend.withdraw.index')
@section('title',  __('Manual Withdraw Requests'))
@section('withdraw_header')
    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <x-icon name="request" height="28" width="28" class="me-1 text-primary align-middle"/>
                {{ __('Manual Withdraw Requests') }}
            </h1>
            <div class="text-muted small lh-sm">
                {{ __('Review, approve, or reject all manual withdraw requests submitted by users.') }}
            </div>
        </div>
    </div>
@endsection
@section('withdraw_content')
    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <form action="{{ route('admin.withdraw.manual-request') }}" method="GET" class="row g-2 g-md-3">
                    {{-- Date Range Picker --}}
                    <div class="col-md-6 col-xl-auto">
                        <div class="input-group">
                            <input type="hidden" name="daterange" value="{{ request('daterange') }}">
                            <div id="reportrange" class="form-control d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span class="text-nowrap flex-grow-1"></span>
                                </div>
                                <x-icon name="angle-down" class="text-muted flex-shrink-0"/>
                            </div>
                        </div>
                    </div>
                    {{-- Search Input --}}
                    <div class="col-md-6 col-xl-auto">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                   placeholder="{{ __('Search') }}...">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            {{-- Transactions Table --}}
            <div class="table-responsive">
                <table class="table caption-top border mb-0">
                    <thead class="table-light fw-semibold text-nowrap">
                    <tr class="align-middle">
                        <th>{{ __('User | TXN ID') }}</th>
                        <th>{{ __('Amount | Fee') }}</th>
                        <th>{{ __('Description | Provider') }}</th>
                        <th>{{ __('Time') }}</th>
                        @can('withdraw-action')
                            <th>{{ __('Action') }}</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($withdrawRequests as $transaction)
                        <tr class="align-middle">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle shadow-sm me-2" width="36" height="36"
                                         src="{{ asset($transaction->user->avatar_alt) }}" alt="User Avatar">
                                    <div>
                                        <div class="text-nowrap">{{ $transaction->user->name }}</div>
                                        <div class="small text-muted  text-uppercase">{{ strtoupper($transaction->trx_id) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-primary-emphasis fw-bold">
                                    {{ $transaction->amount . ' ' . $transaction->currency  }}
                                </div>
                                <div class="small text-muted">{{ $transaction->fee . ' ' . $transaction->currency }}</div>
                            </td>
                            <td>
                                <div>{{ $transaction->description }}</div>
                                <div class="small text-muted">{{ ucwords($transaction->provider) }}
                                </div>
                            </td>
                            <td>
                                <div>{{ $transaction->created_at->format('Y-m-d H:i') }}</div>
                                <div class="small text-muted">{{ $transaction->created_at->diffForHumans() }}</div>
                            </td>
                            @can('withdraw-action')
                                <td>
                                    <button type="button" class="btn btn-primary" data-coreui-toggle="modal"
                                            data-coreui-target="#review-{{ $transaction->id }}">
                                        <i class="fa-duotone fa-arrow-right-from-bracket"></i>
                                        {{ __('Review Request') }}
                                    </button>

                                    @include('backend.withdraw.partials._review_modal')

                                </td>
                            @endcan
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <h4 class="text-muted">{{ __('No withdraw requests found.') }}</h4>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $withdrawRequests->links() }}
            </div>
        </div>
    </div>

@endsection