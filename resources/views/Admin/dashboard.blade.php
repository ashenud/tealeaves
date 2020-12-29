@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

@endsection

@section('navbar')
@include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">
        
    </div>

</div>
@endsection

@section('footer')
@include('layouts.footer')
@endsection

@section('sidebar')
@include('layouts.sidebars.admin')
@endsection

@section('script')

<script type="text/javascript" src="{{asset('js/jquery.waypoints.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/jquery.counterup.min.js')}}"></script>

<script>
    $(function() {
        $('.side-link.li-dash').addClass('active');
    }); 
</script>

@endsection
