<html>
<body>
<table border=1>
	<thead>
		<tr>
			<?php
				for ($i=0; $i < sizeof($headers); $i++) { 
					echo "<th>".$headers[$i]."</th>";
				}
			?>
		</tr>

	</thead>
	<tbody>
		<?php foreach ($data as $value) { ?>
		<tr>
			<td><?php echo $value['SO_num'];  ?></td>
			<td><?php echo $value['SO_Date'];  ?></td>
			<td><?php echo $value['created_By'];  ?></td>
			<td><?php echo $value['retailer_name'];  ?></td>
			<td><?php echo $value['area_name'];  ?></td>
			<td><?php echo $value['beat_name'];  ?></td>
			<td><?php echo $value['hub_name'];  ?></td>
			<td><?php echo $value['order_status'];  ?></td>
			<td><?php echo $value['product_code'];  ?></td>
			<td><?php echo $value['product_description'];  ?></td>
			<td><?php echo $value['mrp'];  ?></td>
			<td><?php echo $value['SO_Qty'];  ?></td>
			<td><?php echo $value['SO_val'];  ?></td>
			<td><?php echo $value['picked_Date'];  ?></td>
			<td><?php echo $value['Picked_qty'];  ?></td>
			<td><?php echo $value['picked_by'];  ?></td>
		</tr>
		<?php } ?>
	</tbody>
	
</table>
</body>
</html>