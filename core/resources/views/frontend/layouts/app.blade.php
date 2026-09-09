<!DOCTYPE html>
<html lang="en">
{{-- Head Include Here--}}
@include('frontend.layouts.partials._head')
<body>

{{-- Preloader Include Here--}}
@unless(request()->routeIs('home'))
	@include('frontend.layouts.partials._preloader')
@endunless

{{-- Offcanvas Include Here --}}
@include('frontend.layouts.partials._offcanvas')


{{-- Header Top Include Here --}}
@include('frontend.layouts.partials._header_top')

{{-- Header Sticky Include Here --}}
@include('frontend.layouts.partials._header_sticky')


{{--<< Dynamic Content Show Here >>--}}
@yield('content')

{{-- Cookies Include Here--}}
@if(setting('cookie_status'))
	@include('frontend.layouts.partials._cookies')
@endif



{{-- Footer Include Here --}}
@include('frontend.layouts.partials._footer')

{{-- Script Include Here --}}
@include('frontend.layouts.partials._script')
</body>
</html>
