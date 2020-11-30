@if(count($final_products) > 0)	
				<div class="row">				
					<div class="col-md-6 col-sm-12">
						 
							<h4>Invoiced By</h4>
							<div class="portlet-body box2">
								<div class="row static-info">
									<div class="col-md-12 value">
										 {{$legalEntity->business_legal_name}}<br> 
											 {{$legalEntity->address1}}<br> 
											{{$legalEntity->address2}} <br>
										  {{$legalEntity->city}}, {{$legalEntity->state_name}}, {{$legalEntity->country_name}}, {{$legalEntity->pincode}}. 
										 <br><br>  VAT/ TIN : {{$legalEntity->tin_number}}<br> 
											PAN No: {{$legalEntity->pan_number}}<br>
                                            FSSAI No: {{$legalEntity->fssai}}

									</div>
								</div>
							</div>
						 
					</div>
					<div class="col-md-6 col-sm-12">
						 
							<h4>Billing Address</h4>
							<div class="portlet-body box2">
								<div class="row static-info">
									<div class="col-md-12 value">
									@if(is_object($billing))	 
										{{$billing->company}}<br>
										{{$billing->fname}} {{$billing->lname}} <br>
										{{$billing->addr1}} <br>
										{{$billing->addr2}} <br>
										{{$billing->city}} <br>
										{{$billing->state_name}} <br>
										{{$billing->postcode}}, {{$billing->country_name}} <br>
										T: {{$billing->telephone}} ,
										M: {{$billing->mobile}} <br>
                                        FSSAI No: {{$billing->fssai}}
									@endif	
									</div>
								</div>
							</div>
						 
					</div>
				</div>
				
                <div class="tabbable-line">
                    <form id="add_invoice_form" action="/salesorders/addInvoiceData" method="POST">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                
                                    
                                        <h4>Invoice Product</h4>
                                   
                                    <div class="portlet-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>SKU# </th>
                                                        <th> Product Name </th>
                                                        <th> MRP </th>
                                                        <th> Cost/Unit </th>
                                                        <th> Ordered Qty </th>
                                                        <th> Available Qty </th>
                                                        <th> Tax % </th>
                                                        <th> Tax Value </th>
                                                        <th> Net Value </th>
                                                        <th> Scheme Discount Value </th>
                                                        <th> Total Value </th>
                                                        <th> Comment </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $sno = 1; 
                                                    $tax = 0;
                                                    $discount = 0;
                                                    $shippingAmount = 0;
                                                    $otherDiscount = 0;
                                                    $grandTotal = 0;
                                                    ?>
                                                    @foreach($final_products as $product)
                                                        <?php 
                                                        $taxValue = (int)($product->remain_qty*$product->price*$tax)/100;
                                                        $netValue = (int)$product->remain_qty*$product->price;
                                                        $discountValue = (int)($product->price*$discount)/100;
                                                        $totalValue = (int)(($netValue+$taxValue)-($discountValue));
                                                        $grandTotal +=$totalValue; 
                                                        $currency = $product->symbol;
                                                        ?>
                                                    <tr>
                                                        <td>{{$sno}}<input type="hidden" name="invorderItems[]" value="{{$product->product_id}}"></td>
                                                        <td>{{$product->sku}}</td>
                                                        <td>{{$product->pname}} {{!empty($product->seller_sku) ? '('.$product->seller_sku.')' : ''}}</td>
                                                        <td>{{$currency}} {{number_format($product->mrp, 2)}}</td>
                                                        <td>{{$currency}} {{number_format($product->price, 2)}}</td>
                                                        <td>{{(int)$product->qty}}<br>
                                                        <span style="font-size:10px;">Shipped Qty:{{(int)$product->shippedQty}}<br>Invoiced Qty:{{(int)$product->invoiced_qty}}</span>
                                                        </td>
                                                        <td><input type="number"  min="1" max="{{(int)$product->remain_qty}}" value="{{(int)$product->remain_qty}}" name="available_qty[{{$product->product_id}}]"></td>
                                                        <td>{{$tax}}</td>
                                                        <td>{{$currency}} {{number_format($taxValue, 2)}}</td>
                                                        <td>{{$currency}} {{number_format($netValue, 2)}}</td>
                                                        <td>{{$currency}} {{number_format($discountValue, 2)}}</td>
                                                        <td>{{$currency}} {{number_format($totalValue, 2)}}</td>
                                                        <td><textarea name="invoice_pr_comments[{{$product->product_id}}]" rows="1"></textarea>
                                                            </td>
                                                    </tr>
                                                    <?php $sno++; ?>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="well">
                                                <div class="row static-info align-reverse">
                                                    <div class="col-md-10 name  text-right"> Shipping Amount: </div>
                                                    <div class="col-md-2">{{$currency}} {{number_format($shippingAmount, 2)}}</div>
                                                </div>
                                                <div class="row static-info align-reverse">
                                                    <div class="col-md-10 name text-right"> Other Discount: </div>
                                                    <div class="col-md-2  ">{{$currency}} {{number_format($otherDiscount, 2)}}</div>
                                                </div>
                                                <div class="row static-info align-reverse">
                                                    <div class="col-md-10 name text-right"> Grand Total: </div>
                                                    <div class="col-md-2  ">{{$currency}} {{number_format(($grandTotal-$shippingAmount), 2)}}</div>
                                                </div>                    
                                                                    
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">                            
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="gds_order_id" id="gds_order_id" value="{{$orderdata->gds_order_id}}">
                                <input type="hidden" name="shipment_grid_id" id="shipment_grid_id" value="{{$shipmentId}}">
                                <div class="row"><div class="col-md-12">
                                        <h4>Invoice Status</h4>
                                        <p class="text-danger" id="ajaxResponse"></p>
                                        <select id="invoice_status" name="invoice_status" class="form-control">
                                            <option value="">Select Status</option>
                                            @if(isset($commentStatusArr) && is_array($commentStatusArr))
												@foreach($commentStatusArr as $statusId=>$statusValue)
												<option value="{{$statusId}}">{{$statusValue}}</option>
												@endforeach
                                            @endif	
                                        </select>
                                    </div></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Comment</h4>
                                        <textarea name="order_comment" id="order_comment" class="form-control" rows="4"></textarea>
                                    </div>
                                </div>
                           <br>
                           <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input {{$enbaleCreateInvoice == false ? 'disabled="disabled"' : ''}} type="submit" id="btnSubmit" class="btn green-meadow" value="Submit">
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
