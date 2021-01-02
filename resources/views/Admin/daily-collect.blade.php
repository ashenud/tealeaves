@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/collection-table-style.css')}}">

@endsection

@section('navbar')
@include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">

        <div class="row common-area">
            <div class="col-md-3">
            <input class="form-control" type="date" max="{{ date('Y-m-d') }}" id="collection_date">
            </div>
        </div> 

        <div class="row supplier-area" id="supplier-area">
            <div class="supplier-list col-md-12">
                <table width="100%" class="table" align="center">
                    <thead>
                        <tr>
                            <th width="5%" height="25"></th>
                            <th width="35%">Supplier</th>
                            <th width="20%">Item Name</th>
                            <th width="10%">C.Price(Rs.)</th>
                            <th width="10%">D.Cost(Rs.)</th>
                            <th width="10%">No. of Units</th>
                            <th width="15%">Amount(Rs.)</th>
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
                                    <select class="form-control supplier-name" id="supplier_1" onchange="getSupplierValues(1)">
                                        <option value=""></option>
                                        @if (isset($data['suppliers']))
                                            @foreach ($data['suppliers'] as $supplier)
                                                <option value="{{ $supplier->value }}">{{ $supplier->sup_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>                                    
                                    <input type="hidden" id="supplier_id_1">                                             
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" id="item_1" class="form-control" value="{{ $data['item_name'] }}" readonly>    
                                    <input type="hidden" id="item_id_1" value="{{ $data['item_id'] }}"> 
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" id="current_price_1" class="form-control text-right" value="{{ $data['item_price'] }}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="hidden" id="delivery_cost_per_unit_1">
                                    <input type="text" id="delivery_cost_1" class="form-control text-right" value="0.00" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="number" id="no_units_1" class="form-control text-right" min="1" autocomplete="off" onkeypress="return event.charCode >= 48" onkeyup="cal_total(1)">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="hidden" id="daily_amount_1">
                                    <input type="text" id="daily_value_1" class="form-control text-right" value="0.00" readonly>
                                </div>
                            </td>
                        </tr>                                         
                    </tbody>                                       
                    
                    <tbody>
                        <input id="count" type="hidden" value="1">  
                        
                        <!--Display Daily Total-->
                        <tr>
                            <td colspan="6" style="text-align: right">TOTAL VALUE (RS.) &nbsp;</td>
                            <td>
                                <div class="form-group">
                                    <b> <input id="daily_total_value" class="form-control daily-total" value="0.00" readonly> </b>
                                </div> 
                            </td>
                        </tr>
                        <tr class="submit-button-row">
                            <td colspan="7" align="right">
                                <input class="btn btn-primary-custom submit-btn" type="button" class="btn" value="SUBMIT COLLECTION DATA"  id="dd" onclick="submit_data_to_db()" />
                            </td>
                        </tr>

                    </tbody>
                </table>
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

<script>

    var count = 1;

    $(document).ready(function() {
        $('#supplier_1').select2();
        $('.side-link.li-collect').addClass('active');
    });

    function getSupplierValues(row) {

        var values =  $("#supplier_" + row).val().split(",");
        $("#supplier_id_" + row).val(values[0]);
        $("#delivery_cost_per_unit_" + row).val(values[1]);

        valid = true;
        if($("#supplier_id_" + row).val() != null && $("#supplier_id_" + row).val() != '' ) {
            var current_row_supplier = $("#supplier_id_" + row).val();

            var suppliers = []; // to check if same supplier exist with same type twice
            for (var i = 1; i <= count; i++) {
                if(i!=row) {
                    if($("#supplier_id_" + i).val() != null && $("#supplier_id_" + i).val() != '') {
                        var value = $("#supplier_id_" + i).val();
                        suppliers.push(value);
                    }
                }
            }

            if(suppliers.indexOf(current_row_supplier) !== -1){
                valid = false;
            } else{
                valid = true;
            }
        }

        if (valid === false) {
            $("#supplier_id_" + row).val('');
            $("#delivery_cost_per_unit_" + row).val('');
            $("#supplier_" + row).val('').trigger("change");
            $("#supplier_" + row).next().find('.select2-selection').addClass('is-invalid');
            swal("Error!", "Can not add same supplier twice", "error");
        }
        else {
            $("#supplier_" + row).next().find('.select2-selection').removeClass('is-invalid');
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
                                            '<select class="form-control supplier-name" id="supplier_' + (row + 1) + '" onchange="getSupplierValues(' + (row + 1) + ')">'+
                                                '<option value=""></option>'+
                                                '@if (isset($data["suppliers"]))'+
                                                    '@foreach ($data["suppliers"] as $supplier)'+
                                                        '<option value="{{ $supplier->value }}">{{ $supplier->sup_name }}</option>'+
                                                    '@endforeach'+
                                                '@endif'+
                                            '</select>'+                                    
                                            '<input type="hidden" id="supplier_id_' + (row + 1) + '">'+                                             
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="text" id="item_' + (row + 1) + '" class="form-control" value="{{ $data["item_name"] }}" readonly>'+    
                                            '<input type="hidden" id="item_id_' + (row + 1) + '" value="{{ $data["item_id"] }}">'+ 
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="text" id="current_price_' + (row + 1) + '" class="form-control text-right" value="{{ $data["item_price"] }}" readonly>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="hidden" id="delivery_cost_per_unit_' + (row + 1) + '">'+
                                            '<input type="text" id="delivery_cost_' + (row + 1) + '" class="form-control text-right" value="0.00" readonly>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="number" id="no_units_' + (row + 1) + '" class="form-control text-right" min="1" autocomplete="off" onkeypress="return event.charCode >= 48" onkeyup="cal_total(' + (row + 1) + ')">'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="hidden" id="daily_amount_' + (row + 1) + '">'+
                                            '<input type="text" id="daily_value_' + (row + 1) + '" class="form-control text-right" value="0.00" readonly>'+
                                        '</div>'+
                                    '</td>'+
                                '</tr>');


        document.getElementById('plus_icon_' + row).style.display = 'none';
        document.getElementById('minus_icon_' + row).style.display = 'block';

        
        $('#supplier_' + (row + 1)).select2();        

        count = count + 1;
        $("#count").val(count);

    }

    function remove_item(row) {

        var sum = 0;
        $('#tr_' + row).remove();

        for (var i = 1; i <= count; i++) {

            if (($("#daily_value_" + i).val() != '') && ($("#daily_value_" + i).val() != null)) {
                sum += parseFloat($("#daily_value_" + i).val());
            }
            $("#daily_total_value").val(sum);
        }

    }

    function load_net_total_amt() {
        var sum = 0;
        for (var i = 1; i <= count; i++) {
            if (($("#daily_value_" + i).val() != '') && ($("#daily_value_" + i).val() != null)) {
                sum += parseFloat($("#daily_value_" + i).val());
            }
        }
        $('#daily_total_value').val(parseFloat(sum).toFixed(2));

    }

    function cal_total(row) {

        if( ($("#supplier_id_" + row).val() != "") && (typeof $("#supplier_id_" + row).val() !== 'undefined') ) {

            $("#prodcut_" + row).removeClass('is-invalid');
            var no_of_units = $("#no_units_" + row).val();        
            var unit_price = $("#current_price_" + row).val();
            var delivery_cost_per_unit = $("#delivery_cost_per_unit_" + row).val();

            var sum = 0;

            $("#delivery_cost_" + row).val(parseFloat(no_of_units*delivery_cost_per_unit).toFixed(2));
            $("#daily_amount_" + row).val(parseFloat(no_of_units*unit_price).toFixed(2));
            $("#daily_value_" + row).val(parseFloat(no_of_units*(unit_price - delivery_cost_per_unit)).toFixed(2));

            for (var i = 1; i <= count; i++) {
                if ($("#daily_value_" + i).val() != "" && ($("#daily_value_" + i).val() != null)) {

                    sum += parseFloat($("#daily_value_" + i).val());
                    $("#daily_total_value").val(sum.toFixed(2));
                }
            } 
        }
        else {
            $("#supplier_" + row).next().find('.select2-selection').addClass('is-invalid');
            swal("Retry!", "Please select a supplier first", "error");
            $("#no_units_" + row).val('');  
        }
        
    }

    function submit_data_to_db() {
        if ($('#collection_date').val() === "") {
            $("#collection_date").addClass('is-invalid');
            swal("Retry!", "Please select the collection date", "error");
            $('#s_id').focus();
        }
        else  {
            $("#collection_date").removeClass('is-invalid');
            valid = true;
            var arr = [];
            for (var i = 1; i <= count; i++) {

                // validate if current row actualy has a product
                if( ($("#supplier_id_" + i).val() != "") && (typeof $("#supplier_id_" + i).val() !== 'undefined') ) {

                    var supplier_id = $("#supplier_id_" + i).val();
                    var item_id = $("#item_id_" + i).val();
                    var delivery_cost_per_unit = $("#delivery_cost_per_unit_" + i).val();
                    var delivery_cost = $("#delivery_cost_" + i).val();
                    var daily_amount = $("#daily_amount_" + i).val();
                    $("#supplier_" + i).removeClass('is-invalid');

                    if( $("#no_units_" + i).val() > 0 ) {
                        var no_of_units = $("#no_units_" + i).val();
                        $("#no_units_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#no_units_" + i).addClass('is-invalid');
                    }

                    if( $("#daily_value_" + i).val() > 0 ) {
                        var daily_value = $("#daily_value_" + i).val();
                        $("#daily_value_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#daily_value_" + i).addClass('is-invalid');
                    }

                    if( ($("#current_price_" + i).val() != null) && ($("#current_price_" + i).val() > 0 ) ) {
                        var current_price = $("#current_price_" + i).val();
                        $("#current_price_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#current_price_" + i).addClass('is-invalid');
                    }

                    if (valid === true) {

                        var obj = {
                            'supplier_id': supplier_id,
                            'item_id': item_id,
                            'current_price': current_price,
                            'delivery_cost_per_unit': delivery_cost_per_unit,
                            'no_of_units': no_of_units,
                            'delivery_cost': delivery_cost,
                            'daily_amount': daily_amount,
                            'daily_value': daily_value,                                
                        };

                        arr.push(obj);

                    }
                }
                else {
                    $("#supplier_" + i).addClass('is-invalid');
                }

            }

            if (valid === true) {
                // when the first line is empty
                if(arr.length === 0) {
                    swal("Retry!", "Please add at least one supplier line", "error");
                }
                else {

                    var colection_array = JSON.stringify(arr);
                    // console.log(JSON.parse(colection_array));
                    var collection_date = $('#collection_date').val();
                    var daily_total_value = $('#daily_total_value').val();

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
                                url: '{{url("/admin/insert-collection")}}',
                                type: "POST",
                                data: {

                                    collection_date: collection_date,
                                    colection_array: colection_array,
                                    daily_total_value: daily_total_value,

                                },
                                success: function (data) {
                                    var data = JSON.parse(data);
                                    console.log(data);
                                    if(data.status===true){
                                        sweetAlert({
                                            title: "Done!",
                                            text: data.message,
                                            type: "success"
                                        },
                                        function () {
                                            location.reload();
                                        });
                                    }
                                    else{
                                        sweetAlert({
                                            title: "Opps!",
                                            text: data.message,
                                            type: "error"
                                        },
                                        function () {
                                            location.reload();
                                        });
                                    }
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    swal("Opps!", "Please try again", "error");
                                }
                            });

                        } /* else {
                            swal("Your imaginary file is safe!");
                        } */
                    });
                }

            }
            else {
                swal("Retry!", "Please fill all the blanks", "error");
            }
        }
        /* 
            
        */
    }

</script>

@endsection
