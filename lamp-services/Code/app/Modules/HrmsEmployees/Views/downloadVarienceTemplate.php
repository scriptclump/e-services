<?php

?>
<html>
    <table>

        <tr>
            <?php
            for($i=0;$i<sizeof($headers_line_one);$i++)
            {
                if( $headers_line_one[$i]=='Productive Hours Devation Report' ){
            ?>
                    <td colspan="<?php echo $count ?>" style ="font-weight: bold;"><?php echo $headers_line_one[$i]; ?></td>
            <?php  
                }
                if( $headers_line_one[$i]=='Working Days' ){
            ?>
                    <td colspan="1"></td>

            <?php  
                }
                if( $headers_line_one[$i]=='Total Hours' ){
            ?>
                    <td colspan="3" style ="font-weight: bold; text-align: center;"><?php echo $headers_line_one[$i]; ?></td>
            <?php
                }
                if( $headers_line_one[$i]=='Productive Hours' ){
                ?>
                    <td colspan="3" style ="font-weight: bold; text-align: center;"><?php echo $headers_line_one[$i]; ?></td>
            <?php
            }
            }
            ?>

        </tr>

        <tr>
            <?php
            for($i=0;$i<sizeof($headers);$i++)
            {
            ?>
            <td align="center" style="font-weight: bold;border: 1px solid #999999;"><?php echo $headers[$i]; ?></td>
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
<style>
th {
    margin-top:45px;
}
table {
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid black;
}
</style>