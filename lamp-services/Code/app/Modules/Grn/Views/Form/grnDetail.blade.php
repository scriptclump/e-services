<div class="row">
    <div class="col-md-3">
        <h4>Supplier Details</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong> Name </strong></td>
                            <td>{{$grnProductArr[0]->business_legal_name}} </td>
                        </tr>
						<tr>
                            <td><strong> Supplier Code </strong></td>
                            <td>{{ $grnProductArr[0]->le_code }}</td>
                        </tr>						 
                        <tr>
                            <td><strong> Address </strong></td>
                            <td>{{$grnProductArr[0]->address1}}, {{$grnProductArr[0]->address2}},
                            {{$grnProductArr[0]->state_name}}, {{$grnProductArr[0]->city}} - {{$grnProductArr[0]->pincode}} </td>
                        </tr>
                        <tr>
                            <td><strong> State </strong></td>
                            <td>{{$grnProductArr[0]->state_name}}</td>
                        </tr>
                        <tr>
                            <td><strong> State Code</strong></td>
                            <td>{{ $grnProductArr[0]->state_code }}</td>
                        </tr>
                        <tr>
                            <td><strong> Phone </strong></td>
                            <td>{{$grnProductArr[0]->legalMobile}}</td>
                        </tr>
                        <tr>
                            <td><strong> Email </strong></td>
                            <td>{{$grnProductArr[0]->legalEmail}}</td>
                        </tr>
						<tr>
                            <td><strong> GSTIN / UIN </strong></td>
                            <td>{{$grnProductArr[0]->gstin}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <h4>Billing Address</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>Name</strong></td>
                            <td> {{$billingAddress->business_legal_name}} </td>
                        </tr>
                        <tr>
                            <td valign="top"><strong> Address </strong></td>
                            <td valign="top">{{$billingAddress->address1}}<br />{{$billingAddress->address2}} </td>
                        </tr>
						<tr>
                            <td><strong> State </strong></td>
                            <td>{{$billingAddress->state}}</td>
                        </tr>
						<tr>
                            <td><strong> State Code</strong></td>
                            <td>{{ $billingAddress->state_code }}</td>
                        </tr>
						<tr>
                            <td><strong> Country</strong></td>
                            <td>{{ $billingAddress->country_name }}</td>
                        </tr>						
						<tr>
                            <td><strong> GSTIN / UIN </strong></td>
                            <td>{{$deliveryGTIN}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
	<div class="col-md-3">
        <h4>Delivery Address</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        @if(!empty($whInfo->le_wh_code))
                        <tr>
                            <td><strong>Code:</strong></td>
                            <td> {{$whInfo->le_wh_code}} </td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Name</strong></td>
                            <td> {{$whInfo->lp_wh_name}} </td>
                        </tr>
                        <tr>
                            <td valign="top"><strong> Received At </strong></td>
                            <td valign="top">{{$grnProductArr[0]->dc_address1}}<br />{{$grnProductArr[0]->dc_address2}} </td>
                        </tr>
						<tr>
                            <td><strong> State </strong></td>
                            <td>{{$grnProductArr[0]->state_name}}</td>
                        </tr>
						<tr>
                            <td><strong> State Code</strong></td>
                            <td>{{ $grnProductArr[0]->state_code }}</td>
                        </tr>
                        
                        <tr>
                            <td><strong> Phone </strong></td>
                            <td>{{$whInfo->phone_no}}</td>
                        </tr>
                        <tr>
                            <td><strong> Email</strong></td>
                            <td> {{$whInfo->email}}</td>
                        </tr>
                        <tr>
                            <td><strong> Contact&nbsp;Person</strong></td>
                            <td> {{$whInfo->contact_name}}</td>
                        </tr>
						<tr>
                            <td><strong> GSTIN / UIN </strong></td>
                            <td>{{$deliveryGTIN}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <h4>GRN Details</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><strong>GRN Code</strong></td>
                        <td> {{$grnProductArr[0]->inward_code}} </td>
                    </tr>
                    <tr>
                        <td><strong> Date </strong></td>
                        <td> {{Utility::dateTimeFormat($grnProductArr[0]->created_at)}} </td>
                    </tr>
                    <tr>
                        <td><strong>PO No</strong></td>
                        <td> {{$po_code}} </td>
                    </tr>                    
                    @if(!empty($grnProductArr[0]->invoice_no))
                    <tr>
                        <td valign="top"><strong> Invoice No </strong></td>
                        <td valign="top"> {{$grnProductArr[0]->invoice_no}}, <strong>Invoice Date:</strong> {{Utility::dateFormat($grnProductArr[0]->invoice_date)}}, <strong>Ref.No.</strong> {{$grnProductArr[0]->inward_ref_no}}</td>
                    </tr>
                    @endif                    
                    <tr>
                        <td><strong> Created By </strong></td>
                        <td> {{$grnProductArr[0]->firstname}} {{$grnProductArr[0]->lastname}}</td>
                    </tr>
                    <tr>
                        <td><strong> Approval&nbsp;Status </strong></td>
                        <td> {{$approvedStatus}} </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 
$isGst = 0;
foreach($grnProductArr as $product){
	$taxData = property_exists($product, 'tax_data') ? json_decode($product->tax_data, true) : [];
	if(!empty($taxData))
	{
		$isGst = 1;
	}
}
$discount_before_tax = (isset($grnProductArr[0]->discount_before_tax) && $grnProductArr[0]->discount_before_tax == 1)?$grnProductArr[0]->discount_before_tax:0;
?>
<div class="row">
	<div class="col-md-12">
	<h4 style="float: left;">Product Details</h4>
        <div class="text-right" style="float: right; font-size:11px;padding-top: 15px;"><b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b></div>
		<div class="table-scrollable">
			<table class="table table-striped table-bordered table-advance table-hover" id="sample_2">
				<thead>
					<tr>
						<th rowspan="2">&nbsp;</th>
						<!--th rowspan="2">SKU#</th-->
						<th rowspan="2">HSN Code</th>
						<th rowspan="2" width="20%">Product Title</th>
						<th rowspan="2">MRP</th>
						<th rowspan="2">Rate</th>
						<th rowspan="2">PO Qty</th>
						<th rowspan="2">GRN Qty</th>
                                                @if($discount_before_tax==1)
                                                <th rowspan="2">Base Rate</th>
						<th colspan="2">Discount&nbsp;</th>
                                                @endif
						<th rowspan="2">Taxable Value</th>
						<th rowspan="2">Tax&nbsp;%</th>
						<th rowspan="2">Tax&nbsp;Value</th>
						@if($isGst)
							<th colspan="2">CGST</th>
							<th colspan="2">SGST/UTGST</th>
							<th colspan="2">IGST</th>
						@endif
                                                @if($discount_before_tax==0)
						<th colspan="2">Discount&nbsp;</th>
                                                @endif
						<th rowspan="2">Total</th>
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
				$slno = 0;
				$totalTaxValue = 0.00;
				$totalRowDiscount = 0.00;
				$row_total = 0.00;
                                $totalBaseDiscBeforeTax = 0.00;
				$totQty = 0.00;
				$totalBaseValue = 0.00;
				$grandTotalValue = 0.00;
				$subTotal = 0.00;
				$totSubTotal = 0.00;
                $totalTax = 0.00;
                $totaldiscount = 0.00;     
                $CGST = 0.00;
                $total_cgst = 0.00;
                $SGST = 0.00;
                $total_sgst = 0.00;
                $IGST = 0.00;
                $total_igst = 0.00;
                $UTGST = 0.00;     
                $total_utgst = 0.00;                                          
				?>
				@foreach($grnProductArr as $product)
                                <?php $totQty = $totQty + (int)$product->received_qty;
								$totalTaxValue = $totalTaxValue + $product->tax_amount;
								$totalRowDiscount = $totalRowDiscount + $product->discount_total;
								$row_total = $row_total + $product->row_total;
								$totalBaseDiscBeforeTax += ($product->sub_total+$product->discount_total);
                                $totalTax = $totalTax + $product->tax_amount;
                                $totaldiscount = $totaldiscount + $product->discount_total;
                                $taxData = property_exists($product, 'tax_data') ? json_decode($product->tax_data, true) : [];
                                 $taxData = isset($taxData[0]) ? $taxData[0] : [];

                                if(isset($taxData['Tax Percentage'])){
								$taxType = isset($taxData['Tax Type']) ? $taxData['Tax Type'] : '';
								$taxPercentage = isset($taxData['Tax Percentage']) ? $taxData['Tax Percentage'] : 0.00;
                                }else{ 
                               
                                $taxType = isset($taxData['Tax_Type']) ? $taxData['Tax_Type'] : '';
                                $taxPercentage = isset($taxData['Tax_Percentage']) ? $taxData['Tax_Percentage'] : 0.00;

                                }

                                $CGST = isset($taxData['CGST_VALUE']) ? $taxData['CGST_VALUE'] : 0.00;								
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
						<td align="center"><a class="grnItem" href="javascript:void(0);" id="{{$product->inward_prd_id}}"><i class="fa fa-plus"></i></a></td>
						<!--td align="center">{{$product->sku}}</td-->            
						<td>{{$product->hsn_code}}</td>
						<td><span>{{$product->product_title}}</span><br/><span style="font-size: 11px;font-weight: bold;">SKU - {{$product->sku}}</span></td>
						<td class="rightAlignment">{{number_format($product->mrp, 2)}}</td>
						<td class="rightAlignment">{{number_format($product->price, 5)}}</td>
						<td class="rightAlignment">{!!html_entity_decode($product->orderd_qty)!!}</td>
						<td>{{(int)$product->received_qty}} <span style="font-size:10px;">(Eaches)</span><br>
						<span style="font-size:10px;">
						@if($product->quarantine_stock)
						<strong>Quarantine: {{(int)$product->quarantine_stock}}</strong><br>
						@endif
                                               
						@if($product->free_qty)
						<strong>Free: {{(int)$product->free_qty}}</strong><br>
						@endif
						@if($product->damage_qty)
						<strong>Damage: {{(int)$product->damage_qty}}</strong><br>
						@endif
						@if($product->missing_qty)
						<strong>Short: {{(int)$product->missing_qty}}</strong><br>
						@endif
						@if($product->excess_qty)
						<strong>Excess: {{(int)$product->excess_qty}}</strong>
						@endif
						</span>
						</td>
                                                @if($discount_before_tax==1)
                                                    <td class="rightAlignment">{{number_format($product->sub_total+$product->discount_total,2)}}</td>
                                                    @if($product->discount_type)
                                                            <td valign="middle" align="right">{{number_format($product->discount_percentage, 2)}}</td>
                                                    @else
                                                            <td valign="middle" align="right">0.00</td>
                                                    @endif							
                                                    <td class="rightAlignment">{{number_format($product->discount_total, 2)}}</td>
                                                @endif
						<td class="rightAlignment">{{number_format($product->sub_total, 5)}}</td>
						@if($taxType != '')
							<td class="rightAlignment">{{$taxType .' @ '. number_format($product->tax_per, 2)}}</td>
						@else
							<td class="rightAlignment">{{number_format($product->tax_per, 2)}}</td>
						@endif		
						<td class="rightAlignment">{{number_format($product->tax_amount, 5)}}</td>  
						@if($isGst)
							<td class="rightAlignment">{{number_format($cgst_tax_percentage, 2)}}</td>
							<td>{{number_format($CGST, 2)}}</td>
                            @if($SGST !=0)
							<td class="rightAlignment">{{number_format($sgst_tax_percentage, 2)}}</td>
							<td>{{number_format($SGST, 2)}}</td>
                            @elseif($UTGST !=0)
                            <td class="rightAlignment">{{number_format($utgst_tax_percentage, 2)}}</td>
                            <td>{{number_format($UTGST, 2)}}</td>
                            @else
                            <td>0</td>
                            <td class="rightAlignment">0</td>
                            @endif
							<td class="rightAlignment">{{number_format($igst_tax_percentage, 2)}}</td>
							<td>{{number_format($IGST, 2)}}</td> 
						@endif
                                                @if($discount_before_tax==0)
						@if($product->discount_type)
							<td valign="middle" align="right">{{number_format($product->discount_percentage, 2)}}</td>
						@else
							<td valign="middle" align="right">0.00</td>
						@endif							
						<td class="rightAlignment">{{number_format($product->discount_total, 2)}}
							@if($product->discount_inc_tax)
							<br /><span style="font-size:10px;">(INC TAX)</span>
							@else
							<br /><span style="font-size:10px;">(EXC TAX)</span>
							@endif
						</td>
                                                @endif
						<td class="rightAlignment">{{number_format($product->row_total, 5)}}</td>
					</tr>
					@if(isset($packArr[$product->inward_prd_id]) && count($packArr[$product->inward_prd_id]) > 0)
					<tr style="display:none;" id="packinfo-{{$product->inward_prd_id}}">
						<td colspan="13">
							<table class="table table-striped">
								<thead>
									<tr>
										<th></th>
										<th style="font-size:10px;"><strong>Pack Size</strong></th>
										<th style="font-size:10px;"><strong>Received</strong></th>
										<th style="font-size:10px;"><strong>Tot. Rec. Qty</strong></th>
										<th style="font-size:10px;"><strong>MFG Date</strong></th>
										<th style="font-size:10px;"><strong>EXP Date</strong></th>
										<th style="font-size:10px;"><strong>Freshness</strong></th>
									</tr>
								</thead>
								@foreach($packArr[$product->inward_prd_id] as $pack)	
									<tr>
										<td></td>
                                                                                @if($pack->pack_level == 'Eaches')
										<td><span style="font-size:10px;">{{$pack->pack_level}} ({{$pack->pack_qty}})</span></td>
                                                                                @else
										<td><span style="font-size:10px;">{{$pack->pack_level}} ({{$pack->pack_qty}} Eaches)</span></td>
                                                                                @endif
										<td><span style="font-size:10px;">{{$pack->received_qty}}</span></td>
										<td><span style="font-size:10px;">{{$pack->tot_rec_qty}}</span></td>
										<td><span style="font-size:10px;">@if(!empty($pack->mfg_date) && $pack->mfg_date != '0000-00-00' && $pack->mfg_date != '1970-01-01') {{ date('d-m-Y', strtotime($pack->mfg_date)) }}@endif</span></td>
										<td><span style="font-size:10px;">@if(!empty($pack->exp_date) && $pack->exp_date != '0000-00-00' && $pack->exp_date != '1970-01-01') {{ date('d-m-Y', strtotime($pack->exp_date)) }}@endif</span></td>
										<td><span style="font-size:10px;">{{round($pack->freshness_per)}} %</span></td>
									</tr>
								@endforeach	
							</table>
						</td>
					</tr>
					@endif
	
				@endforeach
					<tr>
					<td class="bold" align="left" valign="middle"></td>
					<td align="left" valign="middle"></td>
					<td valign="middle"></td>
					<td valign="middle"></td>
					<td valign="middle"></td>
					
					<td class="bold" valign="middle"><strong>Total</strong></td>
					<td valign="middle">{{$totQty}}</td>
                                        @if($discount_before_tax==1)
                                            <td valign="middle" align="right">{{number_format($totalBaseDiscBeforeTax,2)}}</td>
                                            <td valign="middle"></td>
                                            <td class="bold" valign="middle" align="right">{{number_format($totalRowDiscount, 5)}}</td>
                                        @endif
					<td valign="middle">{{number_format($grnProductArr[0]->base_total, 5)}}</td>
						<td valign="middle"></td>
						<td valign="middle" align="right">{{number_format($totalTaxValue, 2)}}</td>
					@if($isGst)
						<td valign="middle"></td>
						<td valign="middle" align="right">{{number_format($total_cgst, 2)}}</td>
                         @if($total_sgst!=0)	
						<td valign="middle"></td>
						<td valign="middle" align="right">{{number_format($total_sgst, 2)}}</td>
                         @elseif($total_utgst!=0)
                        <td align="middle"></td>
                        <td align="middle">{{number_format($total_utgst,2)}}</td>
                         @else
                        <td align="middle">0</td>
                        <td align="middle">0</td>
                        @endif
						<td valign="middle"></td>
						<td valign="middle" align="right"> {{number_format($total_igst, 2)}}</td>
					@endif
                                        @if($discount_before_tax==0)
					<td valign="middle"></td>
					<td class="bold" valign="middle" align="right">{{number_format($totalRowDiscount, 5)}}</td>
                                        @endif
					<td class="bold" valign="middle" align="right">{{number_format($row_total, 5)}}</td>
				  </tr>
				</tbody>
			</table>
		</div>
		</div>
		</div>
		<div class="row">
                                                
	
    <div class="col-md-12">
        <div class="table-scrollable">
		<table class="table table-striped table-bordered table-advance table-hover" id="sample_3">
			<thead>
				<tr>
					<th align="right" style="text-align:right !important;">Total Items Qty</th>
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
				<tr class="odd gradeX">
					<td>{{$totQty}}</td>
				<td> {{number_format($grnProductArr[0]->base_total, 2)}}</td>
				<td> {{number_format($grnProductArr[0]->shipping_fee, 2)}}</td>
				<td>&nbsp;</td>
				<td>0.00</td>
				<td>0.00</td>
					<td>
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
					</td>                    
				<td> {{number_format($grnProductArr[0]->grand_total, 2)}}</td>
				</tr>
			</tbody>
		</table>
	</div>
    </div>
	</div>
	<div class="row">
		<div class="col-md-12">
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
</div>
	</div>