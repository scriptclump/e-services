<?php $symbol = 'Rs.'; ?>
<div class="row">
<div class="col-md-6 col-sm-12">
	<div class="portlet ">
		<h4>Invoiced By</h4>
		<div class="portlet-body box2">
			<div class="row static-info">
				<div class="col-md-12 value">
				@if(is_object($legalEntity) && is_object($whDetails))
					 {{$legalEntity->business_legal_name}}<br> 
						@if(isset($whDetails->address1) && !empty($whDetails->address1)) T: {{$whDetails->address1}} <br> @endif  
						@if(isset($whDetails->address2) && !empty($whDetails->address2))  {{$whDetails->address2}} <br> @endif
					  {{$whDetails->city}}, {{$whDetails->state_name}},@if(isset($whDetails->country_name) && !empty($whDetails->country_name)) {{$whDetails->country_name}} @else India, @endif {{$whDetails->pincode}}
					  <br>  <strong>State Code:</strong> {{$whDetails->state_code}} 
					 <br>  <strong>GSTIN No:</strong> {{$whDetails->tin_number}}
					 <br>  <strong>FSSAI No:</strong> {{$legalEntity->fssai}}
				@endif		
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-6 col-sm-12">
	<div class="portlet">
		<h4>Billing Address</h4>
		<div class="portlet-body box2">
			<div class="row static-info">
				<div class="col-md-12 value">
				@if(is_object($billing))	 
					<strong>{{ucwords($billing->company)}}</strong><br>
					<strong>Name:</strong> {{$billing->fname}} {{$billing->lname}} <br>
					<strong>Address:</strong> {{$billing->addr1}}  {{$billing->addr2}},<br>@if(!empty($billing->locality)) {{$billing->locality}}, @endif @if(!empty($billing->landmark)){{$billing->landmark}}, @endif {{$billing->city}}, {{$billing->state_name}}, {{$billing->postcode}}, {{$billing->country_name}} 
					<br>
					<strong>State Code:</strong> {{$billing->state_code}}
					<br>
					@if(isset($billing->telephone) && !empty($billing->telephone)) T: {{$billing->telephone}} @endif  @if(isset($billing->mobile) && !empty($billing->mobile))   , M: {{$billing->mobile}} @endif <br>
					<strong>FSSAI No:</strong> {{$billing->fssai}}
					<br>
				@endif	
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div class="tabbable-line">
	@if(count($invoicedProdArr)>0)
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="portlet">

				<h4>Product Description</h4>

				<div class="portlet-body">
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th>SNo</th>
									<th>SKU</th>
									<th> Product Name </th>
									<th> HSN Code </th>
									<th> MRP </th>
									<th> Unit</th>
									<th width="7%">Qty </th>
									<th width="7%"> Inv CFC </th>
									<th style="text-align:right;"> Net Value</th>
									<th style="text-align:right;"> Tax(%)</th>
									<th style="text-align:right;"> Tax Amt</th>
									<th style="text-align:right;">SCH Disc</th>
									<th style="text-align:right;"> Total</th>
									<th> Comment </th>
								</tr>
							</thead>
							<tbody>
								<?php $sno = 1; 
									$taxPer = 0;
									$discount = 0;
									$shippingAmount = 0;
									$otherDiscount = 0;
									$grandTotal = 0;
									$totInvoicedQty = 0;
							        $totNetValue = 0;
							        $totTaxValue = 0;
							        $totDiscountValue = 0;
							        $totCGST = $totSGST = $totIGST = $totUTGST = 0;                        
									?>
								@foreach($invoicedProdArr as $product)
								<?php
									$taxPer = (isset($taxArr[$product->product_id]) ? (float)$taxArr[$product->product_id] : 0);
        							$singleUnitPrice = (($product->total / (100 + $taxPer) * 100) / $product->qty);

									
									$taxValue = $product->item_tax_amount;
        							$netValue = ($singleUnitPrice * $product->invoicedQty);
									$discount = 0;

									$subTotal = $taxValue + $netValue;
									if ($orderdata->discount_before_tax == 1) {
							            $singleUnitPrice = (($product->cost) / $product->qty);
							            $discount = ($singleUnitPrice*$product->invoicedQty*$product->discount)/100;//$product->discount_amt;
							            $productTotal = $discount + $product->item_row_total;
							            $product->item_price = $productTotal / $product->invoicedQty;
							        }
									
																		
									//$discountValue = ($discount*$product->invoicedQty);
									
									$grandTotal +=$product->row_total_incl_tax;

									$totCGST = $totCGST + $product->CGST;
									$totSGST = $totSGST + $product->SGST;
									$totIGST = $totIGST + $product->IGST;
									$totUTGST = $totUTGST + $product->UTGST;

									?>
								<tr>
									<td>{{$sno}}</td>
									<td>{{$product->sku}}</td>
									<td>{{$product->pname}}</td>
									<td>{{$product->hsn_code}}</td>
									<td align="right">{{number_format($product->mrp, 2)}}</td>
        							<td align="right">{{number_format($product->item_price, 2)}}</td>
									<td>{{(int)$product->invoicedQty}}</td>
									<td>{{number_format($product->invCfc,2)}}</td>
									<td align="right">{{number_format($product->item_row_total, 2)}}</td>
									<td>{{$taxPer}}</td>
									<td align="right">{{number_format($taxValue, 2)}}</td>
									<td align="right">{{number_format($discount, 2)}}</td>
									<td align="right">{{number_format(($product->row_total_incl_tax), 2)}}</td>
									<td>{{$product->comments}}</td>
								</tr>
								<?php 
								$sno = $sno + 1;
								$totInvoicedQty = $totInvoicedQty + $product->invoicedQty;
								$totNetValue = $totNetValue + $product->item_row_total;
								$totTaxValue = $totTaxValue + $taxValue;
								$totDiscountValue = $totDiscountValue + $discount; 
								?>
								@endforeach
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"><strong>Total</strong></td>
									<td><strong>{{(int)$totInvoicedQty}}</strong></td>
									<td></td>
									<td align="right"><strong>{{number_format($totNetValue, 2)}}</strong></td>
									<td></td>
									<td align="right"><strong>{{number_format($totTaxValue, 2)}}</strong></td>
									<td align="right"><strong>{{number_format($totDiscountValue, 2)}}</strong></td>
									<td align="right"><strong>{{number_format(($grandTotal + $shippingAmount), 2)}}</strong></td>
									<td></td>
								</tr>
								<?php
                $bill_discount_amt = isset($invoicedProdArr[0]->bill_discount_amt) ? $invoicedProdArr[0]->bill_discount_amt : 0;
								$grandTotalWithRound = Utility::getRoundOff(($grandTotal + $shippingAmount - $bill_discount_amt-$ecash_applied), 'gtround');
								$roundoff = Utility::getRoundOff(($grandTotal + $shippingAmount - $bill_discount_amt-$ecash_applied), 'roundoff');

								if($totSGST > 0 || $totCGST > 0 || $totIGST > 0) {
								  $gstData = array('SGST'=>$totSGST, 'CGST'=>$totCGST, 'IGST'=>$totIGST, 'UTGST'=>$totUTGST);
								}
								else {
								  $gstData = array();
								}
								?>
							</tbody>
						</table>
						<table class="table table-hover table-bordered table-striped">
							<thead>
							<tr>
								<th>Invoice Qty</th>	
								<th>Sub Total</th>	
								@if(is_array($gstData) && count($gstData) > 0)
								@foreach($gstData as $gstKey=>$gstVal)
								<th align="left">{{$gstKey}}</th>
								@endforeach
								@endif
								<th>Total Tax	</th>
								<th>Bill Disc.</th>
								<th>E-Cash Applied</th>
								<th>Roundoff</th>
								<th>Grand Total</th>
							</tr>
							</thead>
							<tr>
								<td>{{(int)$totInvoicedQty}}</td>	
								<td>{{$symbol}} {{number_format($totNetValue, 2)}}</td>	
								@if(is_array($gstData) && count($gstData) > 0)
								@foreach($gstData as $gstKey=>$gstVal)
								<td align="left">{{$symbol}} {{number_format($gstVal,2)}}</td>
								@endforeach
								@endif
								<td>{{$symbol}} {{number_format($totTaxValue, 2)}}</td>
								<td>{{$symbol}} {{number_format($bill_discount_amt, 2)}}</td>
								<td>{{$symbol}} {{number_format($ecash_applied, 2)}}</td>	
								<td>{{$symbol}} {{number_format($roundoff, 2)}}</td>	
								<td>{{$symbol}} {{number_format($grandTotalWithRound, 2)}}</td>
							</tr>
						</table>						
					</div>
				</div>
			</div>
		</div>
	</div>
