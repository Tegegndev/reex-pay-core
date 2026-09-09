@extends('backend.virtual_card.index')
@section('title', __('Virtual Cards'))

@section('virtual_card_header')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start">
            {{ __('All Virtual Cards') }}
        </div>
    </div>
@endsection

@section('virtual_card_content')
    <div class="card-body">
        <div class="d-flex justify-content-end mb-3">
            <form action="{{ route('admin.virtual-card.list') }}" method="GET" class="row g-2 g-md-3">
                <div class="col-md-6 col-xl-auto">
                    <x-form.select name="status"
                        :options="$statuses"
                        :selected="request('status')"
                        :includeBlank="true"
                    />
                </div>
                <div class="col-md-6 col-xl-auto">
                    <x-form.select name="provider_id"
                        :options="$providers->pluck('name', 'id')->toArray()"
                        :selected="request('provider_id')"
                        :includeBlank="true"
                    />
                </div>
                <div class="col-md-6 col-xl-auto">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="{{ __('Search by card, user, email...') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Card') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Provider') }}</th>
                        <th>{{ __('Wallet') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Issued') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cards as $card)
                    <tr>
                        <td>
                            <span class="badge bg-success">•••• {{ $card->last4 }}</span><br>
                            <small class="text-muted">{{ $card->name_on_card }}</small>
                        </td>
                        <td>
                            {{ $card->user->first_name }} {{ $card->user->last_name }}<br>
                            <small class="text-muted">{{ $card->user->email }}</small>
                        </td>
                        <td>{{ $card->provider->name ?? '-' }}</td>
                        <td>{{ $card->wallet->currency->code ?? '-' }}</td>
                        <td><span class="badge bg-{{ $card->status->badgeColor() }}">{{ $card->status->label() }}</span></td>
                        <td>{{ $card->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa-regular fa-inbox fa-3x mb-3"></i>
                                <h5>{{ __('No cards found') }}</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($cards->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $cards->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection