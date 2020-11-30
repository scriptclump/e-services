<?php

//product_title   cat_name  sku
?>
<html>
	<table>
		<tr>
			<?php
			for($i=0;$i<sizeof($headers_one);$i++)
			{
				if($headers_one[$i]!="" && $headers_one[$i]!='(m/d/y)'){
					?>
					<td colspan="4" align="center"><?php echo $headers_one[$i]; ?></td>
					<?php
				}else{
					?>
					<td><?php echo $headers_one[$i]; ?></td>
					<?php
				}
			}
			?>

		</tr>
		<tr>
			<?php
			for($i=0;$i<sizeof($headers_two);$i++)
			{
			?>
			<td><?php echo $headers_two[$i]; ?></td>
			<?php 
				}
			?>

		</tr>
		<?php
		if(!empty($data))
		{

			$previousData = "";
			$currentData = "";
			$html_data="";
			$loopCounter = 0;
			foreach ($data as $key => $value) 
			{
				$currentData = $value['sku'] . $value['prmt_det_id'];

				if($previousData==$currentData){
					$html_data .= "
						
						<td>{$value['pack_type']}</td>
						<td>{$value['esu']}</td>
						<td>{$value['end_range']}</td>
						<td>{$value['price']}</td>";
				}else{
					if($loopCounter!=0){
						$html_data .= "</tr>";
					}
					$html_data .= "<tr>
						<td>{$value['sku']}</td>
						<td>{$value['product_title']}</td>
						<td>{$value['mrp']}</td>
						<td>{$value['prmt_lock_qty']}</td>
						<td>{$value['StateName']}</td>
						<td>{$value['CustomerType']}</td>
						<td>{$value['wh_name']}</td>
						<td>{$value['start_date']}</td>
						<td>{$value['end_date']}</td>
						<td></td>
						<td></td>
						<td>{$value['pack_type']}</td>
						<td>{$value['esu']}</td>
						<td>{$value['end_range']}</td>
						<td>{$value['price']}</td>";
				}
				$loopCounter++;
				$previousData = $currentData;
			}

			echo $html_data;
		}

		?>
	</table>

</html>