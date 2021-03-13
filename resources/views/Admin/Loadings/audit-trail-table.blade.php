
<div class="btn-edit-area">
    <button class="btn btn-primary-custom top-button edit-btn" onclick="printAuditTrail('{{ $data['audit_month'] }}')">PRINT</button>
</div>

<table style="width: 1600px" class="audit-table">
    <thead>
        <tr>
            <th style="width: 160px">Supplier ID</th>
            <th style="width: 200px">Supplier Name</th>
            @for ($i = 1; $i <= 31; $i++)
            <th style="width: 40px; text-align: center;">{{ $i }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @if (isset($data['supplier_data']))
            @foreach ($data['supplier_data'] as $supplier)
                <tr style="height: 30px">
                    <td> {{ $supplier['supplier_no'] }} </td>
                    <td> {{ $supplier['supplier_name'] }} </td>
                    @php
                        $all_days =  array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0, 24 => 0, 25 => 0, 26 => 0, 27 => 0, 28 => 0, 29 => 0, 30 => 0, 31 => 0);
                        $daily_data = array();
                        if(isset($supplier['daily_data'])) {                                 
                            $keys = array_keys($all_days + $supplier['daily_data']);
                            foreach ($keys as $key) {
                                $daily_data[$key] = (empty($all_days[$key]) ? 0 : $all_days[$key]) + (empty($supplier['daily_data'][$key]) ? 0 : $supplier['daily_data'][$key]);
                            }

                            foreach ($daily_data as $day => $values) {
                                if($values != 0) {
                                    echo'<td align="center">'. $values .'</td>';
                                }
                                else {
                                    echo'<td align="center">&nbsp</td>';
                                }
                            }

                        }                                      
                    @endphp
                </tr>
            @endforeach
        @endif  
    </tbody>
</table>