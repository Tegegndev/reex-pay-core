@extends('backend.settings.index')
@section('setting_title', __('Plugins Manage'))
@section('setting_content')
    <div class="table-responsive  rounded ">
        <table class="table mb-0 caption-top">
            <thead>
            <tr>
                <th>{{ __('Logo') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Manage') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($plugins as $plugin)
                <tr class="align-middle">
                    <td class="text-center">
                        <div class="avatar avatar-md"><img class="avatar-img" src="{{ asset($plugin->logo) }}"
                                                           height="40" alt=""><span
                                    class="avatar-status bg-{{ $plugin->status ? 'success' : 'danger' }}"></span></div>
                    </td>
                    <td>
                        <div class="text-nowrap">{{ $plugin->name }}</div>
                        <div class="small text-body-secondary text-nowrap">{{ $plugin->description }}</div>
                    </td>
                    <td class="text-uppercase ">
                        @if($plugin->status)
                            <span class="badge bg-success">{{ __('Activated') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Not Activated') }}</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary d-flex align-items-center edit-modal"
                                data-edit-url="{{ route('admin.settings.plugin.edit',$plugin->id) }}">
                            <x-icon name="manage" height="18" class="me-1"/>
                            {{ __('Manage') }}
                        </button>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    {{-- plugin mange modal --}}
    @include('backend.settings.plugin.partials._manage')

@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            editFormByModal('manageModal','edit-append',false,true);
        });
    </script>
@endsection
