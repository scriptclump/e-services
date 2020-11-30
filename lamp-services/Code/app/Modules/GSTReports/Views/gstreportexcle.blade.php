<html>
    <table>
        <tr>
            <th></th>
            <th></th>
            <th colspan="3" class="text-center">Invoice Details</th>
            <th rowspan="2">Rate</th>
            <th rowspan="2">Taxable Value</th>
            <th colspan="4" class="text-center">Amount</th>
            <th></th>
        </tr>
        <tr>
            <th>S.No.</th>
            <th>GSTIN / UIN</th>
            <th>No.</th>
            <th>Date</th>
            <th>Value</th>
            <th></th>
            <th></th>
            <th>Integrated Tax</th>
            <th>Central Tax</th>
            <th>State / UT Tax</th>
            <th>Cess</th>
            <th>Place of Supply (Name of state)</th>
        </tr>
        <?php
        $sno = 1;
        for ($i = 0; $i < count($data); $i++) {
            ?>
            <tr>
                <td>{{ $sno }}</td>
                <td></td>
                <td>{{ $data[$i]["invoice_code"] }}</td>
                <td>{{ $data[$i]["invoice_date"] }}</td>
                <td>{{ $data[$i]["invoice_value"] }}</td>
                <td>{{ $data[$i]["rate"] }}</td>
                <td>{{ $data[$i]["taxable_value"] }}</td>
                <td></td>
                <td>{{ $data[$i]["cgst"] }}</td>
                <td>{{ $data[$i]["sgst"] }}</td>
                <td></td>
                <td>{{ $data[$i]["state_name"] }}</td>
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
