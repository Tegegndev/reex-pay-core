<div class="modal fade" id="delete_fee_setting_modal" tabindex="-1" aria-labelledby="deleteFeeSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFeeSettingModalLabel">{{ __('Delete Fee Setting') }}</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="delete_fee_setting_form">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="mb-0">{{ __('Are you sure you want to delete this fee setting?') }}</p>
                        <div class="fw-bold mt-2" id="feeSettingName"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
