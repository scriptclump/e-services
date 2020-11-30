<?php
	// echo "<pre>";print_r($headers);die;
?>
<html>
<head></head>
	<body>
		<table>
			<thead>
				<tr><td>WarehouseId : <?php echo $warehouseId ?> </td></tr>
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
				foreach ($products_info as $value) {?>
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
							echo $value['sku']
						?>
					</td>

					<td>
						<?php
							echo $value['soh']
						?>
					</td>

					<td>
						<?php
							echo "0";
						?>
					</td>

					<td>
						<?php
							// echo $value['dit_qty']
							echo "0";
						?>
					</td>

					<td>
						<?php
							// echo $value['dnd_qty']
							echo "0";
						?>
					</td>

				</tr>
				<?php }
				
				?>
			</tbody>
		</table>
	</body>
</html>