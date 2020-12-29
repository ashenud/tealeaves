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
        <table class="table">
            <thead>
              <tr>
                <th scope="col">Class</th>
                <th scope="col">Heading</th>
                <th scope="col">Heading</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">Default</th>
                <td>Cell</td>
                <td>Cell</td>
              </tr>
          
              <tr class="table-primary">
                <th scope="row">Primary</th>
                <td>Cell</td>
                <td>Cell</td>
              </tr>
              <tr class="table-secondary">
                <th scope="row">Secondary</th>
                <td>Cell</td>
                <td>Cell</td>
              </tr>
              <tr class="table-success">
                <th scope="row">Success</th>
                <td>Cell</td>
                <td>Cell</td>
              </tr>
              <tr class="table-danger">
                <th scope="row">Danger</th>
                <td>Cell</td>
                <td>Cell</td>
              </tr>
              <tr class="table-warning">
                <th scope="row">Warning</th>
                <td>Cell</td>
                <td>Cell</td>
              </tr>
            </tbody>
          </table>
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
