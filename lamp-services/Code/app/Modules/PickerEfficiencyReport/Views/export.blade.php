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
			<td><?php echo $value['picked_date'];  ?></td>
			<td><?php echo $value['picked_By'];  ?></td>
			<td><?php echo $value['scheduled_piceker_date'];  ?></td>
			<td><?php echo $value['hub_name'];  ?></td>
			<td><?php echo $value['order_num'];  ?></td>
			<td><?php echo $value['order_qty'];  ?></td>
			<td><?php echo $value['order_val'];  ?></td>
			<td><?php echo $value['skus_order'];  ?></td>
			<td><?php echo $value['picked_qty'];  ?></td>
			<td><?php echo $value['cancelled_qty'];  ?></td>
			<td><?php echo $value['comment'];  ?></td>
			<td><?php echo $value['Picking_Start_Time'];  ?></td>
			<td><?php echo $value['complition_Time'];  ?></td>
			<td><?php echo $value['duration'];  ?></td>
			<td><?php echo $value['area_name'];  ?></td>
			<td><?php echo $value['order_fill_rate'];  ?></td>
		</tr>
		<?php } ?>
	</tbody>
	
</table>
</body>
</html>