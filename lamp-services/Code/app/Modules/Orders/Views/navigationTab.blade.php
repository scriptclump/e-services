<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="POST">
<div class="portlet-body">
	<div class="tabbable-line">
		<ul class="nav nav-tabs nav-tabs-lg">
			<li class="{{($actionName == 'orderDetail' ? 'active' : '')}}"> <a title="Order Details" href="#tab_1" data-toggle="tab" onclick="getOrderDetail('{{$orderdata->gds_order_id}}')"> Details </a> </li>
			<li><a href="#tab_3" title="Invoices" data-toggle="tab" onclick="invoiceList({{$orderdata->gds_order_id}});"> Invoices <span class="badge badge-success" id="totalInvoices">0</span> </a> </li>
			
			<li> <a title="Shipments" title="Shipments" href="#tab_8" data-toggle="tab" onclick="shipmnentList({{$orderdata->gds_order_id}});"> Shipments <span class="badge badge-success" id="totalShipments">0</span></a> </li>
			@if($actionName == 'addShipment' || $actionName== 'shipmentDetail')
			<li class="active"><a href="#tab_2" title="{{$tabHeading}}" data-toggle="tab">{{$tabHeading}}</a></li>
			@endif
			@if($actionName == 'createInvoice' || $actionName== 'createInvoiceNew' || $actionName== 'invoiceDetail')
			<li class="active"><a title="{{$tabHeading}}" href="#tab_2" data-toggle="tab">{{$tabHeading}}</a></li>
			@endif
			<li><a href="#tab_6" title="Cancellations" data-toggle="tab" onclick="cancelList({{$orderdata->gds_order_id}});"> Cancellations <span class="badge badge-success" id="totCancelled">0</span></a> </li>
			@if($actionName == 'createCancellation' || $actionName== 'cancelDetail')
			<li class="active"><a title="{{$tabHeading}}" href="#tab_2" data-toggle="tab">{{$tabHeading}}</a></li>
			@endif
			<li><a href="#tab_4" title="Returns" data-toggle="tab" onclick="returnOrderList('{{$orderdata->gds_order_id}}')">Returns <span class="badge badge-success" id="totReturns">0</span></a> </li>

			@if($actionName == 'createReturn' || $actionName== 'returnDetail')
			<li class="active"><a href="#tab_2" data-toggle="tab">{{$tabHeading}}</a> </li>
			@endif
			<li><a href="#tab_7" title="Refunds" data-toggle="tab" onclick="refundOrderList('{{$orderdata->gds_order_id}}')">Refunds <span class="badge badge-success" id="totRefunds">0</span></a> </li>
			<li><a href="#tab_5" title="History" data-toggle="tab" onclick="commentHistoryList({{$orderdata->gds_order_id}});"> History <span class="badge badge-success" id="totalComments">0</span> </a> </li>
            <li><a href="#payments" data-toggle="tab" onclick="collectionGrid({{$orderdata->gds_order_id}});"> Payments <span class="badge badge-success" id="totalPayments">0</span></a> </li>
            <li><a href="#verification" data-toggle="tab" onclick="verificationGrid({{$orderdata->gds_order_id}});"> Verification <span class="badge badge-success" id="totVerification">0</span></a> </li>
            <li><a href="#pendingpayments" data-toggle="tab" onclick="PendingPaymentHistoryGrid({{$orderdata->gds_order_id}});"> NCT History<span class="badge badge-success" id="totNctHistory">0</span></a> </li>
             @if(isset($roll_back) && $roll_back==1)
            <li><a href="#roll_back" title="RollBack Order" data-toggle="tab"> RollBack <span class="badge badge-success" id="rollbackList">0</span></a> </li>
            @endif
		</ul>
	</div>
</div>

