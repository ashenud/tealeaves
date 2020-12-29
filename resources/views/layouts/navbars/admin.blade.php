<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="hamburger hamburger-btn" id="hamburger" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <span class="nav-title-custom navbar-brand mb-0 h1">TEALEAVES |</span> <span class="current-page"> Dashboard</span>
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <!-- Avatar -->
            <li class="nav-item dropdown">
                <a class="nav-link user-anchor dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink"
                    role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                    <i class="now-ui-icons user-icon users_circle-08"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <li>
                        <a class="dropdown-item" href="{{ url('/admin/profile') }}">
                            <i class="now-ui-icons users_single-02"></i>My profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('/admin/settings') }}">
                            <i class="now-ui-icons ui-1_settings-gear-63"></i>Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="now-ui-icons media-1_button-power"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>