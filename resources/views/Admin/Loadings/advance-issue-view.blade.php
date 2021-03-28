<div class="content">

    <div class="container">

        <div class="data-table-area">
            <table width="98%" class="table data-table table-hover">
                <thead>
                    <tr>
                        <th width="15%" scope="col">Supplier ID</th>
                        <th width="25%" scope="col">Supplier Name</th>
                        <th width="18%" scope="col">Advanced Date</th>
                        <th width="18%" scope="col">Amount</th>
                        <th width="22%" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        @if ($data['month_end'] == 0)

        <a class="btn btn-add-floating btn-primary btn-lg btn-floating" data-mdb-toggle="modal" data-mdb-target="#insert_model" type="button">
            <i class="fas fa-plus"></i>
        </a>

        <!-- Insert Modal -->
        <div class="modal fade" id="insert_model" aria-labelledby="insert_model_Label" data-mdb-backdrop="static" data-mdb-keyboard="false" aria-hidden="true">
            <div class="modal-dialog .modal-side .modal-top-right">
                <div class="modal-content custom-modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="insert_model_Label">INSERT ADVANCE DETAILS</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-area">
                            <table class="table insert-table">
                                <tr>
                                    <td>Date</td>
                                    <td> : </td>
                                    <td><input type="date" id="date" class="form-control" min="{{ date('Y-m-01',strtotime($data['advance_month'])) }}" max="{{ date('Y-m-t',strtotime($data['advance_month'])) }}"></td>
                                </tr>
                                <tr>
                                    <td>Advance No.</td>
                                    <td> : </td>
                                    <td><input type="text" id="advance_no" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <td>Supplier</td>
                                    <td> : </td>
                                    <td>
                                        <select class="form-control supplier-name" id="supplier">
                                            <option value="">Select Supplier</option>
                                            @if (isset($data['suppliers']))
                                                @foreach ($data['suppliers'] as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->sup_no }}</option>
                                                @endforeach
                                            @endif
                                        </select>   
                                    </td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td> : </td>
                                    <td><input type="number" min="0" id="amount" class="form-control" onblur="formant_money(this)"></td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td> : </td>
                                    <td><input type="text" id="remarks" class="form-control"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-secondory-custom" data-mdb-dismiss="modal">
                            CANCEL
                        </button>
                        <button type="button" id="submit-data" onclick="submit_data_to_db()" class="btn btn-primary-custom float-right">APPROVE</button>
                    </div>
                </div>
            </div>
        </div>

        @endif

    </div>

</div>   

<script>    

    function advanceDatatable() {

        var advance_month = "{{ $data['advance_month'] }}";
        var regEx = /^\d{4}-\d{2}$/;
        if(advance_month.match(regEx)) {
            var apiURL = baseURL+'admin/advance-datatable/'+advance_month;
            console.log(apiURL);
        }

        advanceTable = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            // ajax: "{{ url('admin/advance-datatable/') }}",
            ajax: {
                url: "{{ url('admin/advance-datatable/') }}",
                method: "GET",
                data: {
                    "advance_month": advance_month
                },
            },
            columns: [
                    { data:'supplier_id', name:'supplier_id'},
                    { data:'supplier_name', name:'supplier_name'},
                    { data:'advance_date', name:'advance_date'},
                    { data:'amount', name:'amount', orderable: false, searchable: false},
                    { data:'action', name:'action', orderable: false, searchable: false},
            ],
            order: [ 2, 'desc' ]
        });

    }

</script>