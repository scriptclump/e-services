<html>
	<table>
		<tr>
		<?php
			if(isset($productinfo) && count($productinfo)>0){
				foreach($headers as $key=> $value){
					if($value === 'ELP' && isset($inventoryELP) && $inventoryELP==1){
						echo '<th>'.str_replace("_", " ", $value).'</th>';
					}elseif($value === 'DLP' && isset($inventoryDLP) && $inventoryDLP==1){
						echo '<th>'.str_replace("_", " ", $value).'</th>';
					}elseif($value === 'FLP' && isset($inventoryFLP) && $inventoryFLP==1){
						echo '<th>'.str_replace("_", " ", $value).'</th>';
					}else{
						echo '<th>'.str_replace("_", " ", $value).'</th>';
					}					
				}	
			}
		?>
		</tr>
		<?php
			foreach ($productinfo as $key => $rowData) {
				echo "<tr>";
				foreach ($rowData as $key => $value) {
					if($key === 'ELP' && isset($inventoryELP) && $inventoryELP==1){
						echo "<td>".$value."</td>";
					}elseif($key === 'DLP' && isset($inventoryDLP) && $inventoryDLP==1){
						echo "<td>".$value."</td>";
					}elseif($key === 'FLP' && isset($inventoryFLP) && $inventoryFLP==1){
						echo "<td>".$value."</td>";
					}else{
						echo "<td>".$value."</td>";
					}
				}
				echo "</tr>";
			}
		?>
	</table>
</html>