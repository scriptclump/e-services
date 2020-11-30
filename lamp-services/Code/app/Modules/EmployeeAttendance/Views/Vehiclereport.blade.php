<?php

?>
<html>
    <table>
        <tr>
            <?php
            for($i=0;$i<sizeof($headers);$i++)
            {
            ?>
            <td  style="font-weight: bold;border: 1px solid #999999;"><?php echo $headers[$i]; ?></td>
            <?php  
                }
            ?>

        </tr>
        <?php
        if(!empty($data))
        {
               foreach ($data as $emp) {
        ?>           
        <tr>
            <?php
                foreach ($emp as $rec) {
            ?>
            <td><?php echo $rec ?></td>
            <?php
                }
            ?>
        </tr>
        <?php
               }

        
        }

        ?>
        </tr>
    </table>

</html>