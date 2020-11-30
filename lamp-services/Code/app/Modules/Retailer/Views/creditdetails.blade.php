@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Credit Details</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Credit Details</div>
                <div class="pull-right margin-top-10">
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
                        <strong>Shop Name</strong> : {{$creditDetails->business_legal_name}}<br/>
                        <strong>DC/FC Name</strong> : {{$creditDetails->user_name}}<br/>
                        <strong>Current Credit Limit</strong> : {{$creditDetails->creditlimit}}<br/>
                        <strong>Recommended Credit Limit</strong> : {{$creditDetails->pre_approve_limit}}<br/>
                        <strong>Approval Status</strong> : {{$creditDetails->approval_status_name}}<br/>
                    </div>
                </div>
                <br/>
                <div  class="row">
                    <div class="col-md-4 col-sm-4">
                        @include('PurchaseOrder::Form.approvalForm')
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