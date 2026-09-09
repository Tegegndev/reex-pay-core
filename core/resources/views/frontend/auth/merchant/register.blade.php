@extends('frontend.layouts.auth')
@section('title', __('Merchant Register'))
@section('auth-content')
    
    @php
        $myCurrentLocation = getLocation();
        $allCountries = getCountries();
    @endphp
    
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-gray">
        <div class="bg-white col-11 col-sm-10 col-md-8 col-lg-5 col-xl-4 p-4 rounded style-shadow">
            <div id="RegisterContainer" class="px-3 py-4">
                <div class="mb-4 text-center">
                    <img src="{{ asset(setting('logo')) }}" alt="Logo" class="img-fluid mb-2" style="max-width: 130px;">
                    <h4 class="mt-3">{{ __('Create your merchant account') }}</h4>
                    <p class="text-muted small">{{ __('Build your business with REEXPAY') }}</p>
                    <div class="mt-2 mb-3">
                        <span class="badge bg-success p-2">
                            <i class="fa-duotone fa-store me-1"></i> {{ __('Business Account') }}
                        </span>
                    </div>
                </div>
                
                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <strong>{{ $error }}</strong><br>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="alert alert-info mb-4">
                    <i class="fas fa-lightbulb me-2"></i> 
                    {{ __('Merchant benefits: Accept payments, generate QR codes, access API, reduced fees') }}
                </div>
                
                {{-- Registration Form --}}
                <form action="{{ route('merchant.register') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        {{-- Personal Information --}}
                        <div class="col-12 mb-2">
                            <h6 class="border-bottom pb-2 text-success"><i class="fas fa-user me-2"></i>{{ __('Personal Information') }}</h6>
                        </div>
                        
                        <div class="col-md-6 col-12">
                            <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First Name" required value="{{ old('first_name') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last Name" required value="{{ old('last_name') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="username" class="form-label">{{ __('Username') }}</label>
                            <input type="text" name="username" class="form-control" id="username" placeholder="Choose a username" required value="{{ old('username') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Email Address" required value="{{ old('email') }}">
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="country">{{ __('Country') }}</label>
                            <select class="form-select" id="countrySelect" name="country" required>
                                <option selected disabled>{{ __('Select Country') }}</option>
                                @foreach($allCountries as $country)
                                    <option value="{{ $country['code'].':'.$country['dial_code'] }}" @selected(old('country',  $myCurrentLocation['dial_code'] ) == $country['dial_code']) >
                                        {{ title($country['name'])  }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="phone">{{ __('Phone Number') }}</label>
                            <div class="input-group">
                                <span class="input-group-text" id="phone">{{ $myCurrentLocation['dial_code'] }}</span>
                                <input type="text" class="form-control" placeholder="Phone" name="phone" aria-label="phone" aria-describedby="phone" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm Password" required>
                        </div>
                        
                        {{-- Business Information --}}
                        <div class="col-12 mt-4 mb-2">
                            <h6 class="border-bottom pb-2 text-success"><i class="fas fa-store me-2"></i>{{ __('Business Information') }}</h6>
                        </div>
                        
                        <div class="col-12">
                            <label for="business_name" class="form-label">{{ __('Business Name') }}</label>
                            <input type="text" name="business_name" class="form-control" id="business_name" placeholder="Your business name" required value="{{ old('business_name') }}">
                        </div>
                        <div class="col-12">
                            <label for="business_address" class="form-label">{{ __('Business Address') }}</label>
                            <input type="text" name="business_address" class="form-control" id="business_address" placeholder="Business address" required value="{{ old('business_address') }}">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mt-4">
                        <i class="fa-light fa-right-to-bracket"></i> {{ __('Register as Merchant') }}
                    </button>
                </form>
            </div>
            
            <p class="text-center mt-4">
                {{ __('Already have a merchant account?') }}
                <a href="{{ route('merchant.login') }}" class="text-success text-decoration-none">{{ __('Sign In') }}</a>
            </p>
            
            {{-- User Registration Link --}}
            <div class="border-top mt-4 pt-3">
                <p class="text-center mb-0 sm-mb-text">
                    {{ __("Want to register as a regular user?") }}
                    <a href="{{ route('user.register') }}" class="text-decoration-none text-primary fw-semibold">
                        <i class="fa-duotone fa-user me-1"></i> {{ __('User Registration') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
