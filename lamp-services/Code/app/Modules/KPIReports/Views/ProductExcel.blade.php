<?php 

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
            $i=1;

            foreach ($data as $value) 
            {
                
                echo "<tr>
                    <td>{$value->Product_ID}</td>
                    <td>{$value->Product_Name}</td>
                    <td>{$value->SKU}</td>
                    <td>{$value->MRP}</td>
                    <td>{$value->Category_ID}</td>
                    <td>{$value->Category_Name}</td>
                    <td>{$value->SOH}</td>
                    <td>{$value->Available_CFC}</td>
                    <td>{$value->DIT}</td>
                    <td>{$value->Missing}</td>
                    <td>{$value->CPEnabled}</td>
                    <td>{$value->KVI}</td>
                    <td>{$value->SubCategory}</td>
                    <td>{$value->CFC_Sold}</td>
                    <td>{$value->Total_Orders}</td>
                    <td>{$value->TBV}</td>                    
                    <td>{$value->TBV_Contrib}</td>
                    <td>{$value->TGM}</td>
                    <td>{$value->TGM_Contrib}</td>
                    <td>{$value->Inventory_Stock_Days}</td>
                    <td>{$value->Color}</td>
                    <td>{$value->Brand_ID}</td>
                    <td>{$value->Brand_Name}</td>
                    <td>{$value->Manafacture_ID}</td>
                    <td>{$value->Manafacture_Name}</td>
                    <td>{$value->Is_Sellable}</td>
                    <td>{$value->Hub_Name}</td>
                    <td>{$value->DC_Name}</td>
                    <td>{$value->Order_Date}</td>

                    </tr>";

                    $i++;
            }
        }

        ?>
	</table>
</html>