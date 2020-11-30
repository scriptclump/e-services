<html>
	<table>
		<tr>
			<?php
			for($i=0;$i<sizeof($headers);$i++)
			{
			?>
			<td><?php echo $headers[$i]; ?></td>
			<?php 
				}
			?>

		</tr>
		<?php
		if(!empty($data))
		{

			foreach ($data as $key => $value) 
			{
				echo "<tr>
					<td>".(isset($value['name']) ? $value['name'] : '')."</td>
					<td>".(isset($value['mobile_no']) ? $value['mobile_no'] : '')."</td>
					<td>".(isset($value['dc_name']) ? $value['dc_name'] : '')."</td>
					<td>".(isset($value['dc_code']) ? $value['dc_code'] : '')."</td>
					</tr>";
			}
		}

		?>
	</table>

</html>