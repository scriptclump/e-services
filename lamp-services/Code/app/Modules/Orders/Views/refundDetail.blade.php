@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/detail/{{$orderdata->gds_order_id}}">Order Details</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Cancel</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption"><span class="caption-subject font-dark sbold uppercase">Refund #{{$products[0]->refund_grid_id}} <span class="hidden-xs">| {{date('M d, Y h:i:s',strtotime($products[0]->created_at))}}</span> </span> </div>
            </div>
             @include('Orders::navigationTab') 			
        </div>
    </div>
</div>
@stop

@section('userscript')
@include('Orders::paymentsScript')                                         
@include('Orders::gridJsFile')
@stop
