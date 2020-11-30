@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
	<div class="col-md-12">
		<ul class="page-breadcrumb breadcrumb">
			<li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/salesorders/index">Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li><a href="/salesorders/detail/{{$returnProductArr[0]->gds_order_id}}">Order Details</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
			<li>Return Details</li>
		</ul>
	</div>
</div>

<div class="row">
    <div class="col-md-12">		
        <div class="portlet light portlet-fit portlet-datatable bordered">
			<div class="portlet-title">
				<div class="caption">Return #{{$returnProductArr[0]->return_grid_id}}</div>				
			 </div>
			 
                @include('Orders::navigationTab')                                         
            </div>
	</div>
</div>
@stop

@section('style')
<style type="text/css">   

td.alignRight {
    text-align: right;
    padding-right: 102px;
 }
</style>
@stop


@section('userscript')
@include('Orders::paymentsScript')                                         
@include('Orders::gridJsFile')
@stop

