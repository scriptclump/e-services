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
							echo $value['dcname']
						?>
					</td>
					<td>
						<?php
							echo $value['crate_num']
						?>
					</td>

					<td>
						<?php
							echo $value['order_code']
						?>
					</td>

					<td>
						<?php
							echo $value['order_date']
						?>
					</td>

					<td>
						<?php
							echo $value['hub_name']
						?>
					</td>

					<td>
						<?php
							echo $value['pickername']
						?>
					</td>

					<td>
						<?php
							echo $value['picking_time']
						?>
					</td>

					<td>
						<?php
							echo $value['verifier_name']
						?>
					</td>

					<td>
						<?php
							echo $value['verification_time']
						?>
					</td>

					<td>
						<?php
							echo $value['verification_status']
						?>
					</td>

					<td>
						<?php
							echo $value['sku']
						?>
					</td>

					<td>
						<?php
							echo $value['product_title']
						?>
					</td>

					<td>
						<?php
							echo $value['reason']
						?>
					</td>

					<td>
						<?php
							echo $value['wrong_qty']
						?>
					</td>


				</tr>
				<?php }
				
				?>
			</tbody>
		</table>
	</body>
</html>