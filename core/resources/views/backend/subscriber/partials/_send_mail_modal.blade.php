<div class="modal fade" id="sendMailModal" tabindex="-1" aria-labelledby="sendMailModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<form action="{{ route('admin.subscriber.send-mail') }}" method="POST">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title" id="sendMailModalLabel">
						<i class="fa-solid fa-envelope me-2"></i> {{ __('Send Email to ') }} <span id="subscriber-email">{{ __('All Subscribers') }}</span>
					</h5>
					<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
				</div>
				
				<div class="modal-body">

					<div id="subscribers-id-container" class="d-none">
						<input type="hidden" name="subscribers_id" id="subscribers-id">
					</div>

					<div class="mb-3">
						<label class="form-label">{{ __('Subject') }}</label>
						<input type="text" name="subject" class="form-control" required>
					</div>
					
					<div class="mb-3">
						<label class="form-label">{{ __('Message') }}</label>
						<textarea name="message" rows="5" class="form-control" required></textarea>
					</div>
					
					<div class="d-flex justify-content-end gap-2 pt-2">
						<button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">
							<i class="fa-solid fa-xmark me-1"></i> {{ __('Cancel') }}
						</button>
						<button type="submit" class="btn btn-primary" id="sendMailBtn">
							<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
							<span class="mail-btn-text">
						        <i class="fa-solid fa-paper-plane me-1"></i> {{ __('Send Now') }}
						    </span>
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
