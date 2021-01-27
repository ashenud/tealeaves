<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- base URL -->
    <script>
        var baseURL = '/';
    </script>

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
    <link rel="stylesheet" href="{{asset('css/custom-layout.css')}}">

    <!--select 2-->
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}">

    @yield('title')

    @yield('style')

    @yield('head-scripts')

</head>
<body>
    <div class="wrapper">
        
        <!--top navbar-->
        @yield('navbar')
        <!--end of top navbar-->

        <!-- main body (sidebar and content) -->
        <div class="main-body open-sidebar" id="main-body">

            <!-- content -->
            @yield('content')
            <!-- end of content -->

        </div>
        <!-- end of main body (sidebar and content) -->

        <!--top footer-->
        @yield('footer')
        <!--end of footer-->

    </div>

    <!--sidebar-->
    @yield('sidebar')
    <!--end of sidebar-->

    <script type="text/javascript" src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/mdb.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/select2.min.js')}}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="{{asset('js/custom-script.js')}}"></script>
    
    @yield('script')

</body>
</html>
