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
					<td>".(isset($value['category_name']) ? $value['category_name'] : '')."</td>
					<td>".(isset($value['category_id']) ? $value['category_id'] : '')."</td>
					<td>".(isset($value['dc_name']) ? $value['dc_name'] : '')."</td>
					<td>".(isset($value['dc_code']) ? $value['dc_code'] : '')."</td>
					</tr>";
			}
		}

		?>
	</table>

</html>