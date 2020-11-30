@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">                
        <div class="caption"><?php echo $description; ?></div>
          <div class="actions"></div>
          <div class="col-sm-12 col-md-12" style=" height: 500px; overflow: hidden;">
            <iframe src=<?php echo $src; ?>></iframe>       
            </div>
          </div> 
      </div>
    </div>
  </div>
{{HTML::style('css/switch-custom.css')}}
@stop
@section('style')      
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
<style type="text/css">

</style>
@include('includes.validators')
@include('includes.ignite')

  <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
  <script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
  <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
@stop
@extends('layouts.footer')