@if(is_object($trackInfo))	
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-bordered table-striped">
			@if($trackInfo->cfc_cnt!=0)<tr>
			<td style="width:15%;">CFC</td><td style="width:10%;">{{(int)$trackInfo->cfc_cnt}}</td><td>{{isset($cratesList[16004]) ? $cratesList[16004] : ''}}</td>
			</tr>@endif
			@if($trackInfo->bags_cnt!=0)<tr>
			<td style="width:15%;">Bags</td><td style="width:10%;">{{(int)$trackInfo->bags_cnt}}</td><td>{{isset($cratesList[16006]) ? $cratesList[16006] : ''}}</td>
			</tr>@endif
			@if($trackInfo->crates_cnt!=0)<tr>
			<td style="width:15%;">Crates</td><td style="width:10%;">{{(int)$trackInfo->crates_cnt}}</td><td>
			{{isset($cratesList[16007]) ? $cratesList[16007] : ''}}
			</td>
			</tr>@endif
		</table>
	</div>
</div>
@endif
@if(isset($invoicedProdArr[0]->remarks)) 
<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-bordered table-striped">
			<tr>
				<td width="5%"><strong>Remarks:</strong></td>
				<td>{{$invoicedProdArr[0]->remarks}}</td>
				<td width="5%"><a href="{{URL::to('/')}}/salesorders/invoicedetail/{{$invoicedProdArr[0]->gds_invoice_grid_id}}/{{$invoicedProdArr[0]->gds_order_id}}?edit=1"><span class="glyphicon glyphicon-pencil"></span></a></td>
			</tr>
		</table>
	</div>
