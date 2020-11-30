@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> MANAGE PRODUCTS </div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question-circle-o"></i></a></span> </div>
      </div>
      <div class="portlet-body">

		<table id="hierarchicalGrid"></table>

		</div>
    </div>
  </div>
</div>

{{HTML::style('css/switch-custom.css')}}

  

@stop

@include('includes.ignite')

@section('userscript')

@stop

@extends('layouts.footer')