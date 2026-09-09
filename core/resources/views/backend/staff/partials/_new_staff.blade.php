<div class="modal fade" id="new_staff_modal" aria-hidden="true" aria-labelledby="logoutmodal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutmodal">{{ __('Add New Staff') }}</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.staff.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="avatar">{{ __('Avatar') }}</label>
                            <x-img name="avatar"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="name">{{ __('Staff Name') }}</label>
                            <input class="form-control" type="text" name="name" placeholder="Staff Name" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="email">{{ __('Staff Email') }}</label>
                            <input class="form-control" type="email" name="email" placeholder="Staff Email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="password">{{ __('Staff Password') }}</label>
                            <input class="form-control" type="password" name="password" placeholder="Staff Password" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                            <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="form-label" for="role">{{ __('Select Role') }}</label>
                            <select class="form-select" name="role" required>
                                <option selected disabled>{{ __('Select Role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ title($role) }}</option>
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
                            <x-icon name="check" height="20"/> {{ __('Create Staff') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
