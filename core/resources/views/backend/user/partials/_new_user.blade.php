<div class="modal fade" id="new_user_modal" aria-hidden="true" aria-labelledby="logoutmodal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usermodal">{{ __('Add New User') }}</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.user.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="avatar">{{ __('Avatar') }}</label>
                            <x-img name="avatar"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="name">{{ __('First Name') }}</label>
                            <input class="form-control" type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="last_name">{{ __('Last Name') }}</label>
                            <input class="form-control" type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="name">{{ __('Username') }}</label>
                            <input class="form-control" type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="email">{{ __('Email') }}</label>
                            <input class="form-control" type="email" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="country">{{ __('Country') }}</label>
                            <select class="form-select" id="countrySelect" name="country" required>
                                <option selected disabled>{{ __('Select Country') }}</option>
                                @foreach(getJsonData('country_codes') as $country)
                                    <option value="{{ $country['code'].':'.$country['dial_code'] }}">
                                        {{ title($country['name'])  }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="phone">{{ __('Phone Number') }}</label>
                            <div class="input-group">
                                <span class="input-group-text" id="phone"></span>
                                <input type="text" class="form-control" placeholder="phone" name="phone" aria-label="phone" aria-describedby="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="password">{{ __('Password') }}</label>
                            <input class="form-control" type="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="confirm_password">{{ __('Confirm Password') }}</label>
                            <input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="role">{{ __('User Type') }}</label>
                            <select class="form-select" name="role" required>
                                <option selected disabled>{{ __('Select User Type') }}</option>
                                @foreach(\App\Enums\UserRole::options() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="status">{{ __('Status') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input coevs-switch" type="checkbox" name="status" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary" type="submit">
                            <x-icon name="check" height="20"/> {{ __('Create User') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
