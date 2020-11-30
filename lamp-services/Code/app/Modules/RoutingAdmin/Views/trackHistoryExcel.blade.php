<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<table>
			<thead>
 				<tr> 	
 					<th>Hub</th>
 					<th>DO's</th>
 					<th>Vehicle No</th>
 					<th>Attempted Date</th>
 					<th>Distance</th>
 					<th>Orders Assigned</th>
 					<th>Invoice Value</th>
 					<th>Delivered</th>
 					<th>PR</th>
 					<th>FR</th>
 					<th>Hold</th>
 					<th>Collected Value</th>
 					<th>Returned Value</th>
 				</tr>
 			</thead>
 			<tbody>
 				@foreach($tripdata as $key => $data)	
		        <tr>
		            @foreach($data as $d)
		            <td>{{$d}}</td>
		            @endforeach    
		        </tr>
		        @endforeach 				
 			</tbody>
	        
	    </table>
	</body>   
    
</html>