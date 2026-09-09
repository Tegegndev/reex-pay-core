<section id="quick-start" class="content-section">
	<h2>@lang('Quick Start')</h2>
	<p>@lang('Get up and running with REEXPAY API in just a few steps'):</p>
	
	<div class="row mb-4">
		<div class="col-md-6">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="fas fa-step-forward me-2"></i>@lang('Step 1: Get Credentials')</h5>
				</div>
				<div class="card-body">
					<ol>
						<li>@lang('Login to your REEXPAY dashboard')</li>
						<li>@lang('Navigate to') <strong>@lang('Merchant → CONFIG')</strong></li>
						<li>@lang('Copy your Merchant ID, API Key, and Client Secret')</li>
					</ol>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0"><i class="fas fa-step-forward me-2"></i>@lang('Step 2: Make Request')</h5>
				</div>
				<div class="card-body">
					<ol>
						<li>@lang('Set required headers with your credentials')</li>
						<li>@lang('POST to') <code>/api/v1/initiate-payment</code></li>
						<li>@lang('Redirect user to returned payment URL')</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	
	<!-- API Testing Link -->
	<div class="text-center">
		<a href="#testing" class="btn btn-primary btn-lg">
			<i class="fas fa-vial me-2"></i>@lang('Test API Now')
		</a>
		<p class="text-muted mt-2">@lang('Try REEXPAY API endpoints directly in your browser')</p>
	</div>

</section>
