<html>
	<table>
		<tr>
		<?php 
			if(isset($productinfo) && count($productinfo)>0){
				foreach ($productinfo[0] as $key => $value) {
					echo '<th>'.str_replace("_", " ", $key).'</th>';
				}
			}
		?>
		</tr>
		
		<?php
			foreach ($productinfo as $indexkey => $rowData) {
				echo "<tr>";
				foreach ($rowData as $key => $value) {
					echo "<td>".$value."</td>";
				}
				echo "</tr>";
			}
		?>
	</table>
</html>