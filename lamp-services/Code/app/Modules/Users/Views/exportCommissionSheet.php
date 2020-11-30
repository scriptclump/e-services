<html>
    <table>
        <?php
            if(isset($headers)){
                echo "<thead><tr>";
                foreach ($headers as $head) {
                    echo "<th>".$head."</th>";
                }
                echo "</tr></thead>";
            }
        ?>
        <tbody>
        <?php
            if(!empty($data))
            {
                foreach ($data as $value){
                    echo "<tr>";
                    foreach ($value as $record) {
                        echo "<td>".$record."</td>";
                    }
                    echo "</tr>";
                }
            }
        ?>
        </tbody>
    </table>
</html>