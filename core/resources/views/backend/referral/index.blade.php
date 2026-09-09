@extends('backend.layouts.app')
@section('title', __('Referrals'))
@section('content')
    <div class="clearfix my-4 ">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold" >{{ __('Referrals') }}</h2>
                <p class="text-muted mb-0 small">{{ __('Easily manage and customize referral rewards, levels, and settings below.') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.referral.card.content') }}" class="btn btn-info d-flex align-items-center text-white">
                    <i class="fa-solid fa-file-lines me-1"></i>
                    <span class="d-none d-sm-inline">{{ __('Referral Content') }}</span>
                </a>
                <a href="#new-reward-modal" data-coreui-toggle="modal" class="btn btn-primary d-flex align-items-center">
                    <x-icon name="add" class="icon"/>
                    <span class="d-none d-sm-inline ms-1">{{ __('Add Reward') }}</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row bg-white g-3 rounded-2 p-3">
        @foreach($referralRewards as $rewardType => $rewards)
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light  border-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ __(':name Rewards', ['name' => ucwords($rewardType)]) }}</h5>
                            <p class="small text-muted mb-0">{{ __('Manage and customize reward levels below.') }}</p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div class="form-check form-switch">
                                <input
                                        id="reward-switch-{{ $rewardType }}"
                                        class="form-check-input coevs-switch"
                                        type="checkbox"
                                        name="status"
                                        data-type="{{ $rewardType }}"
                                        value="1"
                                        @checked(setting($rewardType.'_rewards'))
                                        aria-label="{{ __('Toggle status for :rewardType', ['rewardType' => $rewardType]) }}"
                                        data-coreui-toggle="tooltip"
                                        data-coreui-placement="top"
                                        title="{{ __('If disabled, no rewards will be given out for :rewardType actions.', ['rewardType' => $rewardType]) }}">
                            </div>
                        </div>


                    </div>
                    <div class="card-body ">
                        <div class="list-group">
                            @foreach($rewards as $reward)
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-light border rounded-1 mb-3">
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0 me-3">{{ __('Level :count', ['count' => $reward->level]) }}</h6>
                                        <span class="badge rounded-pill bg-success text-white px-3 py-2">{{ $reward->percentage }}%</span>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary edit-modal" data-edit-url="{{ route('admin.referral.edit', ['id' => $reward->id]) }}">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger text-white delete" data-url="{{ route('admin.referral.delete', ['type' => $rewardType, 'id' => $reward->id]) }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @include('backend.referral.partials._new_reward_modal')
    @include('backend.referral.partials._update_reward_modal')

@endsection
@push('scripts')
    <script>
        "use strict";
        $(document).on('change', '.coevs-switch', function () {
            let type = $(this).data('type');
            let status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.referral.status-update', ['type' => ':type', 'status' => ':status']) }}"
                    .replace(':type', type)
                    .replace(':status', status),
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        notifyEvs('success', response.message);
                    }
                }
            });
        });
        editFormByModal('edit-reward-modal', 'edit-reward-data');
    </script>
@endpush