<html>
    <table>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th colspan="4" class="text-center">Amount</th>
        </tr>
        <tr>
            <th>S.No.</th>
            <th>HSN</th>
            <th>Description</th>
            <th>UQC</th>
            <th>Total Quantity</th>
            <th>Total Value</th>
            <th>Total Taxable Value</th>
            <th>Integrated Tax</th>
            <th>Central Tax</th>
            <th>State / UT Tax</th>
            <th>Cess</th>
        </tr>
        <?php
        $sno = 1;
        for ($i = 0; $i < count($data); $i++) {
            ?>
            <tr>
                <td>{{ $sno }}</td>
                <td>{{ $data[$i]["hsn_code"] }}</td>
                <td>{{ $data[$i]["hsn_desc"] }}</td>
                <td></td>
                <td>{{ $data[$i]["total_qty"] }}</td>
                <td>{{ $data[$i]["total_value"] }}</td>
                <td>{{ $data[$i]["taxable_value"] }}</td>
                <td></td>
                <td>{{ $data[$i]["cgst"] }}</td>
                <td>{{ $data[$i]["sgst"] }}</td>
                <td></td>
            </tr>
            <?php
            $sno++;
        }
        ?>
    </table>
    <style>
        table {
            border-collapse: collapse;
        }

        table, td, th {
            border: 1px solid black;
        }
        .text-center{
            text-align: center;
        }
    </style>
</html>
