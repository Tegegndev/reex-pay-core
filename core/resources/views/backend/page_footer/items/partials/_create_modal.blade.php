<div class="modal fade" id="create-footer-item-modal" tabindex="-1" aria-labelledby="createFooterItemLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content shadow-sm rounded-3">
			<div class="modal-header">
				<h5 class="modal-title" id="createFooterItemLabel">{{ __('Add New Footer Item') }}</h5>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				@include('backend.page_footer.items.partials._form_data')
			</div>
		</div>
	</div>
</div>
