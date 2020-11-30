<html>
<head></head>
<body>
	<table>
		<thead>
			<tr>
				<?php
				for($i=0;$i < sizeof($headers); $i++)
					{
				?>
						<th>
							<?php echo $headers[$i]; ?>
						</th>
				<?php
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($inventory as $value)
			 {
			?>
			<tr>
				<td><?php echo $value['product_id']  ?></td>
				<td><?php echo $value['product_title']  ?></td>
				<td><?php echo $value['sku']  ?></td>
				<td><?php echo $value['mrp']  ?></td>
				<td><?php echo $value['esp']  ?></td>
			</tr>
			<?php
		}
			?>
		</tbody>
	</table>
</body>
</html>