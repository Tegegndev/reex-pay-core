<div class="col-sm-12 col-md-6">
	<div class="card shadow-sm border-0 h-100">
		<div class="card-body px-4">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
				<h5 class="card-title mb-0 fw-semibold text-capitalize">
					{{ __('Latest Transactions') }}
				</h5>
			
			</div>
			
			<div>
				<div class="table-responsive">
					<table class="table  border mb-0">
						<thead class="table-light fw-semibold">
						<tr class="align-middle text-nowrap">
							<th>{{ __('User | TXN ID') }}</th>
							<th>{{ __('Amount | Type') }}</th>
							<th>{{ __('Status|Time') }}</th>
						</tr>
						</thead>
						<tbody>
						@forelse($transactions as $transaction)
							@php
								$avatarData = getUserAvatarDetails($transaction->user->first_name, $transaction->user->last_name);
								$color = $transaction->status->color();
								$amountColor = $transaction->amount_flow->color($transaction->status);
								$amountSign = $transaction->amount_flow->sign($transaction->status);
							@endphp
							<tr class="align-middle">
								{{-- User Information --}}
								<td>
									<div class="d-flex align-items-center">
										<div>
											<a href="{{ route('admin.user.manage', $transaction->user->username) }}" class="text-decoration-none">
												{{ $transaction->user->name }}
											</a>
											<div class="small text-muted text-uppercase">{{ strtoupper($transaction->trx_id) }}</div>
										</div>
									</div>
								</td>
								
								{{-- Amount Information --}}
								<td>
									<div class="{{ $amountColor }} fw-bold">
										{{ $amountSign . $transaction->amount . ' ' . $transaction->currency }}
									</div>
									<div class="small text-muted">
										{{ __('Fee: :fee | Type :type', ['fee' => getSymbol($transaction->currency) . $transaction->fee, 'type' => $transaction->trx_type->label()]) }}
									</div>
								</td>
								
								
								
								{{-- Transaction Time --}}
								<td>
									<div><span class="badge bg-{{ $color }} text-uppercase">{{ $transaction->status->label() }}</span></div>
									<div class="small text-muted">{{ $transaction->created_at->diffForHumans() }}</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="3">
									<div class="py-5">
										<h5 class="text-muted mt-2 text-center">{{ __('No Data found') }}</h5>
									</div>
								</td>
							</tr>
						@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>




