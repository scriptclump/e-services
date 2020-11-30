<html dir="ltr" lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Trip</title>
        <style>
            @media print {body {-webkit-print-color-adjust: exact;}}
            body {
                margin: 0px;
                padding: 0px;
                color: #333;
                font-family: "Open Sans", sans-serif !important;
                -webkit-print-color-adjust: exact;
            }
            table {
                border-collapse: collapse;
            }
            .hedding1{
                background-color: #c0c0c0 !important;
                color: #000 !important;   
                -webkit-print-color-adjust: exact !important;
            }
            .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td{padding:4px;}
            .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{padding:5px;}
            .table-striped>tbody>tr:nth-of-type(odd) {
                background-color: #fbfcfd !important;
                -webkit-print-color-adjust: exact !important;
            }
            .printmartop {margin-top: 10px;}
            .container {margin-top: 20px;}
            .small1 {font-size: 73%;}
            .small2 {font-size: 65.5%;}
            .bg {background-color: #efefef;padding: 8px 0px;}
            .bold{font-weight: bold;}
            .table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
            .table-bordered>thead>tr>th{border: 1px solid #000 !important;}
            .page-break{ display: block !important; clear: both !important; page-break-after:always !important;}
            .table-headings th{background:#c0c0c0 !important; font-weight:bold !important; border:1px solid #000 !important;}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <table width="100%" border="0" cellspacing="5" cellpadding="5">
                        <tr>
                            <td width="40%" align="right" valign="top">
                                <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><img src="/img/ebutor.png" alt="" height="42" width="42" ></td>
                                        <td><strong style="padding-top:-20px;">@if(is_object($leInfo)){{$leInfo->business_legal_name}}@endif</strong></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" align="right" valign="middle">
                                <div style="padding-left:20px; padding-top:10px; font-size:10px; float:right;">
                                    @if(is_object($leInfo))
                                        {{$leInfo->address1}} {{(empty($leInfo->address2) ? '' : ','.$leInfo->address2.',')}}<br>
                                        {{$leInfo->city}}, {{$legalEntity->state_name}}, {{(empty($leInfo->country_name) ? 'India' : $leInfo->country_name)}}, {{$leInfo->pincode}}
                                        </br>TIN No : {{$leInfo->tin_number}}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row" style="margin:5px 0px;">
                <div class="col-md-12 text-center">
                    <h4 style="text-align:center">Trip sheet of {{$toAddress->lp_wh_name}}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table width="100%" class="table table-bordered thline printtable " cellpadding="1">
                        <tr style="font-size:12px; text-align:left" class="hedding1 table-headings">
                            <th width="33%" >Trip Details</th>
                            <th width="33%" >From WH Address</th>
                            <th width="33%" >To WH Address</th>
                        </tr>
                        <tr>
                            <td valign="top" style="font-size:11px;">
                                <strong>Trip Date: </strong><?php echo date('d/m/Y H:i:s'); ?><br/>
                                <strong>Docket No: </strong>{{ $Reportinfo[0]['st_docket_no'] }}<br/>
                                <strong>Hub Name: </strong>{{ $Reportinfo[0]['lp_wh_name'] }}<br/>
                                <strong>Vehicle No: </strong>{{ $Reportinfo[0]['st_vehicle_no'] }}<br/>
                                <strong>Driver Name: </strong>{{ $Reportinfo[0]['st_driver_name'] }} @if(!empty($Reportinfo[0]['st_driver_mobile'])) (M: {{ $Reportinfo[0]['st_driver_mobile'] }})@endif
                            </td>
                            <td valign="top" style="font-size:11px;">
                                @if(is_object($fromAddress))   
                                <strong>Name: </strong>{{$fromAddress->lp_wh_name}},<br/>
                                <strong>Address: </strong>{{$fromAddress->address1}} {{$fromAddress->address2}},<br>
                                @if(!empty($fromAddress->locality)) {{$fromAddress->locality}}, @endif @if(!empty($fromAddress->landmark)){{$fromAddress->landmark}}, @endif {{$fromAddress->city}}, {{$fromAddress->state_name}}, {{$fromAddress->country_name}}, {{$fromAddress->pincode}}<br>
                                <strong>Contact Person: </strong>{{$fromAddress->contact_name}} (M: {{$fromAddress->phone_no}})
                                @endif                  
                            </td>
                            <td valign="top" style="font-size:11px;">
                                @if(is_object($toAddress))
                                <strong>Name: </strong>{{$toAddress->lp_wh_name}},<br/>
                                <strong>Address: </strong>{{$toAddress->address1}} {{$toAddress->address2}},<br>
                                @if(!empty($toAddress->locality)) {{$toAddress->locality}}, @endif @if(!empty($toAddress->landmark)){{$toAddress->landmark}}, @endif {{$toAddress->city}}, {{$toAddress->state_name}}, {{$toAddress->country_name}}, {{$toAddress->pincode}}<br>
                                <strong>Contact Person: </strong>{{$toAddress->contact_name}} (M: {{$toAddress->phone_no}})
                                @endif 
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table width="100%" class="table table-bordered thline printtable " cellpadding="1" style="margin-top:30px;">
                        <thead>
                        <tr class="hedding1 table-headings" style="font-size:11px; font-weight: bold;">   
                            <td>SR. No.</td>
                            <td>Container Id</td>
                            <td>Order No</td>
                            <td>Invoice No</td>
                            <td>Area/Beats</td>
                            <td>Container Type</td>
                        </tr>
                    </thead>
                        <?php
                            $totCartons = 0;
                            $totCrates = 0;
                            $totBags = 0;
                            $srno = 1;
                            $containerCount = $Reportinfo[count($Reportinfo)-1];
                            for ($i = 0; $i < count($Reportinfo) - 1; $i++) {
                        ?>
                        <tr style="font-size:12px;">
                            <td align="center">{{ $srno}}</td>
                            <td>{{ $Reportinfo[$i]['crates_id'] }}</td>
                            <td>{{ $Reportinfo[$i]['order_code'] }}</td>
                            <td>{{ $Reportinfo[$i]['invoice_code'] }}</td>
                            <td>{{ $Reportinfo[$i]['beat_area'] }}</td>
                            <td>{{ $Reportinfo[$i]['container_value'] }}</td>
                        </tr>
                        <?php
                                if($Reportinfo[$i]['container_type'] == 16004){ //CFC
                                    $totCartons = $totCartons + 1;
                                } else if($Reportinfo[$i]['container_type'] == 16007){ //Crate
                                    $totCrates = $totCrates + 1;
                                } else if($Reportinfo[$i]['container_type'] == 16006){ //Bag
                                    $totBags = $totBags + 1;
                                }
                                $srno++;
                            }
                        ?>
                    </table>
                    <table width="100%" class="table table-bordered thline printtable " cellpadding="1" style="margin-top:30px;">
                        <tr class="hedding1 table-headings" style="font-size:11px; font-weight: bold;">
                            <td>Crate Count</td>
                            <td>Bag Count</td>
                            <td>CFC Count</td>
                            <td>Total Count</td>
                        </tr>
                        <tr style="font-size:12px;">
                            <td align="right">{{ $totCrates }}</td>
                            <td align="right">{{ $containerCount["bags_cnt"] }}</td>
                            <td align="right">{{ $containerCount["cfc_cnt"] }}</td>
                            <td align="right">{{ $containerCount["cfc_cnt"] + $totCrates + $containerCount["bags_cnt"] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="page-break"></div>
        </div>
    </body>
</html>
