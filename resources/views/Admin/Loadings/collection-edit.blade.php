<div class="supplier-list col-md-12">
    <table width="100%" class="table" align="center">
        <thead>
            <tr>
                <th width="5%" height="25"></th>
                <th width="30%">Supplier</th>
                <th width="20%">Item Name</th>
                <th width="10%">C.Price(Rs.)</th>
                <th width="10%">D.Cost(Rs.)</th>
                <th width="10%">No. of Units</th>
                <th width="15%">Amount(Rs.)</th>
            </tr>
        </thead>
        <tbody id="item_tbl">
            @foreach ($data['suppliers'] as $key => $supplier)
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
                        <div class="form-group">
                            <select class="form-control supplier-name" id="supplier_{{ $key+1 }}" disabled>
                                <option value="{{ $supplier->supplier_id }}">{{ $supplier->sup_name }}</option>
                            </select>                                    
                            <input type="hidden" id="sup_collection_id_{{ $key+1 }}" value="{{ $supplier->id }}">                                          
                            <input type="hidden" id="supplier_id_{{ $key+1 }}" value="{{ $supplier->supplier_id }}">                                          
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" id="item_{{ $key+1 }}" class="form-control" value="{{ $supplier->item_name }}" readonly>    
                            <input type="hidden" id="item_id_{{ $key+1 }}" value="{{ $supplier->item_id }}"> 
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" id="current_price_{{ $key+1 }}" class="form-control text-right" value="{{ $supplier->current_units_price }}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="hidden" id="delivery_cost_per_unit_{{ $key+1 }}" value="{{ $supplier->delivery_cost_per_unit }}">
                            <input type="text" id="delivery_cost_{{ $key+1 }}" class="form-control text-right" value="{{ $supplier->delivery_cost }}" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="number" id="no_units_{{ $key+1 }}" class="form-control text-right" min="1" value="{{ $supplier->number_of_units }}" autocomplete="off" onkeypress="return event.charCode >= 48" onkeyup="cal_total({{ $key+1 }})">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="hidden" id="daily_amount_{{ $key+1 }}" value="{{ $supplier->daily_amount }}">
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
                <td colspan="6" style="text-align: right">TOTAL VALUE (RS.) &nbsp;</td>
                <td>
                    <div class="form-group">
                        <b> <input id="daily_total_value" class="form-control daily-total" value="{{ $data['daily_total_value'] }}" readonly> </b>
                    </div> 
                </td>
            </tr>
            <tr class="submit-button-row">
                <td colspan="7" align="right">
                    <input id="collection_id" type="hidden" value="{{ $data['collection_id'] }}">
                    <button class="btn btn-primary-custom submit-btn" onclick="cancelSubmition()">CANCEL</button>
                    <button class="btn btn-primary-custom submit-btn" onclick="submit_edited_data_to_db()">SUBMIT COLLECTION DATA</button>
                </td>
            </tr>

        </tbody>
    </table>
</div>                