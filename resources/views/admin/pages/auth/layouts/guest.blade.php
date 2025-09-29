@extends('admin.layouts.app')

@section('content-admin')
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                @include('admin.navbars.nav_login')
            </div>
        </div>
    </div>
    @yield('content-guest')
    @include('admin.pages.auth.layouts.footer')
@endsection
