<div class="modal fade" id="view-cardholder-{{ $holder->id }}" tabindex="-1" aria-labelledby="viewCardholderLabel-{{ $holder->id }}" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title fw-bold" id="viewCardholderLabel-{{ $holder->id }}">
					{{ __('Cardholder Details') }}
				</h5>
				<button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="card mb-4 shadow-sm border-0 cardholder-modal-section">
					<div class="card-header bg-light border-0 pb-2">
						<div class="row g-3 align-items-center">
							<div class="col-12">
								<span class="cardholder-modal-section-title">{{ __('Cardholder Information') }}</span>
							</div>
							<div class="col-12 mt-2">
								<span class="badge bg-{{ $holder->status->badgeColor() }} cardholder-modal-badge">{{ $holder->status->label() }}</span>
							</div>
						</div>
					</div>
					<div class="card-body pt-3 pb-2">
						<div class="row mb-2">
							<div class="col-md-6 mb-2">
								<div class="cardholder-modal-label">{{ __('Type') }}</div>
								<div class="fw-semibold cardholder-modal-value">{{ $holder->card_type->label() }}</div>
							</div>
							@if($holder->gender)
							<div class="col-md-6 mb-2">
								<div class="cardholder-modal-label">{{ __('Gender') }}</div>
								<div class="fw-semibold cardholder-modal-value">{{ $holder->gender->label() }}</div>
							</div>
							@endif
						</div>
						<div class="row mb-2">
							@if($holder->dob)
							<div class="col-md-6 mb-2">
								<div class="cardholder-modal-label">{{ __('DOB') }}</div>
								<div class="fw-semibold cardholder-modal-value">{{ $holder->dob->format('Y-m-d') }}</div>
							</div>
							@endif
							@if(!empty($holder->relation))
							<div class="col-md-6 mb-2">
								<div class="cardholder-modal-label">{{ __('Relation') }}</div>
								<div class="fw-semibold cardholder-modal-value">{{ $holder->relation }}</div>
							</div>
							@endif
						</div>
						@if(!empty($holder->full_address))
						<div class="mb-2">
							<div class="cardholder-modal-label">{{ __('Address') }}</div>
							<div class="fw-semibold cardholder-modal-address">{{ $holder->full_address }}</div>
						</div>
						@endif
						@if($holder->business)
						<div class="mb-2">
							<div class="cardholder-modal-label">{{ __('Business') }}</div>
							<div class="fw-semibold cardholder-modal-business">{{ $holder->business->business_name }}</div>
							@if(!empty($holder->business->full_address))
							<div class="text-muted small cardholder-modal-business">{{ $holder->business->full_address }}</div>
							@endif
							@if(!empty($holder->business->business_type))
							<div class="text-muted small cardholder-modal-business">{{ $holder->business->business_type }}</div>
							@endif
						</div>
						@endif
						<div class="row mb-2">
							<div class="col-md-6 mb-2">
								<div class="cardholder-modal-label">{{ __('Requested At') }}</div>
								<div class="fw-semibold cardholder-modal-value">{{ $holder->created_at->format('Y-m-d H:i') }}</div>
							</div>
							@if($holder->approved_at)
							<div class="col-md-6 mb-2">
								<div class="cardholder-modal-label">{{ __('Approved At') }}</div>
								<div class="fw-semibold cardholder-modal-value">{{ $holder->approved_at->format('Y-m-d H:i') }}</div>
							</div>
							@endif
						</div>
						@if($holder->suspended_at)
						<div class="mb-2">
							<div class="cardholder-modal-label">{{ __('Suspended At') }}</div>
							<div class="fw-semibold cardholder-modal-value">{{ $holder->suspended_at->format('Y-m-d H:i') }}</div>
						</div>
						@endif
					</div>
				</div>
				@if($holder->kyc_documents && !$holder->business)
				<div class="card mb-3 border-0 shadow-sm cardholder-modal-section">
					<div class="card-header bg-light border-0 pb-2">
						<span class="cardholder-modal-section-title">{{ __('KYC Documents') }}</span>
					</div>
					<div class="card-body pt-3 pb-2">
						<div class="row g-3">
							@foreach($holder->kyc_documents as $type => $doc)
								<div class="col-md-4 col-12 mb-2">
									<div class="cardholder-modal-label mb-1">{{ str_replace('_',' ', ucfirst($type)) }}</div>
									@php
										$isImage = is_string($doc) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $doc);
									@endphp
									@if($isImage)
										<img src="{{ asset($doc) }}" alt="{{ $type }}" class="img-fluid rounded border mb-2" style="max-height:160px;object-fit:cover;">
									@else
										<div class="fw-semibold cardholder-modal-kyc">{{ $doc }}</div>
									@endif
								</div>
							@endforeach
						</div>
					</div>
				</div>
				@endif
			</div>
			<div class="modal-footer bg-light p-3">
				@if($holder->status->isPending())
					@can('virtual-card-action')
					<form action="{{ route('admin.virtual-card.cardholders.action', $holder->id) }}" method="POST" class="w-100">
						@csrf
						<div class="d-flex w-100">
							<button type="submit" name="action" value="approve"
									class="btn btn-success text-white  d-flex align-items-center justify-content-center me-2">
								<i class="fa-solid fa-check me-2"></i> {{ __('Approve & Issue') }}
							</button>
							<button type="submit" name="action" value="reject"
									class="btn btn-danger text-white  d-flex align-items-center justify-content-center">
								<i class="fa-solid fa-times me-2"></i> {{ __('Reject Request') }}
							</button>
						</div>
					</form>
					@endcan
				@else
					<button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">{{ __('Close') }}</button>
				@endif
			</div>
		</div>
	</div>
</div>