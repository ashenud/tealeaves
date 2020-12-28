<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-success">
    <div class="container-fluid" style="margin: -10px;">
        <a class="navbar-brand" href="#">TEALEAVES</a>
        <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link li-dash" href="{{ url('admin/dashboard') }}">
                        <i class="now-ui-icons design_app"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle li-sup" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="now-ui-icons business_badge"></i> SUPPLIERS
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="{{ url('/admin/supplier-insert') }}">
                                <i class="now-ui-icons design-2_ruler-pencil"></i>ADD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ url('admin/supplier-view') }}">
                                <i class="now-ui-icons design_bullet-list-67"></i>VIEW
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle li-items" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="now-ui-icons design_app"></i> ITEMS
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design-2_ruler-pencil"></i>ADD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design_bullet-list-67"></i>VIEW
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle li-collect" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="now-ui-icons shopping_cart-simple"></i> DAILY COLLECT
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design-2_ruler-pencil"></i>ADD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design_bullet-list-67"></i>VIEW
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle li-issue" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="now-ui-icons shopping_delivery-fast"></i> DAILY ISSUE
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design-2_ruler-pencil"></i>ADD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design_bullet-list-67"></i>VIEW
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle li-report" href="#" id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="now-ui-icons files_single-copy-04"></i> REPORTS
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design-2_ruler-pencil"></i>ADD
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="now-ui-icons design_bullet-list-67"></i>VIEW
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="now-ui-icons media-1_button-power"></i>LOGOUT
                    </a>
                    <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
                
            </ul>
        </div>
    </div>
</nav>