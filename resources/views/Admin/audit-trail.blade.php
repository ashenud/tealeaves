@extends('layouts.app')

@section('title')
    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

    <!-- for datatable -->
    <link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

    <link rel="stylesheet" href="{{ asset('css/audit-trail-style.css') }}">
    {{-- <style>
        .header-fixed thead {
            display: block;
        }
        .header-fixed tbody {
            display: block;
            overflow: auto;
            height: 400px;
        }
        .header-fixed th,
        .header-fixed td {
            width: 118px;
        }
        
        /* width */
        .header-fixed tbody::-webkit-scrollbar {
            width: 4px;
        }
        
        /* Track */
        .header-fixed tbody::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
    
        /* Handle */
        .header-fixed tbody::-webkit-scrollbar-thumb {
            background: #888; 
            border-radius: 10px;
        }
    
        /* Handle on hover */
        .header-fixed tbody::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }
    
    </style> --}}
@endsection

@section('navbar')
    @include('layouts.navbars.admin')
@endsection

@section('content')
    <div class="content">

        <div class="container">

            <div class="row common-area">
                <div class="col-md-3">
                <input class="form-control audit-month" type="month" max="{{ date('Y-m') }}" value="{{ date('Y-m', strtotime('first day of last month')) }}" id="audit_month" onchange="loadData()">
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

    function printAuditTrail(audit_month) {
        var pageURL = baseURL+'admin/print-audit-trail/'+audit_month;
        window.open(pageURL, '_blank');
    }

</script>

@endsection
