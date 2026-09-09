@extends('backend.user.manage')

@section('user_manage_content')
    <div class="row">
        {{-- Update Information --}}
        <div class="col-12">
            <div class="card border-0">
                <div class="card-body px-0">
                    <form action="{{ route('admin.user.update-info', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="first_name">{{ __('First Name') }}</label>
                                <input class="form-control" type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="First Name" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="last_name">{{ __('Last Name') }}</label>
                                <input class="form-control" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="Last Name">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label" for="username">{{ __('Username') }}</label>
                                <input class="form-control" type="text" name="username" value="{{ $user->username }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">{{ __('Email Address') }}</label>
                                <input class="form-control" type="email" name="email" value="{{ maskSensitive($user->email) }}" disabled>
                            </div>
                            
                            {{-- Business Information (Show if user is merchant) --}}
                            @if($user->role === \App\Enums\UserRole::MERCHANT)
                            <div class="col-md-6">
                                <label class="form-label" for="business_name">{{ __('Business Name') }}</label>
                                <input class="form-control" type="text" name="business_name" value="{{ old('business_name', $user->business_name) }}" placeholder="Business Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="business_address">{{ __('Business Address') }}</label>
                                <input class="form-control" type="text" name="business_address" value="{{ old('business_address', $user->business_address) }}" placeholder="Business Address">
                            </div>
                            @endif
                            
                            <div class="col-md-6">
                                <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                <input class="form-control" type="text" name="phone" value="{{ old('phone', maskSensitive($user->phone,'phone')) }}" placeholder="Phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="gender">{{ __('Gender') }}</label>
                                <select class="form-select" name="gender">
                                    <option value="" {{ $user->gender ? '' : 'selected' }}>{{ __('Select Gender') }}</option>
                                    @foreach(\App\Enums\Gender::cases() as $gender)
                                        <option value="{{ $gender->value }}" {{ old('gender', $user->gender) === $gender ? 'selected' : '' }}>
                                            {{ $gender->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label" for="birthday">{{ __('Birthday') }}</label>
                                <input class="form-control" type="date" name="birthday" value="{{ old('birthday', $user->birthday) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="country">{{ __('Country') }}</label>
                                <input class="form-control" type="text" disabled value="{{ $user->country }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label" for="state">{{ __('State') }}</label>
                                <input class="form-control" type="text" value="{{ $user->state }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="city">{{ __('City') }}</label>
                                <input class="form-control" type="text" value="{{ $user->city }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="postal_code">{{ __('Postal Code') }}</label>
                                <input class="form-control" type="text" value="{{ $user->postal_code }}">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label" for="address">{{ __('Address') }}</label>
                                <textarea class="form-control" name="address" rows="2" placeholder="Address">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button class="btn btn-primary" type="submit">
                                <x-icon name="check" height="20" /> {{ __('Update Information') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Update Password --}}
        <div class="col-12 mt-4">
            <div class="card border-0">
                <div class="card-body px-0">
                    <h5 class="card-title fw-semibold text-capitalize mb-4 border-bottom pb-2">
                        {{ __('Update Password') }}
                    </h5>
                    
                    <form action="{{ route('admin.user.password-update', $user->id) }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <input type="password" name="password" id="password"
                                       class="form-control" placeholder="Enter new password">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" placeholder="Confirm password">
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="check" class="me-1" height="18" /> {{ __('Update Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection