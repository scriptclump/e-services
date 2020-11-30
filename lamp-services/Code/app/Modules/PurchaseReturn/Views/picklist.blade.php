<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Picklist</title>
        <style type="text/css">
            .container{width:1000px; margin:0 auto;}
            @media print {
                body {-webkit-print-color-adjust: exact;}
                thead {display: table-header-group;}
            }
            td{ font-size: 14px;}
            .bborder{border-bottom:1px solid #000;border-right:1px solid #000;}
            /* onload="window.print();"*/
        </style>
    </head>
    <?php
    use App\Modules\PurchaseReturn\Models\PurchaseReturn;
    $this->_prModel = new PurchaseReturn();
    ?>
    <body onload="window.print();">
        <div class="container">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="50%" align="left" valign="top">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="left" valign="middle">
                                    @if(isset($leDetail->logo) and !empty($leDetail->logo))
                                    <img src="{{$leDetail->logo}}" alt="" height="42" width="42" style="float:left;">
                                    @endif
                                    <h2 style="float:left; padding-left:10px; padding-top:6px; font-size:18px; font-family:arial, sans-serif;">{{$leDetail->business_legal_name}}</h2>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" valign="middle">
                                    <span style="font-size:12px; font-family:arial, sans-serif;"><strong>
                                            {{$leDetail->address1}}, @if(!empty($leDetail->address2)){{$leDetail->address2}},<br>@endif
                                            {{$leDetail->city}}, {{$leDetail->state_name}}, {{isset($leDetail->country_name) ? $leDetail->country_name : 'India'}}, {{$leDetail->pincode}}</strong></span></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" align="left" valign="top" style="float:right;">
                        <table width="100%" align="right" cellpadding="0" cellspacing="7" style="float:right; text-align:left; font-family:arial, sans-serif;font-size:12px; font-weight:normal;margin-top:12px">
                            <tr>
                                <th>Date</th>
                                <th>:</th>
                                <td>{{Utility::dateFormat(date('d-m-Y'))}}</td>
                            </tr>
                            <tr>
                                <th>DC</th>
                                <th>:</th>
                                <td>{{$prDetails[0]->lp_wh_name}}</td>
                            </tr>
                            <tr>
                                <th>Picked By</th>
                                <th>:</th>
                                <td>{{$prDetails[0]->picker_name}}</td>
                            </tr>
                            <tr>
                                <th>Checked By</th>
                                <th>:</th>
                                <td></td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>
            <h1 style="border-bottom:0px solid #323334; font-family: arial, sans-serif; text-align:center;padding-bottom:15px; font-size:16px;">Purchase Returns Picklist</h1>
            <table border="0" width="100%" cellpadding="2" cellspacing="0" style="font-family: arial, sans-serif; font-size:12px; text-align:left;">
                <thead>
                    <tr style="font-size:12px; font-weight:silver;">
                        <th width="3%" class="bborder" style="border-top:1px solid #000;border-left:1px solid #000;">S No</th>

                        <th width="10%" class="bborder" style="border-top:1px solid #000;">SKU</th>
                        <th width="35%" class="bborder" style="border-top:1px solid #000;">Product Description</th>

                        <th width="6%" class="bborder" style="border-top:1px solid #000;" align="right">MRP</th>
                        <th width="5%"colspan="3" class="bborder" style="border-top:1px solid #000; font-size:12px;">Qty (EA)</th>
                        <th width="5%" class="bborder" style="border-top:1px solid #000;">Pick QTY</th>
                        <th width="15%" class="bborder" style="border-top:1px solid #000;">Remarks</th>
                    </tr>
                </thead>
                @foreach($prDetails as $return)
                <tr style="background-color:#cccccc">
                    <td class="bborder" style="border-left:1px solid #000;"></td>
                    <td class="bborder" colspan="1"><strong>ID:</strong> {{isset($return->pr_code) ? $return->pr_code : $return->pr_id}}</td>
                    <td class="bborder" colspan="2"><strong>Supplier Name:</strong> {{$return->business_legal_name}}, <strong>M:</strong>{{(isset($return->mobile_no) ? $return->mobile_no : '')}} </td>
                    <td class="bborder" colspan="1">SOH</td>
                    <td class="bborder" colspan="1">DIT</td>
                    <td class="bborder" colspan="1">DND</td>
                    <td class="bborder" colspan="2"><?php /* <strong>Sch. Date :</strong> {{date('d-m-Y',strtotime($orderProduct->picked_at))}} */ ?></td>
                </tr>
                <?php
                $sno = 1;
                $pr_Products = $this->_prModel->getPrProducts($return->pr_id);
                ?>
                @foreach($pr_Products as $prProduct)
                <tr>
                    <td class="bborder" style="border-left:1px solid #000;">{{$sno++}}&nbsp;</td>
                    <td class="bborder">{{$prProduct->sku}}</td>
                    <td class="bborder">{{$prProduct->product_name}}</td>
                    <td class="bborder" align="right"><strong>{{number_format($prProduct->mrp, 1)}}</strong></td>
                    <td class="bborder" align="center">{{$prProduct->qty}}
                    </td>
                    <td class="bborder" align="center">{{$prProduct->dit_qty}}
                    </td>
                    <td class="bborder" align="center">{{$prProduct->dnd_qty}}
                    </td>
                    <td class="bborder"></td>
                    <td class="bborder">{{$prProduct->pr_remarks}}</td>
                </tr>
                @endforeach
                @endforeach
            </table>
        </div>
    </body>
</html>
