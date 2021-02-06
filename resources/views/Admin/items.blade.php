@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

<link rel="stylesheet" href="{{ asset('css/item-style.css') }}">

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
                        <th width="20%" scope="col">Item Code</th>
                        <th width="20%" scope="col">Item Name</th>
                        <th width="20%" scope="col">Item Type</th>
                        <th width="20%"scope="col">Item Price</th>
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
                        <h5 class="modal-title" id="insert_model_Label">INSERT ITEM DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="form-outline mb-4">
                                <input type="text" id="item_code" name="item_code" class="form-control" required/>
                                <label class="form-label" for="item_code">Item Code</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="text" id="item_name" name="item_name" class="form-control" required/>
                                <label class="form-label" for="item_name">Item Name</label>
                            </div>
                            <div class="mb-4">
                                <label class="select2-label" for="item_type">Select Type</label> 
                                <select class="form-control" id="item_type" name="item_type">
                                    @if (isset($data['item_types']))
                                        @foreach ($data['item_types'] as $type)
                                            @if ($type->id != config('application.tealeaves_type') && $type->id != config('application.teabag_type') && $type->id != config('application.dolamite_type'))
                                                <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-outline mb-3">
                                <input type="number" id="unit_price" name="unit_price" min="0" class="form-control" required/>
                                <label class="form-label" for="unit_price">Unit Price</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="insertItem()" class="btn btn-primary-custom float-right">INSERT</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div class="modal fade" id="view_model" tabindex="-1" aria-labelledby="view_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
            <div class="modal-dialog .modal-side .modal-top-right">
                <div class="modal-content custom-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="view_model_Label">VIEW ITEM DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="form-outline mb-4">
                                <input type="text" id="item_code1" name="item_code1" class="form-control" readonly/>
                                <label class="form-label" for="item_code1">Item Code</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="text" id="item_name1" name="item_name1" class="form-control" readonly/>
                                <label class="form-label" for="item_name1">Item Name</label>
                            </div>
                            <div class="mb-4">
                                <label class="select2-label" for="item_type1">Select Type</label> 
                                <select class="form-control" id="item_type1" name="item_type1" disabled>
                                    @if (isset($data['item_types']))
                                        @foreach ($data['item_types'] as $type)
                                            <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-outline mb-3">
                                <input type="number" id="unit_price1" name="unit_price1" min="0" class="form-control" readonly/>
                                <label class="form-label" for="unit_price1">Unit Price</label>
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
                        <h5 class="modal-title" id="edit_model_Label">EDIT ITEM DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <div class="form-outline mb-4">
                                <input type="text" id="item_code2" name="item_code2" class="form-control" required/>
                                <label class="form-label" for="item_code2">Item Code</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="text" id="item_name2" name="item_name2" class="form-control" required/>
                                <label class="form-label" for="item_name2">Item Name</label>
                            </div>
                            <div class="form-outline mb-3">
                                <input type="number" id="unit_price2" name="unit_price2" min="0" class="form-control" required/>
                                <label class="form-label" for="unit_price2">Unit Price</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="item_id" name="item_id"/>
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="editItem()" class="btn btn-primary-custom float-right">EDIT</button>
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

    var itemTable;

    $(document).ready(function() {
        $('#item_type').select2();
        $('.side-link.li-item').addClass('active');
        itemDatatable();
    });

    function itemDatatable() {
    
        itemTable = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/items-datatable') }}",
        columns: [
                { data:'item_code', name:'item_code'},
                { data:'item_name', name:'item_name'},
                { data:'type_name', name:'type_name'},
                { data:'unit_price', name:'unit_price'},
                { data:'action', name:'action', orderable: false, searchable: false},
        ]});

    }

    function insertItem() {
        if ( $("#item_code").val().length === 0 ){
            swal("Opps !", "Please enter item code", "error");
        }
        else if ($("#item_name").val().length === 0) {
            swal("Opps !", "Please enter item name", "error");
        }
        else if($("#item_type").val().length === 0) {
            swal("Opps !", "Please enter item type", "error");
        }
        else if($("#unit_price").val().length === 0) {
            swal("Opps !", "Please enter unit price", "error");
        }
        else {

            var item_code = $("#item_code").val();
            var item_name = $("#item_name").val();
            var item_type = $("#item_type").val();
            var unit_price = $("#unit_price").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{url("/admin/item-insert")}}',
                type: 'POST',
                data: {
                    item_code:item_code,
                    item_name:item_name,
                    item_type:item_type,
                    unit_price:unit_price
                },
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        console.log(data);
                        swal("Good Job !", data.message, "success");
                        $("#item_code").val('');
                        $("#item_name").val('');
                        $("#item_type").val('').trigger('change');
                        $("#unit_price").val('');
                        $('#insert_model').modal('toggle');
                        itemTable.ajax.reload();
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
                url: '{{url("/admin/item-get-data")}}',
                type: 'GET',
                data: {id:id},
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        // console.log(data);
                        $("#item_code1").val(data.data.item_code);
                        $("#item_name1").val(data.data.item_name);
                        $("#item_type1").val(data.data.item_type).trigger('change');
                        $("#unit_price1").val(data.data.unit_price);
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
                url: '{{url("/admin/item-get-data")}}',
                type: 'GET',
                data: {id:id},
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        // console.log(data);
                        if(data.data.item_type === 1 || data.data.item_type === 2 || data.data.item_type === 3) {
                            $("#item_code2").val(data.data.item_code).attr('readonly', true);
                            $("#item_name2").val(data.data.item_name).attr('readonly', true);
                            $("#unit_price2").val(data.data.unit_price);
                            $("#item_id").val(data.data.id);
                            $('#edit_model').modal('toggle');
                        }
                        else {
                            $("#item_code2").val(data.data.item_code).attr('readonly', false);
                            $("#item_name2").val(data.data.item_name).attr('readonly', false);
                            $("#unit_price2").val(data.data.unit_price);
                            $("#item_id").val(data.data.id);
                            $('#edit_model').modal('toggle');
                        }
                    }
                    else {
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });
    }

    function editItem() {
        if ( $("#item_code2").val().length === 0 ){
            swal("Opps !", "Please enter item code", "error");
        }
        else if ($("#item_name2").val().length === 0) {
            swal("Opps !", "Please enter item name", "error");
        }
        else if($("#unit_price2").val().length === 0) {
            swal("Opps !", "Please enter unit price", "error");
        }
        else {

            var item_id = $("#item_id").val();
            var item_code = $("#item_code2").val();
            var item_name = $("#item_name2").val();
            var unit_price = $("#unit_price2").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{url("/admin/item-edit")}}',
                type: 'POST',
                data: {
                    item_id:item_id,
                    item_code:item_code,
                    item_name:item_name,
                    unit_price:unit_price
                },
                dataType: 'JSON',
                success: function (data) { 
                    if(data.result == true) {
                        console.log(data);
                        itemTable.ajax.reload();
                        $('#edit_model').modal('toggle');
                        swal("Good Job !", data.message, "success");
                    }
                    else {
                        console.log(data);
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });
                    

        }
    }

    function deleteItem(id) {
        
        swal({
            title: 'Are you sure?',
            text: "You are going to delete this item !",
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
                    url: '{{url("/admin/item-delete")}}',
                    type: 'POST',
                    data: {item_id:id},
                    dataType: 'JSON',
                    success: function (data) { 
                        if(data.result == true) {
                            console.log(data);
                            itemTable.ajax.reload();
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
