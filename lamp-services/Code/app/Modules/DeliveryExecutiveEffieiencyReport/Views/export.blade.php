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
			<td><?php echo $value['delivery_date'];  ?></td>
			<td><?php echo $value['deliverdBy'];  ?></td>
			<td><?php echo $value['delivery_time'];  ?></td>
			<td><?php echo $value['submit_time'];  ?></td>
			<td><?php echo $value['hub_name'];  ?></td>
			<td><?php echo $value['order_num'];  ?></td>
			<td><?php echo $value['inv_qty'];  ?></td>
			<td><?php echo $value['inv_val'];  ?></td>
			<td><?php echo $value['inv_SKU'];  ?></td>
			<td><?php echo $value['delivered_qty'];  ?></td>
			<td><?php echo $value['return_qty'];  ?></td>
			<td><?php echo $value['reason'];  ?></td>
			<td><?php echo $value['duration'];  ?></td>
			<td><?php echo $value['order_status'];  ?></td>
			<td><?php echo $value['area_name'];  ?></td>
			<td><?php echo $value['beat_name'];  ?></td>
			<td><?php //echo $value['estimated_distance']; //now not in use  ?></td>
		</tr>
		<?php } ?>
	</tbody>	
</table>
</body>
</html>