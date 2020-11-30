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
		<p>Uploaded Inventory Sheet got rejected, Please check the below negative values.</p>
		<br/><br/><br/>
			<table>	
				<tr>
					<th>Product Id</th>
					<th>Product Title</th>
					<th>Old SOH</th>
					<th>Uploaded SOH</th>
					<th>Dit Qty</th>
					<th>Dnd Qty</th>
					<th>Quarantine Qty</th>
				</tr>
				@if($tableData)
					@foreach($tableData as $tableValue)
						<tr>
							<td>{{$tableValue['product_id']}}</td>
							<td>{{$tableValue['product_title']}}</td>
							<td>{{$tableValue['inv_soh']}}</td>
							<td>{{$tableValue['cur_soh']}}</td>
							<td>{{$tableValue['dit_qty']}}</td>
							<td>{{$tableValue['dnd_qty']}}</td>
							<td>{{$tableValue['quarantine_qty']}}</td>
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