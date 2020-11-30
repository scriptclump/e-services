<html>
    <head>
        <style>
            @media print {body {-webkit-print-color-adjust: exact;}}
            body{-webkit-print-color-adjust: exact;}
            table th {
                background-color: #e7ecf1 !important;
                color: 333 !important;   
            }
            table {
                border-collapse: collapse;
            }

            .printmartop {margin-top: 10px;}
            .container {margin-top: 20px;}
            .th {background-color: #999 !important;color: white !important;}
            .small1 {font-size: 73%;}
            .small2 {font-size: 65.5%;}
            .bg {background-color: #efefef;padding: 8px 0px;}
            .bold{font-weight: bold;}
            .line {border-top: 1px solid #e2e2e2;border-bottom: 1px solid #e2e2e2;}
            .table {border: 1px solid #000;}
            .table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
            .table-bordered>thead>tr>th{border: 1px solid #000 !important;}
            .table-bordered {border: 1px solid #000 !important;}
            th {background-color: #efefef;font-weight: bold;}
        </style>
    </head>
    <body>
        <div class="table-responsive">
            <table class="table" border="1" cellpadding="5" align="left" style="font-size:11px;">
                <thead>
                    <tr>
                        <th width="5%">
                            <?php
                            if ($type == "Inward") {
                                echo "Inward ID";
                            }

                            if ($type == "Outward") {
                                echo "Outward ID";
                            }
                            ?>
                        </th>
                        <th width="20%">Product Title</th>
                        <th width="10%">Transaction #</th>
                        <th width="10%">Transaction Type</th>
                        <th width="10%">State</th>
                        <th width="5%">Tax Type</th>
                        <th width="5%">Tax %</th>
                        <th width="10%">Tax Amount</th>
                        <th width="20%">DC Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $inward_outeardId = "";
                    foreach ($data as $value) {
                        if ($type == "Inward") {
                            $inward_outeardId = $value['inward_id'];
                        }
                        if ($type == "Outward") {
                            $inward_outeardId = $value['outward_id'];
                        }

                        echo "<tr><td>" . $inward_outeardId . "</td>";
                        echo "<td>" . $value['product_title'] . "</td>";
                        echo "<td align='right'>" . $value['transaction_no'] . "</td>";
                        echo "<td>" . $value['master_lookup_name'] . "</td>";
                        echo "<td>" . $value['state'] . "</td>";
                        echo "<td>" . $value['tax_type'] . "</td>";
                        echo "<td>" . $value['tax_percent'] . "</td>";
                        echo "<td>" . $value['tax_amount'] . "</td>";
                        echo "<td>" . $value['lp_wh_name'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>