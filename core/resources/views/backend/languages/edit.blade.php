<form method="POST" action="{{ route('admin.language.update', ['language' => $language->id]) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf

    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="flag" class="form-label">{{ __('Language Flag') }}</label>
            <x-img name="flag" old="{{ $language->flag }}" :ref="'coevs-language-flag'"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <div>
                <label class="form-label" for="language_name">{{ __('Language Name') }}</label>
                <input class="form-control" name="language_name" id="language_name" value="{{ $language->name }}"  type="text" placeholder="Enter Language name" required>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div>
                <label class="form-label"  for="language_code">{{ __('Language Code') }}</label>
                <input class="form-control" name="language_code" id="language_code" @disabled($language->code == 'en') value="{{ $language->code }}"  type="text" placeholder="EnterLanguage Code" required>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-4 mb-3 mt-2">
            <div class="card">
                <div class="form-check form-switch card-body p-2  border rounded d-flex align-items-center">
                    <label class="form-check-label flex-grow-1" for="default">{{ __('Default') }}</label>
                    <input class="form-check-input coevs-switch me-2 flex-shrink-0" type="checkbox" value="1" name="is_default" @checked($language->is_default)  id="default">
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mt-2">
            <div class="card">
                <div class="form-check form-switch card-body p-2  border rounded d-flex align-items-center">
                    <label class="form-check-label flex-grow-1" for="status">{{ __('Status') }}</label>
                    <input class="form-check-input coevs-switch me-2 flex-shrink-0" type="checkbox" role="switch" name="status" @checked($language->status) value="1" id="status">
                </div>
            </div>
        </div>

    </div>

    <div class="mt-3">
        <button class="btn btn-primary" type="submit"><x-icon name="check" height="20"/> {{ __('Save Now') }}</button>
    </div>
</form>
