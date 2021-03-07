@extends('layouts.app')

@section('title')
    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

    <!-- for datatable -->
    <link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

    <link rel="stylesheet" href="{{ asset('css/audit-trail-style.css') }}">

@endsection

@section('navbar')
    @include('layouts.navbars.admin')
@endsection

@section('content')
    <div class="content">

        <div class="container">

            <div class="row common-area">
                <div class="col-md-3">
                <input class="form-control audit-month" type="month" max="{{ date('Y-m', strtotime('first day of last month')) }}" value="{{ date('Y-m', strtotime('first day of last month')) }}" id="audit_month" onchange="loadData()">
                </div>
            </div> 
    
            <div class="row audit-trail-area mt-4" id="audit-trail-area">
                
            </div>

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


<script>

    $(document).ready(function() {
        $('.side-link.li-audit-trail').addClass('active');
        $('#audit_month').trigger("change");
    });   

    function loadData() {

        var audit_month = $('#audit_month').val();
        var regEx = /^\d{4}-\d{2}$/;
        if(audit_month.match(regEx)) {
            var apiURL = baseURL+'admin/load-audit-trail-table/'+audit_month;
            console.log(apiURL);
            $('#audit-trail-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
            $('#audit-trail-area').load(apiURL);
        }
        else {
            swal("Retry!", "Please select a valid date", "error");
        }        

    }

</script>

@endsection
