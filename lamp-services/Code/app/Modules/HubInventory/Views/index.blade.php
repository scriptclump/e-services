@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> Hub Inventory </div>


            </div>                               
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-4 pull-right">
                        <div class="row">
                <div class="col-md-9">
                <div class="form-group">   
                <select class="form-control select2me" name="hublist1" id="hublist1"></select>
                </div>
                </div>
                <div class="col-md-3">
                <div class="form-group">                       
                 @if($xlsaccess)    
                 <a class="btn green-meadow" href="hubinventoryxls" id='hub_inv'>Export</a>
                @endif
                </div>
                </div>
                </div>
                    </div>    
                </div>
        <div class="col-md-12 text-right" style="font-size:11px;margin-top: -13px;"><b>* All Amounts in </b><i class="fa fa-inr" aria-hidden="true"></i></div>

                <table id="hub_inventory_grid"></table>
            </div>
        </div>
    </div>
</div>

<iframe class="lightbox"  style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    
</iframe>
{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<style type="text/css">
    .margtop{margin-top:15px;}
    .ui-iggrid-filterrow ui-widget { }
    .actions {
    display: inline-block;
    float: right !important;}
    .audiofile{width: 200px !important;}
.rightAlign {
    text-align:right;
}
</style>
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('assets/admin/pages/scripts/hub_inventory/hub_inventory.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script>
    $(document).ready(function () {
	
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/gethubs',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#hublist1").html(rs);
                $("#hublist1").select2().select2('val','');
            }
        });
    });

 $("#hublist1").change(function() {
    var bu = $("#hublist1").val();    
    $("#hub_inv").attr("href", "hubinventoryxls?bu=" + bu);
    
    var filterURL = "gethubinventory?bu=" + bu;
    $("#hub_inventory_grid").igGrid({dataSource: filterURL});
});

</script>
@stop
@extends('layouts.footer')