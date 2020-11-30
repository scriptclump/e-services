@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> Inventory Status Reports </div>
                <div class="actions">  </div>
            </div>
            <div class="actions">                                                                                 
                <span data-original-title="Export to Excel" data-placement="top" class="tooltips">
                <a class="btn green-meadow" href="/inventoryReports/excel" id="toggleFilter_export">Download Inventory Status</a>
                </span>                                
            </div>
            <div class="portlet-body">
                <table id="inventory_status_reports_grid"></table>
            </div>
        </div>
    </div>
</div>
{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<style type="text/css">
    .margtop{margin-top:15px;}
    .ui-iggrid-filterrow ui-widget { }
    .actions {
    display: inline-block;
    float: right !important;}

</style>
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
@include('includes.validators')
@include('includes.ignite')

{{HTML::script('assets/admin/pages/scripts/inventoryStatus/inventory_status_reports.js')}}

<script>
	function GridModalMessage(grid) {
		var modalBackground = $("<div class='ui-widget-overlay ui-iggrid-blockarea' style='position: absolute; display: none; width: 100%; height: 100%;'><div style='position: relative;top:50%; font-size:30px; font-weight: bold; text-align: center;'></div></div>").appendTo(grid.container());
		function _show(message) {
			modalBackground.show().find("div").text(message);
		}
		function _hide() {
			$('.ui-widget-overlay').css('display','none');
		}
		return {
			show: _show,
			hide: _hide
		}
	}
</script>



@stop

@extends('layouts.footer')