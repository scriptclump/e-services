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
					<td>{$value['sup']}</td>
					<td>{$value['sup_code']}</td>
					<td>{$value['dc']}</td>
					<td>{$value['dc_code']}</td>
					<td>{$value['pack_name']}</td>
					</tr>";
			}
		}

		?>
	</table>

</html>