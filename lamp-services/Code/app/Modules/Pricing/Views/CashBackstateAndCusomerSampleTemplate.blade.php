<?php

// echo "<pre>";print_r($headers);die;
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
					<td>{$value['state']}</td>
					<td>{$value['customer']}</td>
					<td>{$value['benificiary']}</td>
					<td>{$value['warehouses']}</td>
					<td>{$value['product_star']}</td>
					</tr>";
			}
		}

		?>
	</table>

</html>