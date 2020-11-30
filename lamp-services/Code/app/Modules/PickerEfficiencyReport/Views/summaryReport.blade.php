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
							echo $value['excess_orders']
						?>
					</td>

					<td>
						<?php
							echo $value['short_orders']
						?>
					</td>


					<td>
						<?php
							echo $value['total_verified_skus'] 
						?>
					</td>
					
					<td>
						<?php
							echo $value['excess_qty']
						?>
					</td>

					<td>
						<?php
							echo $value['short_qty']
						?>
					</td>

					<td>
						<?php
							echo $value['wrongt_qty']
						?>
					</td>


				</tr>
				<?php }
				
				?>
			</tbody>
		</table>
	</body>
</html>