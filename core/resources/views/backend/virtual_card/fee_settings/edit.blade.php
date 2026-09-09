@extends('backend.virtual_card.index')
@section('title', __('Edit Fee Setting'))
@section('virtual_card_header')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start">
            {{ __('Edit Fee Setting') }}
        </div>
        <div class="float-end">
            <a href="{{ route('admin.virtual-card.fee-settings.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> {{ __('Back to List') }}
            </a>
        </div>
    </div>
@endsection
@section('virtual_card_content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.virtual-card.fee-settings.update', $feeSetting) }}" method="POST">
                @csrf
                @method('PUT')
                @include('backend.virtual_card.fee_settings.partials._form', ['feeSetting' => $feeSetting])
            </form>
        </div>
    </div>
@endsection
