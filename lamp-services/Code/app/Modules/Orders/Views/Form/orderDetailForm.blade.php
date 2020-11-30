<div class="orderdet">
@if($editOrder && $orderStatusValue == 17001)
    <button class="btn green-meadow pull-right" id="edit_order" style="margin-left: 0px;margin:8px">Edit Order</button>
    <input type="hidden" id="order_id_toedit" value="<?php echo $orderdata->gds_order_id; ?>">
@endif
@if($openInvoice && $orderStatusValue == 17001)
     <button class="btn green-meadow pull-right" id="openInvoice" style="margin-left: 0px;margin:8px">Create Invoice</button>
     <input type="hidden" id="order_id_toedit" value="<?php echo $orderdata->gds_order_id; ?>">

      <input type="hidden" id="order_status_val" value="<?php echo $orderStatusValue; ?>">
      <div style="display:none; margin-top:5px;" id="ajaxResponse" class="col-md-12 alert alert-danger">
                             
  </div>
@endif 

    <table class="table table-bordered thline table-scrolling">    

<thead> <tr><th> Order Details </th> <th> Customer Details </th> <th> Payment Details </th></tr></thead>   
<tbody>
    <tr>
    
        <td class="col-md-4"><div class="portlet-body  ">
                <div class="static-info"> <div class="row">
                    <div class="col-md-5 name"> Order ID: </div>
                    <div class="col-md-7 value"> {{!empty($orderdata->order_code) ? $orderdata->order_code : $orderdata->gds_order_id}}</div>
                </div></div>                      
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Order Date: </div>
                    <div class="col-md-7 value"> {{date('d-m-Y H:i:s',strtotime($orderdata->order_date))}} </div>
                </div></div>
                @if(!empty($orderdata->order_expiry_date))
                 <div class=" static-info"><div class="row">
                    <div class="col-md-5 name"> Order Expiry Date: </div>
                    <div class="col-md-7 value"> {{date('d-m-Y H:i:s',strtotime($orderdata->order_expiry_date))}} </div>
                </div></div>
                @endif
                <div class=" static-info"><div class="row">
                    <div class="col-md-5 name">Order Status: </div>
                    <div class="col-md-7 value"> {{$orderdata->order_status_id}}</div>
                </div>
            </div>
          <div class=" static-info"><div class="row">
                    <div class="col-md-5 name"> Channel:	 </div>
                    <div class="col-md-7 value"> {{$orderdata->mp_name}} </div>
                </div></div>                
                 <div class=" static-info"><div class="row">
                    <div class="col-md-5 name"> URL:	 </div>
                    <div class="col-md-7 value"> {{$orderdata->mp_url}} </div>
                </div></div>   
                @if(is_object($whInfo))
                <div class=" static-info">
                    <div class="row">
                        <div class="col-md-5 name">Warehouse:</div>
                        <div class="col-md-7 value"> {{$whInfo->lp_wh_name}} </div>
                    </div>
                </div>  
                @endif

                @if(is_object($hubInfo))
                <div class=" static-info">
                    <div class="row">
                        <div class="col-md-5 name">Hub Name:</div>
                        <div class="col-md-7 value"> {{$hubInfo->lp_wh_name}} </div>
                    </div>
                </div>  
                @endif

                <div class=" static-info"><div class="row">
                        <div class="col-md-5 name">Spoke: </div>
                        <div class="col-md-7 value"> {{$orderdata->spokeName}}</div>
                    </div>
                </div>            

                <div class=" static-info"><div class="row">
                        <div class="col-md-5 name">Beat: </div>
                        <div class="col-md-7 value"> {{$orderdata->beat}}</div>
                    </div>
                </div>            

                @if(is_object($userInfo) && isset($userInfo->firstname) && isset($userInfo->lastname))
                <div class=" static-info">
                    <div class="row">
                        <div class="col-md-5 name">Created By:</div>
                        <div class="col-md-7 value">{{$userInfo->firstname}} {{$userInfo->lastname}} (M: {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}})</div>
                    </div>
                </div>  
                @endif
            
            
            <div class=" static-info"><div class="row">
                    <div class="col-md-5 name">Self Order: </div>
                    <div class="col-md-7 value"> {{($orderdata->is_self == 0) ? 'No' : 'Yes'}}</div>
                </div>
            </div>            
            
            </div></td>
        
        <td class="col-md-4">   <div class="portlet-body  ">
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Retailer Name: </div>
                    <div class="col-md-7 value"> {{$orderdata->shop_name}} </div>
                </div></div>
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Retailer Code: </div>
                    <div class="col-md-7 value"> {{$orderdata->le_code}} </div>
                </div></div>
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Name: </div>
                    <div class="col-md-7 value"> {{$orderdata->firstname}} {{$orderdata->lastname}}</div>
                </div></div>
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Phone: </div>
                    <div class="col-md-7 value"> {{$orderdata->phone_no}}</div>
                </div></div>
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Email:</div>
                    <div class="col-md-7 value"> {{$orderdata->email}} </div>
                </div> </div>

                 <div class="static-info"><div class="row">
                    <div class="col-md-5 name">GSTIN No:</div>
                    <div class="col-md-7 value"> {{$gstin}} </div>
                </div> </div>

            </div></td>
        
        <td class="col-md-4">
            <div class="portlet-body ">
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Payment Method: </div>
                    <div class="col-md-7 value"> {{$orderdata->payment_method_id}} </div>
                </div></div>
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Status: </div>
                    <div class="col-md-7 value"> {{$orderdata->payment_status_id}}</div>
                </div></div>
                <div class="static-info"><div class="row">
                    <div class="col-md-5 name"> Currency: </div>
                    <div class="col-md-7 value"> {{$orderdata->code}}</div>
                </div></div>
            </div></td>
        
    <tr>    
    
    
    
    
    
        </tbody>  </table></div>

    <div class="orderdet">    
        
