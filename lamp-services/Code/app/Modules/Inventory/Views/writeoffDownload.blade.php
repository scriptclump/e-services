<?php
	// echo "<pre>";print_r($headers);die;
?>
<html>
<head></head>
	<body>
		<table>
			<thead>
				<tr><td>WarehouseId : <?php echo $warehouseId ?>
					<td> From Date :<?php echo $from_date ?> </td>
					<td>To Date :<?php echo $to_date ?> </td>
				</tr>
				<tr>
				<?php
					foreach ($headers as $values) {
						// print_r($values);
						echo "<th>".$values."</th>";
					}
				?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($data as $value) {?>
				<tr>
					<td>
						<?php
							echo $value['product_id']
						?>
					</td>
					<td>
						<?php
							echo $value['product_title']
						?>
					</td>
					<td>
						<?php
							echo $value['mrp']
						?>
					</td>
					<td>
						<?php
							echo $value['sku']
						?>
					</td>
					<td>
						<?php
							echo $value['sp']
						?>
					</td>
					<td>
						<?php
							echo $value['lp']
						?>
					</td>

					<td>
						<?php
							echo $value['cur_dit_qty']
						?>
					</td>
					<td>
						<?php
							echo $value['cur_dnd_qty']
						?>
					</td>					

				</tr>
				<?php }
				
				?>
			</tbody>
		</table>
	</body>
</html>