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
    </table>

</html>