<table class="table table-bordered thline">    

<thead> <tr><th> Billing Address </th> <th> Shipping Address </th> </tr></thead>   
<tbody>
    <tr>
    
        <td class="col-md-4"> <div class="portlet-body ">
              <div class="static-info"><div class="row">
                    <div class="col-md-12 value">
						{{isset($billing->company) ? $billing->company : ''}} <br>
						{{isset($billing->fname) ? $billing->fname : ''}} {{isset($billing->lname) ? $billing->lname : ''}} <br>
                        {{isset($billing->addr1) ? $billing->addr1 : ''}} <br>
                        @if(!empty($billing->addr2)) {{$billing->addr2}} <br> @endif 
                        @if(!empty($billing->locality)) {{$billing->locality}}, @endif @if(!empty($billing->landmark)){{$billing->landmark}}, @endif {{isset($billing->city) ? $billing->city : ''}} ,  
                        {{isset($billing->state_name) ? $billing->state_name : ''}} , 
                        {{isset($billing->postcode) ? $billing->postcode : ''}}  
                        {{isset($billing->country_name) ? $billing->country_name : ''}} <br>
                        {{isset($billing->telephone) ? 'T: '.$billing->telephone : ''}}{{!empty($billing->mobile) ? ' | M: '.$billing->mobile : ''}}
                    </div>
                </div></div>
            </div></td>
        
        <td class="col-md-4"><div class="portlet-body ">
                <div class="static-info"><div class="row">
                    <div class="col-md-12 value">
						{{isset($shipping->company) ? $shipping->company : ''}} <br>
						{{isset($shipping->fname) ? $shipping->fname : ''}} {{isset($shipping->lname) ? $shipping->lname : ''}} <br>
                        {{isset($shipping->addr1) ? $shipping->addr1 : ''}} <br>
                        @if(!empty($shipping->addr2)) {{$shipping->addr2}} <br> @endif  
                        @if(!empty($shipping->locality)) {{$shipping->locality}}, @endif @if(!empty($shipping->landmark)){{$shipping->landmark}}, @endif {{isset($shipping->city) ? $shipping->city : ''}} ,
                        {{isset($shipping->state_name) ? $shipping->state_name : ''}} ,
                        {{isset($shipping->state_code) ? $shipping->state_code : ''}} , 
                        {{isset($shipping->postcode) ? $shipping->postcode : ''}} 
                        {{isset($shipping->country_name) ? $shipping->country_name : ''}} <br>
{{isset($shipping->telephone) ? 'T: '.$shipping->telephone : ''}}{{!empty($shipping->mobile) ? ' | M: '.$shipping->mobile : ''}}
                    </div></div>
                </div>
            </div></td>
        
        
    </tr>    
    
    
    
    
    
</tbody>    
        </table></div>
        
 
