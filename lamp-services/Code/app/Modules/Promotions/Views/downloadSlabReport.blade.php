<?php

?>
<html>
	<table>

		<tr>
			<?php
			for($i=0;$i<sizeof($headers_line_one);$i++)
			{
				if( $headers_line_one[$i]=='Promotion Information' ){
			?>
					<td colspan="4" style ="font-weight: bold; text-align: center;background:#A9A9A9; "><?php echo $headers_line_one[$i]; ?></td>
			<?php  
				}
				if( $headers_line_one[$i]=='Customer Information' ){
			?>
					<td colspan="7" style ="font-weight: bold; text-align: center;background:#D3D3D3"><?php echo $headers_line_one[$i]; ?></td>
			<?php
				}
				if( $headers_line_one[$i]=='Order Information' ){
				?>
					<td colspan="11" style ="font-weight: bold; text-align: center;background:#A9A9A9;"><?php echo $headers_line_one[$i]; ?></td>
			<?php
			}
			}
			?>

		</tr>

		<tr>
			<?php
			for($i=0;$i<sizeof($headers);$i++)
			{
			?>
			<td align="center" style="background:#ffff00; font-weight: bold;border: 1px solid #999999;"><?php echo $headers[$i]; ?></td>
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
				<td>{$value->promotionid}</td>
				<td>{$value->start_date}</td>
 					<td>{$value->end_date}</td>
 					<td>{$value->promotionStatus}</td>
					<td>{$value->HUBName}</td>
					<td>{$value->SOName}</td>
					<td>{$value->SONumber}</td>
					<td>{$value->AreaName}</td>
					<td>{$value->BeatName}</td>
					<td>{$value->shop_name}</td>
					<td>{$value->RetailerCode}</td>
					<td>{$value->order_code}</td>
					<td>{$value->OrderDate}</td>
					<td>{$value->ProdutName}</td>
					<td>{$value->ProductSKU}</td>
					<td>{$value->mrp}</td>
					<td>{$value->NoOfEaches}</td>
					<td>{$value->ESU_qty}</td>
					<td>{$value->Slabrates}</td>
					<td>{$value->OrderQty}</td>
					<td>{$value->total}</td>
 					<td>{$value->OrderStatus}</td>
					</tr>";
			}
		}

		?>
	</table>

</html>