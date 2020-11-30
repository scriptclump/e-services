@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="POST">
<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/salesorders/index">Sales Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li>Order Details</li>
		</ul>
	</div>
</div>

<div class="row">
    <div class="col-md-12">		
        <div class="portlet light portlet-fit portlet-datatable bordered">
			<div class="portlet-title">
				<div class="caption uppercase">Order #{{$orderdata->order_code}} {{date('d-m-Y H:i:s',strtotime($orderdata->order_date))}}</div>
				<div class="tools">&nbsp;</div>
			 </div>
            
            @include('Orders::navigationTab')
		</div>
	</div>
</div>

@stop


@section('userscript')
@include('Orders::gridJsFile')
@stop
