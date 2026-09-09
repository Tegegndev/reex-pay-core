@php
	$componentKey = strtolower($component->component_key);
	$cacheKey = "component_definition_{$componentKey}";
	$definition = cache()->rememberForever($cacheKey, function () use ($componentKey) {
		$file = resource_path("structure/page_component/{$componentKey}.php");
		return file_exists($file) ? include $file : [];
	});
	$limit = $definition['repeated_content_limit'] ?? null;
	$currentCount = $component->repeatedContents()->count();
	$isLimitOver = $component->limitRepeatedContentsOver();
	$remaining = is_numeric($limit) && $limit > 0 ? max(0, (int)$limit - $currentCount) : null;
@endphp

@if(is_numeric($limit) && $limit > 0)
	<div class="alert {{ $isLimitOver ? 'alert-info' : ($remaining <= 1 ? 'alert-primary' : 'alert-info') }} border-0 bg-light mb-4" role="alert">
		<div class="d-flex align-items-center">
			<div class="flex-shrink-0">
				@if($isLimitOver)
					<i class="fas fa-info-circle fa-lg text-primary"></i>
				@elseif($remaining <= 1)
					<i class="fas fa-lightbulb fa-lg text-info"></i>
				@else
					<i class="fas fa-chart-bar fa-lg text-success"></i>
				@endif
			</div>
			<div class="flex-grow-1 ms-3">
				<h6 class="alert-heading mb-2 text-dark">
					@if($isLimitOver)
						<strong>@lang('Content Limit Information')</strong>
					@elseif($remaining <= 1)
						<strong>@lang('Content Limit Notice')</strong>
					@else
						<strong>@lang('Content Status Overview')</strong>
					@endif
				</h6>
				<div class="mb-2">
					<span class="fw-semibold text-dark">@lang('Current Status'):</span>
					<span class="badge {{ $isLimitOver ? 'bg-danger' : ($remaining <= 1 ? 'bg-info' : 'bg-success') }} ms-1">
						@lang(':current / :limit items used', ['current' => $currentCount, 'limit' => $limit])
					</span>
					@if($remaining !== null && !$isLimitOver)
						<span class="text-muted ms-2">(@lang(':remaining remaining', ['remaining' => $remaining]))</span>
					@endif
				</div>
				
				@if($isLimitOver)
					<div class="mb-2">
						<i class="fas fa-shield-alt text-info me-1"></i>
						<span class="fw-semibold text-dark">@lang('Why this limit exists'):</span> 
						<span class="text-muted">@lang('This limit prevents frontend UI from breaking by maintaining optimal design structure.')</span>
					</div>
					<div class="mb-0">
						<i class="fas fa-cog text-success me-1"></i>
						<span class="fw-semibold text-dark">@lang('To increase limit'):</span> 
						<span class="text-muted">@lang('Edit :file and modify :code to a higher value.', [
							'file' => '<code class="bg-white px-2 py-1 rounded border">resources/structure/page_component/' . $componentKey . '.php</code>',
							'code' => '<code class="bg-white px-2 py-1 rounded border">\'repeated_content_limit\' => ' . $limit . '</code>'
						])</span>
					</div>
				@elseif($remaining <= 1)
					<div class="mb-0">
						<i class="fas fa-lightbulb text-info me-1"></i>
						<span class="text-muted">@lang('You\'re approaching the content limit. Consider increasing the limit in :file if you need more items.', [
							'file' => '<code class="bg-white px-2 py-1 rounded border">resources/structure/page_component/' . $componentKey . '.php</code>'
						])</span>
					</div>
				@else
					<div class="mb-0">
						<i class="fas fa-check-circle text-success me-1"></i>
						<span class="text-muted">@lang('You can add :remaining more :item to this section. Limit can be adjusted in the component structure file if needed.', [
							'remaining' => $remaining,
							'item' => $remaining !== 1 ? 'items' : 'item'
						])</span>
					</div>
				@endif
			</div>
		</div>
	</div>
@else
	<div class="alert alert-light border-0 bg-light mb-4" role="alert">
		<div class="d-flex align-items-center">
			<div class="flex-shrink-0">
				<i class="fas fa-infinity fa-lg text-info"></i>
			</div>
			<div class="flex-grow-1 ms-3">
				<h6 class="alert-heading mb-2 text-dark"><strong>@lang('No Content Limit Set')</strong></h6>
				<div class="mb-0">
					<i class="fas fa-info-circle text-info me-1"></i>
					<span class="text-muted">@lang('This component has no repeated content limit. You can add unlimited items, but consider setting a limit in :file to maintain optimal frontend performance.', [
						'file' => '<code class="bg-white px-2 py-1 rounded border">resources/structure/page_component/' . $componentKey . '.php</code>'
					])</span>
				</div>
			</div>
		</div>
	</div>
@endif