</div>
@endif
	<div class="row">
		<div class="col-md-6"> 
			<form id="add_invoice_form" action="/salesorders/addRemarks" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_method" value="POST">
				<input type="hidden" name="gds_order_id" id="gds_order_id" value="{{$invoicedProdArr[0]->gds_order_id}}">
				<input type="hidden" name="gds_invoice_grid_id" id="gds_invoice_grid_id" value="{{$invoicedProdArr[0]->gds_invoice_grid_id}}">
				                
				<div class="row">
					<div class="col-md-12">
						<h4>Remarks</h4>
						<textarea name="invoice_remarks" id="invoice_remarks" class="form-control" rows="4">@if(isset($_GET['edit'])){{$invoicedProdArr[0]->remarks}}@endif</textarea>
					</div>
				</div>
				<div id="ajaxResponse"></div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<input type="submit" id="btnSubmit" class="btn green-meadow" value="Submit">
						</div>
					</div>

				</div>
			</form>
		</div>

		<div class="col-md-6">
                    @if(isset($cb_response['status']) && $cb_response['status']==200) 
                    <div class="well">
                        <?php 
                        foreach($cb_response['message'] as $usr=>$cb){                
                            $ecashtxt = ' Commission';
                            if($usr =='Customers'){
                                $ecashtxt = ' E-Cash';
                            }
                        ?>
                        <div class="row static-info align-reverse">
                            <div class="col-md-8 name"> {{$usr.$ecashtxt}}: </div>
                            <div class="col-md-3 value"> {{$orderdata->symbol}} {{number_format($cb, 2)}} </div>
                        </div>
                            <?php }   ?>
                    </div>
                    @endif
		</div>
	</div>
	@else
	<div class="row">
		<div class="col-md-6">
			No Products Available to Generate Invoice                
		</div>                                    
	</div>
	@endif
</div>