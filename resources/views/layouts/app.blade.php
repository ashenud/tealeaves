<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('img/apple-icon.png')}}">
    <link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">

    <!--fonts and icons-->
    <link rel="stylesheet" href="{{asset('fontawesome/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('css/material-icons.css')}}">
    <link rel="stylesheet" href="{{asset('css/now-ui-icons.css')}}">
    <link rel="stylesheet" href="{{asset('css/english-fonts.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!--css files-->
    <link rel="stylesheet" href="{{asset('css/mdb.min.css')}}">

    <!--select 2-->
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}">

    @yield('title')

    <style>
        body {
            font-family: 'Nunito';
        }

        .nav-clear {
            height:20px;
            content: "";
            min-width: 100%;
            background-color:#ffffff;
            position: fixed;
            width: 100%;
            top: 0;
        }

        .navbar {
            font-family: "Poppins", sans-serif;
            margin: 10px;
            border-radius: 0.1875rem;
            background-color: #4b7f5f!important;
        }

        .navbar-nav .dropdown-menu {
            text-align: left;
            list-style: none;
            background-color: #fff;
            box-shadow: 0px 10px 50px 0px rgba(0, 0, 0, 0.2);
            border-radius: 0.125rem;
            transition: all 150ms linear;
            font-size: 10px;
            min-width: 10rem;
            padding: .5rem 0;
            margin: 4px 0 0;
            background-clip: padding-box;
        }

        .dropdown-menu:before {
            display: inline-block;
            position: absolute;
            width: 0;
            height: 0;
            vertical-align: middle;
            content: "";
            top: -4px;
            left: 10px;
            right: auto;
            color: #FFFFFF;
            border-bottom: .4em solid;
            border-right: .4em solid transparent;
            border-left: .4em solid transparent;
        }

        .dropdown-item {
            color: #212529;
        }

        .navbar-brand {
            font-size: 40px;
            margin-left: 20px;
        }

        .navbar-nav .nav-item:not(:last-child) {
            margin-right: 5px;
        }
        .navbar-dark .navbar-nav .nav-link.active, .navbar-dark .navbar-nav .show>.nav-link {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 0.1875rem;
        }

        .dropdown-item.active, .dropdown-item:active, .dropdown-item:focus, .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 0.1875rem;
        }

        .navbar .navbar-nav .nav-item.active .nav-link:not(.btn), .navbar .navbar-nav .nav-item .nav-link:not(.btn):focus, .navbar .navbar-nav .nav-item .nav-link:not(.btn):hover, .navbar .navbar-nav .nav-item .nav-link:not(.btn):active {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 0.1875rem;
        }

        .navbar .navbar-nav .nav-link:not(.btn) {
            padding: .5rem;
            cursor: pointer;
        }

        .navbar .navbar-nav .nav-link:not(.btn) {
            text-transform: uppercase;
            font-size: 0.7142em;
            line-height: 1.625rem;
        }

        .dropdown-menu .dropdown-item i {
            margin-right: 5px;
            font-size: 10px;
            position: relative;
            top: 1px;
        }

        .main-body .content {
            margin-top: 100px;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        footer .footer-area {
            font-family: "Poppins", sans-serif;
            margin: 10px;
            border-radius: 0.1875rem;
            background-color: #4b7f5f!important;
        }
    </style>

    @yield('style')

    @yield('head-scripts')

</head>
<body>
    <div class="wrapper">
        <div class="nav-clear"></div>
        <!--top navbar-->
        @yield('navbar')
        <!--end of top navbar-->

        <!-- main body (sidebar and content) -->
        <div class="main-body">

            <!-- content -->
            @yield('content')
            <!-- end of content -->

        </div>
        <!-- end of main body (sidebar and content) -->

        <!--top footer-->
        @yield('footer')
        <!--end of footer-->

    </div>

    <script type="text/javascript" src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/mdb.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/select2.min.js')}}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    @yield('script')

</body>
</html>
