<div class="language-selector">
	@php
		$currentLanguage = $languages->firstWhere('code', app()->getLocale());
	@endphp
	
	<div class="dropdown">
		<a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
			<img src="{{ asset($currentLanguage->flag) }}" alt="{{ $currentLanguage->name }}" class="flag-icon">
			<span class="language-name {{ $textColor ?? '' }}">{{ $currentLanguage->name }}</span>
		</a>
		<ul class="dropdown-menu">
			@foreach($languages as $language)
				<li class="mb-0">
					<a href="{{ route('locale-set', $language->code) }}"
					   class="dropdown-item d-flex align-items-center {{ $language->code == app()->getLocale() ? 'active' : '' }}">
						<img src="{{ asset($language->flag) }}" alt="{{ $language->name }}" class="flag-icon-small me-2">
						{{ $language->name }}
					</a>
				</li>
			@endforeach
		</ul>
	
	</div>
</div>
