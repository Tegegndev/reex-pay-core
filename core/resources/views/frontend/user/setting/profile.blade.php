@extends('frontend.user.setting.index')
@section('title', __('Settings'))

@section('user_setting_content')
    
    {{-- Email Verification Alert --}}
    @if($user->email_verified_at === null)
        <div class="alert alert-danger m-2 d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ __('Please verify your email') }}</span>
            <a href="{{ route('user.settings.verify-email') }}" class="alert-link text-success ms-2">
                {{ __('Verify Now') }}!
            </a>
        </div>
    @endif
    
    {{-- Profile Update Form --}}
    <form action="{{ route('user.settings.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            
            <h5 class="mb-4">{{ __('My Profile') }}</h5>
            
            {{-- Avatar --}}
            <div class="mb-4">
                <label for="avatar" class="form-label">{{ __('Profile Image') }}</label>
                <x-img name="avatar" :old="$user->avatar" :ref="'avatar'" :name="'avatar'"/>
            </div>
            
            {{-- Personal Information --}}
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="form-control"
                        value="{{ old('first_name', $user->first_name) }}"
                    >
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="form-control"
                        value="{{ old('last_name', $user->last_name) }}"
                    >
                </div>
            </div>
            
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="username" class="form-label">{{ __('Username') }}</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        value="{{ old('username', $user->username) }}"
                    >
                </div>
                <div class="col-md-6">
                    <label for="gender" class="form-label">{{ __('Gender') }}</label>
                    <select class="form-select" id="gender" name="gender">
                        @foreach(\App\Enums\Gender::cases() as $gender)
                            <option value="{{ $gender->value }}" {{ old('gender', $user->gender) === $gender ? 'selected' : '' }}>
                                {{ $gender->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Business Information (Only for Merchants) --}}
            @if($user->role === \App\Enums\UserRole::MERCHANT)
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="business_name" class="form-label">{{ __('Business Name') }}</label>
                        <input
                            type="text"
                            id="business_name"
                            name="business_name"
                            class="form-control"
                            value="{{ old('business_name', $user->business_name) }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="business_address" class="form-label">{{ __('Business Address') }}</label>
                        <input
                            type="text"
                            id="business_address"
                            name="business_address"
                            class="form-control"
                            value="{{ old('business_address', $user->business_address) }}"
                        >
                    </div>
                </div>
            @endif
            
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="birthday" class="form-label">{{ __('Birthday') }}</label>
                    <input
                        type="date"
                        id="birthday"
                        name="birthday"
                        class="form-control"
                        value="{{ old('birthday', $user->birthday) }}"
                    >
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">
                        {{ __('Email') }}
                        @if($user->email_verified_at)
                            <span class="badge bg-success ms-1">{{ __('Verified') }}</span>
                        @else
                            <span class="badge bg-danger ms-1">{{ __('Not Verified') }}</span>
                        @endif
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                    >
                </div>
            </div>
            
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone', $user->phone) }}"
                    >
                </div>
                <div class="col-md-6">
                    <label for="postal_code" class="form-label">{{ __('Postal Code') }}</label>
                    <input
                        type="text"
                        id="postal_code"
                        name="postal_code"
                        class="form-control"
                        value="{{ old('postal_code', $user->postal_code) }}"
                    >
                </div>
            </div>
            
            {{-- Location --}}
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="country" class="form-label">{{ __('Country') }}</label>
                    <input
                        type="text"
                        id="country"
                        class="form-control"
                        value="{{ $user->country }}"
                        disabled
                    >
                </div>
                <div class="col-md-6">
                    <label for="state" class="form-label">{{ __('State') }}</label>
                    <input
                        type="text"
                        id="state"
                        name="state"
                        class="form-control"
                        value="{{ old('state', $user->state) }}"
                        placeholder="{{ __('Enter state') }}"
                    >
                </div>
            </div>
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="city" class="form-label">{{ __('City') }}</label>
                    <input
                        type="text"
                        id="city"
                        name="city"
                        class="form-control"
                        value="{{ old('city', $user->city) }}"
                        placeholder="{{ __('Enter city') }}"
                    >
                </div>
            </div>
            
            {{-- Address --}}
            <div class="mb-3">
                <label for="address" class="form-label">{{ __('Address') }}</label>
                <textarea
                    class="form-control rounded"
                    name="address"
                    id="address"
                    rows="4"
                    placeholder="{{ __('Enter your address') }}"
                >{{ old('address', $user->address) }}</textarea>
            </div>
            
            {{-- Submit --}}
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" height="20" class="me-1" /> {{ __('Update Profile') }}
                </button>
            </div>
        </div>
    </form>

@endsection