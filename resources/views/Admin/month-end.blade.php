@extends('layouts.app')

@section('title')
    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

    <!-- for datatable -->
    <link rel="stylesheet" href="{{ asset('css/loan-issue-style.css') }}">

@endsection

@section('navbar')
    @include('layouts.navbars.admin')
@endsection

@section('content')
    <div class="content">

        <div class="container">

            <div class="row common-area mt-5">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <form>

                        <table class="table">
                            <tr>
                                <td>Date</td>
                                <td> : </td>
                                <td><input type="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" readonly></td>
                            </tr>
                            <tr>
                                <td>Loan No.</td>
                                <td> : </td>
                                <td><input type="text" id="loan_no" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td> : </td>
                                <td>
                                    <select class="form-control supplier-name" id="supplier">
                                        <option value="">Select Supplier</option>
                                        @if (isset($data['suppliers']))
                                            @foreach ($data['suppliers'] as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->sup_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>   
                                </td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td> : </td>
                                <td><input type="number" min="0" id="amount" class="form-control" onblur="formant_money(this)"></td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td> : </td>
                                <td><input type="text" id="remarks" class="form-control"></td>
                            </tr>
                            <tr class="submit-button-row">
                                <td colspan="7" align="right">
                                    <input class="btn btn-primary-custom submit-btn" type="button" class="btn" value="APPROVE LOAN"  id="dd" onclick="submit_data_to_db()" />
                                </td>
                            </tr>
                        </table>

                    </form>
                </div>
                <div class="col-md-4"></div>
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
            $('.side-link.li-month-end').addClass('active');
            $('#supplier').select2();
        });

        function formant_money(caller) {
            $(caller).val(parseFloat($(caller).val()).toFixed(2)); 
        }

        

    function submit_data_to_db() {
        if ($('#supplier').val() === "") {
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
            var loan_no = $('#loan_no').val();

            swal({
                title: "Are you sure?",
                text: "Do you want to add this records !",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {                            

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
                            loan_no: loan_no,

                        },
                        success: function (data) {
                            // var data = JSON.parse(data);
                            console.log(data);
                            if(data.result===true){
                                swal("Done!", data.message, "success")
                                .then((value) => {                                    
                                    location.reload();
                                });
                            }
                            else{
                                swal("Opps!", data.message, "error")
                                .then((value) => {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            swal("Opps!", "Please try again", "error");
                        }
                    });

                }
            });

        }

    }

    </script>

@endsection
