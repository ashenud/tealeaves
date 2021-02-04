<html>

    <head>
        <style>
            @page {
                margin: 100px 25px 20px 25px;
            }

            header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                /* background-color: lightblue; */
                height: 50px;
            }

            footer {
                position: fixed;
                bottom: -60px;
                left: 0px;
                right: 0px;
                /* background-color: lightblue; */
                height: 50px;
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
                font-size: x-small;
            }
            tfoot tr td{
                font-weight: bold;
                font-size: x-small;
            }
            .gray {
                background-color: lightgray
            }
        </style>
    </head>

    <body>
        <header>
            <table width="1008px">
                <tr>
                    <td>
                        <h1 style="margin-bottom: -10px; font-size: 30px;">INDRA Leaf Collectors</h1>
                        <h3 style="margin-bottom: 0px;">Wickramagiri, Pahalagama, Theppanawa, Kuruwita. 071-8015237 0453609215</h3>
                    </td>
                </tr>
            </table>
           
            <table width="1008px">
                <tr>
                    <td style="height: 20px;"><hr></td>
                </tr>
            </table>
            
        </header>
        <footer>
        </footer>

        @foreach ($data['supplier_data'] as $supplier)
        <main style="margin-top: 40px">

            <table width="1008px">
                <tr>
                    <td width="40%">
                        <table>
                            <tr>
                                <th align="left">No</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($supplier['supplier_id']))
                                        {{ $supplier['supplier_id'] }}                                        
                                    @else
                                        
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th align="left">Name</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($supplier['supplier_name']))
                                        {{ $supplier['supplier_name'] }}                                        
                                    @else
                                        
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th align="left">Month</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($data['month']))
                                        {{ $data['month'] }}                                        
                                    @else

                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th align="left">Route</th>
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
                        
                    <td width="30%">
                        <table>
                            <tr>
                                <th align="left">Tea Leave Unit Price (1Kg)</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($supplier['current_units_price']))
                                        {{ $supplier['current_units_price'] }}                                        
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th align="left">Total Tea Leave Collection</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($supplier['tea_units']))
                                        {{ $supplier['tea_units'] }}                                        
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th align="left">Total Amount</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($supplier['total_earnings']))
                                        {{ $supplier['total_earnings'] }}                                        
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th align="left">Previous Month Arrears</th>
                                <th align="center"> : </th>
                                <td align="left">
                                    @if (isset($supplier['forwarded_credit']))
                                        {{ $supplier['forwarded_credit'] }}                                        
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td width="20%"></td>
                </tr>
            </table>

            <table width="1008px">
                <tr>
                    <td style="height: 20px;"><hr></td>
                </tr>
            </table>        

            <table width="1008px">
                <tr>
                    <td width="15%">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity(Kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                @php

                                    $date = array();

                                    if(isset($supplier['daily_data'])) {
                                        foreach ($supplier['daily_data'] as $day => $value) {
                                            if($day == 1) {
                                                
                                            }
                                        }
                                    }
                                @endphp

                                @if (isset($supplier['daily_data']))
                                    @foreach ($supplier['daily_data'] as $day => $value)
                                        @if ($day==1)                                            
                                            <tr>
                                                <td align="center">01</td>
                                                <td align="center">{{ $value }}</td>
                                            </tr>
                                        @else                                                                                          
                                            <tr>
                                                <td align="center">01</td>
                                                <td align="center"></td>
                                            </tr>
                                        @endif
                                    @endforeach                              
                                @else
                                    
                                @endif
                                    
                            </tbody>                        
                        </table>
                    </td>
                    <td width="15%">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity(Kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                            </tbody>                        
                        </table>
                    </td>
                    <td width="15%">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity(Kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                            </tbody>                        
                        </table>
                    </td>
                    <td width="15%">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity(Kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                                <tr>
                                    <td align="center">1</td>
                                    <td align="center">10</td>
                                </tr>
                            </tbody>                        
                        </table>
                    </td>
                    <td width="10%"></td>
                    <td width="30%">
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th colspan="2">Deductions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="left">01.Transport Cost</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">02.Previous Month Fretilizer Cost</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">03.Previous Month Arre</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">04. Issued Advances (Total)</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">05. Fertilizer Cost for the Current Month</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">06. Tea Bag Cost</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">07. Chemical Cost </td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <td align="left">08. Dolomite Cost</td>
                                    <td align="center"> : </td>
                                    <td align="left">1000.00</td>
                                </tr>
                                <tr>
                                    <td align="left">09. Other Costs</td>
                                    <td align="center"> : </td>
                                    <td align="left">10</td>
                                </tr>
                            </tbody>                        
                        </table>
                    </td>
                </tr>
            </table>

            <table width="1008px">
                <tr>
                    <td style="height: 20px;"><hr></td>
                </tr>
            </table>           

            <table width="1008px">
                <tr>
                    <td width="30%">
                        <table>
                            <tbody>
                                <tr>
                                    <th align="left">Continuing Arrears</th>
                                    <th align="center"> : </th>
                                    <td align="left">1000.00</td>
                                </tr>
                                <tr>
                                    <th align="left">Fertilizer Arrears for Next Month</th>
                                    <th align="center"> : </th>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <th align="left">Balance for the Next Month</th>
                                    <th align="center"> : </th>
                                    <td align="left">10</td>
                                </tr>
                            </tbody>                        
                        </table>
                    </td>
                    <td width="40%"></td>
                    <td width="30%">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <th align="left">Total Deductions</th>
                                    <th align="center"> : </th>
                                    <td align="left">10</td>
                                </tr>
                                <tr>
                                    <th align="left">Balance Amount</th>
                                    <th align="center"> : </th>
                                    <td align="left">10</td>
                                </tr>
                            </tbody>                        
                        </table>
                    </td>
                </tr>
            </table> 

            <table width="1008px">
                <tr>
                    <td style="height: 20px;"><hr></td>
                </tr>
            </table>       

            <table width="1008px">
                <tr>
                    <td width="5%"></td>
                    <td width="50%">
                        <table>
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
                </tr>
            </table> 

        </main>
        <p></p>
        @endforeach

        
    </body>

</html>