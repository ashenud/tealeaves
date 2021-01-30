@extends('layouts.app')

@section('title')
    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>
@endsection

@section('style')

    <!-- for datatable -->
    <link rel="stylesheet" href="{{ asset('css/loan-issue-style.css') }}">

@endsection

@section('navbar')
    @include('layouts.navbars.admin')
@endsection

@section('content')
    <div class="content">

        <div class="container">

            <div class="row common-area mt-5">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <form>

                        <table class="table">
                            <tr>
                                <td>Date</td>
                                <td> : </td>
                                <td><input type="date" id="date" class="form-control" value="{{ date('Y-m-d') }}"></td>
                            </tr>
                            <tr>
                                <td>Loan No.</td>
                                <td> : </td>
                                <td><input type="text" id="loan_no" class="form-control"></td>
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
                                <td>Remarks</td>
                                <td> : </td>
                                <td><input type="text" id="loan_no" class="form-control"></td>
                            </tr>
                            <tr class="submit-button-row">
                                <td colspan="7" align="right">
                                    <input class="btn btn-primary-custom submit-btn" type="button" class="btn" value="APPROVE LOAN"  id="dd" onclick="submit_data_to_db()" />
                                </td>
                            </tr>
                        </table>

                    </form>
                </div>
                <div class="col-md-4"></div>
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

        $(document).ready(function() {
            $('.side-link.li-loan').addClass('active');
            $('#supplier').select2();
        });

    </script>

@endsection
