<html>
    <table>
        <tr><td></td><td></td><td></td><td>Required</td><td>Required</td></tr>
        <tr>
            <?php
            for ($i = 0; $i < sizeof($headers); $i++) {
                ?>
                <td><?php echo $headers[$i]; ?></td>
                <?php
            }
            ?>

        </tr>
        <?php
        if (!empty($data)) {

            foreach ($data as $key => $value) {
                echo "<tr>
					<td>{$value['brand_name']}</td>
					<td>{$value['product_title']}</td>
					<td>{$value['cat_name']}</td>
					<td>{$value['sku']}</td>
					<td>{$value['hsn_code']}</td>";
                for ($i = 0; $i < count($headers); $i++) {
                    if ($i > 4) {
                        if ($headers[$i] == $value['code']) {
                            echo "<td>{$value['tax_class_code']}</td>";
                        } else {
                            echo "<td></td>";
                        }
                    }
                }

                echo "</tr>";
            }
        }
        ?>
    </table>

</html>