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
            <input class="form-control collection-date" type="date" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" id="collection_date" onchange="loadCollection()">
            </div>
        </div> 

        <div class="row supplier-area" id="supplier-area">
            
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
    var remove_suppliers = [];

    $(document).ready(function() {
        $('#collection_date').trigger("change");
        $('.side-link.li-collect').addClass('active');
    });
    $(document).ajaxComplete(function ( event, xhr, settings ) {      
        if (typeof xhr.responseJSON === 'undefined')   {
            $('#supplier_1').select2();
            count = parseFloat($('#count').val());
            // console.log(count);
        }
        else {
            // console.log(xhr.responseJSON.result);
        }        
    });

    function loadCollection() {

        var collection_date = $('#collection_date').val();
        var regEx = /^\d{4}-\d{2}-\d{2}$/;
        if(collection_date.match(regEx)) {
            var apiURL = baseURL+'admin/load-insert-collection/'+collection_date;
            // console.log(URL);
            $('#supplier-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
            $('#supplier-area').load(apiURL);
        }
        else {
            swal("Retry!", "Please select a valid date", "error");
        }
        

    }

    function loadEditCollection(collection_id) {
        
        remove_suppliers = [];

        var apiURL = baseURL+'admin/load-edit-collection/'+collection_id;
        // console.log(apiURL);
        $('#supplier-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
        $('#supplier-area').load(apiURL);        

    }

    function getSupplierValues(row) {

        var values =  $("#supplier_" + row).val().split(",");
        $("#supplier_id_" + row).val(values[0]);
        $("#delivery_cost_per_unit_" + row).val(values[1]);
        $("#supplier_name_" + row).val(values[2]);

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
            $("#supplier_name_" + row).val('');
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
                                                '<option value="">Select Supplier ID</option>'+
                                                '@if (isset($data["suppliers"]))'+
                                                    '@foreach ($data["suppliers"] as $supplier)'+
                                                        '<option value="{{ $supplier->value }}">{{ $supplier->sup_id }}</option>'+
                                                    '@endforeach'+
                                                '@endif'+
                                            '</select>'+                                    
                                            '<input type="hidden" id="supplier_id_' + (row + 1) + '">'+                                             
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="text" id="supplier_name_' + (row + 1) + '" class="form-control" readonly>'+
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

        if( ($("#actual_supplier_count").val() != "") && (typeof $("#actual_supplier_count").val() !== 'undefined') ) {
            var actual_count = $("#actual_supplier_count").val();
            if(row <= actual_count) {
                var sup_collection_id = $('#sup_collection_id_' + row).val();
                remove_suppliers.push(sup_collection_id);
            }
        }
        else {
            remove_suppliers = [];
        }

        $('#tr_' + row).remove();

        for (var i = 1; i <= count; i++) {

            if (($("#daily_value_" + i).val() != '') && ($("#daily_value_" + i).val() != null)) {
                sum += parseFloat($("#daily_value_" + i).val());
            }
            $("#daily_total_value").val(sum.toFixed(2));
        }

        // console.log(remove_suppliers);

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

            $("#supplier_" + row).next().find('.select2-selection').removeClass('is-invalid');
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

                    if( ($("#current_price_" + i).val() != null) && ($("#current_price_" + i).val() > 0 ) ) {
                        var current_price = $("#current_price_" + i).val();
                        $("#current_price_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#current_price_" + i).addClass('is-invalid');
                    }

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
                        // $("#daily_value_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        // $("#daily_value_" + i).addClass('is-invalid');
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

                    var collection_array = JSON.stringify(arr);
                    // console.log(JSON.parse(collection_array));
                    var collection_date = $('#collection_date').val();
                    var daily_total_value = $('#daily_total_value').val();

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
                                    collection_array: collection_array,
                                    daily_total_value: daily_total_value,

                                },
                                success: function (data) {
                                    // var data = JSON.parse(data);
                                    console.log(data);
                                    if(data.result===true){
                                        swal("Done!", data.message, "success")
                                        .then((value) => {
                                            $('#collection_date').val(collection_date).trigger("change");
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

    function submit_edited_data_to_db() {
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

                    if( ($("#sup_collection_id_" + i).val() != "") && (typeof $("#sup_collection_id_" + i).val() !== 'undefined') ) {
                        var sup_col_id = $("#sup_collection_id_" + i).val();
                    }
                    else {
                        var sup_col_id = 0; 
                    }

                    var supplier_id = $("#supplier_id_" + i).val();
                    var item_id = $("#item_id_" + i).val();
                    var delivery_cost_per_unit = $("#delivery_cost_per_unit_" + i).val();
                    var delivery_cost = $("#delivery_cost_" + i).val();
                    var daily_amount = $("#daily_amount_" + i).val();
                    $("#supplier_" + i).removeClass('is-invalid');

                    if( ($("#current_price_" + i).val() != null) && ($("#current_price_" + i).val() > 0 ) ) {
                        var current_price = $("#current_price_" + i).val();
                        $("#current_price_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#current_price_" + i).addClass('is-invalid');
                    }

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
                        // $("#daily_value_" + i).removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        // $("#daily_value_" + i).addClass('is-invalid');
                    }

                    if (valid === true) {

                        var obj = {
                            'sup_col_id': sup_col_id,
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

                    var collection_array = JSON.stringify(arr);
                    var removed_suppliers = JSON.stringify(remove_suppliers);
                    // console.log(JSON.parse(collection_array));
                    var collection_id = $('#collection_id').val();
                    var collection_date = $('#collection_date').val();
                    var daily_total_value = $('#daily_total_value').val();

                    swal({
                        title: 'Are you sure?',
                        text: "Do you want to add these edited records !",
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
                                url: '{{url("/admin/edit-collection")}}',
                                type: "POST",
                                data: {

                                    collection_id: collection_id,
                                    collection_date: collection_date,
                                    collection_array: collection_array,
                                    removed_suppliers: removed_suppliers,
                                    daily_total_value: daily_total_value,
                                },
                                success: function (data) {
                                    console.log(data);
                                    if(data.result===true){
                                        swal("Done!", data.message, "success")
                                        .then((value) => {
                                            $('#collection_date').val(collection_date).trigger("change");
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
                                    swal("Opps!", "Please try again", "error")
                                    .then((value) => {
                                        location.reload();
                                    });
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

    function confirmCollection(collection_id) {

        swal({
            title: 'Are you sure?',
            text: "Once you confirmed you can not edit these records !",
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
                    url: '{{url("/admin/confirm-collection")}}',
                    type: "POST",
                    data: {
                        collection_id: collection_id,
                    },
                    success: function (data) {
                        console.log(data);
                        if(data.result===true){
                            swal("Done!", data.message, "success")
                            .then((value) => {
                                $('#collection_date').trigger("change");
                            });
                        }
                        else{
                            sweetAlert({
                                title: "Opps!",
                                text: data.message,
                                icon: "error"
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

    function cancelSubmition() {
        $('#collection_date').trigger("change");
    }

</script>

@endsection
