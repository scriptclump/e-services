<html>
<body><table>
			<thead>
				<tr><td>AuditId : <?php echo $audit_id ?> </td></tr>
				<tr><?php
					foreach ($headers as $values) {
						echo "<th>".$values."</th>";
					} ?>
				</tr>
			</thead>
			<tbody><?php foreach ($products_info as $value) {?>
				<tr>

					<td><?php
							echo $value['wh_id']
						?>
					</td>

					<td><?php
							echo $value['product_id']
						?>
					</td>

					<td><?php
							echo $value['product_title']
						?>
					</td>

					<td><?php
							echo $value['sku']
						?>
					</td>

					<td><?php
							echo $value['mrp']
						?>
					</td>

					<td><?php
							echo $value['elp']
						?>
					</td>

					<td><?php
							echo $value['opening_balance']
						?>
					</td>

					<td><?php
							echo $value['soh']
						?>
					</td>

					<td><?php
							echo $value['sales_return_qty']
						?>
					</td>

					<td><?php
							echo $value['purchase_return_qty']
						?>
					</td>

					<td><?php
							echo $value['picked_qty']
						?>
					</td>

					<td><?php
							echo $value['quarantine_qty']
						?>
					</td>

					<td><?php
							echo $value['location_code']
						?>
					</td>

					<td><?php
							echo $value['new_location_code']
						?>
					</td>

					<td><?php
							echo $value['old_bin_qty']
						?>
					</td>

					<td><?php
							echo $value['updated_by']
						?>
					</td>


					<td><?php
							echo $value['good_qty']
						?>
					</td>

					<td><?php
							echo $value['damage_qty']
						?>
					</td>

					<td><?php
							echo ($value['damage_qty'] * $value['elp']);
						?>
					</td>

					<td><?php
							echo $value['expire_qty']
						?>
					</td>

					<td><?php
							echo ($value['expire_qty'] * $value['elp']);
						?>
					</td>

					<td><?php
							echo $value['missing_qty']
						?>
					</td>

					<td><?php
							echo ($value['missing_qty'] * $value['elp']);
						?>
					</td>

					<td><?php
							echo $value['excess_qty']
						?>
					</td>

					<td><?php
							echo ($value['excess_qty'] * $value['elp']);
						?>
					</td>

					<td><?php
							echo $value['bin_qty']
						?>
					</td>

					<td><?php
							echo $value['appr_good_qty']
						?>
					</td>

					<td><?php
							echo $value['appr_damage_qty']
						?>
					</td>

					<td><?php
							echo $value['appr_expire_qty']
						?>
					</td>


			</tr<?php }
				// die;
				?>
			</tbody>
		</table>
	</body>
</html>