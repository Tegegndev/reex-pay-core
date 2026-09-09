<div class="col-12 col-xl-12">
    <form method="POST"  action="{{ route('admin.settings.plugin.update',$plugin->id) }}">
        @method('PUT')
        @csrf
        <div class="row">
            @foreach($plugin->credentials as $field_name => $credential)
                @if($field_name != 'fields')
                    <div class="col-md-12 mb-3">
                        <label class="form-label"
                               for="{{ $field_name }}">{{ ucwords(str_replace('_', ' ', $field_name)) }}</label>
                        <input type="text" name="credentials[{{ $field_name }}]" id="{{ $field_name }}"
                               value="{{ $credential }}"
                               placeholder="Enter {{ $field_name }}" class="form-control">
                    </div>
                @endif
            @endforeach

            @includeIf('backend.settings.plugin.other_fields.'.$plugin->fields_blade,['plugin' => $plugin])

            <div class="col-md-6 mb-3 mt-1">
                <div class="card">
                    <div class="form-check form-switch card-body p-2 border rounded d-flex align-items-center">
                        <label class="form-check-label flex-grow-1" for="status">{{ __('Status') }}</label>
                        <input class="form-check-input coevs-switch me-2 flex-shrink-0" type="checkbox" role="switch"
                               name="status" @checked($plugin->status) value="1" id="status">
                    </div>
                </div>
            </div>
        </div>


        <div class="mt-3 text-end">
            <button class="btn btn-primary" type="submit"> <x-icon name="check" height="20"/> {{ __('Update Now') }}</button>
        </div>
    </form>
</div>
