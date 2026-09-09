<div class="modal fade" id="new_fee_setting_modal" tabindex="-1" aria-labelledby="newFeeSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newFeeSettingModalLabel">{{ __('Add New Fee Setting') }}</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.virtual-card.fee-settings.store') }}" method="POST" id="new_fee_setting_form">
                    @csrf
                    @include('backend.virtual_card.fee_settings.partials._form', ['feeSetting' => null])
                </form>
            </div>
        </div>
    </div>
</div>