<div class="tab-content">
	<div class="tab-pane {{(($actionName == 'orderDetail') ? 'active' : '')}}" id="tab_1">              
	</div>
	
	<div class="tab-pane" id="tab_8">
		<div class="table-container">
			<?php /*@if($actionName != 'addShipment')*/?>
			@if($orderdata->order_status_id !='17009' && $orderdata->order_status_id !='17015' && $orderdata->order_status_id != '17014')
			<div class="row">
			<div class="col-md-12 marbot">
			<?php /*<a class="btn green-meadow pull-right" type="button" href="/salesorders/addshipment/{{$orderdata->gds_order_id}}">CREATE SHIPMENT</a>*/?></div></div>
			@endif			
			<?php /*@endif*/?>
			 
			<table id="shipmentList" class="table-scrolling"></table>
		</div>
	</div>
	
	<div class="tab-pane {{(($actionName != 'orderDetail') ? 'active' : '')}}" id="tab_2">
	@if($actionName == 'addShipment')	
	   @include('Orders::Form.createShipmentForm')
	@endif
	
	@if($actionName == 'shipmentDetail')	
	   @include('Orders::Form.shipmentDetailForm')
	@endif
	
	@if($actionName == 'createCancellation')	
	   @include('Orders::Form.cancellationForm')
	@endif
	
	@if($actionName == 'cancelDetail')	
	   @include('Orders::Form.cancelDetailForm')
	@endif
	
	@if($actionName == 'createInvoice')	
	   @include('Orders::Form.createInvoiceForm')
	@endif

	@if($actionName == 'createInvoiceNew')	
	   @include('Orders::Form.createInvoiceNewForm')
	@endif
	
	@if($actionName == 'invoiceDetail')	
	   @include('Orders::Form.invoiceDetailForm')
	@endif
	@if($actionName == 'createReturn')	
	   @include('Orders::Form.createReturnForm')
	@endif
        @if($actionName == 'returnDetail')	
	   @include('Orders::Form.returnDetailForm')
	@endif
        
	</div>
	
	<div class="tab-pane" id="tab_3">
		<div class="table-container">
			<?php /*@if($actionName != 'createInvoiceNew')*/?>	
			<?php /*
			@if($orderdata->order_status_id !='17009' && $orderdata->order_status_id !='17015' && $orderdata->order_status_id != '17014')
<div class="row">			<div class="col-lg-12 marbot"><a class="btn green-meadow pull-right" type="button" href="/salesorders/createinvoice/{{$orderdata->gds_order_id}}">CREATE INVOICE</a></div></div>
			@endif
			 */?>
		<?php /*	@endif*/?>
			<div>&nbsp;</div>
			<table id="invoiceList" class="table-scrolling"></table>
		</div>
	</div>
	
	<div class="tab-pane {{(($actionName == 'orderDetail') ? 'active' : '')}}" id="tab_1">              
	</div>
	<div class="tab-pane {{(($actionName == 'createReturn') ? 'active' : '')}}" id="tab_9">
	</div>
	<div class="tab-pane" id="tab_4">
		<div class="table-container">
			<?php /*@if($actionName != 'addShipment')*/
				$list_of = array('17006','17007','17008','17021','17020','17019','17018','17023');
				if(in_array($orderdata->order_status_id,$list_of)){?>
					<div class="row" id="createreturn_div" style="display: none;">
					<div class="col-md-12 marbot">
					<a class="btn green-meadow pull-right" type="button" href="/salesorders/createreturn/{{$orderdata->gds_order_id}}">CREATE RETURN</a></div></div>
			<?php } 
			?>			 
			<table id="returnList" class="table-scrolling"></table>
		</div>
	</div>
	
	<div class="tab-pane" id="tab_7">
		<div class="table-container">								
			<div>&nbsp;</div>
			<table id="returnOrderList" class="table-scrolling"></table>
		</div>
	</div>  
	
	<div class="tab-pane" id="verification">
		<div class="table-container">								
			<div>&nbsp;</div>
			<table id="verificationList" class="table-scrolling"></table>
		</div>
	</div>  
	
	<div class="tab-pane" id="tab_5">
		<div class="table-container">
       <div>&nbsp;</div>
		   <table id="commentList" class="table-scrolling"></table>
		</div>
	</div>
    <div class="tab-pane" id="tab_6">
		<div class="table-container"> 
			<?php /*@if($actionName != 'createCancellation')*/
				$cancelArr = array('17001', '17020', '17021', '17013', '17002', '17003', '17004', '17005', '17022', '17023');
			?>
			@if(in_array($orderdata->order_status_id, $cancelArr))
          <div class="row">  <div class="col-lg-12 marbot"> <a class="btn green-meadow pull-right" type="button" href="/salesorders/addOrderCancelation/{{$orderdata->gds_order_id}}">CANCEL ORDER</a> </div></div>
			@endif 
			<?php /*@endif*/?>
			<table id="cancelList" class="table-scrolling"></table>
		</div>
	</div>



 <div class="tab-pane" id="roll_back">

  <div class="row">  <div class="col-lg-12 marbot"> <a class="btn green-meadow pull-right" type="button" onclick="rollbackList({{$orderdata->gds_order_id}});">Roll Back</a> </div></div>
<span id="success_message"></span>

</div>

	
	<div class="tab-pane" id="pendingpayments">
		<div class="table-container">
       <div>&nbsp;</div>
		   <table id="paymentHistoryList" class="table-scrolling"></table>
		</div>
	</div>
	

<?php 
	$paymentArr = array('17007', '17023');
?>

    <div class="tab-pane" id="payments" class="table-scrolling">
@if(in_array($orderdata->order_status_id, $paymentArr))
		<div class="row">

<div class="col-md-6 text-left">
<h4 class="modal-title">Collection Details 
&nbsp; <strong style="color: #337bb6;">[ Balance Amount : <span class='balanceAmt'></span> ]</strong></h4>
</div>


		<div class="col-md-6 text-right">
		<a title="Collection" data-toggle="modal" href="#collection" class="btn green-meadow collectionPopup">Add Payment</a>
		</div>
		</div>        
@endif
		<br />	
		<div class="row">
		<div class="col-md-12">
		<div class="table-responsive">

		<table class="collectionGrid table-scrolling"></table>

		</div>
		</div>
		</div>        
        

        
        
        
	</div>
 @include('Orders::collection')
 @include('Orders::editCollection')
 @include('Orders::paymentsScript')                                         


</div>