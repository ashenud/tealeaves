@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

<link rel="stylesheet" href="{{ asset('css/supplier-style.css') }}">

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
                        <th width="25%" scope="col">Supplier Name</th>
                        <th width="30%" scope="col">Supplier Address</th>
                        <th width="25%"scope="col">Supplier Contact</th>
                        <th width="20%"scope="col">Actions</th>
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
                        <h5 class="modal-title" id="insert_model_Label">INSERT SUPPLIER DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="form-outline mb-4">
                                <input type="text" id="supplier_name" name="supplier_name" class="form-control" required/>
                                <label class="form-label" for="supplier_name">Supplier Name</label>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="text" id="supplier_address" name="supplier_address" class="form-control" required/>
                                <label class="form-label" for="supplier_address">Supplier Address</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="number" id="supplier_contact" name="supplier_contact" min="0" class="form-control" required/>
                                <label class="form-label" for="supplier_contact">Supplier Contact</label>
                            </div>

                            <div class="mb-4">
                                <label class="select2-label" for="supplier_route">Select Route</label> 
                                <select class="form-control" id="supplier_route" name="supplier_route">
                                    @if (isset($data['route']))
                                        @foreach ($data['route'] as $route)
                                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="insertSupplier()" class="btn btn-primary-custom float-right">INSERT</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div class="modal fade" id="view_model" tabindex="-1" aria-labelledby="view_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
            <div class="modal-dialog .modal-side .modal-top-right">
                <div class="modal-content custom-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="view_model_Label">VIEW SUPPLIER DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="form-outline mb-4">
                                <input type="text" id="supplier_name1" name="supplier_name1" class="form-control" disabled/>
                                <label class="form-label" for="supplier_name1">Supplier Name</label>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="text" id="supplier_address1" name="supplier_address1" class="form-control" disabled/>
                                <label class="form-label" for="supplier_address1">Supplier Address</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="number" id="supplier_contact1" name="supplier_contact1" min="0" class="form-control" disabled/>
                                <label class="form-label" for="supplier_contact1">Supplier Contact</label>
                            </div>

                            <div class="mb-4">
                                <label class="select2-label" for="supplier_route1">Select Route</label> 
                                <select class="form-control" id="supplier_route1" name="supplier_route1" disabled>
                                    @if (isset($data['route']))
                                        @foreach ($data['route'] as $route)
                                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="edit_model" tabindex="-1" aria-labelledby="edit_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
            <div class="modal-dialog .modal-side .modal-top-right">
                <div class="modal-content custom-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_model_Label">EDIT SUPPLIER DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="form-outline mb-4">
                                <input type="text" id="supplier_name2" name="supplier_name2" class="form-control" required/>
                                <label class="form-label" for="supplier_name2">Supplier Name</label>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="text" id="supplier_address2" name="supplier_address2" class="form-control" required/>
                                <label class="form-label" for="supplier_address2">Supplier Address</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="number" id="supplier_contact2" name="supplier_contact2" min="0" class="form-control" required/>
                                <label class="form-label" for="supplier_contact2">Supplier Contact</label>
                            </div>

                            <div class="mb-4">
                                <label class="select2-label" for="supplier_route2">Select Route</label> 
                                <select class="form-control" id="supplier_route2" name="supplier_route2">
                                    @if (isset($data['route']))
                                        @foreach ($data['route'] as $route)
                                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="supplier_id2" name="supplier_id2"/>
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="editSupplier()" class="btn btn-primary-custom float-right">EDIT</button>
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

    var supplierTable;

    $(document).ready(function() {
        $('#supplier_route').select2();
        $('#supplier_route2').select2();
        $('.side-link.li-supp').addClass('active');
        supplierDatatable();
    });

    function supplierDatatable() {
    
        supplierTable = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/suppliers-datatable') }}",
        columns: [
                { data:'sup_name', name:'sup_name'},
                { data:'sup_address', name:'sup_address'},
                { data:'sup_contact', name:'sup_contact'},
                { data:'action', name:'action', orderable: false, searchable: false},
        ]});

    }

    function insertSupplier() {
        if ( $("#supplier_name").val().length === 0 ){
            swal("Opps !", "Please enter supplier name", "error");
        }
        else if ($("#supplier_address").val().length === 0) {
            swal("Opps !", "Please enter supplier address", "error");
        }
        else if($("#supplier_contact").val().length === 0) {
            swal("Opps !", "Please enter supplier contact", "error");
        }
        else if($("#supplier_route").val().length === 0) {
            swal("Opps !", "Please enter supplier route", "error");
        }
        else {

            var supplier_name = $("#supplier_name").val();
            var supplier_address = $("#supplier_address").val();
            var supplier_contact = $("#supplier_contact").val();
            var supplier_route = $("#supplier_route").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{url("/admin/supplier-insert")}}',
                type: 'POST',
                data: {
                    supplier_name:supplier_name,
                    supplier_address:supplier_address,
                    supplier_contact:supplier_contact,
                    supplier_route:supplier_route
                },
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        console.log(data);
                        swal("Good Job !", data.message, "success");
                        $("#supplier_name").val('');
                        $("#supplier_address").val('');
                        $("#supplier_contact").val('');
                        $("#supplier_contact").val('');
                        $('#insert_model').modal('toggle');
                        supplierTable.ajax.reload();
                    }
                    else {
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });

        }
    }

    function sendDataToViewModel(id){
        // console.log(id);
        $.ajax({
                url: '{{url("/admin/supplier-get-data")}}',
                type: 'GET',
                data: {id:id},
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        // console.log(data);
                        $("#supplier_name1").val(data.data.sup_name);
                        $("#supplier_address1").val(data.data.sup_address);
                        $("#supplier_contact1").val(data.data.sup_contact);
                        $("#supplier_route1").val(data.data.route_id).trigger('change');
                        $('#view_model').modal('toggle');
                    }
                    else {
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });
    }

    function sendDataToEditModel(id){
        // console.log(id);
        $.ajax({
                url: '{{url("/admin/supplier-get-data")}}',
                type: 'GET',
                data: {id:id},
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        // console.log(data);
                        $("#supplier_name2").val(data.data.sup_name);
                        $("#supplier_address2").val(data.data.sup_address);
                        $("#supplier_contact2").val(data.data.sup_contact);
                        $("#supplier_route2").val(data.data.route_id).trigger('change');
                        $("#supplier_id2").val(data.data.id);
                        $('#edit_model').modal('toggle');
                    }
                    else {
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });
    }

    function editSupplier() {
        if ( $("#supplier_name2").val().length === 0 ){
            swal("Opps !", "Please enter supplier name", "error");
        }
        else if ($("#supplier_address2").val().length === 0) {
            swal("Opps !", "Please enter supplier address", "error");
        }
        else if($("#supplier_contact2").val().length === 0) {
            swal("Opps !", "Please enter supplier contact", "error");
        }
        else if($("#supplier_route2").val().length === 0) {
            swal("Opps !", "Please enter supplier route", "error");
        }
        else {

            var supplier_id = $("#supplier_id2").val();
            var supplier_name = $("#supplier_name2").val();
            var supplier_address = $("#supplier_address2").val();
            var supplier_contact = $("#supplier_contact2").val();
            var supplier_route = $("#supplier_route2").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{url("/admin/supplier-edit")}}',
                type: 'POST',
                data: {
                    supplier_id:supplier_id,
                    supplier_name:supplier_name,
                    supplier_address:supplier_address,
                    supplier_contact:supplier_contact,
                    supplier_route:supplier_route
                },
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        console.log(data);
                        supplierTable.ajax.reload();
                        $('#edit_model').modal('toggle');
                        swal("Good Job !", data.message, "success");
                    }
                    else {
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });
                    

        }
    }

    function deleteSupplier(id) {

        swal({
            title: 'Are you sure?',
            text: "You are going to delete this supplier !",
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
                    url: '{{url("/admin/supplier-delete")}}',
                    type: 'POST',
                    data: {supplier_id:id},
                    dataType: 'JSON',
                    success: function (data) { 
                        if(data.result == true) {
                            console.log(data);
                            supplierTable.ajax.reload();
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

    function activateSupplier(id) {

        swal({
            title: 'Are you sure?',
            text: "You are going to activate this supplier !",
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
                    url: '{{url("/admin/supplier-reactivate")}}',
                    type: 'POST',
                    data: {supplier_id:id},
                    dataType: 'JSON',
                    success: function (data) { 
                        if(data.result == true) {
                            console.log(data);
                            supplierTable.ajax.reload();
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
