
<table style="width: 98%" class="audit-table">
    <thead>
        <tr>
            <th style="width: 12%">Supplier ID</th>
            <th style="width: 25%">Supplier Name</th>
            <th style="width: 15%">Total Collected</br>Tea (KG)</th>
            <th style="width: 15%">Total Tea Leaves Value</br>(KG * U.Price)</th>
            <th style="width: 16%">Total Deduction</br>(Delivery Cost, Dolamite,Fertilizer, Chemicals,Tea Bags, Advance,Other)</th>
            <th style="width: 16%">Payable Amount</br>(Total Tea Leave Value - Total Deduction)</th>
        </tr>
    </thead>
    <tbody>
        <tr style="height: 30px; background: aliceblue; font-weight: bold;">
            <td colspan="2"> Column Total </td>
            <td align="center"> {{ $data['grand_colection'] }} </td>
            <td align="right" style="padding-right: 10px;"> {{ number_format($data['grand_earnings'],2) }} </td>
            <td align="right" style="padding-right: 10px;"> {{ number_format($data['grand_deduction'],2) }} </td>
            <td align="right" style="padding-right: 10px;"> {{ number_format($data['grand_income'],2) }} </td>
        </tr>        
        @if (isset($data['supplier_data']))
            @foreach ($data['supplier_data'] as $supplier)
                <tr style="height: 20px !important">
                    <td> {{ $supplier['supplier_no'] }} </td>
                    <td> {{ $supplier['supplier_name'] }} </td>
                    <td align="center"> {{ $supplier['number_of_units'],2 }} </td>
                    <td align="right" style="padding-right: 10px;"> {{ number_format($supplier['total_earnings'],2) }} </td>
                    <td align="right" style="padding-right: 10px;"> {{ number_format($supplier['total_deduction'],2) }} </td>
                    <td align="right" style="padding-right: 10px;"> {{ number_format((floor($supplier['current_income'] / 10) * 10),2) }} </td>
                </tr>
            @endforeach
        @endif  
    </tbody>
</table>