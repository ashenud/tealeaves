<html>

    <head>

        <title>@if (isset($data['audit_month'])) {{ strtoupper($data['audit_month']) }} @endif MONTHLY TEA LEAVES COLECTION</title>

        <style>
            @page {
                margin: 25px 20px 20px 20px;
            }

            /* header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                background-color: lightblue;
                height: 50px;
            } */

            /* footer {
                position: fixed;
                bottom: -60px;
                left: 0px;
                right: 0px;
                background-color: lightblue;
                height: 50px;
            } */

            p {
                page-break-after: always;
            }

            p:last-child {
                page-break-after: never;
            }
            * {
            font-family: Verdana, Arial, sans-serif;
            }

            .audit-table,
            .audit-table tr,
            .audit-table th,
            .audit-table td {             
                border-collapse: collapse;
                font-size: 11px;
                border: none;
                border-bottom: 0.1rem solid;
            }

            .audit-table th {                
                height: 30px;
            }

            .audit-table td {                
                height: 25px;
            }

            .gray {
                background-color: lightgray
            }
        </style>
    </head>

    <body>
        {{-- <header></header>
        <footer></footer> --}}

        <main>
            <table width="100%">
                <tr>
                    <td align="center">
                        <h1 style="margin-bottom: -10px; margin-top: -10px; font-size: 30px;">INDRA Leaf Collectors</h1>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <h3 style="margin-bottom: 5px;">"Wikramagiri", Pahalagama, Theppanawa, Kuruwita. 071-8015237 / 0453609215</h3>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <h2 style="margin-bottom: 5px;">@if (isset($data['audit_month'])) {{ strtoupper($data['audit_month']) }} @endif MONTHLY TEA LEAVES COLECTION</h2>
                    </td>
                </tr>
            </table>
            <table class="audit-table" width="100%" border="1">
                <thead>
                    <tr style="background-color: #f0ffe5;">
                        <th style="width: 40px">SUP ID</th>
                        <th style="width: 140px;">Supplier Name</th>
                        @for ($i = 1; $i <= 31; $i++)
                        <th style="width: 24px; text-align: center;">{{ $i }}</th>
                        @endfor
                        <th style="width: 30px; text-align: center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['supplier_data']))
                        @php $grand_monthly_total = 0; @endphp
                        @foreach ($data['supplier_data'] as $supplier)
                            <tr style="height: 30px">
                                <td align="center"> {{ $supplier['supplier_no'] }} </td>
                                <td style="padding-left: 5px;"> {{ $supplier['supplier_name'] }} </td>
                                @php
                                    $all_days =  array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0, 24 => 0, 25 => 0, 26 => 0, 27 => 0, 28 => 0, 29 => 0, 30 => 0, 31 => 0);
                                    $daily_data = array();
                                    $monthly_total = 0;
                                    if(isset($supplier['daily_data'])) {                                 
                                        $keys = array_keys($all_days + $supplier['daily_data']);
                                        foreach ($keys as $key) {
                                            $daily_data[$key] = (empty($all_days[$key]) ? 0 : $all_days[$key]) + (empty($supplier['daily_data'][$key]) ? 0 : $supplier['daily_data'][$key]);
                                        }
            
                                        foreach ($daily_data as $day => $values) {
                                            $monthly_total += $values;
                                            if($values != 0) {
                                                echo'<td align="center">'. $values .'</td>';
                                            }
                                            else {
                                                echo'<td align="center"> </td>';
                                            }
                                        }
            
                                    }
                                    $grand_monthly_total += $monthly_total;                                 
                                @endphp
                                <td align="center"><b> {{ $monthly_total }} </b></td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td colspan="33" align="center">MONTHLY GRAND TOTAL</td>
                            <td align="center">{{ $grand_monthly_total }}</td>
                        </tr>
                    @endif  
                </tbody>
            </table>
        </main>

        
    </body>

</html>