@php
	$customCss = \App\Models\CustomCode::getCached(\App\Enums\CustomCodeType::CSS);
@endphp

@if ($customCss && $customCss->content)
	<style>{!! $customCss->content !!}</style>
@endif
