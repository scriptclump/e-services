<html>
	<table>
		<tr>
			
			<th>Product Id</th>
			<th>Product Title</th>
			<th>SKU</th>
			<th>KVI</th>
			<th>MRP</th>
			<th>VAT %</th>
			<th>Sellable</th>
			<th>CP Enabled</th>
			<th>SOH</th>
			<th>ATP</th>
			<th>ESU</th>
			<th>ESP</th>
			<th>ELP</th>
			<th>PTR</th>
			<th>Orders on Hand</th>
			<th>Available Inventory</th>
			<th>CFC Qty</th>
			<th>Available CFC</th>
			<th>CFC-ELP</th>
			<th>DIT Qty</th>
			<th>D&D Qty</th>
			<th>Freebie Product Title</th>
			<th>Freebie SKU</th>
			<th>Offer Pack</th>
			<!-- <th>Category</th>
			<th>Sub Category</th> -->
			<th>PO Code</th>
			<th>PO Date</th>
			<th>Manufacturer</th>
			
		</tr>
		<?php
		$Sellable = "";
		$Enabled = "";
		$cfc_elp = "";
		for($i=0;$i<sizeof($productinfo);$i++)
		{
			if($productinfo[$i]['is_sellable'] == 0)
			{
				$Sellable = "No";
			} else {
				$Sellable = "Yes";
			}

			if($productinfo[$i]['cp_enabled'] == 0)
			{
				$Enabled = "No";
			} else {
				$Enabled = "Yes";
			}

			$cfc_elp = ($productinfo[$i]['cfc_qty'] * $productinfo[$i]['elp']);
			
		?>
		<tr>
			
			<td><?php echo $productinfo[$i]['product_id']  ?></td>
			<td><?php echo $productinfo[$i]['product_title']  ?></td>
			<td><?php echo $productinfo[$i]['sku']  ?></td>
			<td><?php echo $productinfo[$i]['kvi']  ?></td>
			<td><?php echo $productinfo[$i]['mrp']  ?></td>
			<td><?php echo $productinfo[$i]['vatpercentage']  ?></td>
			<td><?php echo $Sellable  ?></td>
			<td><?php echo $Enabled  ?></td>
			<td><?php echo $productinfo[$i]['soh']  ?></td>
			<td><?php echo $productinfo[$i]['atp']  ?></td>
			<td><?php echo $productinfo[$i]['esu'];  ?></td>
			<td><?php echo $productinfo[$i]['esp']  ?></td>
			<td><?php echo $productinfo[$i]['elp']  ?></td>
			<td><?php echo $productinfo[$i]['ptrvalue'] ?></td>
			<td><?php echo $productinfo[$i]['order_qty']  ?></td>
			<td><?php echo $productinfo[$i]['available_inventory']  ?></td>
			<td><?php echo $productinfo[$i]['cfc_qty']  ?></td>
			<td><?php echo $productinfo[$i]['available_cfc_qty']  ?></td>
			<td><?php echo $cfc_elp  ?></td>
			<td><?php echo $productinfo[$i]['dit_qty']  ?></td>
			<td><?php echo $productinfo[$i]['dnd_qty']  ?></td>
			<td><?php echo $productinfo[$i]['frebee_desc']  ?></td>
			<td><?php echo $productinfo[$i]['freebee_sku']  ?></td>
			<td><?php echo $productinfo[$i]['pack_type']  ?></td>
			<!-- <td><?php //echo $productinfo[$i]['category_name']  ?></td>
			<td><?php //echo $productinfo[$i]['sub_category_name']  ?></td> -->
			<td><?php echo $productinfo[$i]['po_code']  ?></td>
			<td><?php echo $productinfo[$i]['po_date']  ?></td>
			<td><?php echo $productinfo[$i]['manufacturer_name']  ?></td>
			
			</tr>
		<?php
		}
		?>
		
	</table>


</html>