@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

<link rel="stylesheet" href="{{ asset('css/month-end-style.css') }}">

@endsection

@section('navbar')
@include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">

        <input type="hidden" id="current_tealeave_price" value="@if (isset($data['tealeave_price'])) {{ $data['tealeave_price'] }} @endif" >

        <div class="data-table-area">
            <table width="98%" class="table data-table table-hover">
                <thead>
                    <tr>
                        <th width="25%" scope="col">Month</th>
                        <th width="25%" scope="col">Ended Date</th>
                        <th width="28%" scope="col">Create Month End</th>
                        <th width="25%"scope="col">Print Bill</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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

    var monthEndTable;

    $(document).ready(function() {
        $('#supplier_route').select2();
        $('#supplier_route2').select2();
        $('.side-link.li-month-end').addClass('active');
        monthEndDatatable();
    });

    function monthEndDatatable() {
    
        monthEndTable = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/month-end-datatable') }}",
        columns: [
                { data:'month', name:'month', orderable: false,},
                { data:'ended_date', name:'ended_date', orderable: false, searchable: false},
                { data:'create', name:'create', orderable: false, searchable: false},
                { data:'print', name:'print', orderable: false, searchable: false},
        ]});

    }

    function createMonthEnd(id) {

       var current_tealeave_price = $("#current_tealeave_price").val();

        swal({
            title: 'Are you sure?',
            html: "Current tealeave price : <span style='font-weight: bold'>Rs."+ current_tealeave_price + "</span>"+
                   "<br>"+
                   "Once you create month end, you can't undo !",
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
                    url: '{{url("/admin/create-month-end")}}',
                    type: 'POST',
                    data: {month_end_id : id},
                    dataType: 'JSON',
                    success: function (data) { 
                        console.log(data);
                        if(data.result == true) {
                            monthEndTable.ajax.reload();
                            swal("Good Job !", data.message, "success");
                        }
                        else {
                            swal("Opps!", data.message, "error");
                        }                      
                    }
                });
            } else {
                swal("Month end creation canceled!");
            }
        })
        
    }

    function printBill(id) {

        var pageURL = baseURL+'admin/print-bulk-bills/'+id;

        window.open(pageURL, '_blank');

    }

</script>

@endsection
