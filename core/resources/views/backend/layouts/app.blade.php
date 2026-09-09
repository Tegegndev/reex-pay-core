<!doctype html>
<html lang="en">
@include('backend.layouts.partials._head')
<body>

@include('backend.layouts.partials._sidebar')

<div class="wrapper d-flex flex-column min-vh-100">
    @include('backend.layouts.partials._header')
    
    <div class="body flex-grow-1 px-4 mb-3">
        <div class="container-lg">

            {{-- Any Warning Messages Here --}}
            @include('backend.partials._messages')
            
             {{--  Main Content --}}
             @yield('content')
        </div>
    </div>

    {{-- delete modal --}}
    @include('backend.partials._delete_modal')

    @include('backend.layouts.partials._footer')
</div>

@include('backend.layouts.partials._scripts')
</body>
</html>
