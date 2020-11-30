<html>
    <head>
    </head>
    <body>
        <table>
            <thead>
                <tr><?php
                    for ($i = 0; $i < sizeof($headers); $i++) {
                        ?>
                        <th>
                            <?php echo $headers[$i]; ?>
                        </th>
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($res as $value) {
                    ?>
                    <tr>
                        <td><?php echo $value['product_id'] ?></td>
                        <td><?php echo $value['manufacturer_name'] ?></td>
                        <td><?php echo $value['product_title'] ?></td>
                        <td><?php echo $value['sku'] ?></td>
                        <td><?php echo $value['cfc_qty'] ?></td>
                        <td><?php echo $value['mrp'] ?></td>
                        <td><?php echo $value['cp_enabled'] ?></td>
                        <td><?php echo $value['po_code'] ?></td>
                        <td><?php echo $value['po_date'] ?></td>
                        <td><?php echo $value['po_qty'] ?></td>
                        <td><?php echo $value['avg_qty'] ?></td>
                        <td><?php echo $value['available_CFC'] ?></td>
                        <td><?php echo $value['opentobuy_cfc'] ?></td>
                        <td><?php echo $value['cfcToBuy'] ?></td>
                        <td><?php echo $value['minCFCElp'] ?></td>
                        <td><?php echo $value['LastBoughtCFCRate'] ?></td>
                        <td><?php echo $value['TotalAmount'] ?></td>
                        <td><?php echo $value['SupplierName'] ?></td>
                        <td><?php echo $value['WDSWD'] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </body>
</html>