<div class="modal fade" id="updateTranslate" tabindex="-1" role="dialog" aria-labelledby="modal-default" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="h6 modal-title">{{ __('Edit Keyword') }}</h2>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.language.translate-update') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control key-key" name="key">
                    <input type="hidden" class="form-control key-group" name="group">
                    <input type="hidden" class="form-control key-language" name="language">


                    <div class="site-input-groups mb-3">
                        <label class="form-label key-label"></label>
                        <input type="text" class="form-control key-value" name="value">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"> <x-icon name="check" height="20" width="20" class="me-1" /> {{ __('Save Now') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
