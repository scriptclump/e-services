@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Sales Orders</li>
        </ul>
    </div>
</div>
<?php 
$picklistBtn = $shipmentBtn = $reassignordersBtn = $challanBtn = $invoiceBtn = $deliveredBtn = $deliveryExeBtn =$deliveryBtn= $dsrBtn = $stockTransBtn = $stockSheetBtn = $stockHubBtn = $outForDelivery = $stockTransHubDCBtn = $stockInDc= $stockDCBtn = $pendingpayments = 'true';

if ($status === 'allorders'){
  $invoiceBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $shipmentBtn = 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
else if ($status === 'picklist'){
  $invoiceBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $picklistBtn = 'true';
  $reassignordersBtn = 'false';
}
elseif($status === 'open'){
  $shipmentBtn = 'false';
  $invoiceBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'dispatch'){
  $picklistBtn = 'false';
  $invoiceBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'true';
}
elseif($status === 'invoiced'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $stockTransBtn = 'true';
  $stockSheetBtn = 'true';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'partial'){
  $picklistBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'completed'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'hold'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'true';
  $deliveryExeBtn = 'true';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'delivered' || $status === 'partialdelivered'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'return'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'returnapproval'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'missingquantities'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'damagedquantities'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'approvedMissingquantities'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'approvedDamagedquantities'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'shortcollections'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}

elseif($status === 'cancelbycust' || $status === 'cancelbyebutor'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'stocktransit'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'true';
  $stockHubBtn = 'true';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'stockhub'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'true';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'ofd'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'true';
  $deliveryBtn= 'true';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'rah'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'true';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
elseif($status === 'stocktransitdc'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'true';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'true';
  $reassignordersBtn = 'false';
}
elseif($status === 'stockindc'){
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}
else if($status == 'nct') {
  $picklistBtn = 'false';
  $shipmentBtn = 'false';
  $challanBtn = 'false';
  $deliveredBtn = 'false';
  $invoiceBtn = 'true';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $dsrBtn = 'false';
  $stockTransBtn = 'false';
  $stockSheetBtn = 'false';
  $stockHubBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockInDc = 'false';
  $stockDCBtn = 'false';
  $reassignordersBtn = 'false';
}elseif($status == 'partialcancel')
{
  $shipmentBtn = 'false';
  $stockTransBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockHubBtn = 'false';
  $stockDCBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $stockSheetBtn = 'false';
  $picklistBtn = 'false';
  $challanBtn = 'false';
  $reassignordersBtn = 'false';
}elseif($status == 'unpaid')
{
  $shipmentBtn = 'false';
  $stockTransBtn = 'false';
  $stockTransHubDCBtn = 'false';
  $stockHubBtn = 'false';
  $stockDCBtn = 'false';
  $deliveryExeBtn = 'false';
  $deliveryBtn= 'false';
  $stockSheetBtn = 'false';
  $picklistBtn = 'false';
  $challanBtn = 'false';
  $reassignordersBtn = 'false';
}

if($skipSit && $status === 'invoiced') {
  $stockTransBtn = 'false';
  $deliveryExeBtn = 'true';
  $deliveryBtn= 'false';
  $reassignordersBtn = 'false';
}

?>
@if($order_count>0) 
  <div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Your operations will be blocked soon, Please clear your pending orders.
  </div>
@endif

<div class="row">
<div class="col-md-12 col-sm-12">
  <div class="portlet light tasks-widget">
	 <div class="portlet-title">
		  <div class="caption col-md-2">Sales Orders</div>
  <div class="col-md-3" style="margin-top:8px">
    <div class="form-group">
        <div class="caption"></div>
        <input type="hidden" id="hidden_buid" name="hidden_buid" value='<?php if(Session()->has('business_unitid')){ echo Session::get('business_unitid'); }else{ echo '';}?>'>
        <select id="business_unit_id" name="business_unit_id" class="form-control business_unit_id select2me" style="margin-left: -71px"></select>       
    </div>
</div>
<div class="col-md-2" style="margin-top:8px">
    <div class="form-group">
        <div class="caption"></div>
        <input type="hidden" id="" name="" value=''>
        <select id="primary_secoundary_sales_id" name="primary_secoundary_sales_id" class="form-control select2me" style="margin-left: -71px">
          @if(isset($primaryAccess) && $primaryAccess==1) 
          <option value="1" @if ($sales_type == 1) {{ 'selected'}} @endif>Primary and Intermediate Sales</option>
          @endif
          <option value="2" @if ($sales_type == 2) {{ 'selected'}} @endif>Secondary Sales</option>
        </select>       
    </div>
</div>
<div class="actions">
<div class="btn-group">
<button type="button" class="btn green-meadow">Print</button>
<button type="button" class="btn green-meadow dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-angle-down"></i></button>
<ul class="dropdown-menu" role="menu">
<li>
@if($picklistBtn!=='false')
<a class="btn green-meadow" dispaly='none' href="#printPickList" data-toggle="modal" id="printPL">Print Picklist</a>
@endif
</li>
<li>
@if($picklistBtn!=='false' and $generateTripSheet == true)
<a class="btn green-meadow" dispaly='none' href="#printTripsheet" data-toggle="modal" id="printTR">Generate Tripsheet</a>
@endif
</li>
<li>
@if($challanBtn!=='false')
<a class="btn green-meadow" id="challan" {{$challanBtn}}>Print Challan</a>
@endif
</li>
<li>
@if($invoiceBtn!=='false')
<a class="btn green-meadow" id="invoice">Print Invoice</a>
@endif
</li>
@if($stockSheetBtn!=='false')
<li>
@if($status == 'stocktransitdc')
<a target="_blank" class="btn green-meadow" href="/salesorders/printtriphub">Print Trip Sheet</a>
<a class="btn green-meadow downloadTripSheet" href="#downloadTripSheet" trans-type="stHubDc" data-toggle="modal">Download Trip Sheet (XLS)</a>
<a class="btn green-meadow" href="/salesorders/triphubpdf">Download Trip Sheet (PDF)</a>
@endif
</li>
@endif
@if($status == 'stocktransit')
<li>
<a target="_blank" class="btn green-meadow" href="/salesorders/printtrip">Print Trip Sheet</a>
<a class="btn green-meadow downloadTripSheet" trans-type="stDcHub" href="#downloadTripSheet" data-toggle="modal">Download Trip Sheet (XLS)</a>
<a class="btn green-meadow" href="/salesorders/trippdf">Download Trip Sheet (PDF)</a>
</li>
@endif
</ul>
</div>
                  
                  @if($openToInv && $status === 'open')
                    <a class="btn green-meadow" id="genOpnInv" {{$shipmentBtn}}>Create Invoice</a>
                  @endif

                 

                  @if($shipmentBtn!=='false')
                    <a class="btn green-meadow" id="genShipment" {{$shipmentBtn}}>Create Shipment</a>
                  @endif
                 
                  @if($stockTransBtn!=='false' && $stDCHub!==false)
                  <button type="button" href="#stockTransfer" data-toggle="modal" class="btn green-meadow stock_transfer" transfer_type="dctohub">Stock Transfer</button>
                  @endif
                  
                  @if($stockTransHubDCBtn!=='false' && $stHubDC!==false)
                  <button type="button" href="#stockTransfer" data-toggle="modal" class="btn green-meadow stock_transfer" title="Stock Transfer Hub to DC" transfer_type="hubtodc">ST Hub-DC </button>
                  @endif

                  @if($stockHubBtn!=='false' && $confSTHub!==false)
                  <button type="button" href="#stockInHub" data-toggle="modal" class="btn green-meadow stock_hub" confirm_type="hub">Confirm Stock Hub</button>
                  @endif

                  @if($stockDCBtn!=='false' && $confSTDC!==false)
                  <button type="button" href="#stockInHub" data-toggle="modal" class="btn green-meadow stock_hub" confirm_type="dc">Confirm Stock at DC</button>
                  @endif
<?php /*
                  @if($deliveredBtn!=='false')
                  <button type="button" href="#markAsDelivered" data-toggle="modal" class="btn green-meadow" id="delivered">Mark as Delivered</button>
                  @endif
*/?>
                  @if($deliveryExeBtn!=='false')
                  <button type="button" href="#AssignDelExecutive" data-toggle="modal" class="btn green-meadow" id="AssignDelExe">Assign Del. Executive</button>
                  @endif

                  @if($deliveryBtn!=='false'  && isset($ofddelivery) && $ofddelivery==1)
                  <button type="button" href="#Delivery" data-toggle="modal" class="btn green-meadow" id="delivery">Delivery</button>
                  @endif
                  
                  @if($reassignordersBtn!=='false'  && isset($checkersListFeature) && $checkersListFeature==1)
                  <button type="button" href="#ReassignOrders" data-toggle="modal" class="btn green-meadow" >Reassign Orders</button>
                  @endif 



<div class="btn-group">
<button type="button" class="btn green-meadow">Reports</button>
<button type="button" class="btn green-meadow dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-angle-down"></i></button>
<ul class="dropdown-menu reportsmarg" role="menu">
<li>
    <a href="#exportOrders" data-toggle="modal" class="btn green-meadow" id="soInvoiceExcel">SO Vs Invoice</a></li>
<li>
    <a href="#orderDetails" data-toggle="modal" class="btn green-meadow" id="exportExcel">Order Detail Report</a>
</li>
<li>
    <a href="#consolidateOrders" data-toggle="modal" class="btn green-meadow" id="consolidateExcel">Line Item Sales Report</a>
</li>

<li>
    <a href="#salesReturnOrders" data-toggle="modal" class="btn green-meadow">Sales Returns</a>
</li>
<li>
    <a href="#salesVouchers" data-toggle="modal" class="btn green-meadow">Finance - Sales Report</a>
</li>
<li>
    <a href="#salesSummary" data-toggle="modal" class="btn green-meadow">Sales Summary Report</a>
</li>
<li>
    <a href="#locReport" data-toggle="modal" class="btn green-meadow">LOC Report</a>
</li>
<li>
    <a href="#orderSummary" data-toggle="modal" class="btn green-meadow" id="emptypopup">Order Summary Report</a>
</li>
@if(isset($dcFCSales) && $dcFCSales==1)    
<li>
    <a href="#dcfcSalesReport" data-toggle="modal" class="btn green-meadow" id="dcfc_sales_report">DC/FC Sales Report</a>
</li>
@endif
@if(isset($apobSales) && $apobSales==1)    
<li>
    <a href="#apobSalesReport" data-toggle="modal" class="btn green-meadow" id="apob_sales_report">APOB/DC Sales Report</a>
</li>
@endif
@if(isset($retailerSales) && $retailerSales==1)    
<li>
    <a href="#retailerSalesReport" data-toggle="modal" class="btn green-meadow" id="retailer_sales_report">Retailer Sales Report Report</a>
</li>
@endif
<li>
  <a href="#ofdOrdersReport" data-toggle="modal" class="btn green-meadow" >OFD Orders Report</a>
</li>
@if(isset($pp_reports) && $pp_reports)
<li>
    <a href="#profitablityPoints" data-toggle="modal" class="btn green-meadow">Profitability Points Report</a>
</li>
@endif
</ul>
</div>
                     

                   <?php /*<a href="javascript:void(0);" id="toggleFilter" class="btn green-meadow"><i class="fa fa-filter"></i></a>*/?>
                </div>
	 </div>

  <div style="display:none; margin-top:5px;" id="ajaxResponse" class="col-md-12 alert alert-danger">
                             
  </div>
	<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

    <div class="portlet-body">
		<?php /*@include('Orders::orderFilter')*/?>
   
   	<div class="row">
			<div class="col-md-12">
				<div class="caption">
					<div class="caption-subject bold font-blue" style="float:left; height:60px;"> FILTER BY :&nbsp</div>
					<div class="caption-helper sorting" style="padding-left: 10px;">
						
          
           @if(isset($openOrders) && $openOrders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/open" class="{{($status == 'open') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders yet to generate Pick List">Open (<span id="allorders">{{$totOpened}}</span>)</a>&nbsp;
           @endif

            @if(isset($pickingOrders) && $pickingOrders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/picklist" class="{{($status == 'picklist') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Picking in Progress">Picklist (<span id="allorders">{{$totPicklist}}</span>)</a>&nbsp;
            @endif
              
            @if(isset($pickingCmptd) && $pickingCmptd==1)            
            <a href="{{$app['url']->to('/')}}/salesorders/index/dispatch" class="{{($status == 'dispatch') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Picking Completed">RTD (<span id="dispatch">{{$totRtoD}}</span>)</a>&nbsp;
            @endif

            @if(isset($invoiceOders) && $invoiceOders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/invoiced" class="{{($status == 'invoiced') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Invoiced Orders Ready to Dispatch from DC to HUB">Invoiced (<span id="invoiced">{{$totInvoiced}}</span>)</a>&nbsp;
            @endif
            
            @if(isset($dCToHub) && $dCToHub==1 && $skipSit!=1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/stocktransit" class="{{($status == 'stocktransit') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="In transit from DC to HUB yet to receive in HUB.">SIT DC-HUB (<span id="stock_transit">{{$totSitDCtoHub}}</span>)</a>&nbsp;
            @endif

             @if(isset($ordersReceived) && $ordersReceived==1 && $skipSit!=1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/stockhub" class="{{($status == 'stockhub') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders Received at HUB">Stock In Hub (<span id="stock_hub">{{$totStockInHub}}</span>)</a>&nbsp;
            @endif

            @if(isset($ordersDelivery) && $ordersDelivery==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/ofd" class="{{($status == 'ofd') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders Out for Delivery">OFD (<span id="ofd">{{$totOutForDelivery}}</span>)</a>&nbsp;
            @endif
           
            @if(isset($ordersHold) && $ordersHold==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/hold" class="{{($status == 'hold') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders on Hold">Hold (<span id="hold">{{$totHold}}</span>)</a>&nbsp;
            @endif
            
            @if(isset($returnedOrders) && $returnedOrders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/returnapproval" class="{{($status == 'returnapproval') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Partially or Full Returned Orders waiting for Hub Incharge Approval">PRAH (<span id="return">{{$totPRAH}}</span>)</a>&nbsp;
            @endif           

            @if(isset($approvedByHub) && $approvedByHub==1 && $hideRetSit!==true)
            <a href="{{$app['url']->to('/')}}/salesorders/index/rah" class="{{($status == 'rah') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Returns Approved by Hub Manager and ready to send back to DC">RAH (<span id="return">{{$totRAH}}</span>)</a>&nbsp;
            @endif

            @if(isset($returnOrders) && $returnOrders==1 && $hideRetSit!==true)
            <a href="{{$app['url']->to('/')}}/salesorders/index/stocktransitdc" class="{{($status == 'stocktransitdc') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Return Orders transferred from HUB to DC and yet to receive in DC">SIT HUB-DC (<span id="return">{{$totSitHubToDc}}</span>)</a>&nbsp;
            @endif            

             @if(isset($returnedOders) && $returnedOders==1 && $hideRetSit!==true)
            <a href="{{$app['url']->to('/')}}/salesorders/index/stockindc" class="{{($status == 'stockindc') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Partial or Full Returned Orders waiting for DC Incharge Approval">PRAD (<span id="return">{{$totPRAD}}</span>)</a>&nbsp;
            @endif
            <br />    

            @if(isset($customerOders) && $customerOders==1)        
            <a href="{{$app['url']->to('/')}}/salesorders/index/cancelbycust" class="{{($status == 'cancelbycust') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Cancelled by Customer">Cancelled-C (<span id="completed">{{$cancelledByCust}}</span>)</a>&nbsp;
            @endif
            
            @if(isset($cancelledOders) && $cancelledOders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/cancelbyebutor" class="{{($status == 'cancelbyebutor') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Cancelled by Ebutor">Cancelled-E (<span id="completed">{{$cancelledByEbutor}}</span>)</a>&nbsp;
            @endif

             @if(isset($partiallyOders) && $partiallyOders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/partialcancel" class="{{($status == 'partialcancel') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Partially Cancelled">PC (<span id="completed">{{$totpartialcnt}}</span>)</a>&nbsp;
            @endif

            @if(isset($fullReturns) && $fullReturns==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/return" class="{{($status == 'return') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Full Returns">FR (<span id="return">{{$totFullReturns}}</span>)</a>&nbsp;
            @endif

             @if(isset($partiallyDelivered) && $partiallyDelivered==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/partialdelivered" class="{{($status == 'partialdelivered') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Partially Delivered">PD (<span id="delivered">{{$totPartialDelivered}}</span>)</a>&nbsp;
            @endif
             
            @if(isset($deliveredOrders) && $deliveredOrders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/delivered" class="{{($status == 'delivered') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Delivered Orders">Delivered (<span id="delivered">{{$totDelivered}}</span>)</a>&nbsp;
            @endif
           
            @if(isset($missingQty) && $missingQty==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/missingquantities" class="{{($status == 'missingquantities') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Returns With Missing Quantities">RWMQ(<span id="RWMQ">{{$totRWMQ}}</span>)</a>&nbsp;
            @endif

            @if(isset($damagedQty) && $damagedQty==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/damagedquantities" class="{{($status == 'damagedquantities') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Returns With Damaged Quantities">RWDQ (<span id="RWDQ">{{$totRWDQ}}</span>)</a>&nbsp;
            @endif

            @if(isset($shortCollections) && $shortCollections==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/shortcollections" class="{{($status == 'shortcollections') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders With Short Collections">OWSH(<span id="OWSh">{{$returnDataOWSM}}</span>)</a>&nbsp;
            @endif

            @if(isset($approvedMissing) && $approvedMissing==1)
             <a href="{{$app['url']->to('/')}}/salesorders/index/approvedMissingquantities" class="{{($status =='approvedMissingquantities') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Return Approved With Missing Quantities">RAWMQ(<span id="RAWMQ">{{$totRAWMQ}}</span>)</a>&nbsp;
             @endif

            @if(isset($approvedDamaged) && $approvedDamaged==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/approvedDamagedquantities" class="{{($status =='approvedDamagedquantities') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Return Approved With Damaged Quantities">RAWDQ(<span id="RAWDQ">{{$totRAWDQ}}</span>)</a>&nbsp;
            @endif

            @if(isset($transactionOrders) && $transactionOrders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/nct" class="{{($status == 'nct') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="None Cash Transaction Orders">NCT (<span id="nct">{{$totalPendingPayments}}</span>)</a>&nbsp;
            @endif

            @if(isset($paymentApproval) && $paymentApproval==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/completed" class="{{($status == 'completed') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders which completed Returns and Payment Approval">Completed (<span id="completed">{{$totalCompletedOrders}}</span>)</a>&nbsp;
            @endif
           
            @if(isset($unpaidorders) && $unpaidorders==1)
            <a href="{{$app['url']->to('/')}}/salesorders/index/unpaid" class="{{($status == 'unpaid') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Orders which Payment Status is Unpaid">Unpaid (<span id="unpaid">{{$totUnpaidCnt}}</span>)</a>&nbsp;
            @endif
            <a href="{{$app['url']->to('/')}}/salesorders/index/allorders" class="{{($status == 'allorders' || $status == '') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="All Accepted Orders">All (<span id="allorders">{{$totalOrders}}</span>)</a>&nbsp;

					</div>

				</div>
			</div>
				</div>
		

<div class="portlet-body">
<div class="row">
<div class="col-md-12">
<table id="orderList" ></table>
</div>
</div>
</div>


	

			</div>
		</div>
	</div>


    </div>
  </div>
</div>

@include('Orders::Form.consolidatePopup')
@include('Orders::Form.orderDetailsPopup')
@include('Orders::Form.soInvoicePopup')
@include('Orders::Form.returnReportPopup')
@include('Orders::Form.salesVouchersReportPopup')
@include('Orders::Form.salesSummaryPopup')
@include('Orders::Form.locReportPopup')
@include('Orders::Form.orderSummaryPopup')
@include('Orders::Form.dcfcSalesReportPopup')
@include('Orders::Form.apobSalesReportPopup')
@include('Orders::Form.retailerSalesReportPopup')
@include('Orders::Form.ofdOrdersListPopup')
@if(isset($pp_reports) && $pp_reports)
  @include('Orders::Form.profitablityPointsPopup')
@endif

  <div style="display:none; margin-top:5px;" id="collectionAjaxResponse" class="col-md-12 alert alert-danger">
                             
  </div>

@include('Orders::Form.markAsDeliveredPopup')
@include('Orders::Form.stockTransferPopup')
@include('Orders::Form.stockInHubPopup')
@include('Orders::Form.assgnToDeliveryExecutivePopup')

@include('Orders::Form.printPicklistPopup')
@include('Orders::Form.generateTripsheetPopup')
@include('Orders::Form.tripSheetForm')
@include('Orders::Form.picklistErrorsPopup')

 @include('Orders::collection')
@include('Orders::Form.invoiceError')
@include('Orders::Form.reassignOrdersPopup')


@stop

@section('style')
<style type="text/css">
.reportsmarg{ margin-left:-82px !important;  }
.fa-android{color:#5b9bd1!important;}
.fa-apple{color:#5b9bd1!important;}
.fa-windows{color:#5b9bd1!important;}
.fa-desktop{color:#5b9bd1!important;}

.portlet.light > .portlet-title > .actions .dropdown-menu li > a {
  color: #555;
  background:#fff;
  text-align:left;
}
.centerAlignment { text-align: center;}

#orderList_ChannelID {text-align:center !important;}
#orderList_OrderValue,.dataaliright,#orderList_InvoiceValue,#orderList_ReturnValue,
#orderList_collected_amount {
    /*padding-right: 40px;*/
    text-align: right !important;
}
.data__aliright{
  text-align: right;
  padding-right: 10px !important;
}
    .dataaliright{ padding-right:40px!important;}

    .ui-widget-content a{color:#5b9bd1!important;}

#orderList_Actions {text-align:center !important;}

.captionmarg{margin-top:15px;}
.sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px; margin-top:15px; font-size:12px;}

.sorting a{ list-style-type:none !important;font-size:12px;}
.sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#5b9bd1 !important;}
.sorting a:active{
    text-decoration: underline !important;
 }

.inactive{text-decoration:none !important; color:#999 !important;font-size:12px;}
.active{text-decoration:underline !important; color:#5b9bd1 !important;font-size:12px; }

.checkboxmarleft{margin-left:19px !important; text-align:center !important;}
.pad1{padding-left:0px;  line-height: 30px;    margin-bottom: 10px;}
.pad2{padding:0px;  margin-bottom: 10px;}

 .dropdown>.dropdown-menu:before, .dropdown-toggle>.dropdown-menu:before, .btn-group>.dropdown-menu:before {
    right: 9px;
    left: auto;
}
.dropdown>.dropdown-menu:after, .dropdown-toggle>.dropdown-menu:after, .btn-group>.dropdown-menu:after {
    right: 10px;
    left: auto;
}

.ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
.ui-datepicker .ui-datepicker-prev .ui-icon, .ui-datepicker .ui-datepicker-next .ui-icon{
  color:#000 !important;
}

@media(device-width: 768px) and (device-height: 1024px){
    ::-webkit-scrollbar {
        -webkit-appearance: none;
        width: 7px;
    }
    ::-webkit-scrollbar-thumb {
        border-radius: 4px;
        background-color: rgba(0,0,0,.5);
        -webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
    }
}
.pickListErrors>tbody>tr>td{ border:1px solid #ddd; padding:5px;}
.pickListErrors{ border-collapse:collapse;}
.mainhead{ font-family:Arial, Helvetica, sans-serif; font-size:14px; height:30px;}
.subhead{ font-family:Arial, Helvetica, sans-serif; font-size:12px; height:30px; border-bottom:1px solid #efefef;}
.normaltext{ font-family:Arial, Helvetica, sans-serif; font-size:12px; height:30px;}

.bu1{
    margin-left: 10px;
    font-size: 19px;
    color:#000000;
}
.bu2{
    margin-left: 20px;
    font-size: 18px;
    color:#1d1d1d;
}.bu3{
    margin-left: 30px;
    font-size: 16px;
    color:#3a3a3a;
}.bu4{
    margin-left: 40px;
    font-size: 14px;
    color:#535353;
}.bu5{
    margin-left: 50px;
    font-size: 13px;
    color: #6d6c6c;
}.bu6{
    margin-left: 60px;
    font-size: 11px;
    color:#868383;
}
</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.core.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.lob.js"></script>
<?php /*<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>*/?>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders_grid.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/consignment/consignment_script.js') }}" type="text/javascript"></script>

<script type="text/javascript">
      window.asd = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
</script>
<script type="text/javascript">

$(document).ready(function() {
	getSalesOrderList('{{$status}}');
  $('#consolidateOrders').on('hide.bs.modal', function () {
    $("#cons_order_fdate").datepicker('setDate', null);
    $("#cons_order_tdate").datepicker('setDate', null);
    $('#loc_dc_id')[0].sumo.unSelectAll();
  });

  $('#profitablityPoints').on('hide.bs.modal', function () {
    $("#pp_fdate").datepicker('setDate', null);
    $("#pp_tdate").datepicker('setDate', null);
    //$('#loc_option_list')[0].sumo.unSelectAll();
  });

  $('#pp_fdate').datepicker({
    maxDate: 0,
    onSelect: function () {
      var select_date = $(this).datepicker('getDate');
      var nextdayDate = getNextDay(select_date);
      $('#pp_tdate').datepicker('option', 'minDate', nextdayDate);
    }
  });

  $('#pp_tdate').datepicker({
    maxDate:'0',
  });

  $(document).on('change','#center_type',function() {
    var centerTypeId = $(this).val();
    var all_access = $('input[name="all_access"]').val();
    if(centerTypeId!='') {
      $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: '/salesorders/getCenterList/'+centerTypeId+'/'+all_access,
        type: "POST",
        data: {},
        success: function (data) {
          $("#loc_option_list").select2('data', null);
          $('#loc_option_list').html(data);
        },
        error: function(error) {
            alert("Please select any one of the options");
        }
      });
    }
  });

  $(document).on('change','#invoice_code',function() {

      var invoiceId = $(this).val();

      if(invoiceId!='') {



         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/salesorders/getInvoiceDueAmount/'+invoiceId,
             type: "POST",
             data: {},
             dataType: 'json',
             success: function (response) {
                //alert(response.Due_Amt)
                $('#invoice_due').val(response.Due_Amt)             
                $('#collection_amount').val(response.Due_Amt)             
              },
             error: function (response) {             }
         });



      }

  });
  $(document).on('click','.collectionPopup',function() {

      $('form#collection_form')[0].reset();
      collection_validator.reset();
      collection_validator.resetForm();
      $(".error").removeClass("error");
      $('#invoice_code').select2("val", "");      
      var orderId = $(this).attr('collection-order-attr');

         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/salesorders/getInvoicesByOrderid/'+orderId,
             type: "POST",
             data: {},
             success: function (response) {
                $('#invoice_code').html(response)
                $('#invoice_code').select2("val", $("#invoice_code option:eq(1)").val());
                $("#invoice_code").trigger('change');             },
             error: function (response) {             }
         });



      collectionGrid(orderId)

  } );
	
    $('.trpsh_hub').on('change', function() {

         var hub_id = $(this).val();

         var trans_type = ($(this).attr('sheet-type')=='dc') ? 'dc' :'hub';

         if(hub_id!='') {
          
           $.ajax({
               headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
               url: "/salesorders/getvehiclebyhub/"+hub_id+"/"+trans_type,
               type: "POST",
               data: {},
               dataType: 'json',
               success: function (response) {
                 if (response.status == 200) {
                     $('.loderholder').hide();
                     $('#trpsh_vehicle').html(response.data);
                     $('#trpsh_vehicle').select2('val','')
                 } else {
                     $('.loderholder').hide();
                     $('#ajaxResponse').html(response.message).show();
                 }
               },
               error: function (response) {             }
           });

       }
    });

    $('#ass_hub').on('change', function() {

         var hub_id = $(this).val();

         if(hub_id!='') {
          
           $.ajax({
               headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
               url: "/salesorders/getvehiclebyhub/"+hub_id+"/hub",
               type: "POST",
               data: {},
               dataType: 'json',
               success: function (response) {
                 if (response.status == 200) {
                     $('.loderholder').hide();
                     $('#ass_vehicle').html(response.data);
                     $('#ass_vehicle').select2('val','')
                 } else {
                     $('.loderholder').hide();
                     $('#ajaxResponse').html(response.message).show();
                 }
               },
               error: function (response) {             }
           });

       }
    });


/*    $('#st_hub').on('change', function() {

         var hub_id = $(this).val();

         var st_type = ($(this).attr('sheet-type')=='dc') ? 'dc' :'any';

         if(hub_id!='') {
          
           $.ajax({
               headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
               url: "/salesorders/getvehiclebyhub/"+hub_id+"/"+st_type,
               type: "POST",
               data: {},
               dataType: 'json',
               success: function (response) {
                 if (response.status == 200) {
                     $('.loderholder').hide();
                     $('#stock_vehicle_number').html(response.data);
                     $('#stock_vehicle_number').select2('val','')
                 } else {
                     $('.loderholder').hide();
                     $('#ajaxResponse').html(response.message).show();
                 }
               },
               error: function (response) {             }
           });

       }
    });*/

    

/*    $('#stock_vehicle_number').on('change', function() {

         var vehicle_id = $(this).val();

         if(vehicle_id!='') {
          
           $.ajax({
               headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
               url: "/salesorders/getdriverbyvehicle/"+vehicle_id,
               type: "POST",
               data: {},
               dataType: 'json',
               success: function (response) {
                 if (response.status == 200) {
                     $('.loderholder').hide();
                     $('#stock_driver_name').html(response.data);
                     $('#stock_driver_name').select2('val','')
                 } else {
                     $('.loderholder').hide();
                     $('#ajaxResponse').html(response.message).show();
                 }
               },
               error: function (response) {             }
           });

      } else {
          $('#stock_driver_name').select2('val','');
          $('#stock_driver_mobile').val('');
      }
    });*/

      $('.downloadTripSheet').on('click', function() {

          if($(this).attr('trans-type')=='stDcHub') {
              $('#downloadTripSheetForm').attr('href','/salesorders/trip/');
          } else if($(this).attr('trans-type')=='ofd'){
              $('#downloadTripSheetForm').attr('href','/salesorders/tripofd/');
          } else {
              $('#downloadTripSheetForm').attr('href','/salesorders/triphub/');
          }

      });

                $('#downloadTripSheetForm').validate({
                  rules: {
                      trpsh_hub: {
                          required: true
                      },
                      trpsh_vehicle: {
                          required: true
                      }
                  },
                submitHandler: function (form) {
                                      
                    window.location = $('#downloadTripSheetForm').attr('href')+$('#trpsh_vehicle').val();
                }
              });

    $('#ass_delivereddate,#delivereddate,#pickdate').on('keydown',function() {

      return false;
    })

    $('#pickdate').datepicker({maxDate:0,minDate:0}).datepicker("setDate",new Date());

    $('#delivereddate').datepicker({maxDate:0,minDate:0}).datepicker("setDate",new Date());
    $('#ass_delivereddate').datepicker({maxDate:0,minDate:0}).datepicker("setDate",new Date());


  $('#fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#order_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#order_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#cons_order_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#cons_order_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
    $('#tdate').datepicker({
        maxDate:'+1D',
    });
    $('#order_tdate').datepicker({
        maxDate:'+1D',
    });
    $('#cons_order_tdate').datepicker({
        maxDate:'+1D',
    });
  
  $('#return_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#return_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
$('#return_tdate').datepicker({
      maxDate:'+1D',
    });
  $('#ofd_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#ofd_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#ofd_tdate').datepicker({
      maxDate:'+0D',
    });

  $('#sv_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#sv_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#sv_tdate').datepicker({
      maxDate:'0D',
    });

  $('#ss_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#sv_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#ss_tdate').datepicker({
      maxDate:'0D',
    });


   $('#cc_fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#cc_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#cc_tdate').datepicker({
      maxDate:'0D',
    });
   $("#locReport").on('show.bs.modal', function () {
   $("#cc_fdate").val('');
   $("#cc_tdate").val('');
    });

  $('#os_fdate').datepicker({
      maxDate:0,
       onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#os_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
  $('#os_tdate').datepicker({
      maxDate:'0D',
    });
   $("#orderSummary").on('show.bs.modal', function () {
   $("#os_fdate").val('');
   $("#os_tdate").val('');
    });


  $('#collected_on').datepicker({maxDate:0,minDate:0});


  $('#challan').on('click', function(){
        var selected = getChkVal();
        var status = getStatusVal();
        if(selected.length>0) {
         //$('.loderholder').show();
         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: "/salesorders/saveordersession",
             type: "POST",
             data: {ids: selected, statusCodes:status, action:'printChallan'},
             dataType: 'json',
             success: function (response) {
               if (response.status == 200) {
                   $('.loderholder').hide();
                   window.open('/salesorders/bulkprint', '_blank');
               } else {
                   $('.loderholder').hide();
                   $('#ajaxResponse').html(response.message).show();
               }
             },
             error: function (response) {             }
         });
       }
       else {
        $('#ajaxResponse').html('Please select at least one order.').show();
        return false;
       } 
  });

  

  $('#invoice').on('click', function(){
    if($.trim($('#orderList').html())!='') {
        
     var status = getStatusVal();
       var selected = getChkVal();

       if(selected.length > 50) {
        $('#ajaxResponse').html('You can not choose more than 50 orders.').show();
        return false;
       }
        
        if(selected.length>0) {
         //$('.loderholder').show();
         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: "/salesorders/savebulkinvoicesession",
             type: "POST",
             data: {ids: selected, statusCodes:status},
             dataType: 'json',
             success: function (response) {
               if (response.status == 200) {
                   $('.loderholder').hide();
                   window.open('/salesorders/bulkinvoiceprint', '_blank');
               } else {
                   $('.loderholder').hide();
                  $('#ajaxResponse').html(response.message).show();
               }
             },
             error: function (response) {             }
         });
       }
       else {
        $('#ajaxResponse').html('Please select at least one order.').show();
        return false;
       }
    }

  }
  );
  
  

  $('#delivered').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();

    if(selected.length>0) {


      var markDelivered_status = 'success';

      $.each(status,function(key, val) {

        if(val!=17007 && val!=17021 && val!=17014) {

          $('#ajaxResponse').html("Sorry, you can not mark as delivered. Make sure order status should be invoices/shipped.").show();

          markDelivered_status = 'failed'

        }

      });

      if(markDelivered_status == 'failed') {
        return false;
      } else {




          $.ajax({
              headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
              type: "POST",
              url: '/salesorders/getOrderMarkDeliveredDetails',
              data: {ids: selected},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function () {
                  $('#loader1').hide();
              },
              success: function (data) {
                  $('#delivered_by').select2("val", data.delivered_by);      
                  $('#delivereddate').val(data.delivered_on);      
              }
          });







      }


    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });


  $('.stock_transfer').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();
    var hubIds = getHubsVal();
    var transfer_type = $(this).attr('transfer_type');

    $('.transfer_type').val(transfer_type);
    $('form#stockTransferForm')[0].reset();
    $('#stock_delivered_by').select2('val','');

    if(selected.length>0) {

      var stockTransfer_status = 'success';

      $.each(status,function(key, val) {
        
        if(transfer_type=='hubtodc')
        {
          if(val!=17022 && val!=17023) {

            $('#ajaxResponse').html("Sorry, you can not perform Stock Transfer. Make sure order status should be returned/partially delivered.").show();

            stockTransfer_status = 'failed'

          }

        } else {

          if(val!=17021 && val!=17014) {

            $('#ajaxResponse').html("Sorry, you can not perform Stock Transfer. Make sure order status should be invoiced/shipped.").show();

            stockTransfer_status = 'failed'

          }

        }

      });

      if(stockTransfer_status == 'failed') {
        return false;
      }

    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }

    
    if(hubIds.length>1) {
      $('#ajaxResponse').html('You have chosen orders with different Hubs.').show();
      return false;
    }  

  });


  $('#AssignDelExe').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();

    if(selected.length>0) {

      var markDelivered_status = 'success';

      $.each(status,function(key, val) {
        if(val!=17021 && val!=17014 && val!=17025 && val!=17026) {

          $('#ajaxResponse').html("Sorry, you can not mark assign to delivery executive.").show();

          markDelivered_status = 'failed'

        }

      });

      if(markDelivered_status == 'failed') {
        return false;
      } else {
        
          $.ajax({
              headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
              type: "POST",
              url: '/salesorders/getOrderMarkDeliveredDetails',
              data: {ids: selected},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function () {
                  $('#loader1').hide();
              },
              success: function (data) {
                  $('#ass_delivered_by').select2("val", data.delivered_by);    
                  $('#ass_delivereddate').val(data.delivered_on);      
              }
          });

      }

    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });

   

  $('#delivery').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();

    if(selected.length>0) {

      var markDelivered_status = 'success';

      $.each(status,function(key, val) {
        if(val!=17026) {

          $('#ajaxResponse').html("Sorry, you can not mark assign to delivery executive.").show();

          markDelivered_status = 'failed'

        }

      });

      if(markDelivered_status == 'failed') {
        return false;
      } else {
            
                  var popup =  window.open('/salesorders/getDeliveryDetails?gds_order_ids='+selected);
                    popup.onclose = function () { 
                      alert(1);
                      location.reload();
                    }


      }

    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });

        $('.stock_hub').on('click', function() {

            var confirm_type= $(this).attr('confirm_type');
            $('#confirm_stock_type').val(confirm_type);
        
                $.ajax({
                     headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                     url: "/salesorders/getdockets",
                     type: "POST",
                     data: {transfer_type:confirm_type},
                     dataType: 'json',
                      beforeSend: function () {
                         $('#loader1').show();
                      },
                      complete: function () {
                          $('#loader1').hide();
                      },
                     success: function (response) {
                       if (response.status == 200) {
                            $('#loader1').hide();
                            //$('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(response.message).show();

                            if(response.data.length>0) {

                                $('#docket_number').html('<option value="">Please select</option>');

                                $.each(response.data,function(key,val){
                                    $('#docket_number').append('<option value="'+val.st_docket_no+'"">'+val.st_docket_no+'</option>');
                                });
                            }

                       } else {
                          $('.loderholder').hide();
                          $('#ajaxResponse').html('No Dockets were found').show();
                       }
                     },
                     error: function (response) {

                      $('#ajaxResponse').html('Something went wrong').show();

                                  }
                 });


        });

        jQuery.validator.addMethod("partialdocks", function(value, element) {
                return ($('.partialDock:checked').length==0);
        });


        $('#stockInHubForm').validate({
            rules: {
                docket_number: {
                    required: true
                },
                stock_received_by: {
                    required: true
                },
                "container[]":{
                    required: true,
                    partialdocks:true

                },


            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "container[]") {
                  error.insertAfter($('#allDockTab').parent());
                } else {
                  error.insertAfter(element);
                }
            },
            messages : {

              "container[]": {
                  required: "Please scan atleast one complete order to proceed",
                  partialdocks: "Make sure there are no partially scanned orders",
              },

            },
            submitHandler: function (form) {

                  var docket_number = $('#docket_number').val();
                  var stock_received_by = $('#stock_received_by').val();
                  var confirm_stock_type = $('#confirm_stock_type').val();
                  var completeorders = getCompleteScannedOrders();

                  /*
                  $('input[name="container[]"').each(function(){
                    if(!$(this).is(':checked')) {
                        
                        if(jQuery.inArray($(this).val(),completeorders) !== -1) {
                            //remove from complete orders

                            completeorders.splice($.inArray($(this).val(), completeorders), 1);
                        }

                        if(jQuery.inArray($(this).attr('order_code'),partialorders) == -1) {
                          //if already not in partial insert
                          
                          partialorders.push($(this).attr('order_code'));
                        }


                    } else {
                        if(jQuery.inArray($(this).attr('order_code'),partialorders) !== -1) {
                          //remove from completed
                            completeorders.splice($.inArray($(this).val(), completeorders), 1);

                        }
                         else {
                          
                        if(jQuery.inArray($(this).val(),completeorders) == -1) {
                            //if already not in completed insert
                            completeorders.push($(this).val());

                        }

                          
                        }

                    }
                  });

                  $('.containerError').html('');

                  if(partialorders.length > 0) {
                      
                      var partialmsg = 'The following orders were received partially and cant be received';

                      $.each(partialorders,function(key,val){
                          partialmsg = partialmsg + ', '+val;
                      });
                      
                      $('.containerError').html(partialmsg);
                      $('.containerError').append('. </br>');
                  
                      if(!confirm(partialmsg)) {
                        return false;
                      }

                  }*/

                  $.ajax({
                     headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                     url: "/salesorders/confirmstockdocket",
                     type: "POST",
                     data: {docket_number:docket_number,stock_received_by:stock_received_by,confirm_stock_type:confirm_stock_type,completeorders:completeorders},
                     dataType: 'json',
                      beforeSend: function () {
                         $('#loader1').show();
                      },
                      complete: function () {
                          $('#loader1').hide();
                      },
                     success: function (response) {
                       if (response.status == 200) {
                            $('#loader1').hide();
                            $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                           window.location.href = '/salesorders/index';
                       } else {
                          $('.loderholder').hide();
                          $('#ajaxResponse').html(response.message).show();
                       }
                     },
                     error: function (response) {             }
                 });
        }
      });

        $('#docket_number').on('change', function() {

              var dock_no = $(this).val();

              if(dock_no != '') {

              var confirm_stock_type = $('#confirm_stock_type').val();

              getDocketOrders(dock_no,confirm_stock_type);


            }            

        });

        $('#markAsDeliveredForm').validate({
            rules: {
                delivered_by: {
                    required: true
                },
                delivereddate: {
                    required: true,
                    date:true
                }
            },
            submitHandler: function (form) {


                  var selected = getChkVal();
                  var status = getStatusVal();

                  var deliveredBy = $('#delivered_by').val();
                  var deliveredDate = $('#delivereddate').val();

                  $.ajax({
                     headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                     url: "/salesorders/markasdelivered",
                     type: "POST",
                     data: {ids: selected, statusCodes:status, deliveredBy:deliveredBy, deliveredDate:deliveredDate},
                     dataType: 'json',
                      beforeSend: function () {
                         $('#loader1').show();
                      },
                      complete: function () {
                          $('#loader1').hide();
                      },
                     success: function (response) {
                       if (response.status == 200) {
                           window.location.href = '/salesorders/index';
                       } else {
                          $('.loderholder').hide();
                          $('#ajaxResponse').html(response.message).show();
                       }
                     },
                     error: function (response) {             }
                 });
        }
      });


        $('#assignDelExec').validate({
            rules: {
                ass_delivered_by: {
                    required: true
                },
                ass_delivereddate: {
                    required: true,
                    date:true
                },
                 ass_hub: {
                    required: true
                },
            },
            submitHandler: function (form) {


                  var selected = getChkVal();
                  var status = getStatusVal();

                  var deliveredBy = $('#ass_delivered_by').val();
                  var deliveredDate = $('#ass_delivereddate').val();
                  var vehicle_id = $('#ass_vehicle').val();
                  var vehicle_no = $('#ass_vehicle').find('option:selected').attr('vehicle_no');
                 
                  $.ajax({
                     headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                     url: "/salesorders/assigndelexec",
                     type: "POST",
                     data: {ids: selected, statusCodes:status, deliveredBy:deliveredBy, deliveredDate:deliveredDate, vehicle_no:vehicle_no, vehicle_id:vehicle_id},
                     dataType: 'json',
                      beforeSend: function () {
                         $('#loader1').show();
                      },
                      complete: function () {
                          $('#loader1').hide();
                      },
                     success: function (response) {
                       if (response.status == 200) {
                          $('.loderholder').show();
                          $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html('Successfully Assign to Delivery Executive.').show();
                           window.location.href = '/salesorders/index';
                       } else {
                          $('.loderholder').hide();
                          $('#ajaxResponse').html(response.message).show();
                       }
                     },
                     error: function (response) {             }
                 });
        }
      });




        jQuery.validator.addMethod("alphanumeric", function(value, element) {
                return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
        });
        $('#stockTransferForm').validate({
            rules: {
                stock_delivered_by: {
                    required: true
                },
                stock_delivered_mobile: {
                    required: true,
                    number: true,
                    maxlength: 11
                },
                stock_driver_mobile: {
                    number: true,
                    minlength: 10,
                    maxlength: 11
                },
                stock_hub:{
                    required: true
                },
                stock_vehicle_number: {

                  required: true,
                  alphanumeric: true
                },
                stock_driver_name: {
                  alphanumeric: true
                }
            },

              messages: {
              "stock_vehicle_number": {
                  alphanumeric: "Please enter alphanumeric only",
              },
              "stock_driver_name": {
                  alphanumeric: "Please enter alphanumeric only",
              }
            },
                
                submitHandler: function (form) {


                  var selected = getChkVal();
                  var status = getStatusVal();

                  var transfer_type = $('.transfer_type').val();
                  var stock_delivered_by = $('#stock_delivered_by').val();
                  var stock_delivered_by_name = $('#stock_delivered_by option:selected').text();
                  var stock_delivered_mobile = $('#stock_delivered_mobile').val();
                  var stock_vehicle_number = $('#stock_vehicle_number').find('option:selected').attr('vehicle_no');
                  var stock_vehicle_id = $('#stock_vehicle_number').val();
                  var stock_driver_name = $('#stock_driver_name').val();
                  var stock_driver_mobile = $('#stock_driver_mobile').val();

                  $.ajax({
                     headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                     url: "/salesorders/stocktransfer",
                     type: "POST",
                     data: {ids: selected, statusCodes:status,transfer_type:transfer_type, stock_delivered_by: stock_delivered_by, stock_delivered_by_name : stock_delivered_by_name, stock_delivered_mobile:stock_delivered_mobile, stock_vehicle_number:stock_vehicle_number,stock_vehicle_id:stock_vehicle_id, stock_driver_name:stock_driver_name, stock_driver_mobile:stock_driver_mobile},
                     dataType: 'json',
                      beforeSend: function () {
                         $('#loader1').show();
                      },
                      complete: function () {
                          $('#loader1').hide();
                      },
                     success: function (response) {
                       if (response.status == 200) {
                        
                          $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html('Successfully transferred.').show();
                          window.location.href = '/salesorders/index';
                       } else {
                          $('.loderholder').hide();
                          $('#ajaxResponse').html(response.message).show();
                       }
                     },
                     error: function (response) {             }
                 });
        }
      });

        


        $("#assignBtn").click(function(e){
            var selected = getChkVal();
              var checkerBy = $('#checker_id').val();
              var date = new Date();
                date = date.toISOString().split('T')[0];
            if(checkerBy == ""){
                alert("Please Select Checker!");
                return false;
            }
            if(selected == ""){
                alert("Please Select Atleast One Order!");
                return false;
            }
              $.ajax({

                 headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                 url: "/salesorders/reassigOrders",
                 type: "POST",
                 data: {orders: selected, checkerBy:checkerBy,date:date},
                 dataType: 'json',
                  beforeSend: function () {
                     $('#loader1').show();
                  },
                  complete: function () {
                    $('#loader1').hide();
                  },
                 success: function (response) {
                   if (response.status == 200) {
                      $('.loderholder').show();
                      $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html('Successfully Assigned.').show();
                       window.location.href = '/salesorders/index/dispatch';
                   } else {
                      $('.loderholder').hide();
                      $('#ajaxResponse').html(response.message).show();
                   }
                 },
                 error: function (response) {             }
             });
        });



        $('#printPicklistForm').validate({
            rules: {
                picked_by: {
                    required: true
                },
                pickdate: {
                    required: true
                },
                // doc_area: {
                //     required: true
                // }
            },
            submitHandler: function (form) {


                  var selected = getChkVal();
                  var status = getStatusVal();

                  var pickedBy = $('#picked_by').val();
                  var pickDate = $('#pickdate').val();
                  //var docArea = $('#doc_area').val();

                  $.ajax({
                     headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                     url: "/salesorders/savepicklist",
                     type: "POST",
                     data: {ids: selected, statusCodes:status, pickedBy:pickedBy, pickDate:pickDate},
                     dataType: 'json',
                      beforeSend: function () {
                         $('#loader1').show();
                      },
                      complete: function () {
                          $('#loader1').hide();
                      },
                     success: function (response) {
                          if (response.Status == 200) {
                            $('.loderholder').hide();
                            window.open('/picklist/printPicklist', '_blank');
                            window.location.href = '/salesorders/index';
                          } else if(response.Status == 402){

                              if(typeof response.Message !== 'undefined' && response.Message.length>0) {
                                    
                                    $('.pickListErrors').html('');
                                    
                                    $.each(response.Message,function(key,val) {
                                        if(typeof val.error !== 'undefined' && val.error.length>0) {
                                           var tbletext = '<tr bgcolor="#efefef" class="mainhead"><th width="50%" align="left" valign="middle">Order ID : '+val.order_code+'</th><th width="50%" align="left" valign="middle">Status : Failed</th>\n\
                                       </tr><tr><td colspan="2">\n\
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">\n\
<tr class="subhead"><th width="33%" align="left" valign="middle">Product SKU</th><th width="33%" align="left" valign="middle">Bins</th><th width="33%" align="left" valign="middle">Reason</th></tr>';
                                          $.each(val.error,function(ord_key,ord_val) {
                                            tbletext =tbletext+'<tr class="subhead"><td align="left" valign="middle">'+ord_val.product+'</td><td align="left" valign="middle">'+ord_val.bins+'</td><td align="left" valign="middle">'+ord_val.reason+'</td></tr>';
                                          });
                                          tbletext = tbletext+'</table></td></tr>';
                                          $('.pickListErrors').append(tbletext);
                                        } else {
                                            
                                            $('.pickListErrors').append('<tr bgcolor="#efefef" class="mainhead"><th width="50%" align="left" valign="middle">Order ID : '+val.order_code+'</th><th width="50%" align="left" valign="middle">Status : '+val.message+'</th>\n\
                                       </tr>');

                                            //$('.pickListErrors').append('<tr><td>Order : '+val.order_code+'<br>'+'Status: Success<br>Message:'+val.message+'</td></tr>');

                                        }
                                    });

                                    $('.close,#plErrors').trigger('click');
                                    $('#orderList').igGrid('dataBind');

                              }

                          }
                          else {
                            $('#ajaxResponse').html(response.message).show();
                            $('.loderholder').hide();
                          }
                     },
                     error: function (response) {
                     }
                 });
        }
      });
    $('#printTripsheetForm').validate({
        rules: {
            picked_by: {
                required: true
            },
            pickdate: {
                required: true
            }
        },
        submitHandler: function (form) {
            var selected = getChkVal();
            var status = getStatusVal();
            var pickedBy = $('#tr_picked_by').val();
            var pickDate = $('#tr_pickdate').val();
            //var docArea = $('#tr_doc_area').val();
            $.ajax({
               headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
               url: "/salesorders/savepicklist",
               type: "POST",
               data: {ids: selected, statusCodes:status, pickedBy:pickedBy, pickDate:pickDate,generatetrip:1},
               dataType: 'json',
                beforeSend: function () {
                   $('#loader1').show();
                },
                complete: function () {
                    $('#loader1').hide();
                },
               success: function (response) {
                    if (response.Status == 200) {
                      $('.loderholder').hide();
                      window.open('/picklist/printPicklist', '_blank');
                      window.location.href = '/salesorders/index';
                    } else if(response.Status == 402){

                        if(typeof response.Message !== 'undefined' && response.Message.length>0) {
                              $('.pickListErrors').html('');
                              $.each(response.Message,function(key,val) {
                                  if(typeof val.error !== 'undefined' && val.error.length>0) {
                                     var tbletext = '<tr bgcolor="#efefef" class="mainhead"><th width="50%" align="left" valign="middle">Order ID : '+val.order_code+'</th><th width="50%" align="left" valign="middle">Status : Failed</th>\n\
                                 </tr><tr><td colspan="2">\n\
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">\n\
<tr class="subhead"><th width="33%" align="left" valign="middle">Product SKU</th><th width="33%" align="left" valign="middle">Bins</th><th width="33%" align="left" valign="middle">Reason</th></tr>';
                                    $.each(val.error,function(ord_key,ord_val) {
                                      tbletext =tbletext+'<tr class="subhead"><td align="left" valign="middle">'+ord_val.product+'</td><td align="left" valign="middle">'+ord_val.bins+'</td><td align="left" valign="middle">'+ord_val.reason+'</td></tr>';
                                    });
                                    tbletext = tbletext+'</table></td></tr>';
                                    $('.pickListErrors').append(tbletext);
                                  } else {

                                      $('.pickListErrors').append('<tr bgcolor="#efefef" class="mainhead"><th width="50%" align="left" valign="middle">Order ID : '+val.order_code+'</th><th width="50%" align="left" valign="middle">Status : Success</th>\n\
                                 </tr>');

                                  }
                              });
                              $('.close,#plErrors').trigger('click');
                              $('#orderList').igGrid('dataBind');
                        }
                    } else {
                      $('#ajaxResponse').html(response.message).show();
                      $('.loderholder').hide();
                    }
               },
               error: function (response) {             
               }
           });
        }
    });

   $('#genOpnInv').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();

    if(selected.length > 50) {
      alert('You can not select more than 50 orders.');
      return false;
    }

    if(selected.length>0) {
      var res = confirm("Are you sure do you want to 'Generate Invoice' for the selected orders?");
      if(res==true){
        $('.loderholder').show();
        $.ajax({
          headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
          url: "/salesorders/generateInvoiceFromOpen",
          type: "POST",
          data: {ids: selected, statusCodes:status},
          dataType: 'json',
          success: function (responses) {
              if(responses.status==200){
                $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html('Invoice generated successfully.').show();
                $('#orderList').igGrid('dataBind');
                $('.loderholder').hide();
              }else if(responses.message.status_type != undefined && responses.message.status_type =="inventory_error"){
                $("#order_code_inv").html(responses.message.order_code);
                $("#inv_table_body").html(responses.message.inv_html);                
                $("#invoiceError").modal("show");
                $('.loderholder').hide();
              }else{
                $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(responses.message).show();
                $('#orderList').igGrid('dataBind');
                $('.loderholder').hide();
              }
          },
          error: function (response) {             }
        });
        
      }
    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });
        

   $('#genInvoice').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();

    if(selected.length > 50) {
      alert('You can not select more than 50 orders.');
      return false;
    }

    if(selected.length>0) {
      var res = confirm("Are you sure do you want to 'Generate Invoice' for the selected orders?");
      if(res==true){
        $('.loderholder').show();
        $.ajax({
          headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
          url: "/salesorders/genBulkInvoice",
          type: "POST",
          data: {ids: selected, statusCodes:status, action:'createShipment' },
          dataType: 'json',
          success: function (responses) {
                                               
              $('.bulkInvoiceErrors').html('');
              
              $.each(responses,function(key,val) {
                  if(typeof val.Status !== 'undefined' && val.Status == 403) {
                     var tbletext = '<tr bgcolor="#efefef" class="mainhead"><th width="50%" align="left" valign="middle">Order ID : '+val.order_code+'</th><th width="50%" align="left" valign="middle">Status : Failed</th>\n\
                 </tr><tr><th align="left" valign="middle">Message : '+val.Message+'</th></tr>';
                    $('.bulkInvoiceErrors').append(tbletext);
                  } else {
                      
                      $('.bulkInvoiceErrors').append('<tr bgcolor="#efefef" class="mainhead"><th width="50%" align="left" valign="middle">Order ID : '+val.order_code+'</th><th width="50%" align="left" valign="middle">Status : Success</th>\n\
                 </tr><tr><th align="left" valign="middle">Message : '+val.Message+'</th></tr>');

                      //$('.pickListErrors').append('<tr><td>Order : '+val.order_code+'<br>'+'Status: Success<br>Message:'+val.message+'</td></tr>');

                  }
              });
              $('.close,#biErrors').trigger('click');
              $('#orderList').igGrid('dataBind');
              $('.loderholder').hide();           
          },
          error: function (response) {             }
        });
        
      }
    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });


  $('#genShipment').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();

    if(selected.length > 10) {
      alert('You can not select 10 orders.');
      return false;
    }

    if(selected.length>0) {
      var res = confirm("Are you sure do you want to 'Create Shipment' for the selected orders?");
      if(res==true){
        //$('.loderholder').show();
        $.ajax({
          headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
          url: "/salesorders/saveordersession",
          type: "POST",
          data: {ids: selected, statusCodes:status, action:'createShipment' },
          dataType: 'json',
          success: function (response) {
            if (response.status == 200) {
              window.location.href = '/salesorders/bulkshipment';
            } else {
              $('.loderholder').hide();
              $('#ajaxResponse').html(response.message).show();
            }
          },
          error: function (response) {             }
        });
      }
    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });

  $('#generateDSR').on('click', function(){
    var selected = getChkVal();
    var status = getStatusVal();
    if(selected.length>0) {
      var res = confirm("Are you sure do you want to 'Generate DSR' for the selected orders?");
      if(res==true){
        window.location.href = '/salesorders/generatedsr?ids='+selected;
      }
    }
    else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });

   $('#printPL').on('click', function() {
    var status = getStatusVal();
    var selected = getChkVal();
    if(selected.length>0) {

      var printPL_status = 'success';

      $.each(status,function(key, val) {

        if(val!=17001 && val!=17020) {

          $('#ajaxResponse').html("Sorry, you can't generate picklist. Make sure order status should be OPEN ORDER / PICKLIST GENERATED.").show();

          printPL_status = 'failed'

        }

      });

      if(printPL_status == 'failed') {
        return false;
      } else {

          var token = $("#csrf-token").val();


          $.ajax({
              headers: {'X-CSRF-TOKEN': token},
              type: "POST",
              url: '/salesorders/getOrderPickerDetails',
              data: {ids: selected},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function () {
                  $('#loader1').hide();
              },
              success: function (data) {
                  //$('#doc_area').select2("val", data.binArea);      
                  $('#picked_by').select2("val", data.picker_id);      
              }
          });


      }


      
    }else {
      $('#ajaxResponse').html('Please select at least one order.').show();
      return false;
    }
  });


  $( "#toggleFilter" ).click(function() {
	  $( "#filters" ).toggle( "slow", function() {
	  });
	});
});




$.validator.addMethod('checkCollectionAmt', function(value, element) {
        
        
        return (parseFloat($('#invoice_due').val())>=value && value>0);
        
        //if($('#invoice_due').val())
    }, "Please enter valid Amount");

        var collection_validator = $('#collection_form').validate({
            rules: {
                invoice: {
                    required: true
                },
                invoice_due: {
                    required: true
                },
                mode_of_payment: {
                    required: true
                },
                collection_amount: {
                    required: true,
                    checkCollectionAmt:true
                },
                collected_by: {
                    required: true
                },
                collected_on: {
                    required: true
                }
            },
            submitHandler: function (form) {

                    var formData = new FormData();

                    var token = $("#csrf-token").val();
                    if ($('#proof').val())
                        formData.append('proof', $('#proof')[0].files[0]);


                    formData.append('_token', token);
                    formData.append('invoice', $("#invoice_code").val());
                    formData.append('mode_of_payment', $('#mode_of_payment').val());
                    formData.append('reference_num', $('#reference_num').val());
                    formData.append('collection_amount', $('#collection_amount').val());
                    formData.append('collected_by', $('#collected_by').val());
                    formData.append('collected_on', $('#collected_on').val());
                    formData.append('remarks', $('#remarks').val());
                    console.log(formData);
//      formData+='&_token=' + token;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/salesorders/createCollection',
                        processData: false,
                        contentType: false,
                        data: formData,
                        dataType: 'json',
                        beforeSend: function () {
                           $('#loader1').show();
                        },
                        complete: function () {
                            $('#loader1').hide();
                        },
                        success: function (data) {
                          
                          if (data.status == 200) {
                              $('.loderholder').hide();
                              $('#collectionAjaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                              $('html, body').animate({scrollTop: '0px'}, 500);
                              $('form#collection_form')[0].reset();
                              collection_validator.resetForm();
                              collection_validator.reset();
                              $(".error").removeClass("error");
                              $('a[href="#tab_15_2"]').trigger('click');
                              $('#invoice_code').select2("val", "");      
                              $(".collectionGrid").igGrid("dataBind");
                          } else {
                              $('#collectionAjaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                              $('.loderholder').hide();
                              $('html, body').animate({scrollTop: '0px'}, 500);
                          }
                        }
                    });

                
        }
        });

$('#stock_delivered_by').on('change', function() {

  $('#stock_delivered_mobile').val($('option:selected', this).attr('mobile'));
})

$('form[id="salesReturnOrders"]').on('submit', function(event) {
  $('.close').trigger('click');
})

$('a[href="#salesReturnOrders"]').on('click', function() {
  $('form[id="salesReturnOrders"]')[0].reset();  
});

$('a[href="#salesVouchers"]').on('click', function() {
  $('form[id="salesVouchers"]')[0].reset();  
});

$('a.link').on('click touchend', function(e) {
  var link = $(this).attr('href');
  window.location = link;
});

</script>

<script type="text/javascript">

$("#dwn_file").click(function(e){ 
 var fdata = $('#os_fdate').val();
 var tdata = $("#os_tdate").val();
 var status = 0;

if(fdata == null || fdata==''){ 
    e.preventDefault();        
      $("#span_id_os_fdata").show();
 }
else{
  status=1;
  $("#span_id_os_fdata").hide();
  }


  if(tdata == null || tdata==''){ 
    e.preventDefault();        
        $("#span_id_data").show();
}
else{
  status=1;
  $("#span_id_data").hide();
  }
});

$('#emptypopup').click(function () { 
    
     $("#is_active").attr('checked', false);

});

  $(function(){
      var token=$('#csrf-token').val();
      var hidden_buid=$('#hidden_buid').val();
      $.ajax({
      type:'get',
      headers: {'X-CSRF-TOKEN': token},
      url:'/getbu',
      success: function(res){        
          res.forEach(data=>{
              $('#business_unit_id').append(data);

          });
          $('#business_unit_id').select2('val',hidden_buid);
          // $('#business_unit_id')[0].sumo.reload();
      }

      });
  });
    $('#business_unit_id').on('change', function() {
    //alert( this.value );
    var token=$('#csrf-token').val();
    var buid=$(this).val();//alert(buid);
    $.ajax({
       headers: {'X-CSRF-TOKEN': token},
        url: "/salesorders/setbuid",
        type: 'POST',
        data:{
          buid:buid,
        },
        dataType: 'json', // added data type
        success: function(res) {
            /*console.log(res);
            alert(res);*/
            if(res!='' && res!=0){
              //getSalesOrderList('{{$status}}');
              location.reload();
            }
        }
      });
    });
    $('#primary_secoundary_sales_id').on('change',function(){
      var token=$('#csrf-token').val();
      var sales_id = $(this).val();
      var status='{{$status}}';
      window.location = "/salesorders/index/" + status+"/"+sales_id;

    });



    $('#dcfc_fdate').datepicker({
      maxDate:0,
       onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#dcfc_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });

  $('#dcfc_tdate').datepicker({
      maxDate:'0D',
    });
     $("#dcfcSalesReport").on('show.bs.modal', function () {
     $("#dcfc_fdate").val('');
     $("#dcfc_tdate").val('');
     $("#span_id_dcfc_fdata").hide();
     $("#span_id_dcfc_tdata").hide();
    });

    $("#dcfc_dwn_file").click(function(e){ 
     var fdata = $('#dcfc_fdate').val();
     var tdata = $("#dcfc_tdate").val();
     var status = 0;

    if(fdata == null || fdata==''){ 
        e.preventDefault();        
          $("#span_id_dcfc_fdata").show();
     }
    else{
      status=1;
      $("#span_id_dcfc_fdata").hide();
      }


      if(tdata == null || tdata==''){ 
        e.preventDefault();        
            $("#span_id_dcfc_tdata").show();
    }
    else{
      status=1;
      $("#span_id_dcfc_tdata").hide();
      }
    });



    $('#apob_fdate').datepicker({
      maxDate:0,
       onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
        }
    });

  $('#apob_tdate').datepicker({
      maxDate:'0D',
    });
     $("#apobSalesReport").on('show.bs.modal', function () {
     $("#apob_fdate").val('');
     $("#apob_tdate").val('');
     $("#span_id_apob_fdata").hide();
     $("#span_id_apob_tdata").hide();
    });

    $("#apob_dwn_file").click(function(e){ 
     var fdata = $('#apob_fdate').val();
     var tdata = $("#apob_tdate").val();
     var status = 0;

    if(fdata == null || fdata==''){ 
        e.preventDefault();        
          $("#span_id_apob_fdata").show();
     }
    else{
      status=1;
      $("#span_id_apob_fdata").hide();
      }


      if(tdata == null || tdata==''){ 
        e.preventDefault();        
            $("#span_id_apob_tdata").show();
    }
    else{
      status=1;
      $("#span_id_apob_tdata").hide();
      }
    });




  $('#retailer_fdate').datepicker({
      maxDate:0,
       onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#retailer_tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });

  $('#retailer_tdate').datepicker({
      maxDate:'0D',
    });
     $("#retailerSalesReport").on('show.bs.modal', function () {
     $("#retailer_fdate").val('');
     $("#retailer_tdate").val('');
     $("#retailer_span_id_fdata").hide();
     $("#retailer_span_id_tdata").hide();
    });

    $("#retailer_dwn_file").click(function(e){ 
     var fdata = $('#retailer_fdate').val();
     var tdata = $("#retailer_tdate").val();
     var status = 0;

    if(fdata == null || fdata==''){ 
        e.preventDefault();        
          $("#retailer_span_id_fdata").show();
     }
    else{
      status=1;
      $("#retailer_span_id_fdata").hide();
      }


      if(tdata == null || tdata==''){ 
        e.preventDefault();        
            $("#retailer_span_id_tdata").show();
    }
    else{
      status=1;
      $("#retailer_span_id_tdata").hide();
      }
    });

</script>

<style type="text/css">.loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%;    }
    .error{color: red;}
    .SumoSelect > .optWrapper > .options {
    text-align: left;
}
.avoid-clicks {
  pointer-events: none;
}
.business_unit_id{
  height: 29px;
}
</style>

<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>

@stop
