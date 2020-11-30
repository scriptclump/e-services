@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/">Payment</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Payment Details</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Payment# {{$paymentDetails->pay_code}}</div>
                <div class="pull-right margin-top-10">
                    <?php /* @if(isset($featureAccess['printFeature']) && $featureAccess['printFeature'])
                    <a target="_blank" class="btn green-meadow" href="/grn/print/{{$grnProductArr[0]->inward_id}}"><i class="fa fa-print"></i></a>
                    @endif */
                   //echo '<pre/>'; print_r($paymentDetails);?>                    
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                <div class="col-md-12 text-right">
                    &nbsp;
                    <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in (₹)</span>
                </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <strong>Supplier Name</strong> : {{$paymentDetails->business_legal_name}}<br/>
                        <strong>Payment For</strong> : {{$paymentDetails->pay_for}}<br/>
                        <strong>Pay Code</strong> : {{$paymentDetails->pay_code}}<br/>
                        <strong>Pay Date</strong> : {{$paymentDetails->pay_date}}<br/>
                        <strong>Payment Type</strong> : {{$paymentDetails->payment_type}}<br/>
                        <strong>Ref No</strong> : {{$paymentDetails->txn_reff_code}}<br/>                        
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <strong>Supplier Code</strong> : {{$paymentDetails->le_code}}<br/>
                        <strong>Amount</strong> : {{$paymentDetails->pay_amount}}<br/>                        
                        <strong>Ledger Group</strong> : {{$paymentDetails->ledger_group}}<br/>
                        <strong>Ledger Account</strong> : {{$paymentDetails->ledger_account}}<br/>
                        <strong>Created By</strong> : {{$paymentDetails->createdBy}}<br/>
                        <strong>Approval Status</strong> : {{$paymentDetails->approval_status_name}}<br/>
                    </div>
                </div>
                <div  class="row">
                    <div class="col-md-4 col-sm-4">
                        <?php echo $approvalFrom; ?>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
@section('style')
<style type="text/css">
    .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%;    }
    .error{color: red;}
</style>
@stop
@section('userscript')
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/approvalscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/payments_script.js') }}" type="text/javascript"></script>
@stop