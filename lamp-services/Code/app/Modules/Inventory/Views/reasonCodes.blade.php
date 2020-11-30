<html>
<head>
	<title>Reason Codes</title>
</head>
<body>
	<table>
		<thead>
			<tr>
				<th>
					Reason Codes
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