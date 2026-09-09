@extends('backend.layouts.app')
@section('title', 'User Management')

@section('content')
    <div class="mb-3">
        <h4 class="fw-bold">{{ __('Manage Of :type :user_name', ['user_name' => $user->name, 'type' => $user->isMerchant() ? __('Merchant') : __('User')]) }}</h4>
    </div>

    <div class="row g-3">
        {{-- Left Side: User Info --}}
        @include('backend.user.partials._user_info')

        {{-- Right Side: Tabs/Pills for User Management --}}
        <div class="col-md-12 col-lg-8">
            <div class="card border-0 px-3 py-4">

                @include('backend.user.partials._user_manage_header')

                <div class="py-2">
                    {{-- This section will be replaced with dynamic content based on the selected tab --}}
                    @yield('user_manage_content')
                </div>
            </div>
        </div>
    </div>


    {{-- Add Balance Modal --}}
    @include('backend.user.partials._update_balance_modal')

    {{-- Notify User Modal --}}
    @include('backend.user.partials._notify_user_modal')

    {{-- Delete User Confirmation Modal --}}
    @include('backend.user.partials._delete_user_modal')

    {{-- Convert User to Merchant Modal --}}
    @include('backend.user.partials._convert_to_merchant_modal')
@endsection

@push('scripts')
    @include('backend.user.partials._user_manage_scripts')
@endpush