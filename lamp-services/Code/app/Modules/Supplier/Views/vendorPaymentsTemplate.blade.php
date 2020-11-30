<html>
    <head></head>
    <body>
        <table border-width = "5">
            <table>
                <tr></tr>
                <tr>
                    <td colspan="1"><strong>Supplier Name</strong></td>
                    <td align="left" colspan="1"><strong>{{$exceldata_first['supplier_name']}}</strong></td>
                </tr>
                <tr>
                    <td colspan="1"><strong>Period :</strong></td>
                    <td align="left" colspan="1">From {{$exceldata_first['start_date']}}</td>
                    <td align="left" colspan="1">To {{$exceldata_first['end_date']}}</td>
                </tr>
                <tr></tr>
                <tr rowspan="1" style="background-color: #555555;">
                    <th>S No</th>
                    <th>Date</th>
                    <th colspan="1">Name</th>
                    <th colspan="1">Voucher Type</th>
                    <th colspan="1">Supplier Code</th>
                    <th colspan="1">PO Code</th>
                    <th colspan="1">GRN Code</th>
                    <th colspan="1">Payment Code</th>
                    <th colspan="1">Refrence Code</th>
                    <th colspan="1">Debit</th>
                    <th colspan="1">Credit</th>
                </tr>
                <?php $sno = 1;
                $debit = 0; 
                $credit= 0;
                ?>
                @if(!empty($details))
                    @foreach($details as $det)
                        <tr rowspan="1" style="border: 3px;">
                            <td colspan="1">{{$sno}}</td>
                            <td colspan="1">{{$det['ledger_date']}}</td>
                            @if($det['voucher_type'] == 'Payment')
                                <td colspan="1">{{$det['ledger_account']}}</td>
                            @else
                                <td colspan="1">{{$exceldata_first['supplier_name']}}</td>
                            @endif
                            <td colspan="1">{{$det['voucher_type']}}</td>
                            <td colspan="1">{{$exceldata_first['supplier_code']}}</td>
                            <td colspan="1">{{$det['po_code']}}</td>
                            <td colspan="1">{{$det['inward_code']}}</td>
                            <td colspan="1">{{$det['pay_code']}}</td>
                            <td colspan="1">{{$det['ref_code']}}</td>
                            <td colspan="1">{{$det['debit']}}</td>
                            <td colspan="1">{{$det['credit']}}</td>
                            <?php $sno++; 
                            $debit += $det['debit'];
                            $credit += $det['credit'];
                        ?>
                        </tr>
                   @endforeach
                @endif
                <tr></tr>
                <?php
                       echo $closing_balance = $opening_balance + $credit - $debit;
                ?>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <th colspan="1">Opening Balance</th>
                        @if($opening_balance < 0)
                            <?php $opening_balance = abs($opening_balance); ?>
                            <td colspan="1">{{$opening_balance}}</td>
                        @else
                            <td colspan="1"></td>
                            <td colspan="1">{{$opening_balance}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                  l     <td colspan="1"></td>
                        <th colspan="1">Current Total</th>
                        <td colspan="1">{{$debit}}</td>
                        <td colspan="1">{{$credit}}</td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <th colspan="1">Closing Balance</th>
                        @if($closing_balance < 0)
                            <?php $closing_balance = abs($closing_balance); ?>
                            <td colspan="1">{{$closing_balance}}</td>
                        @else
                            <td colspan="1"></td>
                            <td colspan="1">{{$closing_balance}}</td>
                        @endif
                    </tr>
                </table>
            </table>
    </body>
</html>