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
                <input class="form-control loan-month" type="month" max="{{ date('Y-m') }}" value="{{ date('Y-m', strtotime('first day of last month')) }}" id="loan_month" onchange="loadMonthlyLoan()">
                </div>
            </div> 
    
            <div class="row loan-area" id="loan-area">
                
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

    var loanTable;

    $(document).ready(function() {
        $('.side-link.li-loan').addClass('active');
        $('#loan_month').trigger("change");
    });
    
    $(document).ajaxComplete(function ( event, xhr, settings ) {      
        if (typeof xhr.responseJSON === 'undefined')   {  
            $('#supplier_values').select2();
            loanDatatable();
        }
        else {
            // console.log(xhr.responseJSON.result);
        }        
    });

    function loadMonthlyLoan() {

        var loan_month = $('#loan_month').val();
        var regEx = /^\d{4}-\d{2}$/;
        if(loan_month.match(regEx)) {
            var apiURL = baseURL+'admin/load-monthly-loan/'+loan_month;
            // console.log(apiURL);
            $('#loan-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
            $('#loan-area').load(apiURL);
        }
        else {
            swal("Retry!", "Please select a valid date", "error");
        }        

    }

    function getSupplierValues() {

        var values =  $("#supplier_values").val().split("_");
        // console.log(values);
        $("#supplier").val(values[0]);
        $("#supplier_name").val(values[1]);

    }     

    function sendDataToEditModel(id){
        console.log(id);
        $.ajax({
            url: '{{url("/admin/get-loan-data")}}',
            type: 'GET',
            data: {id:id},
            dataType: 'JSON',
            success: function (data) { 
                if(data.result == true) {
                    // console.log(data.data[0]['loan_id']);                        
                    $("#loan_id").val(data.data[0]['loan_id']);
                    $("#instalment_id").val(data.data[0]['instalment_id']);
                    $("#date2").val(data.data[0]['loan_date']);
                    $("#supplier_no2").val(data.data[0]['supplier_no']);
                    $("#supplier_name2").val(data.data[0]['supplier_name']);
                    $("#amount2").val(data.data[0]['amount']);
                    $("#remarks2").val(data.data[0]['remarks']);
                    $('#edit_model').modal('toggle');
                }
                else {
                    swal("Opps !", data.message, "error");
                }                      
            }
        });
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
            $("#supplier_values").next().find('.select2-selection').addClass('is-invalid');
            swal("Retry!", "Please select a supplier", "error");
            $('#supplier_values').focus();
        }
        else if ($('#amount').val() === "" || $('#amount').val() <= 0) {
            $("#supplier_values").next().find('.select2-selection').removeClass('is-invalid');
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

            swal({
                title: 'Are you sure?',
                text: "You are going to add " + amount + " loan amount !",
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
                        url: '{{url("/admin/insert-loan")}}',
                        type: "POST",
                        data: {

                            date: date,
                            supplier: supplier,
                            amount: amount,
                            remarks: remarks,

                        },
                        success: function (data) {
                            // var data = JSON.parse(data);
                            console.log(data);
                            if(data.result===true){
                                swal("Good Job !", data.message, "success");
                                $("#supplier_values").val('').trigger('change');
                                $("#supplier").val('');
                                $("#amount").val('');
                                $("#remarks").val('');
                                $('#insert_model').modal('toggle');
                                loanTable.ajax.reload();
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

    function submit_edited_data_to_db() {
        if ($('#date2').val() === "") {
            $("#date2").addClass('is-invalid');
            swal("Retry!", "Please select a date", "error");
            $('#date2').focus();
        }
        else if ($('#amount2').val() === "" || $('#amount2').val() <= 0) {
            $("#date2").removeClass('is-invalid');
            $("#amount2").addClass('is-invalid');
            swal("Retry!", "Please enter a amount", "error");
            $('#amount2').focus();
        }
        else  {
            
            $("#amount2").removeClass('is-invalid');
            var loan_id = $('#loan_id').val();
            var instalment_id = $('#instalment_id').val();
            var date = $('#date2').val();
            var amount = $('#amount2').val();
            var remarks = $('#remarks2').val();

            swal({
                title: 'Are you sure to edit this record?',
                text: "You are going to add " + amount + " loan amount !",
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
                        url: '{{url("/admin/edit-loan")}}',
                        type: "POST",
                        data: {

                            loan_id: loan_id,
                            instalment_id: instalment_id,
                            date: date,
                            amount: amount,
                            remarks: remarks,

                        },
                        success: function (data) {
                            // var data = JSON.parse(data);
                            console.log(data);
                            if(data.result===true){
                                swal("Good Job !", data.message, "success");
                                $("#loan_id").val('');
                                $("#instalment_id").val('');
                                $("#date2").val('');
                                $("#supplier_no2").val('');
                                $("#supplier_name2").val('');
                                $("#amount2").val('');
                                $("#remarks2").val('');
                                $('#edit_model').modal('toggle');
                                loanTable.ajax.reload();
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

    function removeLoan(loan_id,instalment_id) {
        
        swal({
            title: 'Are you sure?',
            text: "You are going to delete this loan data !",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{url("/admin/delete-loan")}}',
                    type: 'POST',
                    data: {
                        loan_id:loan_id,
                        instalment_id:instalment_id
                    },
                    dataType: 'JSON',
                    success: function (data) { 
                        if(data.result == true) {
                            console.log(data);
                            loanTable.ajax.reload();
                            swal("Done!", data.message, "success")
                        }
                        else {
                            swal("Opps!", data.message, "error")
                        }                      
                    }
                });

            }
        })

    }

</script>

@endsection
