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

				echo "<tr></tr>";
				echo "<tr>
					<td>sku</td>
					<td>uom</td>
					<td>qty</td>
					<td>base_price</td>
					</tr>";
			
		}

		?>
	</table>

</html>