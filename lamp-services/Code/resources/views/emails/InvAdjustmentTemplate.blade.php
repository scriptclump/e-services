<html>
	
	<head>
		<style>
		table {
		    font-family: arial, sans-serif;
		    border-collapse: collapse;
		    width: 100%;
		}
		td, th {
		    border: 1px solid #dddddd;
		    text-align: left;
		    padding: 8px;
		}
		tr:nth-child(even) {
		    background-color: #dddddd;
		}
		caption
		{
			font-style: initial;
		    font-weight: bold;
		    font-size: 15px;
		    text-align: left;
		    padding: 10px;
		}
		</style>
	</head>
	<body>
		Hi All,
		<br/><br/>
		<p>Uploaded Inventory Adjustment Sheet got rejected, Please check the below negative values.</p>
		<br/><br/><br/>
			<table>	
				<tr>
	              <th>Product Id</th>
	              <th>Product Title</th>
	              <th>Uploaded Excess</th>
	              <th>Current DIT</th>
	              <th>Uploaded DIT</th>
	              <th>Current DND</th>
	              <th>Uploaded DND</th>
	            </tr>
				@if($tableData)
					@foreach($tableData as $tableValue)
						<tr>
							<td>{{$tableValue['product_id']}}</td>
							<td>{{$tableValue['product_title']}}</td>
							<td>{{$tableValue['upl_excess']}}</td>
							<td>{{$tableValue['cur_dit']}}</td>
							<td>{{$tableValue['up_dit_qty']}}</td>
							<td>{{$tableValue['cur_dnd_qty']}}</td>
							<td>{{$tableValue['up_dnd_qty']}}</td>
						</tr>
					@endforeach	
				@endif				  					
			</table>
			<br/><br/><br/>
		<br/><br/>
	
		<p>                
            <div>Thanks,</div>
            <div>Ebutor.com</div>
        </p>   
	</body>
</html>