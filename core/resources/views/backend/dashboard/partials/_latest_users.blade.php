<div class="col-sm-12 col-md-6">
	<div class="card shadow-sm border-0 h-100">
		<div class="card-body px-4">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
				<h5 class="card-title mb-0 fw-semibold text-capitalize">
					{{ __('Latest Users') }}
				</h5>
			
			</div>
			
			<div>
				<div class="table-responsive rounded">
					@if($users->isNotEmpty())
						<table class="table mb-0 caption-top">
							<thead>
							<tr>
								<th>{{ __('Name | Joined At	') }}</th>
								<th>{{ __('Email | Status') }}</th>
								<th>{{ __('Action') }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($users as $user)
								@php
									$firstLetter = strtoupper(substr($user->first_name, 0, 1));
									$lastLetter = strtoupper(substr($user->last_name, -1));
									$avatarBgClass = 'avatar-bg-' . strtolower($firstLetter);
								@endphp
								<tr class="align-middle">
									<td>
										<div class="d-flex align-items-center">
											<div class="avatar avatar-md me-2">
												@if($user->avatar)
													<img class="avatar-img" src="{{ asset($user->avatar) }}" height="40"
													     alt="User Avatar">
												@else
													<div class="avatar avatar-md {{ $avatarBgClass }} text-white">
														{{ $firstLetter . $lastLetter }}
													</div>
												@endif
												<span class="avatar-status bg-{{ $user->status ? 'success' : 'danger' }}"></span>
											</div>
											<div>
												<div class="text-nowrap"> {{ title($user->name) }}</div>
												<div class="small text-body-secondary text-nowrap">
													{{ $user->created_at->diffForHumans() }}
												</div>
											</div>
										</div>
									</td>
									<td>
										<div>{{ maskSensitive($user->email) }}</div>
										<div class="small text-muted">
                                        <span class="text-uppercase badge bg-{{ $user->email_verified_at ? 'success' : 'danger' }}">
                                            {{ $user->email_verified_at ? __('Verified') : __('Unverified') }}
                                        </span>
										</div>
									</td>
									<td>
										<a href="{{ route('admin.user.manage', $user->username) }}" class="btn btn-primary">
											<x-icon name="manage" height="20"/>
										</a>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					@else
						<div class="text-center py-5">
							<x-icon name="no-data-found" height="200"/>
							<h5 class="text-muted mt-2">{{ __('No users found') }}</h5>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>




