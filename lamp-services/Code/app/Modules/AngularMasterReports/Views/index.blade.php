@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'MasterReport'); ?>
<div class="master_container">
<app-root></app-root>
</div>
	
@stop
@section('style')
<style type="text/css">
.page-content {
    background: transparent !important;
    border: 1px solid #e5e5e5 !important;
    /*height: 700px !important;*/
}
.page-content-wrapper{
	background: #fff;
}	
.page-sidebar-closed .page-sidebar {
    width: 54px !important;
    border: 1px solid #e5e5e5;
}
@media (min-width: 1200px)
.master_container {
    max-width: 1263px!important;
}
</style>
@stop
@section('script')

	<script type="text/javascript" src="{{ URL::asset('/ng-master-reports/dist/runtime.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('ng-master-reports/dist/polyfills.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('ng-master-reports/dist/styles.js') }} "></script>

	<script type="text/javascript" src=" {{ URL::asset('ng-master-reports/dist/vendor.js') }}"></script>
	
	<script type="text/javascript" src="{{ URL::asset('ng-master-reports/dist/main.js') }}"></script>

@stop
@extends('layouts.footer')
