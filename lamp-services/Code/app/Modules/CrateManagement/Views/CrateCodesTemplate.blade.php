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
					<td>".(isset($value['crate_id']) ? $value['crate_id'] : '')."</td>
					<td>".(isset($value['crate_code']) ? $value['crate_code'] : '')."</td>
					<td>".(isset($value['le_wh_id']) ? $value['le_wh_id'] : '')."</td>
					</tr>";
				}
			}
		?>
	</table>
</html>