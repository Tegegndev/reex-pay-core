<div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="smtpCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form method="POST" action="" id="delete-form-modal">
                    @method('DELETE')
                    @csrf

                    <div class="text-center">
                        <x-icon name="warning-2" height="60" width="60"/>
                    </div>
                    <div class="text-center">
                        <h3 class="mt-3 text-muted">{{ __('Are you sure?') }}</h3>
                        <h5 class="mt-3 text-muted">{{ __("You won't be able to revert this!") }}</h5>
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        <a type="initial" class="btn btn-primary mx-2" data-coreui-dismiss="modal" aria-label="Close">
                            <x-icon name="close-1" height="24" width="24" /> {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-danger text-white" >
                            <x-icon name="delete-2" height="24" width="24"/> {{ __('Delete Now') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
