<?php

//product_title   cat_name  sku
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
					<td>{$value['customer_type']}</td>
					<td>{$value['segment_type']}</td>
					<td>{$value['volume_class']}</td>

					</tr>";
			}
		}

		?>
	</table>

</html>