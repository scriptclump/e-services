<html>
    <table>
        <tr>   
            <td>HUB Name</td>
            <td>{{$bu}}</td>
        </tr>
        <tr>   
            <th>Product ID</th>
            <th>Title</th>
            <th>Image</th>
            <th>Sku Code</th>
            <th>MRP</th>
            <th>Hold Qty</th>
            <th>Return Qty</th>
            <th>DND Qty</th>
            <th>DIT Qty</th> 
            <th>Total</th> 
        </tr>
        @foreach($Reportinfo as $report_info)		
        <tr>
            <td>{{ $report_info['pid'] }}</td>
            <td>{{ $report_info['product_title'] }}</td>            
            <td>{{ $report_info['primary_image'] }}</td>            
            <td>{{ $report_info['sku'] }}</td> 
            <td>{{ $report_info['mrp'] }}</td> 
            <td>{{ $report_info['sum_hid_qty'] }}</td>
            <td>{{ $report_info['sum_ret_qty'] }}</td>            
            <td>{{ $report_info['sum_dnd_qty'] }}</td>            
            <td>{{ $report_info['sum_dit_qty'] }}</td>            
            <td>{{ $report_info['sum_hid_qty']+$report_info['sum_ret_qty']+$report_info['sum_dnd_qty']+$report_info['sum_dit_qty'] }}</td>            
        </tr>
        @endforeach
    </table>
    
</html>