@extends('frontend.layouts.user.index')
@section('title', __('Dashboard'))

@section('styles')
<style>
    body:has(.reexpay-dashboard) { --main-color: #147d55; --main-color-2: #b6d85b; --hover-color: #0f6343; --border-color: #dce9df; --sky-color: #eef7e9; --main-background: #f4f7f2; }
    body:has(.reexpay-dashboard) { background: #f4f7f2; }
    body:has(.reexpay-dashboard) .navbar-area .navbar-wrap { border-color: #dce9df; }
    body:has(.reexpay-dashboard) .navbar-area .logo-area h6, body:has(.reexpay-dashboard) .navbar-area .header-right a { color: #214237; }
    body:has(.reexpay-dashboard) .btn:hover { background: #b6d85b; color: #14231f; }
    body:has(.reexpay-dashboard) .btn-base, body:has(.reexpay-dashboard) .bg-primary { background: #147d55 !important; }
    body:has(.reexpay-dashboard) .btn-base:hover { background: #b6d85b !important; }
    body:has(.reexpay-dashboard) .btn-border-base { color: #147d55; border-color: #147d55; }
    body:has(.reexpay-dashboard) .btn-border-base:hover, body:has(.reexpay-dashboard) .text-primary { color: #147d55 !important; }
    body:has(.reexpay-dashboard) .btn-border-base:hover { color: #fff !important; background: #147d55 !important; }
    .reexpay-dashboard { --ink: #14231f; --muted: #687a71; --green: #147d55; --lime: #d7f36b; }
    .reexpay-dashboard .dashboard-welcome { display: flex; align-items: end; justify-content: space-between; gap: 18px; margin-bottom: 24px; padding: 28px 32px; border-radius: 10px; color: #fff; background: linear-gradient(120deg, #172b25, #147d55); box-shadow: 0 15px 30px rgba(20,65,43,.12); }
    .reexpay-dashboard .dashboard-welcome small { color: #d7f36b; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
    .reexpay-dashboard .dashboard-welcome h1 { margin: 7px 0 0; color: #fff; font-size: 30px; font-weight: 800; }
    .reexpay-dashboard .dashboard-welcome p { max-width: 520px; margin: 7px 0 0; color: #d3e3da; font-size: 14px; }
    .reexpay-dashboard .dashboard-welcome .dashboard-mark { color: var(--lime); font-size: 21px; font-weight: 900; letter-spacing: 2px; }
    .reexpay-dashboard .dashboard-actions { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 24px; }
    .reexpay-dashboard .dashboard-action { display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; padding: 10px 12px; border: 1px solid #dce9df; border-radius: 6px; color: #214237; background: #fff; font-size: 13px; font-weight: 800; text-decoration: none; transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
    .reexpay-dashboard .dashboard-action:hover { transform: translateY(-2px); border-color: #147d55; box-shadow: 0 8px 18px rgba(20,65,43,.08); }
    .reexpay-dashboard .dashboard-action.primary { color: #fff; border-color: #147d55; background: #147d55; }
    .reexpay-dashboard .single-amount-card, .reexpay-dashboard .single-chart-inner { border: 1px solid #e0e9e3; border-radius: 8px; box-shadow: 0 8px 24px rgba(20,65,43,.05); }
    .reexpay-dashboard .single-amount-card { background: #fff; }
    .reexpay-dashboard .single-chart-inner { background: #fff; }
    .reexpay-dashboard .dashboard-divider { height: 1px; margin: 24px 0; background: #dce7df; }
    @media (max-width: 767px) { .reexpay-dashboard .dashboard-welcome { display: block; padding: 23px; } .reexpay-dashboard .dashboard-welcome h1 { font-size: 25px; } .reexpay-dashboard .dashboard-mark { display: block; margin-bottom: 20px; } .reexpay-dashboard .dashboard-actions { grid-template-columns: repeat(2, 1fr); } .reexpay-dashboard .dashboard-action:last-child { grid-column: span 2; } }
</style>
@endsection

@section('content')
    <div class="reexpay-dashboard">
        <div class="dashboard-welcome">
            <div><small>REEXPAY LIMITED</small><h1>{{ __('Welcome back, :name', ['name' => auth()->user()->full_name]) }}</h1><p>{{ __('Your Uganda-first money dashboard. Review your wallet, watch your activity, and move funds with confidence.') }}</p></div>
            <span class="dashboard-mark">REEXPAY</span>
        </div>
        <nav class="dashboard-actions" aria-label="Quick actions">
            <a class="dashboard-action primary" href="{{ route('user.deposit.create') }}"><x-icon name="deposit" height="18" width="18"/>{{ __('Deposit') }}</a>
            <a class="dashboard-action" href="{{ route('user.send-money.create') }}"><x-icon name="send-money" height="18" width="18"/>{{ __('Send') }}</a>
            <a class="dashboard-action" href="{{ route('user.request-money.create') }}"><x-icon name="request_money" height="18" width="18"/>{{ __('Request') }}</a>
            <a class="dashboard-action" href="{{ route('user.withdraw.create') }}"><x-icon name="withdraw" height="18" width="18"/>{{ __('Withdraw') }}</a>
            <a class="dashboard-action" href="{{ route('user.transaction.index') }}"><x-icon name="transaction-4" height="18" width="18"/>{{ __('Activity') }}</a>
        </nav>
        @include('frontend.user.dashboard.partials._amount_card')
        <div class="dashboard-divider"></div>
        @include('frontend.user.dashboard.partials._chart_card')
        <div class="dashboard-divider"></div>
        @include('frontend.user.dashboard.partials._recent_transactions')
    </div>
@endsection

@push('scripts')
    @include('frontend.user.dashboard.partials._script')
@endpush
