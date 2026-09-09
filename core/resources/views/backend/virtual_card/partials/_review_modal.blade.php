<div class="modal fade" id="review-request-{{ $request->uuid }}" tabindex="-1" aria-labelledby="reviewRequestLabel-{{ $request->uuid }}" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			{{-- Modal Header --}}
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title fw-bold" id="reviewRequestLabel-{{ $request->uuid }}">
					{{ __('Virtual Card Request Review') }}
				</h5>
				<button type="button" class="btn-close btn-close-white" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			
			@if($request->status->value === \App\Enums\VirtualCard\VirtualCardRequestStatus::Pending->value)
				<form action="{{ route('admin.virtual-card.requests.review', $request->uuid) }}" method="post">
					@csrf
					<div class="modal-body">
						{{-- Request Information --}}
						<div class="card border mb-3">
							<div class="card-body p-3">
								<h6 class="fw-bold text-primary mb-3">{{ __('Request Information') }}</h6>
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold">{{ __('Request ID') }}</span>
									<span>#{{ $request->uuid }}</span>
								</div>
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold">{{ __('User') }}</span>
									<span>{{ $request->user->name }} ({{ $request->user->email }})</span>
								</div>
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold">{{ __('Wallet') }}</span>
									<span>{{ $request->wallet->currency->name }} ({{ $request->wallet->currency->code }})</span>
								</div>
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold">{{ __('Requested On') }}</span>
									<span>{{ $request->created_at->format('Y-m-d H:i') }}</span>
								</div>
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold">{{ __('Status') }}</span>
									<span>
                                        <span class="badge bg-{{ $request->status->badgeColor() }}">
                                            {{ $request->status->label() }}
                                        </span>
                                        @if($request->admin_note)
											<span class="text-danger small ms-2">{{ $request->admin_note }}</span>
										@endif
                                    </span>
								</div>
								@if($request->card)
									<div class="d-flex justify-content-between mb-2">
										<span class="fw-bold">{{ __('Issued Card') }}</span>
										<span>
                                            <span class="badge bg-success">•••• {{ $request->card->last4 }}</span>
                                            <span class="small text-muted">({{ $request->card->expiry_month }}/{{ $request->card->expiry_year }})</span>
                                        </span>
									</div>
								@endif
							</div>
						</div>
						{{-- Provider & Admin Note --}}
						<div class="card border mb-3">
							<div class="card-body p-3">
								<h6 class="fw-bold text-primary mb-3">{{ __('Provider & Admin Note') }}</h6>
								<div class="mb-3">
									<label for="provider-{{ $request->id }}" class="form-label">{{ __('Select Card Provider') }}</label>
									<select name="provider_id" id="provider-{{ $request->id }}" class="form-select" required>
										<option value="" selected disabled>{{ __('Choose Provider') }}</option>
										@foreach($providers as $provider)
											<option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
												{{ $provider->name }}
											</option>
										@endforeach
									</select>
								</div>
								<div>
									<label for="admin_note_approve-{{ $request->id }}" class="form-label">{{ __('Admin Note (optional)') }}</label>
									<input type="text" name="admin_note" id="admin_note_approve-{{ $request->id }}" class="form-control" maxlength="255" value="{{ old('admin_note') }}">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer bg-light p-3">
						<div class="d-flex w-100">
							<button type="submit" name="action" value="approve"
							        class="btn btn-success w-50 d-flex align-items-center justify-content-center me-2 text-white">
								<i class="fa-solid fa-check me-2"></i> {{ __('Approve & Issue Card') }}
							</button>
							<button type="submit" name="action" value="reject"
							        class="btn btn-danger w-50 d-flex align-items-center justify-content-center text-white">
								<i class="fa-solid fa-times me-2"></i> {{ __('Reject Request') }}
							</button>
						</div>
					</div>
				</form>
			@else
				<div class="modal-body">
					<div class="card border mb-3">
						<div class="card-body p-3">
							<h6 class="fw-bold text-primary mb-3">{{ __('Request Information') }}</h6>
							<div class="d-flex justify-content-between mb-2">
								<span class="fw-bold">{{ __('Request ID') }}</span>
								<span>#{{ $request->uuid }}</span>
							</div>
							<div class="d-flex justify-content-between mb-2">
								<span class="fw-bold">{{ __('User') }}</span>
								<span>{{ $request->user->name }} ({{ $request->user->email }})</span>
							</div>
							<div class="d-flex justify-content-between mb-2">
								<span class="fw-bold">{{ __('Wallet') }}</span>
								<span>{{ $request->wallet->currency->name }} ({{ $request->wallet->currency->code }})</span>
							</div>
							<div class="d-flex justify-content-between mb-2">
								<span class="fw-bold">{{ __('Requested On') }}</span>
								<span>{{ $request->created_at->format('Y-m-d H:i') }}</span>
							</div>
							<div class="d-flex justify-content-between mb-2">
								<span class="fw-bold">{{ __('Status') }}</span>
								<span>
                                    <span class="badge bg-{{ $request->status->badgeColor() }}">
                                        {{ $request->status->label() }}
                                    </span>
                                    @if($request->admin_note)
										<span class="text-danger small ms-2">{{ $request->admin_note }}</span>
									@endif
                                </span>
							</div>
							@if($request->card)
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold">{{ __('Issued Card') }}</span>
									<span>
                                        <span class="badge bg-success">•••• {{ $request->card->last4 }}</span>
                                        <span class="small text-muted">({{ $request->card->expiry_month }}/{{ $request->card->expiry_year }})</span>
                                    </span>
								</div>
							@endif
						</div>
					</div>
				</div>
				<div class="modal-footer bg-light p-3">
					<button type="button" class="btn btn-secondary w-100" data-coreui-dismiss="modal">
						{{ __('Close') }}
					</button>
				</div>
			@endif
		</div>
	</div>
</div>