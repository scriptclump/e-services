<html>
    <table>
        <tr> 
            <th>DC Name</th>  
            <th>FF Name</th>
            <th>Call Date</th>
            <th>Check In</th>
            <th>Check Out</th>  
            <th>Time Spent</th>  
        </tr>
        @foreach($Reportinfo as $report_info)
        <?php 
            if($report_info['Created_At'])
            {
                $ordDateArray = explode(' ',$report_info['Created_At']);
                $ordDate = $ordDateArray[0];
            }
            else
            {
                $ordDate =  $report_info['Created_At'];
            }
        ?>  
        <tr>
            <td>{{ $report_info['DC'] }}</td>
            <td>{{ $report_info['NAME'] }}</td>
            <td>{{ $ordDate }}</td>   
            <td>{{ $report_info['Check_In'] }}</td>            
            <td>{{ $report_info['Check_Out'] }}</td> 
            <td>{{ $report_info['Duration']}}</td>     
        </tr>
        @endforeach
    </table>
    
</html>