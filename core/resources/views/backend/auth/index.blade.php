<!DOCTYPE html>
<html lang="en">
@include('backend.layouts.partials._head')
<body>
<div class="bg-body-tertiary min-vh-100  d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-group d-block d-md-flex rounded">

                    <div class="card col-md-5 text-white d-none d-md-block position-relative" style="background-image: url('{{ asset(setting('login_banner')) }}'); background-size: cover; background-position: center;">
                        <div class="d-flex justify-content-center align-items-center h-100">
                        </div>
                    </div>

                    <div class="card col-md-7 col-12 p-4 mb-0">
                        <div class="card-body">
                            <img src="{{ asset(setting('logo')) }}" class="mb-2 img-fluid" alt="{{ setting('site_title') }}">
                            @yield('auth-content')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.layouts.partials._scripts')
</body>
</html>
