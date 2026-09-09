@extends('frontend.layouts.user.index')
@section('title', __('Cardholders'))
@section('content')
    <div class="single-form-card">
        <div class="card-title d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
            <h6 class="text-white mb-2 mb-md-0">{{ __('Cardholders') }}</h6>
            <div class="d-flex gap-2 flex-row">
                <a class="btn btn-light-primary btn-sm" href="{{ route('user.virtual-card.index') }}">
                    <i class="fa-solid fa-list me-1"></i>{{ __('My Cards') }}
                </a>
                <a class="btn btn-light-success btn-sm" href="{{ route('user.virtual-card.cardholders.create') }}">
                    <i class="fa-solid fa-plus-circle"></i> {{ __('Add New') }}
                </a>
            </div>
        </div>
        <div class="card-main">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Info') }}</th>
                        <th>{{ __('Phone & Country') }}</th>
                        <th class="text-nowrap">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($cardholders as $cardholder)
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    @if($cardholder->card_type->value === \App\Enums\VirtualCard\CardholderType::BUSINESS->value && $cardholder->business)
                                        {{ $cardholder->business->business_name }}
                                        <span class="badge badge-sm bg-{{ $cardholder->card_type->class() }} text-white">{{ $cardholder->card_type->label() }}</span>
                                    @else
                                        {{ $cardholder->full_name }}
                                        <span class="badge badge-sm bg-{{ $cardholder->card_type->class() }} text-white">{{ $cardholder->card_type->label() }}</span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    @if($cardholder->card_type->value === \App\Enums\VirtualCard\CardholderType::BUSINESS->value && $cardholder->business)
                                        {{ $cardholder->business->contact_email }}
                                    @else
                                        {{ $cardholder->email }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    @if($cardholder->card_type->value === \App\Enums\VirtualCard\CardholderType::BUSINESS->value && $cardholder->business)
                                        {{ $cardholder->business->contact_phone }}
                                    @else
                                        {{ $cardholder->mobile }}
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    @if($cardholder->card_type->value === \App\Enums\VirtualCard\CardholderType::BUSINESS->value && $cardholder->business)
                                        {{ $cardholder->business->country }}
                                    @else
                                        {{ $cardholder->country }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge text-white bg-{{ $cardholder->status->badgeColor() }}">
                                    {{ $cardholder->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group align-items-center" role="group" aria-label="Cardholder Actions">
                                    @if($cardholder->status->isPending())
                                        <a href="{{ route('user.virtual-card.cardholders.show', $cardholder) }}" class="btn btn-light-primary btn-sm d-inline-flex justify-content-center align-items-center p-0 text-center" style="width:40px; height:40px;" title="{{ __('View') }}">
                                            <x-icon name="eye" height="20" width="20"/>
                                        </a>
                                        <a href="{{ route('user.virtual-card.cardholders.edit', $cardholder) }}" class="btn btn-light-secondary btn-sm d-inline-flex justify-content-center align-items-center p-0 text-center" style="width:40px; height:40px;" title="{{ __('Edit') }}">
                                            <x-icon name="edit" height="20" width="20"/>
                                        </a>
                                        <button type="button"
                                                class="btn btn-light-danger btn-sm d-inline-flex justify-content-center align-items-center p-0 text-center"
                                                style="width:40px; height:40px;"
                                                title="{{ __('Delete') }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteCardholderModal"
                                                data-cardholder-id="{{ $cardholder->id }}">
                                            <x-icon name="delete" height="20" width="20"/>
                                        </button>
                                    @elseif($cardholder->status->isApproved())
                                        <a href="{{ route('user.virtual-card.cardholders.show', $cardholder) }}" class="btn btn-light-primary btn-sm d-inline-flex justify-content-center align-items-center p-0 text-center" style="width:40px; height:40px;" title="{{ __('View') }}">
                                            <x-icon name="eye" height="20" width="20"/>
                                        </a>
                                    @elseif($cardholder->status->isRejected())
                                        <a href="{{ route('user.virtual-card.cardholders.show', $cardholder) }}" class="btn btn-light-primary btn-sm d-inline-flex justify-content-center align-items-center p-0 text-center" style="width:40px; height:40px;" title="{{ __('View') }}">
                                            <x-icon name="eye" height="20" width="20"/>
                                        </a>
                                        <button type="button"
                                                class="btn btn-light-danger btn-sm d-inline-flex justify-content-center align-items-center p-0 text-center"
                                                style="width:40px; height:40px;"
                                                title="{{ __('Delete') }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteCardholderModal"
                                                data-cardholder-id="{{ $cardholder->id }}">
                                            <x-icon name="delete" height="20" width="20"/>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <x-icon name="no-data-found" height="60"/>
                                <div class="mt-2">@lang('No cardholders found.')</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $cardholders->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @include('frontend.user.virtual_card.partials._delete_cardholder_modal')
@endsection

@push('scripts')
<script>
    'use strict';
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteCardholderModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var cardholderId = button.getAttribute('data-cardholder-id');
        var form = document.getElementById('deleteCardholderForm');
        form.action = "{{ route('user.virtual-card.cardholders.destroy', ':id') }}".replace(':id', cardholderId);
    });
});
</script>
@endpush
