@extends('backend.virtual_card.index')
@section('title', __('Cardholder Management'))
@section('virtual_card_header')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start">
            {{ __('Cardholder Management') }}
        </div>
        <div class="float-end">
            <a href="{{ route('admin.virtual-card.cardholders.index') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-list me-1"></i> {{ __('All Cardholders') }}
            </a>
        </div>
    </div>
@endsection
@section('virtual_card_content')
    <div class="card-body">
        {{-- Filter & Search Bar --}}
        <form action="{{ route('admin.virtual-card.cardholders.index') }}" method="GET" class="row g-2 g-md-3 mb-3 justify-content-end">
            <div class="col-md-3 col-xl-auto">
                <x-form.select name="status" :options="$statuses ?? []" :selected="request('status')" :includeBlank="true"/>
            </div>
            <div class="col-md-4 col-xl-auto">
                <div class="input-group">
                    <input type="hidden" name="daterange" value="{{ request('daterange') }}">
                    <div id="reportrange" class="form-control d-flex align-items-center justify-content-between">
                        <i class="fa-solid fa-calendar-days me-2"></i>
                        <span class="text-nowrap flex-grow-1"></span>
                        <x-icon name="angle-down" class="text-muted flex-shrink-0"/>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-auto">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search by user, email, business...') }}">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
        </form>
        {{-- Cardholders Table --}}
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Cardholder Info') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Requested') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($cardholders as $holder)
                    <tr>
                        {{-- User Info --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $holder->user->avatar_alt }}" class="rounded-circle me-2" width="32" height="32" alt="{{ $holder->user->name ?? '-' }}">
                                <div>
                                    <div class="fw-medium">
                                        <a href="{{ route('admin.user.manage', $holder->user->username) }}" class="text-decoration-none">{{ $holder->user->name }}</a>
                                    </div>
                                    <small class="text-muted">
                                        <span class="text-uppercase badge bg-{{ $holder->user->role->color() }}">{{ $holder->user->role->title() }}</span>
                                    </small>
                                </div>
                            </div>
                        </td>
                        {{-- Cardholder Info --}}
                        <td>
                            <div class="fw-semibold">
                                @if($holder->business)
                                    {{ $holder->business->business_name }}
                                    <span class="badge bg-{{ $holder->card_type->class() }} ms-1">{{ $holder->card_type->label() }}</span>
                                @else
                                    {{ $holder->full_name }}
                                    <span class="badge bg-{{ $holder->card_type->class() }} ms-1">{{ $holder->card_type->label() }}</span>
                                @endif
                            </div>
                            <div class="small text-muted">
                                {{ $holder->email ?? $holder->user->email ?? '-' }}
                            </div>
                        </td>
                        {{-- Status --}}
                        <td>
                            <span class="badge bg-{{ $holder->status->badgeColor() }}">{{ $holder->status->label() }}</span>
                        </td>
                        {{-- Requested At --}}
                        <td>
                            <div>{{ $holder->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $holder->created_at->diffForHumans() }}</small>
                        </td>
                        {{-- Actions --}}
                        <td class="text-end">
                            <button class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#view-cardholder-{{ $holder->id }}">
                                <i class="fa-duotone fa-arrow-right-from-bracket"></i> {{ $holder->status->isPending() ? __('Manage Request') : __('Details Cardholder') }}
                            </button>
                            @include('backend.virtual_card.cardholder.partials._view_modal', ['holder' => $holder])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa-regular fa-inbox fa-3x mb-3"></i>
                                <h5>{{ __('No cardholders found') }}</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($cardholders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $cardholders->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
