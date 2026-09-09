<div class="card">
	<div class="card-body px-0">
		<div class="row g-3">
			@foreach($stats as $stat)
				<div class="col-md-6 col-xl-4">
					<div class="user-stat-card d-flex align-items-center justify-content-between">
						<div class="user-stat-icon {{ $stat['color_class'] }}">
							<x-icon name="{{ $stat['icon'] }}" width="26" height="26" />
						</div>
						<div class="text-end">
							<div class="fs-6 fw-bold text-dark">{{ $stat['value'] }}</div>
							<div class="text-uppercase small text-muted fw-semibold">{{ $stat['title'] }}</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>