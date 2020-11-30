@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/picklist/index">Picklist</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Update</li>
        </ul>
    </div>
</div>

<div class="row">
<div class="col-md-12 col-sm-12">
  <div class="portlet light tasks-widget">
	 <div class="portlet-title">
		<div class="caption">UPDATE ORDER STATUS</div>
                <div class="actions">
                    &nbsp;
                </div>
	 </div>
	<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif
    <div class="portlet-body">
		
		<div class="row">
		<div class="col-md-12">
            <form id="picklist_form" action="/picklist/update" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="POST">
            <div class="row">           
                <div class="col-md-12">
                    <h4>Product Description</h4>
                    <table class="table table-striped table-bordered table-advance table-hover thline">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>EAN</th>
                            <th>MRP</th>
                            <th>Price</th>
                            <th>Pick Qty</th>
                            <th>Shipment Qty</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    @if(is_array($ordersArr) && count($ordersArr) > 0)
                        @foreach($ordersArr as $order_code=>$orders)
                        <tr>
                            <td bgcolor="#ccc" colspan="8">{{$order_code}}</td>
                        </tr>
                        
                        @foreach($orders as $order)
                        <tr>
                            <td>{{$order->sku}}</td>
                            <td>{{$order->pname}}</td>
                            <td>{{$order->upc}}</td>
                            <td>{{$order->mrp}}</td>
                            <td>{{$order->price}}</td>                            
                            <td>{{$order->qty}}</td>
                            <td>
                            <input type="hidden" name="products[{{$order->le_wh_id}}][{{$order->product_id}}]" value="{{$order->product_id}}">
                            <input type="number" size="3" min="1" max="{{$order->qty}}" class="form-control" name="shipment_qty[{{$order->gds_order_id}}][{{$order->product_id}}]" value="{{$order->qty}}"></td>
                            <td><input type="text" class="form-control" name="comment[{{$order->gds_order_id}}][{{$order->product_id}}]" value=""></td>
                        </tr>
                        @endforeach
                        @endforeach
                    @else
                    <tr>
                        <td colspan="8">No Record Found</td>
                    </tr>
                    @endif    
                    </table>
                </div>
            </div>
                        
            <div class="row">
                <div class="col-md-12">                 
                    <h4>Tracking Information</h4>
                    <table class="table table-striped table-bordered table-advance table-hover thline" id="track_data">
                        <thead>
                            <tr>
                                <th>Carrier</th>
                                <th>Service Name</th>
                                <th>Tracking Number</th>
                                <th>Vehicle Number</th>
                                <th>Representative Name</th>
                                <th>Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <tr>
                                <td width="20%">                                    
                                    <select id="courier" name="courier" class="form-control">
                                       <option value="">Select</option>    
                                        @foreach ($couriers as $key => $value)
                                          <option value="{{$value->carrier_id}}">{{$value->carrier}}</option> 
                                        @endforeach
                                    </select>
                                </td>
                                <td width="20%" id="service_selectbox">
                                    <select id="service_name" name="service_name" class="form-control">
                                        <option value="">Select Service</option>  
                                    </select>
                                </td>
                                <td width="15%"><input type="text" name="track_number" class="form-control"></td>
                                <td width="15%"><input type="text" id="vehicle_number" name="vehicle_number" class="form-control"></td>
                                <td width="15%"><input type="text" id="representative_name" name="representative_name" class="form-control"></td>
                                <td width="15%"><input type="number" pattern="\d{10}" id="contact_num" name="contact_num" class="form-control" maxlength="10"></td>
                                
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>

                        
            <div class="row">
                
                <div class="col-md-7">
                    <div class="box2">
                     
                    
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Comment</h4>
                                <textarea name="comments" id="shipment_comment" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="">
                                    <input type="submit" name="btnPickedSubmit" id="btnPickedSubmit" class="btn green-meadow" value="Picked">
                                    <input type="submit" id="btnInvoiceSubmit" class="btn green-meadow" value="Generate Invoice">
                                    <input type="submit" id="btnPrintSubmit" class="btn green-meadow" value="Print Invoice">
                                    <input type="submit" id="btnPrintChallanSubmit" class="btn green-meadow" value="Print Delivery Challan">
                                    <input type="submit" id="btnDeliveredSubmit" class="btn green-meadow" value="Delivred">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>              
        </form> 




		</div>
	</div>
	

			</div>
		</div>
	</div>


    </div>
  </div>
</div>
@stop

@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders.js') }}" type="text/javascript"></script>
<script type="text/javascript">
 
    $(document).ready(function() {
                        
        $('#courier').change(function () {
            getCarrierServices();
        });
        
        $("#picklist_form").validate({
            rules: {                
                courier:"required",
                service_name:"required",
                track_number:"required",
            },
            
            submitHandler: function(form) {
                var form = $('#order_shipment_form');
                   $.ajax({
                            url: form[0].action,
                            type: "POST",
                            data: form.serialize(),
                            dataType: 'json',
                            success: function(data) {
                                if(data.status == 200) {
                                    $('#shippedAjaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);                                   
                                    window.location.href='/salesorders/shipmentdetail/'+data.gds_ship_grid_id;
                                }
                                else {
                                    $('#shippedAjaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
                                }
                            },
                            error:function(response){
                                $('#shippedAjaxResponse').html('Unable to saved comment');
                            }
                    }); 
            }
        });
    }); 
</script>
@stop       