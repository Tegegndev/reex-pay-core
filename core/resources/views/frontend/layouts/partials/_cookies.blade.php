<!-- REEXPAY cookie consent popup -->
<div id="cookieConsent" class="cookie-consent shadow-lg rounded-4 p-4">
	<div class="d-flex align-items-start mb-3">
		<div class="cookie-icon me-2 mt-1">
			<x-icon name="cookie" i="cookie"/>
		</div>
		<div>
			<h6 class="mb-1 fw-semibold text-dark">{{ setting('cookie_title') }}</h6>
			<p class="mb-1 text-secondary" style="font-size: 1rem;">
				{{ setting('cookie_summary') }}
				<a href="{{ url(setting('cookie_url')) }}" class="text-primary text-decoration-underline" target="_blank" rel="noopener">{{ __('Read more') }}...</a>
			</p>
		</div>
	</div>
	<div class="d-flex gap-2 flex-wrap">
		<button type="button" class="btn btn-primary flex-fill" id="cookieAccept">{{ __('Accept') }}</button>
		<button type="button" class="btn btn-outline-primary flex-fill" id="cookieReject">{{ __('Decline') }}</button>
	</div>
</div>
