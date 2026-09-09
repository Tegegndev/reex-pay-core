@php use App\Enums\TrxType; @endphp
@php use App\Enums\TrxStatus; @endphp
@extends('backend.deposit.index')
@section('title',  __('Manual Deposit Requests'))
@section('deposit_header')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-3">
            <div class="mb-3 mb-lg-0">
                <h1 class="h3 fw-bold mb-1">
                    <x-icon name="request" height="28" width="28" class="me-1 text-primary align-middle"/>
                    {{ __('Manual Deposit Requests') }}
                </h1>
                <div class="text-muted small lh-sm">
                    <span class="d-block">{{ __('Review, approve, or reject all manual deposit requests submitted by users.') }}</span>
                </div>
            </div>
        </div>
    </div>
@endSection
@section('deposit_content')
    <div class="card-body">
        <div class="d-flex justify-content-end mb-3">
            <form action="{{ route('admin.deposit.manual-request') }}" method="GET" class="row g-2 g-md-3">
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
            <table class="table caption-top mb-0">
                <thead class="table-light fw-semibold text-nowrap">
                <tr class="align-middle">
                    <th>{{ __('User | TXN ID') }}</th>
                    <th>{{ __('Amount | Fee') }}</th>
                    <th>{{ __('Description | Provider') }}</th>
                    <th>{{ __('Time') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($depositRequests as $transaction)
                    <tr class="align-middle">
                        <td>
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle shadow-sm me-2" width="36" height="36"
                                     src="{{ asset($transaction->user->avatar_alt) }}" alt="User Avatar">
                                <div>
                                    <div class="text-nowrap">{{ $transaction->user->name }}</div>
                                    <div class="small text-muted text-uppercase">{{ strtoupper($transaction->trx_id) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-primary-emphasis fw-bold">
                                {{ $transaction->amount . ' ' . $transaction->currency }}
                            </div>
                            <div class="small text-muted">{{ $transaction->fee . ' ' . $transaction->currency }}</div>
                        </td>
                        <td>
                            <div>{{ $transaction->description }}</div>
                            <div class="small text-muted">{{ ucwords($transaction->provider) }}</div>
                        </td>
                        <td>
                            <div>{{ $transaction->created_at->format('Y-m-d H:i') }}</div>
                            <div class="small text-muted">{{ $transaction->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary" data-coreui-toggle="modal"
                                    data-coreui-target="#review-{{ $transaction->id }}">
                                <i class="fa-duotone fa-arrow-right-from-bracket"></i>
                                {{ __('Review Request') }}
                            </button>

                            @include('backend.deposit.partials._review_modal')

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <h4 class="text-muted">{{ __('No deposit requests found.') }}</h4>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $depositRequests->links() }}
        </div>
    </div>

@endsection
@push('scripts')

@endpush