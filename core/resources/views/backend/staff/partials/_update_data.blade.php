<form action="{{ route('admin.staff.update', $staff->id) }}" method="post" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="avatar">{{ __('Avatar') }}</label>
            <x-img name="avatar" :old="$staff->avatar" :ref="'coevs-profile-img'"/>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="name">{{ __('Staff Name') }}</label>
            <input class="form-control" type="text" name="name" value="{{ $staff->name }}" placeholder="Staff Name" required>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="email">{{ __('Staff Email') }}</label>
            <input class="form-control" type="email" name="email" value="{{ $staff->email }}" placeholder="Staff Email" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="password">{{ __('Staff Password') }}</label>
            <input class="form-control" type="password" name="password" placeholder="Staff Password">
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm Password">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="role">{{ __('Select Role') }}</label>
            <select class="form-select" name="role" required>
                <option selected disabled>{{ __('Select Role') }}</option>
                @foreach($roles as $role)
                    <option @selected($staffRole == $role) value="{{ $role }}">{{ title($role) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <label class="form-label" for="status">{{ __('Status') }}</label>
            <div class="form-check form-switch">
                <input class="form-check-input coevs-switch" type="checkbox" name="status" value="1" @checked($staff->status)>
            </div>
        </div>
    </div>
    <div class="text-end">
        <button class="btn btn-primary" type="submit">
            <x-icon name="check" height="20"/> {{ __('Update Staff') }}
        </button>
    </div>
</form>
