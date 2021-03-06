@php
set_time_limit(0);
ini_set("memory_limit",-1);
ini_set('max_execution_time', 0);
@endphp

<html>

    <head>

        <title>@if (isset($data['month'])) {{ strtoupper($data['month']) }} @endif MONTHLY BULK BILL VIEW</title>

        <style>
            @page {
                margin: 0px 0px -10px 10px;
            }

            p {
                page-break-after: always;
            }

            p:last-child {
                page-break-after: never;
            }
            * {
            font-family: Verdana, Arial, sans-serif;
            }
            table{
                font-size: 0.88rem;
                margin-bottom: -3px;
                padding: 0;
            }
            table tr td{
                padding: 0;
            }
            tfoot tr td{
                font-weight: bold;
                font-size: 8px;
            }
            .gray {
                background-color: lightgray
            }
        </style>
    </head>

    <body>
        
		@php $m = 0; @endphp

        @foreach ($data['supplier_data'] as $supplier)

            @php $m ++; @endphp

            <main>
                <table width="800px" style="margin-top: -10px">
                    <tr>
                        <td width="50%">
                            <h1 style="margin-bottom: -20px; margin-top: -14px; font-size: 25px;">INDRA Leaf Collectors</h1>
                        </td>
                        <td width="10%">
                            <h2> R4391 </h2>
                        </td>
                        <td width="10%"> </td>
                        <th align="right" width="15%"><h2 style="margin-bottom: -10px; font-size: 20px;">USER ID</h2></th>
                        <th align="center" width="5%"><h2 style="margin-bottom: -10px; font-size: 20px;"> : </h2></th>
                        <th align="left" width="10%">
                            <h2 style="margin-bottom: -10px; font-size: 20px;">
                            @if (isset($supplier['supplier_no']))
                                {{ $supplier['supplier_no'] }}                                        
                            @else
                                
                            @endif
                            </h2>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <h3 style="margin-bottom: 0px; margin-top: -10px; font-size: 12px;">"Wikramagiri", Pahalagama, Theppanawa, Kuruwita. 071-8015237 / 045-2261493</h3>
                        </td>
                    </tr>
                </table>
            
                <table width="800px">
                    <tr>
                        <td style="height: 3px;"><hr></td>
                    </tr>
                </table>

                <table width="800px" style="margin-top: -10px;">
                    <tr>
                        <td width="40%">
                            <table width="100%">
                                <tr>
                                    <th align="left">NAME</th>
                                    <th align="center"> : </th>
                                    <td align="left" style="text-transform: uppercase;">
                                        @if (isset($supplier['supplier_name']))
                                            {{ $supplier['supplier_name'] }}                                        
                                        @else
                                            
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th align="left">MONTH</th>
                                    <th align="center"> : </th>
                                    <td align="left" style="text-transform: uppercase;">
                                        @if (isset($data['month']))
                                            {{ $data['month'] }}                                        
                                        @else

                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th align="left">ROUTE</th>
                                    <th align="center"> : </th>
                                    <td align="left" style="text-transform: uppercase;">
                                        @if (isset($supplier['route_name']))
                                            {{ $supplier['route_name'] }}                                        
                                        @else
                                            0.00
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td width="10%"></td>
                            
                        <td width="40%" style="vertical-align: top;">
                            <table width="100%">
                                <tr>
                                    <th align="left" style="margin-bottom: -10px; font-size: 18px;">RATE</th>
                                    <th align="center" style="margin-bottom: -10px; font-size: 18px;"> : </th>
                                    <th align="left" style="margin-bottom: -10px; font-size: 18px;">
                                        @if (isset($supplier['current_units_price']))
                                            {{ number_format($supplier['current_units_price'],2) }}                                        
                                        @else
                                            0.00
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th align="left">COLLECTION TOTAL</th>
                                    <th align="center"> : </th>
                                    <th align="left">
                                        @if (isset($supplier['tea_units']))
                                            {{ $supplier['tea_units'] }}                                        
                                        @else
                                            0
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th align="left">TOTAL</th>
                                    <th align="center"> : </th>
                                    <th align="left">
                                        @if (isset($supplier['total_earnings']))
                                            {{ number_format($supplier['total_earnings'],2) }}                                        
                                        @else
                                            0.00
                                        @endif
                                    </th>
                                </tr>
                            </table>
                        </td>

                        <td width="10%"></td>
                    </tr>
                </table>

                <table width="800px">
                    <tr>
                        <td style="height: 3px;"><hr></td>
                    </tr>
                </table>        

                <table width="800px" style="margin-top: -10px">
                    <tr>
                        <td width="12%">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <th width="40%">DATE</th>
                                        <th width="30%">KG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    @php
                                        $all_days =  array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0, 24 => 0, 25 => 0, 26 => 0, 27 => 0, 28 => 0, 29 => 0, 30 => 0, 31 => 0);
                                        $daily_data = array();
                                        if(isset($supplier['daily_data'])) {                                        
                                            $keys = array_keys($all_days + $supplier['daily_data']);
                                            foreach ($keys as $key) {
                                                $daily_data[$key] = (empty($all_days[$key]) ? 0 : $all_days[$key]) + (empty($supplier['daily_data'][$key]) ? 0 : $supplier['daily_data'][$key]);
                                            }

                                            $j = 0;
                                            foreach ($daily_data as $day => $values) {
                                                $j++;
                                                if($j < 9) {
                                                    echo '<tr>';
                                                    echo    '<td align="center">0'.$day.'</td>';
                                                    if($values != 0) {
                                                        echo'<th align="center">'. $values .'</th>';
                                                    }
                                                    else {
                                                        echo'<td align="center"></td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                            }

                                        }
                                        else {
                                            echo '<tr>';
                                            echo    '<td align="center">01</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">02</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">03</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">04</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">05</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">06</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">07</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">08</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                        }                                        
                                    @endphp
                                        
                                </tbody>                        
                            </table>
                        </td>
                        <td width="12%">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <th width="40%">DATE</th>
                                        <th width="30%">KG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @php
                                        if(isset($supplier['daily_data'])) {    
                                            $j = 0;
                                            foreach ($daily_data as $day => $values) {
                                                $j++;
                                                if($j > 8 && $j < 17) {
                                                    echo '<tr>';
                                                    if($day == 9) {
                                                        echo'<td align="center">0'.$day.'</td>';
                                                    }
                                                    else {
                                                        echo'<td align="center">'.$day.'</td>';
                                                    }
                                                    if($values != 0) {
                                                        echo'<th align="center">'. $values .'</th>';
                                                    }
                                                    else {
                                                        echo'<td align="center"></td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                            }
                                        }
                                        else {
                                            echo '<tr>';
                                            echo    '<td align="center">01</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">02</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">03</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">04</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">05</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">06</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">07</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">08</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                        }
                                    @endphp

                                </tbody>                        
                            </table>
                        </td>
                        <td width="12%">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <th width="40%">DATE</th>
                                        <th width="30%">KG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        if(isset($supplier['daily_data'])) {    
                                            $j = 0;
                                            foreach ($daily_data as $day => $values) {
                                                $j++;
                                                if($j > 16 && $j < 25) {
                                                    echo '<tr>';
                                                    echo    '<td align="center">'.$day.'</td>';
                                                    if($values != 0) {
                                                        echo'<th align="center">'. $values .'</th>';
                                                    }
                                                    else {
                                                        echo'<td align="center"></td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                            }
                                        }
                                        else {
                                            echo '<tr>';
                                            echo    '<td align="center">01</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">02</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">03</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">04</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">05</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">06</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">07</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">08</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                        }
                                    @endphp
                                </tbody>                        
                            </table>
                        </td>
                        <td width="12%">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <th width="40%">DATE</th>
                                        <th width="30%">KG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        if(isset($supplier['daily_data'])) {    
                                            $j = 0;
                                            foreach ($daily_data as $day => $values) {
                                                $j++;
                                                if($j > 24 && $j < 32) {
                                                    echo '<tr>';
                                                    echo    '<td align="center">'.$day.'</td>';
                                                    if($values != 0) {
                                                        echo'<th align="center">'. $values .'</th>';
                                                    }
                                                    else {
                                                        echo'<td align="center"></td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                            }                                    
                                            echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                        }
                                        else {
                                            echo '<tr>';
                                            echo    '<td align="center">01</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">02</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">03</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">04</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">05</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">06</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">07</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                            echo '<tr>';
                                            echo    '<td align="center">08</td>';
                                            echo    '<td align="center"></td>';
                                            echo '</tr>';
                                        }
                                    @endphp
                                </tbody>                        
                            </table>
                        </td>
                        <td width="5%"></td>
                        <td width="47%">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <th width="55%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DEDUCTIONS</th>
                                        <th width="10%">&nbsp;</th>
                                        <th width="30%">&nbsp;</th>
                                        <th width="5%">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th align="left">01 TRANSPORTATION</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['delivery_cost']))
                                                {{ number_format($supplier['delivery_cost'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">02 FROM LAST MONTH</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['forwarded_credit']))
                                                {{ number_format($supplier['forwarded_credit'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">03 ADVANCES</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['installment_advance']))
                                                {{ number_format($supplier['installment_advance'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">04 FERTILIZER - CURRENT</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['installment_fertilizer']))
                                                {{ number_format($supplier['installment_fertilizer'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">05 TEA BAGS</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['issue_teabag']))
                                                {{ number_format($supplier['issue_teabag'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">06 CHEMICALS</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['issue_chemical']))
                                                {{ number_format($supplier['issue_chemical'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">07 DOLAMITE </th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['issue_dolamite']))
                                                {{ number_format($supplier['issue_dolamite'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">08 OTHER</th>
                                        <th align="center"> : </th>
                                        <td align="right">&nbsp;&nbsp;&nbsp;
                                            @if (isset($supplier['installment_loan']))
                                                {{ number_format($supplier['installment_loan'],2) }}                              
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td align="left">&nbsp;</td>
                                    </tr>
                                </tbody>                        
                            </table>
                        </td>
                    </tr>
                </table>

                <table width="800px">
                    <tr>
                        <td style="height: 3px;"><hr></td>
                    </tr>
                </table>           

                <table width="800px" style="margin-top: -10px" >
                    <tr>
                        <td width="30%">
                            <table style="margin-bottom: -10px; margin-top: -2px;" width="100%">
                                <tbody>
                                    <tr>
                                        <th width="50%" align="left">CREDITS</th>
                                        <th width="10%" align="center"> : </th>
                                        <td width="40%" align="right">
                                            @if (isset($supplier['current_credit']))
                                                {{ number_format($supplier['current_credit'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th align="left">FERT.CREDITS</th>
                                        <th align="center"> : </th>
                                        <td align="right">
                                            @if (isset($supplier['fert_credt']))
                                                {{ number_format($supplier['fert_credt'],2) }}                                        
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th align="left">CARRIED</th>
                                        <th align="center"> : </th>
                                        <th align="right">
                                            {{ number_format( $supplier['current_income'] - (floor($supplier['current_income'] / 10) * 10) ,2) }}
                                        </th>
                                    </tr>
                                </tbody>                        
                            </table>
                        </td>
                        <td width="23%"></td>
                        <td width="47%"  style="vertical-align: top;">
                            <table style="margin-bottom: -10px; margin-top: -2px;" width="100%">
                                <tbody>
                                    <tr>
                                        <th width="55%" align="left">DEDUCTION TOTAL</th>
                                        <th width="10%" align="center"> : </th>
                                        <td width="30%" align="right">&nbsp;&nbsp;&nbsp;
                                            {{ number_format(($supplier['total_issues'] + $supplier['total_installment'] + $supplier['forwarded_credit'] + (isset($supplier['delivery_cost']) ? $supplier['delivery_cost'] : 0) ),2) }}
                                        </td>
                                        <td width="5%" align="left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left" style="font-size: 18px; padding-top: 15px;">PAYABLE AMOUNT</th>
                                        <th align="center" style="font-size: 18px; padding-top: 15px;"> : </th>
                                        <th align="right" style="font-size: 18px; border-bottom: 2px solid; padding-top: 15px;">&nbsp;&nbsp;&nbsp;
                                            {{ number_format(floor($supplier['current_income'] / 10) * 10 ,2)}}
                                        </th>
                                        <td width="5%" align="left" style="padding-top: 15px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th align="left">&nbsp;</th>
                                        <th align="center">&nbsp;</th>
                                        <td align="right" style="border-top: 1px solid;">&nbsp;</td>
                                        <td width="5%" align="left">&nbsp;</td>
                                    </tr>
                                </tbody>                        
                            </table>
                        </td>
                    </tr>
                </table> 

                <table width="800px" style="margin-top: -10px">
                    <tr>
                        <td style="height: 3px;"><hr></td>
                    </tr>
                </table>       

                <table width="800px" style="margin-top: -6px">
                    <tr>
                        <td width="50%">
                            <table style="font-size: 10px">
                                <tbody>
                                    <tr>
                                        <th align="center">1957 No 51 Tea Control Act</th>
                                    </tr>
                                    <tr>
                                        <th align="center">I Certified that I Handed Over the Above Mentioned Amount of Tea Leaves</th>
                                    </tr>
                                    <tr>
                                        <th align="center">for the Tea Factory and I Received the Above Mentioned Amount of Payment for them.</th>
                                    </tr>
                                </tbody>                        
                            </table>
                        </td>
                        <td width="5%"></td>
                        <td width="20%" style="vertical-align: bottom;">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <th align="center"></th>
                                    </tr>
                                    <tr>
                                        <th align="center"> ....................... </th>
                                    </tr>
                                    <tr>
                                        <th align="center">Date</th>
                                    </tr>
                                </tbody>                        
                            </table>
                        </td>
                        <td width="20%" style="vertical-align: bottom;">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <th align="center"></th>
                                    </tr>
                                    <tr>
                                        <th align="center"> ....................... </th>
                                    </tr>
                                    <tr>
                                        <th align="center">Signature</th>
                                    </tr>
                                </tbody>                        
                            </table>
                        </td>
                        <td width="5%"></td>
                    </tr>
                </table> 

            </main>

            {{-- @php if($m == 1) { break;  } @endphp --}}

            <p></p>

        @endforeach

        
    </body>

</html>