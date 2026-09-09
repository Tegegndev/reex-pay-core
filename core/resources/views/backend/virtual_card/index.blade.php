@extends('backend.layouts.app')
@section('content')
	@php($virtualCardMenu = getAdminMenuByCode('virtual-card-management'))
	
	{{--Deposit Header Dynamic Content Show Here--}}
	@yield('virtual_card_header')
	
	<div class="card border-0 px-3 py-4">
		@if($virtualCardMenu && isset($virtualCardMenu['sub_menus']))
			<ul class="nav nav-pills bg-light rounded p-1">
				{{-- Deposit Menu Load From admin_menus Config File --}}
				@foreach($virtualCardMenu['sub_menus'] as $menu)
					<li class="nav-item ">
						<a class="nav-link {{ isActive($menu['route'],$menu['params'] ?? [] ) }}" aria-current="page" href="{{ route($menu['route'], $menu['params'] ?? []) }}">
							<x-icon name="{{ $menu['icon'] }}" height="18" width="18"/> {{ title($menu['label']) }}
						</a>
					</li>
				@endforeach
			
			</ul>
		@endif
		<div class="py-3">
			{{-- Deposit Dynamic Content Show Here --}}
			@yield('virtual_card_content')
		</div>
	</div>
@endSection
