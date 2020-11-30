<!DOCTYPE html>
<html lang="en-US">
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
        <div>
            {{$name}},
            <br />
            
            Below are the SKU's requested by the users from last few hours
            <br /><br /><br />
            
            <table border="">	
				<tr>
                    <th>Product Title</th>
                    <th>Requested Qty</th>
                    <th>Warehouse</th>
                </tr>
                @foreach($productList as $productData)
					<tr>
                        <td>{{ $productData->product_title }}</td> 
                        <td>{{ $productData->requested_qty }}</td>
                        <td>{{ $productData->le_wh_id }}</td>
					</tr>		
    		    @endforeach
            </table>
			<br/><br/><br/>
            <p>                
                <div>Thanks,</div>
                <div>Ebutor Tech Support</div>
            </p>             
        </div>
    </body>
</html>
