@extends('layouts.app')

@section('title')
<title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')
    <style>
        .insert-card-header-sm {
            padding-left: 1rem !important;
        }
        .mg-t-100 {
            margin-top: 100px;
        }
        .insert-card{
            box-shadow: rgba(0, 0, 0, 0.2) 0px 5px 25px 0px;
        }
        .float-right{
            float :right;
        }
        .select2-label{
            font-size: 12px;
            color: black;
            margin: -18px 0 0 10px;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

    </style>
@endsection

@section('navbar')
@include('layouts.navbars.admin')
@endsection

@section('content')
<div class="content">
   
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card insert-card border border-dark">
                    <div class="insert-card-header-sm card-header">
                        <h4>INSERT SUPPLIER DETAILS</h4>
                    </div>
                    <div class="card-body">
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

                            <button type="button" id="submit-data" onclick="insertSupplier()" class="btn btn-success float-right">INSERT</button>
                        </div>
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

@section('script')

<script>
    $(function() {
        $('.navbar-nav .nav-link.li-sup').addClass('active');
    }); 

    $(document).ready(function() {
        $('#supplier_route').select2();
    });

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
                url: '{{url("/admin/supplier-insert-action")}}',
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
                    }
                    else {
                        swal("Opps !", data.message, "error");
                    }                      
                }
            });

        }
    }

</script>

@endsection
