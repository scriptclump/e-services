<html>
<head>
	<title>Replenishment Codes</title>
</head>
<body>
	<table>
		<thead>
			<tr>
				<th>
					Replanishment Codes
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($reasoncodes as $value)
			{
			?>
			<tr>
				<td>
					<?php echo $value; ?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>

</body>
</html>