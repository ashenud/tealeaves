@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

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
                        <th width="25%" scope="col">Month</th>
                        <th width="25%" scope="col">Ended Date</th>
                        <th width="30%" scope="col">Create Month End</th>
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

    var supplierTable;

    $(document).ready(function() {
        $('#supplier_route').select2();
        $('#supplier_route2').select2();
        $('.side-link.li-month-end').addClass('active');
        supplierDatatable();
    });

    function supplierDatatable() {
    
        supplierTable = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/month-end-datatable') }}",
        columns: [
                { data:'month', name:'month'},
                { data:'ended_date', name:'ended_date'},
                { data:'create', name:'create'},
                { data:'print', name:'print', orderable: false, searchable: false},
        ]});

    }

    function createMonthEnd(id) {

        swal({
            title: "Are yoy sure ?",
            text: "You are going to create this month end !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {

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
                        if(data.result == true) {
                            console.log(data);
                            supplierTable.ajax.reload();
                            swal(data.message, {
                                icon: "success",
                            });
                        }
                        else {
                            swal(data.message, {
                                icon: "error",
                            });
                        }                      
                    }
                });
                
            } else {
                swal("Month end creation canceled!");
            }
        });
    }

</script>

@endsection
