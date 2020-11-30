@if(count($productArr) > 0)
<div class="row">
<div class="col-lg-12 orderdet">    <table class="table table-bordered thline">
        <thead> <tr><th class="col-md-6">Invoiced By</th><th class="col-md-6">Billing Address</th>   </tr></thead>
        <tbody>
        <tr><td><div class="row static-info">
					<div class="col-md-12 value">
						 {{$legalEntity->business_legal_name}}<br> 
							 {{$legalEntity->address1}}<br> 
							{{$legalEntity->address2}} <br>
						  {{$legalEntity->city}}, {{$legalEntity->state_name}}, {{$legalEntity->country_name}}, {{$legalEntity->pincode}}. 
						 <br><br>  VAT/ TIN : {{$legalEntity->tin_number}}<br> 
							PAN No: {{$legalEntity->pan_number}}<br>
                            FSSAI No: {{$legalEntity->fssai}}
					</div>
				</div></td>
       <td><div class="row static-info">
					<div class="col-md-12 value">
					@if(is_object($billing))	 
						{{$billing->company}}<br>
						{{$billing->fname}} {{$billing->lname}} <br>
						{{$billing->addr1}} <br>
						{{$billing->addr2}} <br>
						{{$billing->city}}, {{$billing->state_name}}, {{$billing->postcode}}, {{$billing->country_name}} <br>
						T: {{$billing->telephone}} | M: {{$billing->mobile}} <br>
                        FSSAI No: {{$billing->fssai}}
					@endif	
					</div>
				</div></td>   </tr>
        
        </tbody>        
        
				
    </table>	 </div>				
								
		
</div>

<div class="tabbable-line">
    <form id="add_invoice_form" action="/salesorders/saveInvoice" method="POST">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                                                
                                                    
                    <div class="portlet-body">
                        <div class="table-responsive">
                            <table class="table table-bordered thline">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>SKU# </th>
                                        <th> Product Name </th>
                                        <th> MRP </th>
                                        <th>Cost/Unit</th>
                                        <th> Ordered Qty </th> 
                                        <th> Invoice Qty </th>
                                        <th> Tax %</th>
                                        <th> Tax Value </th>
                                        <th> Total Value </th>
                                        <th class="10%"> Comment </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    @foreach($productArr as $product)
                                    <?php
                                    $hasInvoiceQty = 0;
                                    $shippedQty = isset($shippeddArr[$product->product_id]) ? (int)$shippeddArr[$product->product_id] : 0;
                                    $invoicedQty = isset($invoicedArr[$product->product_id]) ? (int)$invoicedArr[$product->product_id] : 0;
                                    

                                    $canceledQty = isset($canceledArr[$product->product_id]) ? (int)$canceledArr[$product->product_id] : 0;
                                    
                                    if($shipmentId > 0 && $invoicedQty <= $shippedQty) {
                                        $hasInvoiceQty = ($shippedQty - $invoicedQty);
                                    }
                                    else if(!$shipmentId) {
                                        $hasInvoiceQty = ($product->qty - ($invoicedQty + $canceledQty));
                                    }
                                    ?>    
                                    <tr>
                                        <td>{{$sno}}
                                        <input type="hidden" name="products[{{$product->product_id}}]" value="{{$product->product_id}}">
                                        <input type="hidden" name="prices[{{$product->product_id}}]" value="{{$product->price}}">
                                        </td>
                                        <td>{{$product->sku}}</td>
                                        <td>{{$product->pname}} {{!empty($product->seller_sku) ? '('.$product->seller_sku.')' : ''}}</td>
                                        <td>{{$product->symbol}} {{number_format($product->mrp, 2)}}</td>
                                        <td>{{$product->symbol}} {{number_format($product->price, 2)}}</td>
                                        <td>{{(int)$product->qty}}<br>
                                        <span style="font-size:10px;">Invoiced Qty: {{$invoicedQty}}
                                        <br>
                                        Cancelled Qty: {{$canceledQty}}</span></td>
                                        <td><input type="number"  min="{{($hasInvoiceQty > 0 ? 0 : 1)}}" max="{{$hasInvoiceQty}}" value="{{$hasInvoiceQty}}" name="avail_qty[{{$product->product_id}}]"></td>
                                        <td>{{(isset($taxArr[$product->product_id]) ? $taxArr[$product->product_id].'%' : '0.0%')}}</td>
                                        <td>{{$product->symbol}} {{number_format($product->tax, 2)}}</td>
                                        <td>{{$product->symbol}} {{number_format($product->total, 2)}} </td>
                                        <td><textarea name="comments[{{$product->product_id}}]" rows="1"></textarea>
                                            </td>
                                    </tr>
                                    <?php $sno = $sno + 1; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">                            
                <div class="box2">
                    
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="gds_order_id" id="gds_order_id" value="{{$orderdata->gds_order_id}}">
                
                <div class="row">
                    <div class="col-md-12">
                        <strong>Invoice Status</strong>
                        <p class="text-danger" id="ajaxResponse"></p>
                        <select id="invoice_status" name="invoice_status" class="form-control">
                            <option value="">Select Status</option>
                            @if(isset($invoiceStatusArr) && is_array($invoiceStatusArr))
                                @foreach($invoiceStatusArr as $statusId=>$statusValue)
                                <option value="{{$statusId}}">{{$statusValue}}</option>
                                @endforeach
                            @endif  
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h4>Comment</h4>
                        <textarea name="order_comment" id="order_comment" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <br>
               <div class="row">
                    <div class="col-md-12">                
                        <input type="submit" id="btnSubmit" class="btn green-meadow" value="Submit">
                    </div>                            
                </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="portlet-body">
                    <div class="table-responsive">
                        <h4>Comment History</h4>
                        @include('Orders::comments')                                        
                    </div>
                </div>	
            </div>
        </div>
                
    </form>
    
</div>
@else
    <div>&nbsp;</div>
        <div>
            <div class="alert alert-danger">
              {{$notifyMessage}} 
            </div>
        </div>
@endif
