<div class="modal fade" id="cardDetailsModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title text-white" id="cardDetailsModalLabel">{{ __('Card Details') }}</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body bg-gradient" id="card-details-content" style="min-height:260px;">
				<!-- Card details will be injected here by JS -->
				<div class="d-flex justify-content-center align-items-center" style="height:260px;">
					<span class="spinner-border text-primary"></span>
				</div>
			</div>
		</div>
	</div>
</div>