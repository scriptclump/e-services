<html>
    <table>
        <tr>   
            <th>Name</th>
            <th>Role Name</th>
            <th>First Check in</th>
            <th>Last Check out</th>
        </tr>
        @foreach($Reportinfo as $report_info)	
        <tr>
            <td>{{ $report_info['user_name'] }}</td>
            <td>{{ $report_info['role_id'] }}</td>
            <td>{{ $report_info['first_checkin_time'] }}</td>            
            <td>{{ $report_info['last_checkout_time'] }}</td>       
        </tr>
        @endforeach
    </table>
    
</html>