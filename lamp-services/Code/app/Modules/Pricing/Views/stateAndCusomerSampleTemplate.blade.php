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
					<td>".(isset($value['state']) ? $value['state'] : '')."</td>
					<td>".(isset($value['customer']) ? $value['customer'] : '')."</td>
					<td>".(isset($value['dc']) ? $value['dc'] : '')."</td>
					</tr>";
			}
		}

		?>
	</table>

</html>