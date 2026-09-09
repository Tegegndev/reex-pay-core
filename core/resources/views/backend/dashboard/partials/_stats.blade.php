<div class="row g-4 mb-4">
	@foreach($stats as $stat)
		<div class="col-12 col-sm-6 col-xl-4 col-xxl-3">
			<div class="card stat-card shadow-sm border-0 position-relative h-100">
				@if (!empty($stat['link']))
					<a href="{{ $stat['link'] }}" class="position-absolute top-0 end-0 m-2 text-muted " data-coreui-toggle="tooltip" title="Go to {{ $stat['title'] }}">
						<x-icon name="arrow-up-right"  height="14" width="14" />
					</a>
				@endif
				
				<div class="card-body d-flex align-items-center">
					<div class="stat-icon me-3 {{ $stat['color_class'] }}">
						<x-icon name="{{ $stat['icon'] }}" height="24" width="24" />
					</div>
					<div>
						<div class="fs-5 fw-bold text-dark">{{ $stat['value'] }}</div>
						<div class="text-uppercase text-muted small fw-semibold">{{ $stat['title'] }}</div>
					</div>
				</div>
			</div>
		</div>
	@endforeach
</div>