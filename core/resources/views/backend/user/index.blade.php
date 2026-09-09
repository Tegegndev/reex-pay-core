@extends('backend.layouts.app')
@section('title', $title)
@section('content')

    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start">{{ $title }}</div>
        @can('user-create')
            <a href="#new_user_modal" data-coreui-toggle="modal" class="btn btn-primary float-end">
                <x-icon name="add" class="icon"/> {{ __('Add New') }}
            </a>
        @endcan
       
    </div>
    
    <div class="card border-0 mb-4">
        <div class="card-body">
            @include('backend.user.partials._filters')
            {{-- User Table --}}
            <div class="table-responsive rounded">
                @if($users->isNotEmpty())
                    <table class="table mb-0 caption-top">
                        <thead>
                        <tr>
                            <th>{{ __('Name | Username') }}</th>
                            <th>{{ __('Email | Status') }}</th>
                            <th>{{ __('KYC Info') }}</th>
                            <th>{{ __('Joined At') }}</th>
                            <th>{{ __('Last Login') }}</th>
                            @can('user-manage')
                                 <th>{{ __('Action') }}</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            @php
                                $avatarData = getUserAvatarDetails($user->first_name, $user->last_name);

                                $kycSubmission = $user->kycSubmission;
                                $kycStatus = $kycSubmission?->status ?? null;

                                $badgeColor = $kycStatus?->color() ?? 'danger';
                                $statusText = $kycStatus?->label() ?? __('Not Submitted');
                                $kycTitle   = $kycSubmission?->kycTemplate->title ?? __('No Submission');
                            @endphp
                            <tr class="align-middle">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md me-2">
                                            @if($user->avatar)
                                                <img class="avatar-img" src="{{ asset($user->avatar) }}" height="40"
                                                     alt="User Avatar">
                                            @else
                                                <div class="avatar avatar-md {{ $avatarData['class'] }} text-white">
                                                    {{ $avatarData['initials'] }}
                                                </div>
                                            @endif
                                            <span class="avatar-status bg-{{ $user->status->color() }}"></span>
                                        </div>
                                        <div>
                                            <div class="text-nowrap"> {{ title($user->name) }}</div>
                                            <div class="small text-body-secondary text-nowrap">
                                                {{ $user->username }}
                                                <span class="text-uppercase badge bg-{{ $user->role->color() }}">{{ $user->role->title() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ maskSensitive($user->email)  }}</div>
                                    <div class="small text-muted">
                                        <span class="text-uppercase badge bg-{{ $user->email_verified_at ? 'success' : 'danger' }}">
                                            {{ $user->email_verified_at ? __('Verified') : __('Unverified') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $kycSubmission ? __('Document Type: :name', ['name' => $kycTitle]) : __('No KYC document submitted') }}
                                    </div>
                                    <div class="small text-muted">
                                        <span class="text-uppercase badge bg-{{ $badgeColor }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $user->created_at->format('Y-m-d H:i') }}</div>
                                    <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div>
                                        {{ optional($user->latestLoginActivity)->login_at?->format('Y-m-d H:i') ?? '--' }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ optional($user->latestLoginActivity)->login_at?->diffForHumans() ?? '--' }}
                                    </div>
                                </td>

                                @can('user-manage')
                                    <td>
                                        <a href="{{ route('admin.user.manage', ['username' => $user->username, 'param' => 'statistics']) }}" class="btn btn-primary">
                                            <x-icon name="manage" height="20"/>
                                            {{ __('Manage') }}
                                        </a>
                                    </td>
                                @endcan
                                
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <x-icon name="no-data-found" height="200"/>
                        <h5 class="text-muted mt-2">{{ __('No users found') }}</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create New User Modal --}}
    @can('user-create')
         @include('backend.user.partials._new_user')
    @endcan

@endsection

@push('scripts')
    <script>
        "use strict";
        $('#countrySelect').on('change', function (e) {
            e.preventDefault();
            var country = $(this).val();
            $('#phone').html(country.split(":")[1]);
        });
    </script>
@endpush
