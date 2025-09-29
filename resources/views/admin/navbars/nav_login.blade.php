<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 my-3 blur blur-rounded py-2 start-0 end-0 mx4">
  <div class="container-fluid container">
    <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="{{ route('admin.dashboard') }}">
      <img class="img-fluid" src="{{ asset('assets/images/logo/logohoanxu.png') }}" alt="Flowbite Logo" style= "width: 230px; height: 90px;" />
    </a>
    <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon mt-2">
        <span class="navbar-toggler-bar bar1"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </span>
    </button>
    <div class="collapse navbar-collapse" id="navigation">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a class="nav-link me-2 fw-bold" href="{{route('login')}}">
            <h2 class="font-weight-bolder text-info text-gradient">Đăng nhập</h2>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav d-lg-block d-none">
        <li class="nav-item">
          <a href="{{ route('home') }}" class="btn btn-sm btn-round mb-0 me-1 bg-gradient-dark">Trang chủ</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- End Navbar -->
