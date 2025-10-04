<!DOCTYPE html>
<html lang={{ app()->getLocale() }}>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Get the logo and favicon from LogoSite model
        $logoSite = \App\Models\LogoSite::first();
        $faviconPath = $logoSite && $logoSite->favicon ? Storage::url($logoSite->favicon) : asset('favicon.ico');
    @endphp

    <link rel="icon" type="image/png" href="{{ $faviconPath }}">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    @vite('resources/assets/frontend/css/styles.css')

    @vite('resources/assets/admin/css/dashboard.css')
    @vite('resources/assets/admin/css/responsive.css')
    @vite('resources/assets/admin/css/components.css')
    @vite('resources/assets/admin/css/styles-admin.css')
    @stack('styles-admin')
    <title>{{ config('app.name') }} - {{ __('dashboard') }}</title>
</head>

<body class="g-sidenav-show bg-gray-100">
    @auth
        @include('admin.navbars.sidebar')
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
            @include('admin.navbars.nav')
            <div class="container-fluid py-4">
                @include('components.toast')
                @include('components.toast-main')
                @yield('content-auth')
                @include('admin.layouts.partials.footer')
            </div>
        </main>
    @endauth

    <footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite('resources/assets/admin/js/dashboard.min.js')
       
        @stack('scripts-admin')
    </footer>
</body>

</html>