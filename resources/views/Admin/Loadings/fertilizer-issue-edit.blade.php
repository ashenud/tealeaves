<div class="supplier-list col-md-12">
    <table width="100%" class="table" align="center">
        <thead>
            <tr>
                <th width="5%" height="25"></th>
                <th width="15%">Supplier ID</th>
                <th width="23%">Supplier Name</th>
                <th width="15%">Fertilizer Name</th>
                <th width="14%">Payment Frequency</th>
                <th width="8%">C.Price(Rs.)</th>
                <th width="8%">No. of Units</th>
                <th width="12%">Amount(Rs.)</th>
            </tr>
        </thead>
        <tbody id="item_tbl">
            @foreach ($data['fertilizer_supplier_issues'] as $key => $supplier)
                <tr id="tr_{{ $key+1 }}">
                    <td>
                        <div class="form-group">
                            @if ( $data['actual_supplier_count'] == ($key+1))
                                <button type="button" onclick="add_item({{ $key+1 }})" class="plus_icon btn btn-floating" id="plus_icon_{{ $key+1 }}"><i class="fas fa-plus"></i></button>
                                <button type="button" onclick="remove_item({{ $key+1 }})" class="minus_icon btn btn-floating" id="minus_icon_{{ $key+1 }}" style="display: none"><i class="fas fa-minus"></i></button>
                            @else
                                <button type="button" onclick="remove_item({{ $key+1 }})" class="minus_icon btn btn-floating" id="minus_icon_{{ $key+1 }}"><i class="fas fa-minus"></i></button>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="form-group readonly-div">
                            <select class="form-control supplier-name readonly-select" id="supplier_id_{{ $key+1 }}" readonly>
                                <option value="{{ $supplier->supplier_id }}">{{ $supplier->sup_id }}</option>
                            </select>                                    
                            <input type="hidden" id="sup_fertilizer_issue_id_{{ $key+1 }}" value="{{ $supplier->id }}">
                            <input type="hidden" id="supplier_id_{{ $key+1 }}" value="{{ $supplier->supplier_id }}">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" id="supplier_name_{{ $key+1 }}" class="form-control" value="{{ $supplier->sup_name }}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group readonly-div">
                            <select class="form-control item-name readonly-select" id="item_{{ $key+1 }}" readonly>
                                <option value="{{ $supplier->item_id }}">{{ $supplier->item_name }}</option>
                            </select>                                    
                            <input type="hidden" id="item_id_{{ $key+1 }}" value="{{ $supplier->item_id }}">                                             
                            <input type="hidden" id="item_type_{{ $key+1 }}" value="{{ $supplier->item_type }}">                                             
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <select class="form-control payment_frequency" id="payment_frequency_{{ $key+1 }}">
                                <option value="">Select Frequency</option>
                                <option value="1" @if ($supplier->payment_frequency == 1) selected @endif>One Month</option>
                                <option value="2" @if ($supplier->payment_frequency == 2) selected @endif>Two Months</option>
                                <option value="3" @if ($supplier->payment_frequency == 3) selected @endif>Three Months</option>
                            </select>                                            
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" id="current_price_{{ $key+1 }}" class="form-control text-right" value="{{ $supplier->current_units_price }}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="number" id="no_units_{{ $key+1 }}" class="form-control text-right" min="1" value="{{ $supplier->number_of_units }}" autocomplete="off" onkeypress="return event.charCode >= 48" onkeyup="cal_total({{ $key+1 }})">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" id="daily_value_{{ $key+1 }}" class="form-control text-right" value="{{ $supplier->daily_value }}" readonly>
                        </div>
                    </td>
                </tr>  
            @endforeach                                       
        </tbody>                                       
        
        <tbody>
            <input id="count" type="hidden" value="{{ $key+1 }}">
            <input id="actual_supplier_count" type="hidden" value="{{ $data['actual_supplier_count'] }}">
            <!--Display Daily Total-->
            <tr>
                <td colspan="7" style="text-align: right">TOTAL VALUE (RS.) &nbsp;</td>
                <td>
                    <div class="form-group">
                        <b> <input id="daily_total_value" class="form-control daily-total" value="{{ $data['daily_total_value'] }}" readonly> </b>
                    </div> 
                </td>
            </tr>
            <tr class="submit-button-row">
                <td colspan="8" align="right">
                    <input id="fertilizer_issue_id" type="hidden" value="{{ $data['fertilizer_issue_id'] }}">
                    <button class="btn btn-primary-custom submit-btn" onclick="cancelSubmition()">CANCEL</button>
                    <button class="btn btn-primary-custom submit-btn" onclick="submit_edited_data_to_db()">SUBMIT FERTILIZER ISSUE DATA</button>
                </td>
            </tr>

        </tbody>
    </table>
</div>                