<html>
	<table>
		<tr>
			
			<?php
			for($i=0;$i<sizeof($Derivedheaders);$i++)
		{
			
			
		?>
			<th><?php echo $Derivedheaders[$i] ;?></th>
			<?php

				}
			?>
		</tr>
		<?php

		for($i=0;$i<sizeof($indentsExport);$i++)
		{
			
			
		?>
		<tr>
			
			



		<?php
			for($j=0;$j<sizeof($Derivedheaders);$j++)
		{
			
			
		?>
			<td><?php echo $indentsExport[$i][$Derivedheaders[$j]]  ?></td>
			<?php

				}
			?>
			</tr>
		<?php
		}
		?>
		
	</table>


</html>