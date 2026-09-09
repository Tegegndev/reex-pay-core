<section id="authentication" class="content-section">
	<h2>@lang('Authentication')</h2>
	<p>@lang('REEXPAY API uses API keys to authenticate requests. You can obtain your credentials from your merchant dashboard.')</p>

	<!-- Environment Credential Cards -->
	<!-- API Environment Notice -->
	<div class="alert alert-info mb-4">
		<div class="d-flex align-items-center">
			<i class="fas fa-info-circle me-3 fs-4"></i>
			<div>
				<h6 class="mb-1">@lang('Environment-Aware API Integration')</h6>
				<p class="mb-0">@lang('REEXPAY API supports both sandbox (testing) and production environments. Always test in sandbox first before going live.')</p>
			</div>
		</div>
	</div>
	
	<!-- Environment Configuration -->
	<div class="card shadow-sm mb-4">
		<div class="card-body">
			<h6 class="card-title">@lang('Environment Configuration')</h6>
			<div class="row">
				<div class="col-md-6">
					<div class="alert alert-warning mb-0">
						<h6 class="alert-heading"><i class="fas fa-flask me-2"></i>@lang('Sandbox Mode')</h6>
						<p class="mb-2"><strong>@lang('Use for:') </strong>@lang('Development, testing, integration')</p>
						<p class="mb-2"><strong>@lang('X-Environment:') </strong><code>sandbox</code></p>
						<p class="mb-0"><strong>@lang('Credentials:') </strong>@lang('Use test_* prefixed API keys')</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="alert alert-success mb-0">
						<h6 class="alert-heading"><i class="fas fa-rocket me-2"></i>@lang('Production Mode')</h6>
						<p class="mb-2"><strong>@lang('Use for:') </strong>@lang('Live payments, real money')</p>
						<p class="mb-2"><strong>@lang('X-Environment:') </strong><code>production</code></p>
						<p class="mb-0"><strong>@lang('Credentials:') </strong>@lang('Use production API keys (no prefix)')</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<h3 class="mt-4">@lang('Required Credentials')</h3>
	<div class="table-responsive">
		<table class="table table-bordered align-middle mb-0">
			<thead class="table-light">
				<tr>
					<th style="white-space:nowrap;">@lang('Credential')</th>
					<th style="white-space:nowrap;">@lang('Header')</th>
					<th style="white-space:nowrap;">@lang('Description')</th>
					<th style="white-space:nowrap;">@lang('Location')</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>@lang('Merchant ID')</code></td>
					<td><code>X-Merchant-Key</code></td>
					<td>@lang('Your unique merchant identifier')</td>
					<td>@lang('Dashboard') &rarr; @lang('Merchant') &rarr; @lang('CONFIG')</td>
				</tr>
				<tr>
					<td><code>@lang('API Key')</code></td>
					<td><code>X-API-Key</code></td>
					<td>@lang('API authentication key')</td>
					<td>@lang('Dashboard') &rarr; @lang('Merchant') &rarr; @lang('CONFIG')</td>
				</tr>
				<tr>
					<td><code>@lang('Client Secret')</code></td>
					<td>-</td>
					<td>@lang('Used for webhook signature verification')</td>
					<td>@lang('Dashboard') &rarr; @lang('Merchant') &rarr; @lang('CONFIG')</td>
				</tr>
				<tr>
					<td><strong>@lang('Production API Key')</strong></td>
					<td><code>X-API-Key</code></td>
					<td>@lang('Production API key (no prefix)')</td>
					<td>@lang('Merchant Dashboard') > @lang('API Config') > @lang('Production Mode')</td>
				</tr>
				<tr>
					<td><strong>@lang('Production Merchant Key')</strong></td>
					<td><code>X-Merchant-Key</code></td>
					<td>@lang('Production merchant identifier (no prefix)')</td>
					<td>@lang('Merchant Dashboard') > @lang('API Config') > @lang('Production Mode')</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="alert alert-warning mt-4">
		<strong><i class="fas fa-exclamation-triangle me-2"></i>@lang('Security Notice')</strong>
		@lang('Never expose your Client Secret in client-side code. Store all credentials securely on your server.')
	</div>
</section>
