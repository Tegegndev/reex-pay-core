<div class="modal fade" id="deleteCardholderModal" tabindex="-1" aria-labelledby="deleteCardholderModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="deleteCardholderModalLabel">@lang('Delete Cardholder')</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
			</div>
			<div class="modal-body">
				@lang('Are you sure you want to delete this cardholder? This action cannot be undone.')
			</div>
			<div class="modal-footer">
				<form id="deleteCardholderForm" method="POST" action="">
					@csrf
					@method('DELETE')
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
					<button type="submit" class="btn btn-danger">@lang('Delete')</button>
				</form>
			</div>
		</div>
	</div>
</div>
