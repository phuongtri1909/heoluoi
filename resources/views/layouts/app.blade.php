@include('layouts.partials.header')


<body data-auth="{{ auth()->check() ? 'true' : 'false' }}">
    <div class="mt-88">
        @include('components.sweetalert')

        <div class="container d-md-none" id="mobileSearchContainer" style="display: none;">
            <div class="position-relative">
                <form action="{{ route('searchHeader') }}" method="GET">
                    <input type="text" name="query" class="form-control mt-3 rounded-4" placeholder="Tìm kiếm truyện..."
                        value="{{ request('query') }}" id="mobileSearchInput">
                    <button type="submit" class="btn search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        @yield('content')
        @include('components.top_button')
        @include('components.reading_settings')
    </div>
    <div id="fb-root" class="w-100"></div>
</body>

@include('layouts.partials.footer')

