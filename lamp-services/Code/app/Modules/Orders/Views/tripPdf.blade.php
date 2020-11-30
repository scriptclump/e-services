<style>
    body {
        margin: 0px;
        padding: 0px;
        color: #333;
        font-family: "Open Sans", sans-serif;
    }
    .page-break{ display: block !important; clear: both !important; page-break-after:always !important;}
</style>
@if(is_object($leInfo))
<table width="100%" border="0" cellspacing="5" cellpadding="5">
    <tr>
        <td width="50%" align="left" valign="middle">
            @if(is_object($leInfo) and $leInfo->logo != "" and $leInfo->logo != "null")
                <img src="{{$leInfo->logo}}" alt="Image" height="42" width="42" >
            @endif
            <strong>{{$leInfo->business_legal_name}}</strong></td>
        <td width="50%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:12px; float:right;">{{$leInfo->address1}}, @if(!empty($leInfo->address2)){{$leInfo->address2}},<br>@endif
                {{$leInfo->city}}, {{$legalEntity->state_name}}, {{empty($leInfo->country_name) ? 'India' : $leInfo->country_name}}, {{$leInfo->pincode}}<br>TIN No : {{$leInfo->tin_number}}</td>
    </tr>
</table>
@endif
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>Trip sheet of {{$toAddress->lp_wh_name}}</h4></td>
    </tr>
</table>
<table width="100%" style="border:1px solid #ccc;font-size:10px !important;" cellspacing="0" cellpadding="2">
    <tr style="font-size:14px;background-color:#e7ecf1 !important; font-weight:bold;">
        <td width="33%" bgcolor="#e7ecf1">Trip Details</td>
        <td height="30" width="33%" bgcolor="#e7ecf1">From WH Address</td>
        <td width="33%" bgcolor="#e7ecf1">To WH Address</td>
    </tr>
    <tr>
        <td valign="top" style="font-size:11px;">
            <strong>Trip Date: </strong><?php echo date('d/m/Y H:i:s'); ?><br/>
            <strong>Docket No: </strong>{{ $Reportinfo[0]['st_docket_no'] }}<br/>
            <strong>Hub Name: </strong>{{ $Reportinfo[0]['lp_wh_name'] }}<br/>
            <strong>Vehicle No: </strong>{{ $Reportinfo[0]['st_vehicle_no'] }}<br/>
            <strong>Driver Name: </strong>{{ $Reportinfo[0]['st_driver_name'] }} @if(!empty($Reportinfo[0]['st_driver_mobile'])) (M: {{ $Reportinfo[0]['st_driver_mobile'] }})@endif
        </td>
        <td valign="top" style="border-right:1px solid #ccc; font-size:11px;">
            @if(is_object($fromAddress))   
            <strong>Name: </strong>{{$fromAddress->lp_wh_name}},<br/>
            <strong>Address: </strong>{{$fromAddress->address1}} {{$fromAddress->address2}},<br>
            @if(!empty($fromAddress->locality)) {{$fromAddress->locality}}, @endif @if(!empty($fromAddress->landmark)){{$fromAddress->landmark}}, @endif {{$fromAddress->city}}, {{$fromAddress->state_name}}, {{$fromAddress->country_name}}, {{$fromAddress->pincode}}<br>
            <strong>Contact Person: </strong>{{$fromAddress->contact_name}} (M: {{$fromAddress->phone_no}})
            @endif
        </td>
        <td valign="top" style="border-right:1px solid #ccc; font-size:11px;">
            @if(is_object($toAddress))
            <strong>Name: </strong>{{$toAddress->lp_wh_name}},<br/>
            <strong>Address: </strong>{{$toAddress->address1}} {{$toAddress->address2}},<br>
            @if(!empty($toAddress->locality)) {{$toAddress->locality}}, @endif @if(!empty($toAddress->landmark)){{$toAddress->landmark}}, @endif {{$toAddress->city}}, {{$toAddress->state_name}}, {{$toAddress->country_name}}, {{$toAddress->pincode}}<br>
            <strong>Contact Person: </strong>{{$toAddress->contact_name}} (M: {{$toAddress->phone_no}})
            @endif
        </td>
    </tr>
</table>
<strong style="font-size:13px !important; ">Product Description</strong>
<table cellspacing="0" cellpadding="3" width="100%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
    <thead>
    <tr style="font-weight:bold;">
        <td bgcolor="#e7ecf1" height="30">SR. No.</td>
        <td bgcolor="#e7ecf1">Container Id</td>
        <td bgcolor="#e7ecf1">Order No</td>
        <td bgcolor="#e7ecf1">Invoice No</td>
        <td bgcolor="#e7ecf1">Area/Beats</td>
        <td bgcolor="#e7ecf1">Container Type</td>
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
    <tr>
        <td style="border-bottom:1px solid #ccc;" height="30">{{ $srno}}</td>
        <td style="border-bottom:1px solid #ccc;">{{ $Reportinfo[$i]['crates_id'] }}</td>
        <td style="border-bottom:1px solid #ccc;">{{ $Reportinfo[$i]['order_code'] }}</td>
        <td style="border-bottom:1px solid #ccc;">{{ $Reportinfo[$i]['invoice_code'] }}</td>
        <td style="border-bottom:1px solid #ccc;">{{ $Reportinfo[$i]['beat_area'] }}</td>
        <td style="border-bottom:1px solid #ccc;">{{ $Reportinfo[$i]['container_value'] }}</td>
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
<table cellspacing="0" cellpadding="3" width="100%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
    <tr>
        <th bgcolor="#e7ecf1">Crate Count</th>
        <th bgcolor="#e7ecf1">Bag Count</th>
        <th bgcolor="#e7ecf1">CFC Count</th>
        <th bgcolor="#e7ecf1">Total Count</th>
    </tr>
    <tr style="font-weight:bold;">
        <td align="right">{{ $totCrates }}</td>
        <td align="right">{{ $containerCount["bags_cnt"] }}</td>
        <td align="right">{{ $containerCount["cfc_cnt"] }}</td>
        <td align="right">{{ $totCrates + $containerCount["bags_cnt"] + $containerCount["cfc_cnt"] }}</td>
    </tr>
</table>