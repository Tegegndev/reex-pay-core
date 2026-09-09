@extends('frontend.layouts.user.index')
@section('title', __('My Virtual Card Requests'))
@section('content')
    <div class="single-form-card">
        <div class="card-title mb-0 d-flex flex-column flex-md-row justify-content-between">
            <h6 class="text-white mb-2 mb-md-0">{{ __('My Virtual Card Requests') }}</h6>
            <div class="d-flex gap-2 flex-row">
                <a class="btn btn-light-primary btn-sm" href="{{ route('user.virtual-card.index') }}"><i class="fa-solid fa-list"></i>{{ __('My Cards') }}</a>
                <button type="button" class="btn btn-light-success btn-sm" data-bs-toggle="modal" data-bs-target="#requestVirtualCardModal">
                    <i class="fa-solid fa-credit-card me-2"></i>
                    {{ __('Request New') }}
                </button>
            </div>
        </div>
        <div class="card-main">
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="background:transparent;">
                    <thead class="bg-light text-secondary border-bottom">
                        <tr>
                            <th scope="col" class="fw-semibold">#{{ __('Request ID') }}</th>
                            <th scope="col" class="fw-semibold">{{ __('Wallet') }}</th>
                            <th scope="col" class="fw-semibold">{{ __('Status') }}</th>
                            <th scope="col" class="fw-semibold">{{ __('Requested') }}</th>
                            <th scope="col" class="fw-semibold">{{ __('Admin Note') }}</th>
                            <th scope="col" class="fw-semibold">{{ __('Card Info') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($cardRequests as $req)
                        <tr style="border-bottom:1px solid #f0f2f5;">
                            <td><span class="text-muted">#{{ $req->uuid }}</span></td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 small">{{ $req->wallet->currency->code ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $req->status->badgeColor() }} text-white rounded-pill px-3 py-2 small">{{ $req->status->label() }}</span>
                            </td>
                            <td><span class="text-dark">{{ $req->created_at->format('M d, Y') }}</span></td>
                            <td>
                                @if($req->admin_note)
                                    @php $note = $req->admin_note; @endphp
                                    @if(Str::length($note) > 20)
                                        <span class="text-danger fw-medium" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $note }}">
                                            {{ Str::limit($note, 20) }} <i class="fa-solid fa-circle-info ms-1"></i>
                                        </span>
                                    @else
                                        <span class="text-danger fw-medium">{{ $note }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">{{ __('—') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($req->card)
                                    <span class="badge bg-success text-white rounded-pill px-3 py-2 small">
                                        <i class="fa-regular fa-credit-card"></i>
                                        •••• {{ $req->card->last4 }}
                                    </span>
                                    <div class="small text-muted">{{ $req->card->expiry_month }}/{{ $req->card->expiry_year }}</div>
                                @else
                                    <span class="text-muted">{{ __('—') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fa-solid fa-circle-info"></i> {{ __('No virtual card requests found.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{--Add Card Request Modal--}}
    @include('frontend.user.virtual_card.request.partials._add_card_request_modal')
@endsection
