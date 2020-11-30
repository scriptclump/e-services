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
            <li>Return</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption"><span class="caption-subject font-dark sbold uppercase">Order Id :  {{$orderdata->gds_order_id}} | {{date('M d, Y h:i:s',strtotime($orderdata->order_date))}}<span class="hidden-xs"></span> </span> </div>
            </div>
             @include('Orders::navigationTab') 			
        </div>
    </div>
</div>
@stop
@section('userscript')
@include('Orders::gridJsFile')
@include('Orders::paymentsScript')                                         
@stop

