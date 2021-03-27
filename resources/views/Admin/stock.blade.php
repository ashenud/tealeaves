@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/custom-table-style.css')}}">

<link rel="stylesheet" href="{{ asset('css/stock-manage-style.css') }}">

@endsection

@section('navbar')
@include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">

        <div class="data-table-area">
            <table width="98%" class="table data-table table-hover">
                <thead>
                    <tr>
                        <th width="20%" scope="col">Item Code</th>
                        <th width="28%" scope="col">Item Name</th>
                        <th width="20%" scope="col">Item Type</th>
                        <th width="15%"scope="col">Item Price</th>
                        <th width="15%"scope="col">Current Stock</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <a class="btn btn-add-floating btn-primary btn-lg btn-floating" data-mdb-toggle="modal" data-mdb-target="#insert_model" type="button">
            <i class="fas fa-plus"></i>
        </a>

        <!-- GRN ADD Modal -->
        <div class="modal fade" id="insert_model" aria-labelledby="insert_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
            <div class="modal-dialog .modal-side modal-dialog-scrollable modal-lg">
                <div class="modal-content custom-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="insert_model_Label">GOOD RECEIVED NOTE</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <table class="table details-table">
                                <tr>
                                    <td>Date</td>
                                    <td> : </td>
                                    <td><input type="date" id="grn_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"></td>
                                </tr>
                                <tr>
                                    <td>GRN No.</td>
                                    <td> : </td>
                                    <td><input type="text" id="grn_no" class="form-control" value="{{ $data['grn_no'] }}" readonly></td>
                                </tr>
                            </table>

                            <table width="100%" class="table" align="center">
                                <thead>
                                    <tr>
                                        <th width="5%" height="25"></th>
                                        <th width="30%" scope="col">Item Code</th>
                                        <th width="45%" scope="col">Item Name</th>
                                        <th width="20%" scope="col">GRN Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="item_tbl">
                                    <tr id="tr_1" style="height: 30px">
                                        <td>
                                            <div class="form-group">
                                                <button type="button" onclick="add_item(1)" class="plus_icon btn btn-floating" id="plus_icon_1"><i class="fas fa-plus"></i></button>
                                                <button type="button" onclick="remove_item(1)" class="minus_icon btn btn-floating" id="minus_icon_1" style="display: none"><i class="fas fa-minus"></i></button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control item-name" id="item_1" onchange="getItemValues(1)">
                                                    <option value="">Select Item</option>
                                                    @if (isset($data['items']))
                                                        @foreach ($data['items'] as $item)
                                                            <option value="{{ $item->value }}">{{ $item->item_code }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>                                    
                                                <input type="hidden" id="item_id_1">                                           
                                            </div>
                                        </td>                                        
                                        <td>
                                            <div class="form-group">
                                                <input type="text" id="item_name_1" class="form-control" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" id="grn_quantity_1" class="form-control text-right" min="1" autocomplete="off" onkeypress="return event.charCode >= 48">
                                            </div>
                                        </td>
                                    </tr>                                         
                                </tbody> 
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="submit_data_to_db()" class="btn btn-primary-custom float-right">ADD GRN</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- WRITE OFF ADJUESTMENT -->
        {{-- <div class="modal fade" id="edit_model" tabindex="-1" aria-labelledby="edit_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
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
        </div> --}}

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
    var count = 1;

    $(document).ready(function() {
        $('#item_1').select2();
        $('.side-link.li-stock').addClass('active');
        itemDatatable();
    });

    function itemDatatable() {
    
        itemTable = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin/stock-datatable') }}",
        columns: [
                { data:'item_code', name:'item_code'},
                { data:'item_name', name:'item_name'},
                { data:'type_name', name:'type_name'},
                { data:'unit_price', name:'unit_price'},
                { data:'current_quantity', name:'current_quantity'},
        ]});

    }
    
    // (product) validate if same item usimg twice
    function getItemValues(row) {

        var values =  $("#item_" + row).val().split(",");
        $("#item_id_" + row).val(values[0]);
        $("#item_name_" + row).val(values[1]);

        valid = true;
        if($("#item_id_" + row).val() != null && $("#item_id_" + row).val() != '' ) {
            var current_row_item = $("#item_id_" + row).val();

            var items = []; // to check if same item exist with same type twice
            for (var i = 1; i <= count; i++) {
                if(i!=row) {
                    if($("#item_id_" + i).val() != null && $("#item_id_" + i).val() != '') {
                        var value = $("#item_id_" + i).val();
                        items.push(value);
                    }
                }
            }

            if(items.indexOf(current_row_item) !== -1){
                valid = false;
            } else{
                valid = true;
            }
        }

        if (valid === false) {
            $("#item_" + row).val('').trigger("change");
            $("#item_id_" + row).val('');
            $("#item_name_" + row).val('');
            $("#grn_quantity_" + row).val('');
            $("#item_" + row).next().find('.select2-selection').addClass('is-invalid');
            swal("Error!", "Can not add same item twice", "error");
        }
        else {
            $("#item_" + row).next().find('.select2-selection').removeClass('is-invalid');
        }
    }
    
    function add_item(row) {

        $('#item_tbl').append('<tr id="tr_' + (row + 1) + '">' +
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<button type="button" onclick="add_item(' + (row + 1) + ')" class="plus_icon btn btn-floating" id="plus_icon_' + (row + 1) + '"><i class="fas fa-plus"></i></button>'+
                                            '<button type="button" onclick="remove_item(' + (row + 1) + ')" class="minus_icon btn btn-floating" id="minus_icon_' + (row + 1) + '" style="display: none"><i class="fas fa-minus"></i></button>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<select class="form-control item-name" id="item_' + (row + 1) + '" onchange="getItemValues(' + (row + 1) + ')">'+
                                                '<option value="">Select Item</option>'+
                                                '@if (isset($data["items"]))'+
                                                    '@foreach ($data["items"] as $item)'+
                                                        '<option value="{{ $item->value }}">{{ $item->item_code }}</option>'+
                                                    '@endforeach'+
                                                '@endif'+
                                            '</select>'+                                  
                                            '<input type="hidden" id="item_id_' + (row + 1) + '">'+                                       
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="text" id="item_name_' + (row + 1) + '" class="form-control" readonly>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="number" id="grn_quantity_' + (row + 1) + '" class="form-control text-right" min="1" autocomplete="off" onkeypress="return event.charCode >= 48">'+
                                        '</div>'+
                                    '</td>'+
                                '</tr>');


        document.getElementById('plus_icon_' + row).style.display = 'none';
        document.getElementById('minus_icon_' + row).style.display = 'block';
      
        $('#item_' + (row + 1)).select2();        

        count = count + 1;
        $("#count").val(count);

    }

    function remove_item(row) {

        $('#tr_' + row).remove();

    }

    function submit_data_to_db() {
        if ($('#grn_date').val() === "") {
            $("#grn_date").addClass('is-invalid');
            swal("Retry!", "Please select the grn date", "error");
            $('#s_id').focus();
        }
        else if ($('#grn_no').val() === "") {
            $("#grn_no").addClass('is-invalid');
            $("#grn_date").removeClass('is-invalid');
            swal("Retry!", "Please select the grn date", "error");
            $('#s_id').focus();
        }
        else  {
            $("#grn_no").removeClass('is-invalid');
            valid = true;
            var arr = [];
            for (var i = 1; i <= count; i++) {

                // validate if current row actualy has a product
                if( ($("#item_id_" + i).val() != "") && (typeof $("#item_id_" + i).val() !== 'undefined') ) {

                    var item_id = $("#item_id_" + i).val();
                    $("#item_" + i).removeClass('is-invalid');

                    if( $("#grn_quantity_" + i).val() > 0 ) {
                        var grn_quantity = $("#grn_quantity_" + i).val();
                        $("#grn_quantity_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#grn_quantity_" + i).addClass('is-invalid');
                    }

                    if (valid === true) {

                        var obj = {
                            'item_id': item_id,
                            'grn_quantity': grn_quantity,                              
                        };

                        arr.push(obj);

                    }
                }
                else {
                    $("#item_" + i).addClass('is-invalid');
                }

            }

            if (valid === true) {
                // when the first line is empty
                if(arr.length === 0) {
                    swal("Retry!", "Please add at least one item line", "error");
                }
                else {

                    var item_array = JSON.stringify(arr);
                    // console.log(JSON.parse(item_array));
                    var grn_date = $('#grn_date').val();
                    var grn_no = $('#grn_no').val();

                    swal({
                        title: 'Are you sure?',
                        text: "Do you want to add this records !",
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
                                url: '{{url("/admin/insert-grn")}}',
                                type: "POST",
                                data: {

                                    grn_date: grn_date,
                                    grn_no: grn_no,
                                    item_array: item_array,

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
                    })

                }

            }
            else {
                swal("Retry!", "Please fill all the blanks", "error");
            }
        }

    }

</script>

@endsection
