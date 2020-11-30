<html>
<head>
</head>
<body>
	<table>
		<thead>
			<tr>
				<th>
					User Name
				</th>
				<th>
				Email ID
				</th>

			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($users as $value)
			{
			?>
			<tr>
				<td>
					<?php echo $value['username']; ?>
				</td>

				<td>
					<?php echo $value['email_id']; ?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>

</body>
</html>