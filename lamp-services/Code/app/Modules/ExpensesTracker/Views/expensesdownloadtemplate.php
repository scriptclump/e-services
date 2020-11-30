<?php

// echo "<pre>";print_r($headers);die;
//product_title   cat_name  sku
?>
<html>
	<table border="1" cellspacing="0" cellpadding="5">
		<tr bgcolor="#efefef">
			<?php
			for($i=0;$i<sizeof($headers_one);$i++)
			{
			?>
				<td align="center" style="background:#efefef; font-weight: bold; border: 1px solid #999999;"><?php echo $headers_one[$i]; ?></td>
					
			<?php	
			}
			?>

		</tr>

		<?php
		if(!empty($data))
		{
			echo $data;
		}

		?>
	</table>

</html>