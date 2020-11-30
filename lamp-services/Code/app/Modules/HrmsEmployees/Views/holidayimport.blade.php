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
					<td>".(isset($value['emp_group_id']) ? $value['emp_group_id'] : '')."</td>
					<td>".(isset($value['group_name']) ? $value['group_name'] : '')."</td>
					</tr>";
			}
		}

		?>		
	</table>
</html>		

