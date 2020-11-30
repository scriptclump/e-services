<html>
    <table>
        <tr>   
            <th>Product ID</th>
            <th>Product Title</th>
            <th>Product Class Name</th>
            <th>Brand Name</th>
            <th>Manufacturer Name</th>
            <th>Warehouse Name</th>
            <th>Variant Value1</th>
            <th>Parent Product Name</th>
            <th>SKU</th>
            <th>ELP</th>
            <th>ESU</th>
            <th>ESP</th>            
            <th>MRP</th>
            <th>Inventory</th>
            <th>Is Sellable</th>
            <th>CP Enabled</th>
           
        </tr>
        @foreach($Reportinfo as $report_info)
        <tr>
            <td>{{ $report_info->product_id }}</td>
            <td>{{ $report_info->product_title }}</td>            
            <td>{{ $report_info->product_class_name }}</td> 
            <td>{{ $report_info->brand_name }}</td>
            <td>{{ $report_info->manufacturer_name }}</td>            
            <td>{{ $report_info->whname }}</td>            
            <td>{{ $report_info->variant_value1 }}</td>            
            <td>{{ $report_info->parent_id }}</td>            
            <td>{{ $report_info->sku }}</td>            
            <td>{{ $report_info->elp }}</td>            
            <td>{{ $report_info->esu }}</td>            
            <td>{{ $report_info->esp }}</td>                                   
            <td>{{ $report_info->mrp }}</td>            
            <td>{{ $report_info->available_inventory }}</td>            
            <td>{{ $report_info->is_sellable }}</td>            
            <td>{{ $report_info->cp_enabled }}</td>            

        </tr>
        @endforeach
    </table>
    
</html>