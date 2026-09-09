@extends('backend.layouts.app')
@section('title',__('App Info'))
@section('content')
    <div class="card border shadow-sm ">
        <div class="card-header bg-light">
            <h4 class="card-title">{{ __('App Info') }}</h4>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($data as $name => $value)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="avatar me-2">
                                <img class="avatar-img" src="{{ asset('general/static/tech/'.str_replace('_version','',$name).'.png') }}" alt="user@email.com">
                            </div>
                            {{title($name) }}
                        </div>
                        <span class="badge text-bg-primary rounded-pill">{{$value}}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

@endsection
