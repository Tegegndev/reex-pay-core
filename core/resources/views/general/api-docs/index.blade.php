@extends('general.api-docs.layout')

@section('title', 'REEXPAY API Documentation')
@section('description', 'Complete API documentation for REEXPAY payment gateway integration with code examples for multiple platforms')
@section('content')

@include('general.api-docs.sections.overview')
@include('general.api-docs.sections.authentication')
@include('general.api-docs.sections.quickstart')
@include('general.api-docs.sections.endpoints')
@include('general.api-docs.sections.webhooks')
@include('general.api-docs.sections.examples')
@include('general.api-docs.sections.woocommerce')
{{--@include('general.api-docs.sections.sandbox-guide')--}}
@include('general.api-docs.sections.testing')
@include('general.api-docs.sections.support')

@endsection
