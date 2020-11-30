<?php

?>
<html>
	<table>

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

			foreach ($data as $value) 
			{
				echo "<tr>
				<td>{$value['prmt_det_name']}</td>
				<td>{$value['start_date']}</td>
				<td>{$value['end_date']}</td>
				<td>{$value['prmt_det_status']}</td>
				<td>{$value['prmt_offer_type']}</td>
				<td>{$value['prmt_offer_value']}</td>
				<td>{$value['PrmtStatus']}</td>
				<td>{$value['ProductInformation']}</td>
				<td>{$value['offer_on']}</td>
				<td>{$value['state_names']}</td>
				<td>{$value['prmt_det_id']}</td>
				<td>{$value['prmt_tmpl_Id']}</td>
				<td>{$value['created_at']}</td>
				</tr>";
			}
		}



		?>
	</table>

</html>