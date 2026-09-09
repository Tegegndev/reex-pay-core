@extends('backend.withdraw.index')

@section('title', __('Withdraw Schedule'))

@section('withdraw_header')
    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <x-icon name="calendar" height="28" width="28" class="me-1 text-primary align-middle"/>
                {{ __('Withdraw Schedule') }}
            </h1>
            <div class="text-muted small lh-sm">
                {{ __('Set and manage the schedule for automatic withdrawals. The system will process withdrawals on the selected days.') }}
            </div>
        </div>
    </div>
@endsection

@section('withdraw_content')
    <div class="alert alert-primary alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-info-circle fs-4 text-primary me-3"></i>
            <div>
                {{ __('Manage your withdraw schedule. Set the withdraw schedule to automate the withdrawals on a specific day of the week.') }}
                <br/>
                <small class="text-muted">{{ __('Example: If you set the withdraw schedule to Monday, the system will automatically process the withdrawals on every Monday.') }}</small>
            </div>
        </div>
    </div>
    <form action="{{ route('admin.withdraw.schedule.update') }}" method="POST">
        @csrf
        <div class="card border-0 mb-4 shadow-sm">
            <div class="card-body">
                <div class="table-responsive rounded">
                    <table class="table  align-middle text-center">
                        <thead class="bg-light">
                        <tr class="text-muted">
                            <th class="text-start px-3">{{ __('Day') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($withdrawSchedules as $key => $withdrawSchedule)
                            <tr>
                                <td class="text-capitalize fw-semibold text-start px-3">
                                    {{ $withdrawSchedule->day }}
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                        <input
                                                class="form-check-input coevs-switch"
                                                type="checkbox"
                                                name="status[{{ $withdrawSchedule->day }}]"
                                                value="Active"
                                                id="daySwitch{{ $key }}"
                                                @checked($withdrawSchedule->status)>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-end p-3">
                <button type="submit" class="btn btn-primary fw-bold">
                    <x-icon name="check" height="20"/>
                    {{ __('Update Schedule') }}
                </button>
            </div>
        </div>
    </form>
@endsection