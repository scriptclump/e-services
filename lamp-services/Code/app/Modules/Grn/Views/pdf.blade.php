<style>
    body {
        margin: 0px;
        padding: 0px;
        color: #333;
        font-family: "Open Sans",sans-serif !important;
    }
    table{ border-collapse: collapse;}
    .thh{ background: #efefef;}
</style>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="50%" align="left" valign="middle">
      @if(isset($leInfo->logo) and !empty($leInfo->logo))
      <img src="{{$leInfo->logo}}" alt="" height="42" width="42" style="float:left" >
      @endif
      <strong style="float:left; line-height:42px;"> {{$leInfo->business_legal_name}}</strong></td>
  <td width="50%">&nbsp;</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>GOODS RECEIVED NOTE</h4></td>
    </tr>
</table>


<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#000" style=" font-size:12px !important;">
        
          <tr bgcolor="#efefef">
            <th width="25%">Supplier</th>
			<th width="25%">Billing Address</th>
            <th width="25%">Delivery Address</th>
            <th width="25%">GRN Details</th>
          </tr>
        
        <tbody>
          <tr>
            <td valign="top" style="font-size:15px;" width="25%">
            <strong>Name:</strong> {{$grnProductArr[0]->business_legal_name}}<br>
            <strong>Address:</strong> {{$grnProductArr[0]->address1}}, {{$grnProductArr[0]->address2}},
                    {{$grnProductArr[0]->state_name}}, {{$grnProductArr[0]->city}} - {{$grnProductArr[0]->pincode}}<br>
            <strong>State:</strong> {{$grnProductArr[0]->state_name}}<br>                 
			<strong>State Code:</strong> {{ $grnProductArr[0]->state_code }}<br>   
            <strong>Phone:</strong> {{$grnProductArr[0]->legalMobile}}<br>
            <strong>Email:</strong> {{$grnProductArr[0]->legalEmail}}<br>
			<strong>GSTIN / UIN:</strong> {{$grnProductArr[0]->gstin}}<br>
            </td>
			<td valign="top" style="font-size:15px;" width="25%">
            <strong>Name:</strong> {{$billingAddress->business_legal_name}}<br>
            <strong>Address:</strong> {{$billingAddress->address1}}, {{$billingAddress->address2}}<br>
            <strong>State:</strong> {{$billingAddress->state}}<br> 
			<strong>State Code:</strong> {{ $billingAddress->state_code }}<br>                 
            <strong>GSTIN / UIN:</strong> {{ $billingAddress->gstin }}<br>
            </td>
            <td valign="top" style="font-size:15px;" width="25%">
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
              <td valign="top" style="font-size:15px;" width="25%">
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
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="right"<span style="float:right;font-size: 10px;">All Amounts in (Rs) </span></td>
    </tr>
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


<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-family:Arial, Helvetica, sans-serif; font-size:10px;">
  <tbody><tr bgcolor="#efefef">
    <td rowspan="2" align="left" valign="middle">S No</td>
    <td rowspan="2" align="left" valign="middle">Product Name</td>
    <td rowspan="2" align="left" valign="middle">HSN Code</td>
    <td rowspan="2" align="left" valign="middle">MRP</td>
    <td rowspan="2" align="left" valign="middle">Rate</td>
    <td rowspan="2" align="left" valign="middle">Qty</td>
    @if($discount_before_tax==1)
    <td rowspan="2" align="left" valign="middle">Base Rate</td>
    <td colspan="2" align="center" valign="middle">Discount</td>
    @endif
    <td rowspan="2" align="left" valign="middle">Taxable Value</td>
    <td rowspan="2" align="left" valign="middle">Tax %</td>
    <td rowspan="2" align="left" valign="middle">Tax Amt</td>
	@if($isGst)
    <td colspan="2" align="center" valign="middle">CGST</td>
    <td colspan="2" align="center" valign="middle">SGST/UTGST</td>
    <td colspan="2" align="center" valign="middle">IGST</td>
	@endif
    @if($discount_before_tax==0)
    <td colspan="2" align="center" valign="middle">Discount</td>
    @endif
    <td rowspan="2" align="left" valign="middle">Total Amt</td>
  </tr>
  @if($isGst)
  <tr bgcolor="#efefef">
    <td align="right" valign="middle">%</td>
    <td align="right" valign="middle">Amt</td>
    <td align="right" valign="middle">%</td>
    <td align="right" valign="middle">Amt</td>
    <td align="right" valign="middle">%</td>
    <td align="right" valign="middle">Amt</td>
    <td align="right" valign="middle">%</td>
    <td align="right" valign="middle">Amt</td>
  </tr>
  @else
	  <tr bgcolor="#efefef">
    <td align="right" valign="middle">%</td>
    <td align="right" valign="middle">Amt</td>
  </tr>
  @endif
  <?php 
$slno = 1;
$totQty = 0;
$totalBaseValue = 0;
$totalTaxValue = 0;
$totalRowDiscount = 0;
$row_total = 0;
$totalBaseDiscBeforeTax = 0.00;
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
    <td align="left" valign="middle">{{$slno}}</td>
    <td align="left" valign="middle">{{$product->product_title}}</td>
    <td align="left" valign="middle">{{$product->hsn_code}}</td>
    <td align="right" valign="middle">{{number_format($product->mrp, 2)}}</td>
    <td align="right" valign="middle">{{number_format($product->price, 2)}}</td>
    <td align="right" valign="middle">{{(int)$product->received_qty}}
		<br>
		  <span style="font-size:10px;">
			@if($product->quarantine_stock) <strong>Quarantine: {{(int)$product->quarantine_stock}}</strong><br>
		  @endif            
		  @if($product->free_qty) <strong>Free: {{(int)$product->free_qty}}</strong><br>
		  @endif
		  @if($product->damage_qty) <strong>Damage: {{(int)$product->damage_qty}}</strong><br>
		  @endif
		  @if($product->missing_qty) <strong>Short: {{(int)$product->missing_qty}}</strong><br>
		  @endif
		  @if($product->excess_qty) <strong>Excess: {{(int)$product->excess_qty}}</strong> 
		  @endif 
		  </span>
	</td>
        @if($discount_before_tax==1)
        <td align="right" valign="middle">{{number_format($product->sub_total+$product->discount_total, 2)}}</td>
        <td align="right" valign="middle">
            @if($product->discount_type)
                    {{ number_format($product->discount_percentage, 2) }}
            @else
                    0.00
            @endif	
        </td>
        <td align="right" valign="middle">{{number_format($product->discount_total, 2)}}</td>
        @endif
    <td align="right" valign="middle">{{number_format($product->sub_total, 2)}}</td>
    <td align="center" valign="middle">
		@if($taxType != '')
			{{ $taxType .' @ '. (float)$product->tax_per}}
		@else
			{{(float)$product->tax_per}}
		@endif
	</td>
    <td align="right" valign="middle">{{number_format($product->tax_amount, 2)}}</td>
    @if($isGst)
		<td align="right" valign="middle">{{number_format($cgst_tax_percentage, 2)}}</td>
		<td align="right" valign="middle">{{number_format($CGST, 2)}}</td>
     @if($SGST !=0)
		<td align="right" valign="middle">{{number_format($sgst_tax_percentage, 2)}}</td>
		<td align="right" valign="middle">{{number_format($SGST, 2)}}</td>
    @elseif($UTGST !=0)
    <td class="right" valign="middle">{{number_format($utgst_tax_percentage, 2)}}</td>
    <td>{{number_format($UTGST, 2)}}</td>
    @else
    <td align="right" valign="middle">0</td>
    <td align="right" valign="middle">0</td>
    @endif
		<td align="right" valign="middle">{{number_format($igst_tax_percentage, 2)}}</td>
		<td align="right" valign="middle">{{number_format($IGST, 2)}}</td>
        @endif
        @if($discount_before_tax==0)
		<td align="right" valign="middle">
			@if($product->discount_type)
				{{ number_format($product->discount_percentage, 2) }}
			@else
				0.00
			@endif	
		</td>
		<td align="right" valign="middle">{{number_format($product->discount_total, 2)}}
			@if($product->discount_inc_tax)
			<br /><span style="font-size:10px;" align="left">(INC TAX)</span>
			@else
			<br /><span style="font-size:10px;" align="left">(EXC TAX)</span>
			@endif
		</td>
			@endif
	
    <td align="right" valign="middle">{{ number_format(($product->row_total), 2) }}</td>
  </tr>
  <?php ++$slno; ?>
  @endforeach
  <tr>
	<td colspan="5" align="right"><strong>Total</strong></td>
	<td align="right"><strong>{{$totQty}}</strong></td>
        @if($discount_before_tax==1)
        <td align="right"><strong>{{number_format($totalBaseDiscBeforeTax,2)}}</strong></td>
        <td align="right"></td>
        <td align="right"><strong>{{number_format($totalRowDiscount, 2)}}</strong></td>
        @endif
	<td align="right"><strong>{{number_format($grnProductArr[0]->base_total, 2)}}</strong></td>
	<td align="right"></td>
	<td align="right"><strong>{{number_format($totalTaxValue, 2)}}</strong></td>			
	@if($isGst)
		<td align="right"></td>
		<td align="right"><strong>{{number_format($total_cgst, 2)}}</strong></td>
		<td align="right"></td>
    @if($total_sgst!=0)
		<td align="right"><strong>{{number_format($total_sgst, 2)}}</strong></td>
		<td align="right"></td>
    @elseif($total_utgst!=0)
    <td align="right"></td>
    <td align="right">{{number_format($total_utgst,2)}}</td>
     @else
    <td align="right">0</td>
    <td align="right">0</td>
    @endif
		<td align="right"><strong>{{number_format($total_igst, 2)}}</strong></td>
        @endif
        @if($discount_before_tax==0)
		<td align="right"></td>
		<td align="right"><strong>{{number_format($totalRowDiscount, 2)}}</strong></td>	
	@endif			
	<td align="right"><strong>{{number_format($row_total, 2)}}</strong></td>
</tr>
</tbody>
</table>
<table width="100%" style=" font-size:11px !important; border-right:1px solid #000; margin-top:10px" cellspacing="0" cellpadding="0" border="1">
	<tr style="font-size:12px !important; font-weight:bold;" class="thh">
	  <td align="right"><strong>Total Items</strong></td>
	  <td align="right"><strong>Total</strong></td>
	  <td align="right"><strong>Shipping Amt</strong></td>
	  <td align="right"><strong>SAC Code</strong></td>
	  <td align="right"><strong>Service Tax %</strong></td>
	  <td align="right"><strong>Service Charge Amt</strong></td>
	  <td align="right"><strong>Bill Discount</strong></td>	  
	  <td align="right"><strong>Grand Total</strong></td>
	</tr>

	<tr>
		<td align="right"><strong>{{ --$slno }}</strong></td>
		<td align="right"><strong>{{number_format($grnProductArr[0]->base_total, 2)}}</strong></td>
		<td align="right"><strong>{{number_format($grnProductArr[0]->shipping_fee, 2)}}</strong></td>
		<td>&nbsp;</td>
		<td align="right"><strong>0.00</strong></td>
		<td align="right"><strong>0.00</strong></td>
		<td align="right"><strong>
			@if(!$grnProductArr[0]->discount_on_bill_options)
				@if($grnProductArr[0]->on_bill_discount_type)
					@if(isset($grnProductArr[0]->discount_on_total))
							{{ number_format($grnProductArr[0]->discount_on_total, 2) }}
							({{ number_format($grnProductArr[0]->on_bill_discount_value, 2).' %' }})
						@else
						   0.00
						@endif    
					@else 
					{{(isset($grnProductArr[0]->discount_on_total)) ? number_format($grnProductArr[0]->discount_on_total, 2) : '0.0'}}
				@endif
			@else
				0.00
			@endif
		</strong>
		</td>            
		<td align="right"><strong>{{number_format($grnProductArr[0]->grand_total, 2)}}</strong></td>
	</tr>
	</table>
 	
	 <table cellpadding="2" cellspacing="2" class="table" width="100%">
                <tr>
                    <td style="word-wrap:break-word;font-size:12px;width:100%;font-weight:bold;">Grand Total In Words: <?php echo Utility::convertNumberToWords(round($grnProductArr[0]->grand_total,2)); ?></td>
                </tr>
                <tr>
                    <td style="word-wrap:break-word;font-size:11px;width:100%;">* Reverse Charges not Applicable</td>
                </tr>
            </table>