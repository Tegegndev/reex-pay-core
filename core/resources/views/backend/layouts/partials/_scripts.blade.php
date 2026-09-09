{{-- =========================
| All JavaScript Files Link
|========================= --}}

{{-- Core Libraries --}}
<script src="{{ asset('general/js/jquery.min.js') }}"></script>
<script src="{{ asset('general/js/jquery-migrate.js') }}"></script>
<script src="{{ asset('general/js/jquery-ui.min.js') }}"></script>

{{-- Plugins --}}
<script src="{{ asset('backend/js/summernote-lite.min.js') }}"></script>
<script src="{{ asset('backend/js/tagify.min.js') }}"></script>
<script src="{{ asset('general/js/chartjs.js') }}"></script>
<script src="{{ asset('general/js/apexcharts.min.js') }}" defer></script>
<script src="{{ asset('general/js/moment.js') }}"></script>
<script src="{{ asset('general/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('general/js/datepicker.min.js') }}"></script>
<script src="{{ asset('general/js/clipboard.min.js') }}"></script>
<script src="{{ asset('backend/js/sortable.js') }}"></script>

{{-- Notifications & Helpers --}}
<script src="{{ asset('general/js/simple-notify.min.js') }}"></script>
<script src="{{ asset('general/js/helpers.js?v=' . config('app.version')) }}"></script>

{{-- Backend Main Scripts --}}
<script src="{{ asset('backend/js/app.js') }}"></script>
<script src="{{ asset('backend/js/main.js?v=' . config('app.version')) }}"></script>

{{-- Real-Time Notifications --}}
@include('general.notification_config._pusher_config')

{{-- Backend  Helpers --}}
@include('backend.partials._helper_js')


{{-- Global Date Range Picker & Notify Configuration --}}
@include('general._notify_evs')
@include('general._date_range_picker')

{{-- Page Specific Scripts --}}
@yield('scripts')
@stack('scripts')