@extends('layouts.app')

@section('title')
    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

    <!-- for datatable -->
    <link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

    <link rel="stylesheet" href="{{ asset('css/loan-issue-style.css') }}">

@endsection

@section('navbar')
    @include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">

        <div class="row common-area">
            <div class="col-md-3">
            <input class="form-control advance-month" type="month" max="{{ date('Y-m') }}" value="{{ date('Y-m', strtotime('first day of last month')) }}" id="advance_month" onchange="loadMonthlyAdvance()">
            </div>
        </div> 

        <div class="row advance-area" id="advance-area">
            
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

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<script>

    var advanceTable;

    $(document).ready(function() {
        $('.side-link.li-advance').addClass('active');
        $('#advance_month').trigger("change");
    });
    
    $(document).ajaxComplete(function ( event, xhr, settings ) {      
        if (typeof xhr.responseJSON === 'undefined')   {  
            $('#supplier').select2();
            advanceDatatable();
        }
        else {
            // console.log(xhr.responseJSON.result);
        }        
    });

    function loadMonthlyAdvance() {

        var advance_month = $('#advance_month').val();
        var regEx = /^\d{4}-\d{2}$/;
        if(advance_month.match(regEx)) {
            var apiURL = baseURL+'admin/load-monthly-advance/'+advance_month;
            // console.log(apiURL);
            $('#advance-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
            $('#advance-area').load(apiURL);
        }
        else {
            swal("Retry!", "Please select a valid date", "error");
        }        

    }

    function formant_money(caller) {
        $(caller).val(parseFloat($(caller).val()).toFixed(2)); 
    }

    function submit_data_to_db() {
        if ($('#date').val() === "") {
            $("#date").addClass('is-invalid');
            swal("Retry!", "Please select a date", "error");
            $('#date').focus();
        }
        else if ($('#supplier').val() === "") {
            $("#date").removeClass('is-invalid');
            $("#supplier").next().find('.select2-selection').addClass('is-invalid');
            swal("Retry!", "Please select a supplier", "error");
            $('#supplier').focus();
        }
        else if ($('#amount').val() === "" || $('#amount').val() <= 0) {
            $("#supplier").next().find('.select2-selection').removeClass('is-invalid');
            $("#amount").addClass('is-invalid');
            swal("Retry!", "Please enter a amount", "error");
            $('#amount').focus();
        }
        else  {
            
            $("#amount").removeClass('is-invalid');
            var date = $('#date').val();
            var supplier = $('#supplier').val();
            var amount = $('#amount').val();
            var remarks = $('#remarks').val();
            var advance_no = $('#advance_no').val();

            swal({
                title: 'Are you sure?',
                text: "You are going to add " + amount + " advance amount !",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {

                    $('button.swal2-confirm').hide();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{url("/admin/insert-advance")}}',
                        type: "POST",
                        data: {

                            date: date,
                            supplier: supplier,
                            amount: amount,
                            remarks: remarks,
                            advance_no: advance_no,

                        },
                        success: function (data) {
                            // var data = JSON.parse(data);
                            console.log(data);
                            if(data.result===true){
                                swal("Good Job !", data.message, "success");
                                $("#supplier").val('').trigger('change');
                                $("#amount").val('');
                                $("#remarks").val('');
                                $("#advance_no").val('');
                                $('#insert_model').modal('toggle');
                                advanceTable.ajax.reload();
                            }
                            else{
                                swal("Opps!", data.message, "error")
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            swal("Opps!", "Please try again", "error");
                        }
                    });
                }
            })

        }

    }

</script>

@endsection
