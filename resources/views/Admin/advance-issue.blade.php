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

        <div class="data-table-area">
            <table class="table data-table table-hover">
                <thead>
                    <tr>
                        <th width="20%" scope="col">Supplier ID</th>
                        <th width="20%" scope="col">Supplier Name</th>
                        <th width="20%" scope="col">Advanced Date</th>
                        <th width="20%" scope="col">Remarks</th>
                        <th width="20%" scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <a class="btn btn-add-floating btn-primary btn-lg btn-floating" data-mdb-toggle="modal" data-mdb-target="#insert_model" type="button">
            <i class="fas fa-plus"></i>
        </a>

        <!-- Insert Modal -->
        <div class="modal fade" id="insert_model" tabindex="-1" aria-labelledby="insert_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
            <div class="modal-dialog .modal-side .modal-top-right">
                <div class="modal-content custom-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="insert_model_Label">INSERT ADVANCE DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <table class="table insert-table">
                                <tr>
                                    <td>Date</td>
                                    <td> : </td>
                                    <td><input type="date" id="date" class="form-control" min="{{ date('Y-m-d',strtotime('-15 days')) }}" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"></td>
                                </tr>
                                <tr>
                                    <td>Advance No.</td>
                                    <td> : </td>
                                    <td><input type="text" id="advance_no" class="form-control" readonly></td>
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
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="submit_data_to_db()" class="btn btn-primary-custom float-right">APPROVE</button>
                    </div>
                </div>
            </div>
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
        $('#supplier').select2();
        advanceDatatable();
    });

    function advanceDatatable() {

        advanceTable = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('admin/advance-datatable') }}",
            columns: [
                    { data:'supplier_id', name:'supplier_id'},
                    { data:'supplier_name', name:'supplier_name'},
                    { data:'advance_date', name:'advance_date'},
                    { data:'remarks', name:'remarks', orderable: false},
                    { data:'amount', name:'amount', orderable: false, searchable: false},
            ],
            order: [ 2, 'desc' ]
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
