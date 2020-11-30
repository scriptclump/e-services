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
                    <td>$i</td>
                    <td>{$value->ManufacName}</td>
                    <td>{$value->BrandName}</td>
                    <td>{$value->categoryName}</td>
                    <td>{$value->PrdName}</td>
                    <td>{$value->company_asset_code}</td>
                    <td>{$value->serial_number}</td>
                    <td>{$value->purchase_date}</td>
                    <td>{$value->warranty_status}</td>
                    <td>{$value->AstStatus}</td>
                    <td>{$value->allocated_to_name}</td>
                    <td>{$value->depresiation_month}</td>
                    <td>{$value->depresiation_per_month}</td>
                    <td>{$value->assetCategory}</td>
                    <td>{$value->is_movable}</td>
                    <td>{$value->ProductValue}</td>

                    </tr>";

                    $i++;
            }
        }

        ?>
    </table>

</html>