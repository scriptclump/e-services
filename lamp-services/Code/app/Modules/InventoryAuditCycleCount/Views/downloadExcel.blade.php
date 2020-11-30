<html>
<head></head>
	<body>
		<table>
			<thead>
				<tr><td>WarehouseId : <?php echo $warehouseId ?> </td></tr>
				<tr>
				<?php
					foreach ($headers as $values) {
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
							echo $value['category_name']
						?>
					</td>
					<td>
						<?php
							echo $value['manufacturer_name']
						?>
					</td>

					<td>
						<?php
							echo $value['brand_name']
						?>
					</td>

					<td>
						<?php
							echo $value['mrp']
						?>
					</td>

					<td>
						<?php
							echo $value['soh']
						?>
					</td>


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
							echo $value['product_group_id']
						?>
					</td>

					<td>
						<?php
							echo $value['sku']
						?>
					</td>



			</tr>
				<?php }
				
				?>
			</tbody>
		</table>
	</body>
</html>