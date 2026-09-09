<head>
	<title>{{ $meta['title'] ?? setting('site_title') }}</title>
	
	<meta name="description" content="{{ $meta['description'] ?? '' }}">
	<meta name="keywords" content="{{ $meta['keywords'] ?? '' }}">
	<meta name="author" content="REEXPAY LIMITED">
	
	@if(!empty($meta['canonical_url']))
		<link rel="canonical" href="{{ $meta['canonical_url'] }}">
	@endif
	
	@if(!empty($meta['robots']))
		<meta name="robots" content="{{ $meta['robots'] }}">
	@endif
	
	{{-- Open Graph --}}
	<meta property="og:title" content="{{ $meta['title'] ?? '' }}">
	<meta property="og:description" content="{{ $meta['description'] ?? '' }}">
	<meta property="og:type" content="website">
	<meta property="og:url" content="{{ url()->current() }}">
	@if(!empty($meta['image']))
		<meta property="og:image" content="{{ asset('storage/' . $meta['image']) }}">
	@endif
	
	{{-- Twitter --}}
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="{{ $meta['title'] ?? '' }}">
	<meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">
	@if(!empty($meta['image']))
		<meta name="twitter:image" content="{{ asset('storage/' . $meta['image']) }}">
	@endif
</head>
