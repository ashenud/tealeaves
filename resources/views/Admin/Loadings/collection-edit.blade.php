<div>
        @if (isset($data['date']))
            
            <p style="display: flex; justify-content: center; margin-top: 75px;">{{ $data['date'] }}</p>
                
        @endif

        <p style="display: flex; justify-content: center; margin-top: 75px;">Ashen Udithamal</p>
</div>
    
{{-- <div class="supplier-list col-md-12">
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
</div>                 --}}