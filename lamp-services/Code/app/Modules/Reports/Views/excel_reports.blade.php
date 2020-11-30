<html>
    <table>
        <tr>
            <th>DC Name</th>   
            <th>NAME</th>
			<th>HUB Name</th>
			<th>Beat</th>
            <th>Orders</th>
            <th>Calls</th>
            <th>TBV</th>
            <th>UOB</th>
            <th>ABV</th>
            <th>TLC</th>
            <th>ULC</th>
            <th>ALC</th>
            <th>Contribution</th>
			<th>TGM</th>
			<th>Delivered Margin</th>
            <th>Cancel Order Count</th> 
            <th>Cancel Order Value</th> 
            <th>Return Order Count</th> 
            <th>Return Order Value</th>
            <th>Cancel Order(%)</th>
            <th>Return Order(%)</th>
            <th>Delivered Value</th>
            <th>Order Date</th> 
        </tr>
        @foreach($Reportinfo as $report_info)
		<?php 
			if($report_info['order_date'])
			{
				$ordDateArray = explode(' ',$report_info['order_date']);
		        $ordDate = $ordDateArray[0];
			}
            else
			{
				 $ordDate =  $report_info['order_date'];
			}
        ?>	
        <tr>
            <td>{{ $report_info['display_name'] }}</td>
            <td>{{ $report_info['name'] }}</td>
			<td>{{ $report_info['hub_name'] }}</td>
			<td>{{ $report_info['beat'] }}</td>
            <td>{{ $report_info['order_cnt'] }}</td>            
            <td>{{ $report_info['calls_cnt'] }}</td> 
            <td>{{ $report_info['tbv'] }}</td>
            <td>{{ $report_info['uob'] }}</td>            
            <td>{{ $report_info['abv'] }}</td>            
            <td>{{ $report_info['tlc'] }}</td>            
            <td>{{ $report_info['ulc'] }}</td>            
            <td>{{ $report_info['alc'] }}</td>            
            <td>{{ $report_info['contrib'] }}</td>  
			<td>{{ $report_info['margin'] }}</td>  			
			<td>{{ $report_info['delivered_margin'] }}</td>  			
            <td>{{ $report_info['cancel_ord_cnt'] }}</td>  
            <td>{{ $report_info['cancel_ord_val'] }}</td>  
            <td>{{ $report_info['return_ord_cnt'] }}</td>  
            <td>{{ $report_info['return_ord_val'] }}</td> 
            <td>{{ $report_info['cancel_percent'] }}</td> 
            <td>{{ $report_info['return_percent'] }}</td> 
            <td>{{ $report_info['today_business'] }}</td> 
             <td>{{ $ordDate }}</td>
        </tr>
        @endforeach
    </table>
    
</html>