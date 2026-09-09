@extends('backend.layouts.app')
@section('title')
   {{ __('Dashboard') }}
@endsection
@section('content')
 
	@can('dashboard-stats')
		@include('backend.dashboard.partials._stats')
	@endcan
	
	@can('transactions-chart')
        @include('backend.dashboard.partials._transactions_chart')
	@endcan
	
	@can('wallet-balance')
        @include('backend.dashboard.partials._wallet_balance')
	@endcan
    <div class="row g-4 mb-4">

	    @can('earning-chart')
		    {{-- Daily Fee Earnings --}}
		    @include('backend.dashboard.partials._admin_earning_chart')
	    @endcan
		   
	    @can('wallet-growth')
		    {{-- Wallet Growth Insight --}}
		    @include('backend.dashboard.partials._wallet_growth')
	    @endcan
    </div>
    
    <div class="row g-4 mb-4">
	    @can('wallet-latest-transactions')
		    {{-- Latest Transactions --}}
		    @include('backend.dashboard.partials._latest_transactions')
	    @endcan
		   
	    @can('wallet-latest-users')
		    {{-- Latest Users --}}
		    @include('backend.dashboard.partials._latest_users')
	    @endcan
    </div>
	
@endSection