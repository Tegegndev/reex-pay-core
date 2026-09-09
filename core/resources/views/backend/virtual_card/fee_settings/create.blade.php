@extends('backend.virtual_card.index')
@section('title', __('Add Fee Setting'))
@section('virtual_card_header')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start">
            {{ __('Add Fee Setting') }}
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
            <form action="{{ route('admin.virtual-card.fee-settings.store') }}" method="POST">
                @csrf
                @include('backend.virtual_card.fee_settings.partials._form', ['feeSetting' => null])
            </form>
        </div>
    </div>
@endsection
