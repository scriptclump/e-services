<html>
<head></head>
	<body>
		<table>
			<thead>
				
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
				foreach ($data as $value) {?>
				<tr>
					<td>
						<?php
							echo $value['verifier_name']
						?>
					</td>
					<td>
						<?php
							echo $from_date
						?>
					</td>

					<td>
						<?php
							echo $to_date
						?>
					</td>

					<td>
						<?php
							echo $value['total_verified_orders']
						?>
					</td>

					<td>
						<?php
							echo $value['total_verified_crate']
						?>
					</td>

					<td>
						<?php
							echo $value['total_verified_skus']
						?>
					</td>

					<td>
						<?php
							echo !empty($value['excess_Orders'])?$value['excess_Orders']:0 
						?>
					</td>
					
					<td>
						<?php
							echo !empty($value['excess_qty'])?$value['excess_qty']:0 
						?>
					</td>

					<td>
						<?php
							echo !empty($value['short_orders'])?$value['short_orders']:0
						?>
					</td>

					<td>
						<?php
							echo !empty($value['short_qty'])?$value['short_qty']:0
						?>
					</td>




					




				</tr>
				<?php }
				
				?>
			</tbody>
		</table>
	</body>
</html>