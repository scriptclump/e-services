<html>
    <table>
        <tr>   
            <th>Business Legal Name</th>
            <th>Feedback Group</th>
            <th>Feedback Type</th>
            <th>Comments</th>
            <th>Created By</th>
            <th>Image</th>
            <th>Audio</th>
            <th>Created At</th> 
        </tr>
        @foreach($Reportinfo as $report_info)		
        <tr>
            <td>{{ $report_info['legal_entity'] }}</td>
            <td>{{ $report_info['feedback_group_type'] }}</td>            
            <td>{{ $report_info['feedback_type'] }}</td> 
            <td>{{ $report_info['comments'] }}</td>
            <td>{{ $report_info['created_by'] }}</td>            
            <td>{{ $report_info['picture'] }}</td>            
            <td>{{ $report_info['audio'] }}</td>            
            <td>{{ $report_info['created_at'] }}</td>            
        </tr>
        @endforeach
    </table>
    
</html>