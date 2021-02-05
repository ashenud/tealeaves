@if ($data['fertilizer_issue_status'] == 0)
    <div class="btn-edit-area">
        <button class="btn btn-primary-custom top-button edit-btn" onclick="loadEditFertilizerIssues({{ $data['fertilizer_issue_id'] }})">EDIT</button>
        {{-- <button class="btn btn-primary-custom top-button" onclick="confirmFertilizerIssues({{ $data['fertilizer_issue_id'] }})">CONFIRM</button> --}}
    </div>
@endif
    
<div class="supplier-list col-md-12">
    <table width="100%" class="table" align="center" style="margin-bottom: 65px;">
        <thead>
            <tr>
                <th width="30%">Supplier</th>
                <th width="20%">Fertilizer Name</th>
                <th width="15%">Payment Frequency</th>
                <th width="10%">C.Price(Rs.)</th>
                <th width="10%">No. of Units</th>
                <th width="15%">Amount(Rs.)</th>
            </tr>
        </thead>
        <tbody id="item_tbl">
            @if (isset($data['suppliers']))
                @foreach ($data['suppliers'] as $supplier)
                    <tr id="tr" style="height: 30px">
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{ $supplier->sup_name }}" readonly>                                                                        
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" value="{{ $supplier->item_name }}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                @if ($supplier->payment_frequency == 1)
                                <input type="text" class="form-control text-center" value="One Month" readonly>
                                @elseif ($supplier->payment_frequency == 2)
                                <input type="text" class="form-control text-center" value="Two Months" readonly>
                                @elseif ($supplier->payment_frequency == 3)
                                <input type="text" class="form-control text-center" value="Three Months" readonly>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control text-right" value="{{ $supplier->current_units_price }}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control text-right" value="{{ $supplier->number_of_units }}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control text-right" value="{{ $supplier->daily_value }}" readonly>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif                                       
        </tbody>                                       
        
        <tbody>            
            <input id="count" type="hidden" value="0">
            <!--Display Daily Total-->
            <tr>
                <td colspan="5" style="text-align: right">TOTAL VALUE (RS.) &nbsp;</td>
                <td>
                    <div class="form-group">
                        <b> <input id="daily_total_value" class="form-control daily-total" value="{{ $data['daily_total_value'] }}" readonly> </b>
                    </div> 
                </td>
            </tr>

        </tbody>
    </table>
</div>                