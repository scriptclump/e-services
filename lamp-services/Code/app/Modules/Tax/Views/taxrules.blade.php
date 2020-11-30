<?php
$maxWidth = max( array_map( 'count',  $taxclass_codes ) );
?>
<html>
<table>
    <tr><!-- headers starts for excel downloading in a tab -->
        <?php
            foreach ($headers as $key => $value) {?>
                <td><?php echo $value['code'];  ?></td>
            <?php }
        ?>
        
    </tr>
    
        <?php
        
        for($i=0; $i<$maxWidth; $i++){
            echo "<tr>";
            foreach ($headers as $headerkey => $headersvalue) 
            {
                $code = ($headersvalue['code']=='*(ALL)')?"*":$headersvalue['code'];
                if(isset($taxclass_codes[$code][$i]) && !empty($taxclass_codes[$code][$i])){
                    echo "<td>{$taxclass_codes[$code][$i]}</td>";
                }
                else{
                    echo "<td></td>";
                }
                
            }
            echo "</tr>";
        }
        ?>
    
</table>
</html>