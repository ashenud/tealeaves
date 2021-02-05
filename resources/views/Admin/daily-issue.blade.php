@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

<!-- for datatable -->
<link rel="stylesheet" href="{{asset('css/issue-table-style.css')}}">

@endsection

@section('navbar')
@include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">

        <div class="row common-area">
            <div class="col-md-3">
            <input class="form-control issue-date" type="date" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" id="issue_date" onchange="loadIssue()">
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
    var remove_supplier_issues = [];

    $(document).ready(function() {
        $('#issue_date').trigger("change");
        $('.side-link.li-issue').addClass('active');
    });
    $(document).ajaxComplete(function ( event, xhr, settings ) {      
        if (typeof xhr.responseJSON === 'undefined')   {            
            $('#supplier_id_1').select2();
            $('#item_1').select2();
            count = $('#count').val();
            // console.log(count);
        }
        else {
            // console.log(xhr.responseJSON.result);
        }        
    });

    function loadIssue() {

        var issue_date = $('#issue_date').val();
        var regEx = /^\d{4}-\d{2}-\d{2}$/;
        if(issue_date.match(regEx)) {
            var apiURL = baseURL+'admin/load-insert-issues/'+issue_date;
            // console.log(URL);
            $('#supplier-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
            $('#supplier-area').load(apiURL);
        }
        else {
            swal("Retry!", "Please select a valid date", "error");
        }
        

    }

    function loadEditIssues(issue_id) {
        
        remove_supplier_issues = [];

        var apiURL = baseURL+'admin/load-edit-issues/'+issue_id;
        // console.log(apiURL);
        $('#supplier-area').html('<p style="display: flex; justify-content: center; margin-top: 75px;"><img src="{{asset("img/loading.gif")}}" /></p>');        
        $('#supplier-area').load(apiURL);        

    }

    // current (product)
    function resetCurrentItem(row) {
        $("#item_" + row).val('').trigger("change");
        $("#current_price_" + row).val('0.00');
        $("#item_id_" + row).val('');
        $("#item_type_" + row).val('');
        $("#no_units_" + row).val('');
        $("#daily_value_" + row).val('0.00');
        load_net_total_amt();
    }

    // (product) validate if same item usimg twice
    function getItemValues(row) {

        var values =  $("#item_" + row).val().split(",");
        $("#item_id_" + row).val(values[1]);
        $("#item_type_" + row).val(values[0]);
        $("#current_price_" + row).val(values[2]);
        $("#no_units_" + row).val('').keyup();

        valid = true;
        if($("#supplier_id_" + row).val() != null && $("#supplier_id_" + row).val() != '' && $("#item_id_" + row).val() != '') {
            var current_row_value = $("#supplier_id_" + row).val()+','+$("#item_id_" + row).val();
            // console.log(current_row_value);
            var suppliers = []; // to check if same supplier exist with same item twice
            for (var i = 1; i <= count; i++) {
                if(i!=row) {
                    if($("#supplier_id_" + i).val() != null && $("#supplier_id_" + i).val() != '') {
                        var value = $("#supplier_id_" + i).val()+','+$("#item_id_" + i).val();
                        suppliers.push(value);
                    }
                }
            }

            if(suppliers.indexOf(current_row_value) !== -1){
                valid = false;
            } else{
                valid = true;
            }
        }

        if (valid === false) {
            $("#item_" + row).val('').trigger("change");
            $("#current_price_" + row).val('0.00');
            $("#item_id_" + row).val('');
            $("#item_type_" + row).val('');
            $("#no_units_" + row).val('');
            $("#daily_value_" + row).val('0.00');
            $("#item_" + row).next().find('.select2-selection').addClass('is-invalid');
            swal("Error!", "Can not add same supplier and item twice", "error");            
            load_net_total_amt();
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
                                            '<select class="form-control supplier-name" id="supplier_id_' + (row + 1) + '"  onchange="resetCurrentItem(' + (row + 1) + ')">'+
                                                '<option value="">Select Supplier</option>'+
                                                '@if (isset($data["suppliers"]))'+
                                                    '@foreach ($data["suppliers"] as $supplier)'+
                                                        '<option value="{{ $supplier->id }}">{{ $supplier->sup_name }}</option>'+
                                                    '@endforeach'+
                                                '@endif'+
                                            '</select>'+                                          
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<select class="form-control item-name" id="item_' + (row + 1) + '" onchange="getItemValues(' + (row + 1) + ')">'+
                                                '<option value="">Select Item</option>'+
                                                '@if (isset($data["items"]))'+
                                                    '@foreach ($data["items"] as $item)'+
                                                        '<option value="{{ $item->value }}">{{ $item->item_name }}</option>'+
                                                    '@endforeach'+
                                                '@endif'+
                                            '</select>'+                                  
                                            '<input type="hidden" id="item_id_' + (row + 1) + '">'+                                       
                                            '<input type="hidden" id="item_type_' + (row + 1) + '">'+                                           
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="text" id="current_price_' + (row + 1) + '" class="form-control text-right" value="0.00" readonly>'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="number" id="no_units_' + (row + 1) + '" class="form-control text-right" min="1" autocomplete="off" onkeypress="return event.charCode >= 48" onkeyup="cal_total(' + (row + 1) + ')">'+
                                        '</div>'+
                                    '</td>'+
                                    '<td>'+
                                        '<div class="form-group">'+
                                            '<input type="text" id="daily_value_' + (row + 1) + '" class="form-control text-right" value="0.00" readonly>'+
                                        '</div>'+
                                    '</td>'+
                                '</tr>');


        document.getElementById('plus_icon_' + row).style.display = 'none';
        document.getElementById('minus_icon_' + row).style.display = 'block';

        
        $('#supplier_id_' + (row + 1)).select2();        
        $('#item_' + (row + 1)).select2();        

        count = count + 1;
        $("#count").val(count);

    }

    function remove_item(row) {

        var sum = 0;

        if( ($("#actual_supplier_count").val() != "") && (typeof $("#actual_supplier_count").val() !== 'undefined') ) {
            var actual_count = $("#actual_supplier_count").val();
            if(row <= actual_count) {
                var sup_issue_id = $('#sup_issue_id_' + row).val();
                remove_supplier_issues.push(sup_issue_id);
            }
        }
        else {
            remove_supplier_issues = [];
        }

        $('#tr_' + row).remove();

        for (var i = 1; i <= count; i++) {

            if (($("#daily_value_" + i).val() != '') && ($("#daily_value_" + i).val() != null)) {
                sum += parseFloat($("#daily_value_" + i).val());
            }
            $("#daily_total_value").val(sum.toFixed(2));
        }

        // console.log(remove_supplier_issues);

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

            $("#supplier_id_" + row).next().find('.select2-selection').removeClass('is-invalid');
            var no_of_units = $("#no_units_" + row).val();        
            var unit_price = $("#current_price_" + row).val();

            var sum = 0;

            $("#daily_value_" + row).val(parseFloat(no_of_units*unit_price).toFixed(2));

            for (var i = 1; i <= count; i++) {
                if ($("#daily_value_" + i).val() != "" && ($("#daily_value_" + i).val() != null)) {

                    sum += parseFloat($("#daily_value_" + i).val());
                    $("#daily_total_value").val(sum.toFixed(2));
                }
            } 
        }
        else {
            $("#supplier_id_" + row).next().find('.select2-selection').addClass('is-invalid');
            swal("Retry!", "Please select a supplier first", "error");
            $("#no_units_" + row).val('');  
        }
        
    }

    function submit_data_to_db() {
        if ($('#issue_date').val() === "") {
            $("#issue_date").addClass('is-invalid');
            swal("Retry!", "Please select the issue date", "error");
            $('#s_id').focus();
        }
        else  {
            $("#issue_date").removeClass('is-invalid');
            valid = true;
            var arr = [];
            for (var i = 1; i <= count; i++) {

                // validate if current row actualy has a product
                if( ($("#supplier_id_" + i).val() != "") && (typeof $("#supplier_id_" + i).val() !== 'undefined') ) {

                    var supplier_id = $("#supplier_id_" + i).val();
                    var item_id = $("#item_id_" + i).val();
                    var item_type = $("#item_type_" + i).val();
                    $("#supplier_id_" + i).removeClass('is-invalid');

                    if( ($("#current_price_" + i).val() != null) && ($("#current_price_" + i).val() > 0 ) ) {
                        var current_price = $("#current_price_" + i).val();
                        $("#item_" + i).next().find('.select2-selection').removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#item_" + i).next().find('.select2-selection').addClass('is-invalid');
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
                            'item_type': item_type,
                            'item_id': item_id,
                            'current_price': current_price,
                            'no_of_units': no_of_units,
                            'daily_value': daily_value,                                
                        };

                        arr.push(obj);

                    }
                }
                else {
                    $("#supplier_id_" + i).addClass('is-invalid');
                }

            }

            if (valid === true) {
                // when the first line is empty
                if(arr.length === 0) {
                    swal("Retry!", "Please add at least one supplier line", "error");
                }
                else {

                    var issue_array = JSON.stringify(arr);
                    // console.log(JSON.parse(issue_array));
                    var issue_date = $('#issue_date').val();
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
                                url: '{{url("/admin/insert-issues")}}',
                                type: "POST",
                                data: {

                                    issue_date: issue_date,
                                    issue_array: issue_array,
                                    daily_total_value: daily_total_value,

                                },
                                success: function (data) {
                                    // var data = JSON.parse(data);
                                    console.log(data);
                                    if(data.result===true){
                                        swal("Done!", data.message, "success")
                                        .then((value) => {
                                            $('#issue_date').val(issue_date).trigger("change");
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
        if ($('#issue_date').val() === "") {
            $("#issue_date").addClass('is-invalid');
            swal("Retry!", "Please select the issue date", "error");
            $('#s_id').focus();
        }
        else  {
            $("#issue_date").removeClass('is-invalid');
            valid = true;
            var arr = [];
            for (var i = 1; i <= count; i++) {

                // validate if current row actualy has a product
                if( ($("#supplier_id_" + i).val() != "") && (typeof $("#supplier_id_" + i).val() !== 'undefined') ) {

                    if( ($("#sup_issue_id_" + i).val() != "") && (typeof $("#sup_issue_id_" + i).val() !== 'undefined') ) {
                        var sup_col_id = $("#sup_issue_id_" + i).val();
                    }
                    else {
                        var sup_col_id = 0; 
                    }

                    var supplier_id = $("#supplier_id_" + i).val();
                    var item_id = $("#item_id_" + i).val();
                    var item_type = $("#item_type_" + i).val();
                    $("#supplier_id_" + i).removeClass('is-invalid');

                    if( ($("#current_price_" + i).val() != null) && ($("#current_price_" + i).val() > 0 ) ) {
                        var current_price = $("#current_price_" + i).val();
                        $("#item_" + i).next().find('.select2-selection').removeClass('is-invalid');
                    }
                    else {
                        valid = false;
                        $("#item_" + i).next().find('.select2-selection').addClass('is-invalid');
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
                    }
                    else {
                        valid = false;
                    }

                    if (valid === true) {

                        var obj = {
                            'sup_col_id': sup_col_id,
                            'supplier_id': supplier_id,
                            'item_type': item_type,
                            'item_id': item_id,
                            'current_price': current_price,
                            'no_of_units': no_of_units,
                            'daily_value': daily_value,                                
                        };

                        arr.push(obj);

                    }
                }
                else {
                    $("#supplier_id_" + i).addClass('is-invalid');
                }

            }

            if (valid === true) {
                // when the first line is empty
                if(arr.length === 0) {
                    swal("Retry!", "Please add at least one supplier line", "error");
                }
                else {

                    var issue_array = JSON.stringify(arr);
                    var removed_supplier_issues = JSON.stringify(remove_supplier_issues);
                    // console.log(JSON.parse(issue_array),JSON.parse(removed_supplier_issues));
                    var issue_id = $('#issue_id').val();
                    var issue_date = $('#issue_date').val();
                    var daily_total_value = $('#daily_total_value').val();

                    swal({
                        title: 'Are you sure?',
                        text: "Do you want to edit these records !",
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
                                url: '{{url("/admin/edit-issues")}}',
                                type: "POST",
                                data: {

                                    issue_id: issue_id,
                                    issue_date: issue_date,
                                    issue_array: issue_array,
                                    removed_supplier_issues: removed_supplier_issues,
                                    daily_total_value: daily_total_value,
                                },
                                success: function (data) {
                                    console.log(data);
                                    if(data.result===true){
                                        swal("Done!", data.message, "success")
                                        .then((value) => {
                                            $('#issue_date').val(issue_date).trigger("change");
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

    function confirmIssues(issue_id) {

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
                    url: '{{url("/admin/confirm-issues")}}',
                    type: "POST",
                    data: {
                        issue_id: issue_id,
                    },
                    success: function (data) {
                        console.log(data);
                        if(data.result===true){
                            swal("Done!", data.message, "success")
                            .then((value) => {
                                $('#issue_date').trigger("change");
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
        $('#issue_date').trigger("change");
    }

</script>

@endsection
