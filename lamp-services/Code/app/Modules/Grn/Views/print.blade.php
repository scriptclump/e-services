<html lang="en">
<head>
<meta charset="UTF-8">
<title>GRN</title>
<link href="{{URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('assets/global/css/custom-ebutor.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
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
            /*.table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td{padding:4px;}
            .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th{padding:5px;}
            .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #fbfcfd !important;
            -webkit-print-color-adjust: exact !important;
            }
            */.printmartop {margin-top: 10px;}
            .container {margin-top: 20px;}

            .small1 {font-size: 73%;}
            .small2 {font-size: 65.5%;}
            .bg {background-color: #efefef;padding: 8px 0px;}
            .bold{font-weight: bold;}


            .table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
            .table-bordered>thead>tr>th{border: 1px solid #000 !important;}
			.table>tbody>tr>th{font-weight:bold !important;}
            .page-break{ display: block !important; clear: both !important; page-break-after:always !important;}

            .table-headings th{background:#c0c0c0 !important; font-weight:bold !important; border:1px solid #000 !important;}
            .newproduct{color: blue;font-weight: bold !important;}
        </style>
</head>
<body>
      <table width="100%" border="0" cellspacing="5" cellpadding="5">
        <tr>
            <td width="65%" align="left" valign="middle">
                @if(isset($leInfo->logo) and !empty($leInfo->logo))
                <img src="{{$leInfo->logo}}" alt="" height="42" width="42" >
                @endif
                <strong> {{$leInfo->business_legal_name}}</strong>
            </td>
            <td>&nbsp;</td>
        </tr>
      </table>
		<h4 align="center">GOODS RECEIVED NOTE</h4>
      <table class="table table-bordered thline printtable " cellpadding="0" cellspacing="0" style=" word-wrap:break-word;font-size:13px;">
        
          <tr style="font-size:16px; text-align:left" class="hedding1 table-headings">
            <th width="20%">Supplier</th>
			<th width="20%">Billing Address</th>
            <th width="20%">Delivery Address</th>
            <th width="20%">GRN Details</th>
          </tr>
        
        <tbody>
          <tr>
            <td valign="top" style="font-size:15px;" width="20%">
            <strong>Name:</strong> {{$grnProductArr[0]->business_legal_name}}<br>
            <strong>Address:</strong> {{$grnProductArr[0]->address1}}, {{$grnProductArr[0]->address2}},
                    {{$grnProductArr[0]->state_name}}, {{$grnProductArr[0]->city}} - {{$grnProductArr[0]->pincode}}<br>
            <strong>State:</strong> {{$grnProductArr[0]->state_name}}<br>                 
			<strong>State Code:</strong> {{ $grnProductArr[0]->state_code }}<br>   
            <strong>Phone:</strong> {{$grnProductArr[0]->legalMobile}}<br>
            <strong>Email:</strong> {{$grnProductArr[0]->legalEmail}}<br>
			<strong>GSTIN / UIN:</strong> {{$grnProductArr[0]->gstin}}<br>
            </td>
			<td valign="top" style="font-size:15px;" width="20%">
            <strong>Name:</strong> {{$billingAddress->business_legal_name}}<br>
            <strong>Address:</strong> {{$billingAddress->address1}}, {{$billingAddress->address2}}<br>
            <strong>State:</strong> {{$billingAddress->state}}<br> 
			<strong>State Code:</strong> {{ $billingAddress->state_code }}<br>                 
            <strong>GSTIN / UIN:</strong>{{ $billingAddress->gstin }} <br>
            </td>
            <td valign="top" style="font-size:15px;" width="20%">
            @if(!empty($whInfo->le_wh_code))
            <strong>Code:</strong> {{$whInfo->le_wh_code}}<br>
            @endif
            <strong>Name:</strong> {{$whInfo->lp_wh_name}}<br>
            <strong>Address:</strong> {{$whInfo->address1}}, {{$whInfo->address2}} 
                  {{$whInfo->city}}, {{$whInfo->state_name}}, {{$whInfo->country_name}}, {{$whInfo->pincode}}<br>
			<strong>State:</strong> {{$whInfo->state_name}} ({{ $whInfo->state_code }})<br>
            <strong>Phone:</strong> {{$whInfo->phone_no}}<br>
            <strong>Email:</strong> {{$whInfo->email}}<br>
            <strong>Contact Person:</strong> {{$whInfo->contact_name}}<br /> 
            <strong>GSTIN / UIN:</strong> {{$whInfo->gstin}}<br />
            </td>
              <td valign="top" style="font-size:15px;" width="20%">
              <strong>GRN No.: </strong> {{$grnProductArr[0]->inward_code}}<br>
              <strong>GRN Date: </strong>{{Utility::dateTimeFormat($grnProductArr[0]->created_at)}}<br>
              <strong>P.O No. / Date:</strong> {{$po_code}} / {{Utility::dateTimeFormat($po_date)}}<br>
              @if(!empty($grnProductArr[0]->invoice_no))
              <strong>Supplier Invoice No. / Date:</strong> {{$grnProductArr[0]->invoice_no}} / {{Utility::dateFormat($grnProductArr[0]->invoice_date)}}<br>
              @endif
              <strong>Supplier Ref.No.:</strong>{{$grnProductArr[0]->le_code}}<br>
              <strong>Created By: </strong>{{$grnProductArr[0]->firstname}} {{$grnProductArr[0]->lastname}}              
            </td>
          </tr>
        </tbody>
      </table>
	
  <?php 
$isGst = 0;
foreach($grnProductArr as $product){
	$taxData = property_exists($product, 'tax_data') ? json_decode($product->tax_data, true) : [];
	$taxTypes = isset($taxData['Tax Type']) ? $taxData['Tax Type'] : '';
	if(!empty($taxData) && $taxTypes == 'GST')
	{
		$isGst = 1;
	}
}
$discount_before_tax = (isset($grnProductArr[0]->discount_before_tax) && $grnProductArr[0]->discount_before_tax == 1)?$grnProductArr[0]->discount_before_tax:0;
?>
  
    
	<span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in Rs </span>	
      <table width="100%" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-advance table-hover" style="word-wrap:break-word;font-size:12px;">
        <thead>
          <tr class="hedding1 table-headings">
            <th rowspan="2" >S&nbsp;No</th>			
            <th rowspan="2" bgcolor="#e7ecf1">Product&nbsp;Title</th>
			<th rowspan="2" bgcolor="#e7ecf1">HSN Code</th>
            <th rowspan="2" bgcolor="#e7ecf1" style="text-align: right;">MRP</th>
            <th rowspan="2" bgcolor="#e7ecf1" style="text-align: right;">Rate</th>
            <th rowspan="2" bgcolor="#e7ecf1" style="text-align: right;">Qty</th>
            @if($discount_before_tax==1)
            <th rowspan="2" bgcolor="#e7ecf1" style="text-align: right;">Base Rate</th>
            <th colspan="2" bgcolor="#e7ecf1" align="center" style="text-align: center;">Discount&nbsp;</th>
            @endif
            <th rowspan="2" bgcolor="#e7ecf1" style="text-align: right;">Taxable&nbsp;Value</th>            
			<th rowspan="2" bgcolor="#e7ecf1" style="text-align: right;">Tax&nbsp;%</th>
			<th rowspan="2" bgcolor="#e7ecf1" align="right">Tax&nbsp;Amt&nbsp;</th>
			@if($isGst)
				<th colspan="2" align="center" style="text-align: center;">CGST</th>
				<th colspan="2" align="center" style="text-align: center;">SGST/UTGST</th>
				<th colspan="2" align="center" style="text-align: center;">IGST</th>
			@endif            
            @if($discount_before_tax==0)
            <th colspan="2" bgcolor="#e7ecf1" align="center" style="text-align: center;">Discount&nbsp;</th>
            @endif
            <th rowspan="2" bgcolor="#e7ecf1" align="right">Total&nbsp;Amt&nbsp;</th>
          </tr>
		  
		  @if($isGst)
		  <tr class="hedding1 table-headings">
				<th colspan="1" rowspan="1">%</th>
				<th colspan="1" rowspan="1">Amt</th>
				<th colspan="1" rowspan="1">%</th>
				<th colspan="1" rowspan="1">Amt</th>
				<th colspan="1" rowspan="1">%</th>
				<th colspan="1" rowspan="1">Amt</th>
				<th colspan="1" rowspan="1">%</th>
				<th colspan="1" rowspan="1">Amt</th>
			</tr>
			@else
				<tr class="hedding1 table-headings">
					<th colspan="1" rowspan="1">%</th>
					<th colspan="1" rowspan="1">Amt</th>
				</tr>
			@endif
        </thead>
        <tbody>
        <?php 
$slno = 1;
$totQty = 0;
$totalBaseValue = 0;
$totalTaxValue = 0;
$totalRowDiscount = 0;
$totalBaseDiscBeforeTax = 0.00;
$row_total = 0;
$totalTax = 0;
$totaldiscount = 0;     
$CGST = 0;
$total_cgst = 0;
$SGST = 0;
$total_sgst = 0;
$IGST = 0;
$total_igst = 0;
$UTGST = 0;     
$total_utgst = 0;
?>
        @foreach($grnProductArr as $product)
        <?php 
        $totalTaxValue = $totalTaxValue + $product->tax_amount;
        $totalRowDiscount = $totalRowDiscount + $product->discount_total;
        $totQty = $totQty + $product->received_qty;
        $row_total = $row_total + $product->row_total;
        $totalBaseDiscBeforeTax += ($product->sub_total+$product->discount_total);
        $taxData = property_exists($product, 'tax_data') ? json_decode($product->tax_data, true) : [];
        $taxData = isset($taxData[0]) ? $taxData[0] : [];
		$taxType = isset($taxData['Tax Type']) ? $taxData['Tax Type'] : '';
		$taxPercentage = isset($taxData['Tax Percentage']) ? $taxData['Tax Percentage'] : 0.00;
        $CGST = isset($taxData['CGST_VALUE']) ? $taxData['CGST_VALUE'] : 0;
        $total_cgst = ($total_cgst + $CGST);
        $CGST_percentage = isset($taxData['CGST']) ? $taxData['CGST'] : 0.00;
		$cgst_tax_percentage = 0.00;
		if($CGST > 0)
		{
			$cgst_tax_percentage = (100 / $CGST_percentage);
			$cgst_tax_percentage = number_format(($taxPercentage / $cgst_tax_percentage), 2);
		}
		$SGST = isset($taxData['SGST_VALUE']) ? $taxData['SGST_VALUE'] : 0.00;
		$total_sgst = $total_sgst + $SGST;
		$SGST_percentage = isset($taxData['SGST']) ? $taxData['SGST'] : 0.00;
		$sgst_tax_percentage = 0.00;
		if($SGST > 0)
		{
			$sgst_tax_percentage = (100 / $SGST_percentage);
			$sgst_tax_percentage = number_format(($taxPercentage / $sgst_tax_percentage), 2);
		}
		
		$IGST = isset($taxData['IGST_VALUE']) ? $taxData['IGST_VALUE'] : 0.00;
		$total_igst = $total_igst + $IGST;
		$IGST_percentage = isset($taxData['IGST']) ? $taxData['IGST'] : 0.00;
		$igst_tax_percentage = 0.00;
		if($IGST > 0)
		{
			$igst_tax_percentage = (100 / $IGST_percentage);
			$igst_tax_percentage = number_format(($taxPercentage / $igst_tax_percentage), 2);
		}
		
		$UTGST = isset($taxData['UTGST_VALUE']) ? $taxData['UTGST_VALUE'] : 0.00;
		$total_utgst = $total_utgst + $UTGST;
		$UTGST_percentage = isset($taxData['UTGST']) ? $taxData['UTGST'] : 0.00;
		$utgst_tax_percentage = 0.00;
		if($UTGST > 0)
		{
			$utgst_tax_percentage = (100 / $UTGST_percentage);
			$utgst_tax_percentage = number_format(($taxPercentage / $utgst_tax_percentage), 2);
		}
        ?>
        
            <tr>
            <td align="right" valign="middle">{{$slno}}</td>			
			<td align="left" valign="middle">{{$product->product_title}}</td>
            <td align="left" valign="middle">{{$product->hsn_code}}</td>            
            <td align="right" valign="middle" align="right">{{number_format($product->mrp, 2)}}</td>
            <td valign="middle" align="right">{{number_format($product->price, 2)}}</td>
            <td valign="middle" align="right"> {{(int)$product->received_qty}}<br>
              <span style="font-size:10px;"> @if($product->quarantine_stock) <strong>Quarantine: {{(int)$product->quarantine_stock}}</strong><br>
              @endif
              <?php /*
@if($product->received_qty) <strong>Received: {{(int)$product->received_qty}}</strong><br>
@endif */?>
              @if($product->free_qty) <strong>Free: {{(int)$product->free_qty}}</strong><br>
              @endif
              @if($product->damage_qty) <strong>Damage: {{(int)$product->damage_qty}}</strong><br>
              @endif
              @if($product->missing_qty) <strong>Short: {{(int)$product->missing_qty}}</strong><br>
              @endif
              @if($product->excess_qty) <strong>Excess: {{(int)$product->excess_qty}}</strong> @endif </span>
			</td>
                        @if($discount_before_tax==1)
                            <td align="right">{{ number_format($product->sub_total+$product->discount_total, 2) }}</td>
                            @if($product->discount_type)
                                <td valign="middle" align="right">{{number_format($product->discount_percentage, 2)}}</td>
                            @else
                                <td valign="middle" align="right">0.00</td>
                            @endif
                            <td valign="middle" align="right">{{number_format($product->discount_total, 2)}}</td>
                        @endif
            <td align="right">{{ number_format(($product->sub_total), 2) }}</td>			
			<td valign="middle">
				@if($taxType != '')
					{{$taxType .' @ '. number_format($product->tax_per, 2)}}
				@else
					{{number_format($product->tax_per, 2)}}
				@endif			
			</td>
			<td valign="middle" align="right">{{number_format($product->tax_amount, 2)}}</td>
			@if($isGst)
				<td valign="middle" align="right">{{number_format($cgst_tax_percentage, 2)}}</td>
				<td>{{number_format($CGST, 2)}}</td>
                @if($SGST !=0)
				<td valign="middle" align="right">{{number_format($sgst_tax_percentage, 2)}}</td>
				<td>{{number_format($SGST, 2)}}</td>
                @elseif($UTGST !=0)
                <td class="right" valign="middle">{{number_format($utgst_tax_percentage, 2)}}</td>
                <td>{{number_format($UTGST, 2)}}</td>
                @else
                <td align="right" valign="middle">0</td>
                <td align="right" valign="middle">0</td>
                @endif
				<td valign="middle" align="right">{{number_format($igst_tax_percentage, 2)}}</td>
				<td>{{number_format($IGST, 2)}}</td>
			@endif
                        @if($discount_before_tax==0)
			@if($product->discount_type)
				<td valign="middle" align="right">{{number_format($product->discount_percentage, 2)}}</td>
			@else
				<td valign="middle" align="right">0.00</td>
			@endif
            <td valign="middle" align="right">{{number_format($product->discount_total, 2)}}
                @if($product->discount_inc_tax)
                <br /><span style="font-size: 11px;">(INC TAX)</span>
                @else
                <br /><span style="font-size: 11px;">(EXC TAX)</span>
                @endif
            </td>
                        @endif
            <td valign="middle" align="right">{{ number_format(($product->row_total), 2) }}</td>
          </tr>
		  <?php $slno++; ?>
        @endforeach

        <tr>
            <td class="bold" align="left" valign="middle"></td>
            <td align="left" valign="middle"></td>
            <td valign="middle"></td>
			<td valign="middle"></td>
            <td class="bold" valign="middle"><strong>Total</strong></td>
            <td class="bold" valign="middle"><strong>{{$totQty}}</strong></td>
            @if($discount_before_tax==1)
                <td class="bold" valign="middle" align="right"><strong>{{number_format($totalBaseDiscBeforeTax,2)}}</strong></td>
                <td valign="middle"></td>
                <td class="bold" valign="middle" align="right"><strong>{{number_format($totalRowDiscount, 2)}}</strong></td>
            @endif
            <td class="bold" valign="middle" align="right"><strong>{{number_format($grnProductArr[0]->base_total, 2)}}</strong></td>
            	<td valign="middle"></td>
				<td class="bold" valign="middle" align="right"><strong> {{number_format($totalTaxValue, 2)}}</strong></td>
			@if($isGst)
				<td valign="middle"></td>
				<td class="bold" valign="middle" align="right"><strong> {{number_format($total_cgst, 2)}}</strong></td>	
                 @if($total_sgst!=0)
				<td valign="middle"></td>
				<td class="bold" valign="middle" align="right"><strong> {{number_format($total_sgst, 2)}}</strong></td>
                @elseif($total_utgst!=0)
                <td align="middle"></td>
                <td align="middle" valign="middle" align="right">{{number_format($total_utgst,2)}}</td>
                 @else
                <td align="middle">0</td>
                <td align="middle" valign="middle" align="right">0</td>
                @endif
				<td valign="middle"></td>
				<td class="bold" valign="middle" align="right"><strong> {{number_format($total_igst, 2)}}</strong></td>
			@endif
                        @if($discount_before_tax==0)
			<td valign="middle"></td>
            <td class="bold" valign="middle" align="right"><strong>{{number_format($totalRowDiscount, 2)}}</strong></td>
            @endif
            <td class="bold" valign="middle" align="right"><strong> {{number_format($row_total, 2)}}</strong></td>
          </tr>
        </tbody>
        
      </table>
       
       <table width="100%" class="table table-striped table-bordered table-advance table-hover">
          <thead>
            <tr style="font-size:14px; background-color:#c0c0c0 !important;" class="hedding1 table-headings">
				<th align="right" style="text-align:right !important;">Total Items</th>
				<th align="right" style="text-align:right !important;">Total</th>
				<th align="right" style="text-align:right !important;">Shipping Amt</th>
				<th align="right" style="text-align:right !important;">SAC Code</th>
				<th align="right" style="text-align:right !important;">Service Tax%</th>
				<th align="right" style="text-align:right !important;">Service Charge Amt</th>
				<th align="right" style="text-align:right !important;">Bill Discount</th>				
				<th align="right" style="text-align:right !important;">Grand Total</th>
            </tr>
        </thead>
          <tbody>
            <tr class="odd gradeX bold" align="right" style="font-size:14px;">
				<td><strong>{{--$slno}}</strong></td>
				<td><strong>{{number_format($grnProductArr[0]->base_total, 2)}}</strong></td>
				<td><strong>{{number_format($grnProductArr[0]->shipping_fee, 2)}}</strong></td>
				<td>&nbsp;</td>
				<td><strong>0.00</strong></td>
				<td><strong>0.00</strong></td>
                <td><strong>
                    @if(!$grnProductArr[0]->discount_on_bill_options)
                        @if($grnProductArr[0]->on_bill_discount_type)
                            @if(isset($grnProductArr[0]->discount_on_total))
                                {{ number_format($grnProductArr[0]->discount_on_total, 2) }}
                                ({{ number_format($grnProductArr[0]->on_bill_discount_value, 2).' %' }})
                            @else
                               0.00
                            @endif    
                        @else
                            {{(isset($grnProductArr[0]->discount_on_total)) ? number_format($grnProductArr[0]->discount_on_total,2) : 0.00}}
                        @endif
                    @else
                         0.00
                    @endif
                </strong></td>                
				<td><strong>{{number_format($grnProductArr[0]->grand_total, 2)}}</strong></td>
            </tr>
          </tbody>
        </table>
		<table width="100%" border="0" cellspacing="5" cellpadding="0">
                <tr>
                    <td align="left" valign="top">
                        <table cellpadding="2" cellspacing="2" class="table" width="100%" style="border:1px solid #fff;">
                            <tr>
                                <td style="word-wrap:break-word;font-size:12px;width:100%;font-weight:bold;border-top:1px solid #fff;border-bottom:1px solid #fff;">Grand Total In Words: <?php echo Utility::convertNumberToWords(round($grnProductArr[0]->grand_total,2)); ?></td>
                            </tr>
                            <tr>
                                <td style="word-wrap:break-word;font-size:11px;width:100%;">* Reverse Charges not Applicable</td>
                            </tr>
                        </table>
                    </td>
                    <td align="left" valign="top">
                                                
                    </td>
                </tr>
            </table>

</body>
</html>