<div class="row">
<div class="col-md-12 col-sm-12">
<div class="table-responsive">
<table class="table table-hover table-bordered table-striped thline">
                        <thead>
                            <tr>
                                <th> SNo </th>
                                <th> SKU# </th>
                                <th> Product Name</th>
                                <th> HSN Code</th>
                                <th> Ordered Qty </th>
                                <th> MRP </th>
                                <th> Unit Base Price</th>
                                @if($orderdata->discount_before_tax==1)
                                <th> Cost </th>                                
                                <th> Discount </th>
                                @endif
                                <th> Net Value </th>
                                <th> Tax %</th>
                                <th> Tax Value</th>
                                @if($orderdata->discount_before_tax==0)     
                                <th> Discount </th>
                                @endif
                                <th style="text-align:right;"> Total </th>
                                <th>Fill Rate%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            $totalQty = 0;
                            $totalInvoiced = 0;
                            $totalShipped = 0;
                            $totalDiscount = 0;
                            $sumOfSubtotal = 0;
                            $sumOfSubTax = $sumOfNetValue = 0;
                            $discount_before_tax_total = 0;
                            ?>
                            @foreach($products as $product)
                           
                            <?php $tax_percent = (isset($productTaxArr[$product->product_id]) ? $productTaxArr[$product->product_id] : 0);
                            $orderQty = $product->qty;
                            $unitBasePrice = ((round($product->total,2)/(100+$tax_percent))*100)/$orderQty;
                            $returnedQty = isset($returns[$product->product_id]) ? $returns[$product->product_id] : 0;
                            $cancelQty = isset($itemCancelArr[$product->product_id]) ? $itemCancelArr[$product->product_id] : 0;
                            $shippedQty = isset($itemShippedArr[$product->product_id]) ? $itemShippedArr[$product->product_id] : 0;
                            $pendingQty = ($orderQty - ($cancelQty + $shippedQty));
                            $invQty = isset($itemInvoiceArr[$product->product_id]) ? $itemInvoiceArr[$product->product_id] : 0;

                            $fillRate = Utility::getFillRate($invQty, $product->qty);
                            $netValue = $unitBasePrice * $orderQty;
                            if($orderdata->discount_before_tax == 1){
                                $unitBasePrice = ((round($product->cost,2)) / $product->qty);
                            }else{
                                $unitBasePrice = ((round($product->total,2)/(100+$tax_percent))*100)/$orderQty;
                            }
                            $discount_before_tax_total += $product->cost;
                            ?>
                            <tr>
                                <td>{{$sno++}}</td>
                                <td>{{$product->sku}}</td>
                                <td>{{$product->pname}}
                                <br>
                                @if(isset($product->starname))
                                
                                <?php $starcolors = explode(',',$product->starcolor);
                                foreach($starcolors as $starcolor) {

                                    ?>
                                <span style="font-size:10px; font-weight:bold;"><i style="color:{{$starcolor}};" class="fa fa-star" aria-hidden="true"></i></span>

                                <?php

                                }
                                ?>

                                @endif
                                </td>
                                <td>{{$product->hsn_code}}</td>
                                <td>{{(int)$orderQty}}<br>
                                    <span style="font-size:10px; font-weight:bold;">
                                @if($shippedQty)
                                Shipped:{{(int)$shippedQty}} <br>
                                @endif
                                
                                @if(isset($itemInvoiceArr[$product->product_id]))
                                Invoiced:{{(int)$itemInvoiceArr[$product->product_id]}} <br>
                                @endif
                                
                                @if($cancelQty)
                                Cancelled:{{(int)$cancelQty}}
                                @endif
                                
                                @if($returnedQty)
                                Returns:{{(int)$returnedQty}}
                                @endif
                                
                                @if($pendingQty)
                                Pending:{{(int)$pendingQty}}
                                @endif

                                </span></td>
                                <td>{{$orderdata->symbol}} {{number_format($product->mrp, 2)}}</td>
                                <td>{{$orderdata->symbol}} 
                                    {{round($unitBasePrice, 2)}}
                                </td>
                                @if($orderdata->discount_before_tax==1)
                                <td>{{$orderdata->symbol}} {{number_format($product->cost,2)}}</td>
                                <td>{{$orderdata->symbol}} {{($product->discount_type=='value') ? number_format($product->discount_amt,2) : number_format($product->discount_amt,2).'('.$product->discount.'%)'}}</td>
                                @endif
                                <td>{{$orderdata->symbol}} {{number_format($netValue, 2)}}</td>
                                <td>
                                {{(isset($productTaxArr[$product->product_id]) ? (float)$productTaxArr[$product->product_id].'%' : '0.0%')}}
                                </td>
                                <td>{{$orderdata->symbol}} {{round($product->tax, 2)}}</td>
                                @if($orderdata->discount_before_tax==0)
                                <td>{{$orderdata->symbol}} {{($product->discount_type=='value') ? number_format($product->discount_amt,2) : number_format($product->discount_amt,2).'('.$product->discount.'%)'}}</td>
                                @endif
                                <td align="right">{{$orderdata->symbol}} {{round($product->total, 2)}}</td>
                                <td>{{number_format($fillRate, 2)}}</td>
                                <?php 
                                // if($orderdata->discount_before_tax == 1){
                                //     $unitBasePrice = ((round($product->cost,2)) / $product->qty);
                                //     $netValue = $unitBasePrice * $orderQty;
                                // }
                                $sumOfNetValue = $sumOfNetValue + $netValue;
                                $totalQty = ($totalQty + $product->qty);
                                $totalInvoiced = $totalInvoiced + (isset($itemInvoiceArr[$product->product_id]) ? (int)$itemInvoiceArr[$product->product_id] : 0);
                                $totalDiscount = $totalDiscount + $product->discount_amt;
                                $sumOfSubtotal = ($sumOfSubtotal + $product->total);
                                $sumOfSubTax = ($sumOfSubTax + $product->tax);
                                $totalShipped = $totalShipped +  (isset($itemShippedArr[$product->product_id]) ? $itemShippedArr[$product->product_id] : 0);
                                ?>
                            </tr>
                            @endforeach
                            
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>                                                  
                                <td>&nbsp;</td>                   
                                <td class="bold">Total</td>
                                <td class="bold">{{$totalQty}}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                @if($orderdata->discount_before_tax==1)
                                <td>&nbsp;</td>
                                <td class="bold"> {{$orderdata->symbol}} {{number_format($totalDiscount, 2)}}</td>
                                @endif
                                <td>{{number_format($sumOfNetValue, 2)}}</td>
                                <td>&nbsp;</td>
                                <td class="bold">{{$orderdata->symbol}} {{number_format($sumOfSubTax, 2)}}</td>
                                @if($orderdata->discount_before_tax==0)
                                <td class="bold"> {{$orderdata->symbol}} {{number_format($totalDiscount, 2)}}</td>
                                @endif
                                <td class="bold" align="right">{{$orderdata->symbol}} {{number_format($sumOfSubtotal, 2)}}</td>
                                <td>&nbsp;</td>
                            </tr>                            
                        </tbody>
                    </table>
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<form id="order_status_form" action="/salesorders/updateOrderStatus" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_method" value="POST">
			<input type="hidden" name="gds_order_id" id="gds_order_id" value="{{$orderdata->gds_order_id}}">
			<div class=" portlet-fit box2">
				<strong>Order Status</strong>
				<p class="text-danger" id="ajaxResponse"></p>
				
				<div class="form-group">
					<div class="input-icon right">
						<i class="fa fa-calendar"></i>
						<input type="text" class="form-control" name="gds_comment_date" id="gds_comment_date" readonly="readonly" value="{{date('m/d/Y H:i:s')}}" placeholder="Comment Date">
					</div>							
				</div>
				
				<div class="form-group">
					<select  name="orderStatus" id="orderStatus" class="form-control">
						<option value="">Select Status</option>
						@if(isset($statusMatrixArr) && is_array($statusMatrixArr))
							@foreach($statusMatrixArr as $statusId=>$statusValue)
							<option value="{{$statusId}}">{{($statusId == '17020' ? 'GENERATE PICKLIST' : $statusValue)}}</option>
							@endforeach
						@endif						
					</select>
				</div>
				
				<div class="form-group">
					<textarea  class="form-control" rows="3" id="order_comment" name="order_comment" placeholder="Enter your comment"></textarea>
				</div>
				@if(isset($checkuserfeature) && $checkuserfeature == 1)
				<div class="">
					<input  type="submit" id="btnSubmit" class="btn green-meadow" onclick="validateForm();" value="Submit">
				</div>
                @endif
			</div>
		</form>
	</div>
    
    <div class="col-md-6">
        <div class="well">
			<div class="row static-info align-reverse">
                <div class="col-md-8 name"> Sub Total: </div>
                <div class="col-md-3 value"> {{$orderdata->symbol}} {{ ($orderdata->discount_before_tax==1) ? number_format($discount_before_tax_total, 2) :number_format($sumOfNetValue, 2)}}</div>
            </div>
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Shipping & Handling: </div>
                <div class="col-md-3 value"> {{$orderdata->symbol}} {{number_format($orderdata->ship_total, 2)}} </div>
            </div>

            @if(isset($taxSummary) && is_array($taxSummary) && count($taxSummary) > 0)
            @foreach($taxSummary as $tax)
            <div class="row static-info align-reverse">
                <div class="col-md-8 name">{{$tax['name'].' ('.(float)$tax['tax'].'%)'}}:</div>
                <div class="col-md-3 value">{{$orderdata->symbol}} {{number_format($tax['tax_value'], 2)}} </div>
            </div>  
            @endforeach
            @endif
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Additional Discount: </div>
                <div class="col-md-3 value">-{{$orderdata->symbol}} {{number_format($orderdata->discount_amt, 2)}} </div>
            </div>           
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Grand Total: </div>
                <div class="col-md-3 value"> {{$orderdata->symbol}} {{number_format($orderdata->grand_total, 2)}} </div>
            </div>
        </div>
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
@section('script')
<script type="text/javascript">
$('#gds_comment_date').datepicker({ minDate: "{{date('m/d/Y',strtotime($orderdata->order_date))}} "});
</script>
@stop
