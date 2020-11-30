@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Slab Report'); ?>
<span id="success_message">@include('flash::message')</span>
<span id = "success_message1"></span>
<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget">
<div class="portlet-title">
	<div class="caption">
		PROMOTION SLAB REPORT 
    </div>
        <div class="actions">
         
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
        
        <a href="#" data-id="#" data-toggle="modal" data-target="#download-slab" class="btn green-meadow">Download</a>
        
    </div>
</div>
<div class="portlet-body">

<div class="row">
	<div class="col-md-12">
	<div class="table-scrollable">
	<table id="slab_report"></table>
	</div>
	</div>
</div>

<!-- Module for upload and download -->

<div class="modal fade" id="download-slab" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">SLAB DOWNLOAD</h4>
                </div>
                <div class="modal-body">
                <form  action ="{{url('promotions/slabreportdates')}}" method="POST" id = "download_slab_report" name = "download_slab_report">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        <label>Start Date</label>
                                        <div class="input-icon input-icon-sm right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" name="from_date" id="from_date" class="form-control">
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <div class="input-icon input-icon-sm right">
                                            <i class="fa fa-calendar"></i>
                                            <input type="text" name="to_date" id="to_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="form-group">
                                                <button type="submit" class="btn green-meadow" id="download-excel">Download Slab Report</button>
                                            </div>
                                        </div>
                                        
                                    </div> 


                                </div>
                            </div>                  
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

</div>
</div>

@stop

@section('userscript')
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript" />

<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/slabreport.js') }}" type="text/javascript"></script>


<script>   


$( document ).ready(function() {
    //resetting the field values
    $('#download-slab').on('show.bs.modal', function () {
        $('#from_date').val('');
        $('#to_date').val('');
                 
    });
});   


$( "#download-excel" ).click(function() {
    $('#download-slab').modal('toggle');
});

</script>
@stop
@extends('layouts.footer')
