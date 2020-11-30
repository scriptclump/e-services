<html>
    <table>
        <tr>
            <?php
            for ($i = 0; $i < sizeof($hsn_headers); $i++) {
                ?>
                <td><?php echo $hsn_headers[$i]; ?></td>
                <?php
            }
            ?>

        </tr>
        <?php
        if (!empty($hsn_code_details)) {
            foreach ($hsn_code_details as $key => $value) {
                echo "<tr>
                        <td>{$value['ITC_HSCodes']}</td>
                        <td>{$value['HSC_Desc']}</td>
                        <td>{$value['tax_percent']}</td>
                      </tr>";
            }
        }
        ?>
    </table>
</html>