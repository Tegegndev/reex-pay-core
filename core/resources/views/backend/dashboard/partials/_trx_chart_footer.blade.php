@php
	$symbol = siteCurrency('symbol');

	$colorMap = [
		'deposit'  => 'success',
		'withdraw' => 'danger',
		'payment'  => 'primary',
		'reward'   => 'warning',
	];

	$iconMap = [
		'deposit'  => 'fa-arrow-down',
		'withdraw' => 'fa-arrow-up',
		'payment'  => 'fa-credit-card',
		'reward'   => 'fa-gift',
	];

	$series     = $chartData['series'] ?? [];
	$categories = $chartData['dates'] ?? [];

	$stats = collect($series)->map(function ($item) use ($colorMap, $iconMap) {
		$type  = strtolower($item['name']);
		$total = collect($item['data'])->sum();

		return [
			'type'    => $type,
			'label'   => __(ucfirst($type)) . 's',
			'amount'  => $total,
			'color'   => $colorMap[$type] ?? 'secondary',
			'icon'    => $iconMap[$type] ?? 'fa-chart-line',
			'tooltip' => __('Total') . ' ' . __(ucfirst($type)) . 's',
		];
	});
@endphp

<div class="row g-3">
	@foreach($stats as $stat)
		<div class="col-12 col-md-6 col-xl-3">
			<div class="stat-card stat-filter d-flex justify-content-between align-items-center rounded border shadow-sm px-3 py-2"
			     data-type="{{ $stat['type'] }}">
				<div class="d-flex align-items-center gap-2">
                            <span class="text-{{ $stat['color'] }}" >
                                <i class="fa-solid {{ $stat['icon'] }}"></i>
                            </span>
					<span class="fw-medium text-muted">{{ $stat['label'] }}</span>
				</div>
				<div class="text-end" data-coreui-toggle="tooltip" title="{{ $stat['tooltip'] }}">
					<div class="fw-bold text-dark" >
						{{ $symbol }}<span id="total-{{ $stat['type'] }}">{{ number_format($stat['amount'], 2) }}</span>
					</div>
					<div class="progress progress-thin mt-1 stat-bar">
						<div class="progress-bar bg-{{ $stat['color'] }} w-100"></div>
					</div>
				</div>
			</div>
		</div>
	@endforeach
</div>
