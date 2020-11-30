<?php

?>
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
					<td>{$value['state']}</td>
					<td>{$value['customer']}</td>
					<td>{$value['packname']}</td>
					<td>{$value['dc']}</td>
					</tr>";
			}
		}

		?>
	</table>

</html>