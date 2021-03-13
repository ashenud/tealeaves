@extends('layouts.app')

@section('title')
    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

    <!-- for datatable -->
    <link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

    <link rel="stylesheet" href="{{ asset('css/sales-report-style.css') }}">

@endsection

@section('navbar')
    @include('layouts.navbars.admin')
@endsection

@section('content')
    <div class="content">

        <div class="container">

            <div class="row common-area">
                <div class="col-md-3">
                <input class="form-control sales-month" type="month" max="{{ date('Y-m', strtotime('first day of last month')) }}" value="{{ date('Y-m', strtotime('first day of last month')) }}" id="sales_month" onchange="loadData()">
                </div>
            </div> 
    
            <div class="row sales-report-area mt-4" id="sales-report-area">
                
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
        $('.side-link.li-sales-report').addClass('active');
        $('#sales_month').trigger("change");
    });   

    function loadData() {

        var sales_month = $('#sales_month').val();
        var regEx = /^\d{4}-\d{2}$/;
        if(sales_month.match(regEx)) {
            var apiURL = baseURL+'admin/load-sales-report-table/'+sales_month;
            console.log(apiURL);
            $('#sales-report-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
            $('#sales-report-area').load(apiURL);
        }
        else {
            swal("Retry!", "Please select a valid date", "error");
        }        

    }

</script>

@endsection
