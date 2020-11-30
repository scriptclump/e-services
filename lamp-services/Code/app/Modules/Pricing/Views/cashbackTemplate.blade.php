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
				$effective_date = date('n/j/Y', strtotime($value['effective_date']));
				if($effective_date == "1/1/1970"){
					$effective_date="";
				}
				echo "<tr>
					<td>{$value['product_id']}</td>
					<td>{$value['sku']}</td>
					<td>{$value['product_title']}</td>
					<td>{$value['StateName']}</td>
					<td>{$value['CustomerType']}</td>
					<td>{$effective_date}</td>
					<td>{$value['price']}</td>
					<td>{$value['ptr']}</td>
					<td>{$value['ptr']}</td>
					<td>{$value['ptr']}</td>
					<td>{$value['ptr']}</td>
					<td>{$value['ptr']}</td>
					<td>{$value['ptr']}</td>
					</tr>";
			}
		}

		?>
	</table>

</html>