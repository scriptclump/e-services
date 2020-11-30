@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{ isset($lgid['wh_id'])?Session::get('dc_lg_id'):$legalEntityId}}">
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('dashboard.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('dashboard.dashboard_heads.salestarget_reports')}}</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
    <span style="color : #795548; float:right;">
        <i class="fa fa-clock-o" aria-hidden="true"> {{trans('dashboard.dashboard_heads.last_updated')}}: 
            <span id="last_updated">@if(isset($last_updated)){{$last_updated}}@endif</span>
        </i>
    <span>
</ul>
<div class="page-head">

<input type="hidden" id="wh_id" value="{{$whid}}">
    <div>
       
        <div style="width: 58%; float: left;">
            <div class="page-title">
                <h1>{{trans('dashboard.dashboard_heads.salestarget_reports')}}</h1>  
                 <div class="dc_dropdown">
                  <select class="form-control" id="dc_all_legalentity" onchange="top.location.href = this.options[this.selectedIndex].value">
                    @foreach($dcs as $alldcs)
                    @if($alldcs->lp_name!='' || $alldcs->lp_name!=null)
                    <option value="{{ url('/salestarget/'.$alldcs->le_wh_id) }}" @if($alldcs->le_wh_id == $whid) selected  @endif>{{$alldcs->lp_wh_name}} - ({{$alldcs->name}})</option>
                    @endif
                    @endforeach
            </select>
        </div> 
            </div>
        </div>
        <div class="customDateArea" id="customDatesView">
            <div class="customDateWidth" style="width: 25% !important;">
                <div class="form-group" id="customDatePickerZone">
                    <div class="input-daterange input-group" id="datepicker">
                        <span class="input-group-addon">Select Month</span>
                        <input type="text" class="form-control" name="fromDate" id="fromDate"/>
                    </div>
                </div>
            </div>
            <div class="customDateWidthBtn">
                <input class="form-control" type="button" id="customDateWidthSubmit" value="Go" />
            </div>
        </div>
        <div class="dashboard_dropdown" style="width: 10% !important;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <select class="form-control" id="dashboard_filter_dates">
                <option value="mtd">{{trans('dashboard.dashboard_time.mtd')}}</option>
                <option value="custom">{{trans('dashboard.dashboard_time.custom')}}</option>
            </select>
        </div>
    </div>
</div>
    <div class="row">
        <div class="item active">
        <?php 
            $rowNumber = 6;
            $count = 0;
            if(isset($order_details['dashboard'])){
            foreach ($order_details['dashboard'] as $key => $value)
            {
                $temp = 1;
                if ($count >= $rowNumber)
                {
                    $count = 0;
                    ?>
        </div>
        <div class="item">
        <?php } ?>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12" style="padding-right:0px;">
                <div class="dashboard-stat2 bordered">
                    <?php
                        if(count($value) > 1){
                        foreach($value as $boxElments)
                        {
                    ?>                
                        <div class="display">
                            <div class="number">
                                    <h3 class="font-green-sharp down-count">
                                    <span data-counter="counterup">
                                            <?php
                                                $field_key = property_exists($boxElments, 'key') ? $boxElments->key : '';                                        
                                                $field_id = '';
                                                if($field_key != '')
                                                {
                                                    $field_id = strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '_', $field_key));                 
                                                }                    
                                                $val = property_exists($boxElments, 'val') ? $boxElments->val : 0;
                                                $per = property_exists($boxElments, 'per') ? $boxElments->per : ''; 
                                            ?>  
                                        <span class="data_value" id="<?php echo $field_id; ?>">
                                                <?php echo $val; ?>
                                        </span>
                                            @if($per != '')
                                                <span class="data_per" id="data_per_<?php echo $field_id; ?>">{{ $per }}</span>
                                            @endif
                                        <div class="loader">Loading...</div>
                                    </span>
                                </h3>
                                    <div class="progress-info">
                                        <div class="status neww1">
                                            <div class="status-title"> <a href="javascript:void(0);" id="name_wh_<?php echo $field_id; ?>" name = "similiar"><?php echo $field_key; ?> </a> </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                            <?php if($temp == 1) { ?>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                            <span class="sr-only">76% progress</span>
                                        </span>
                                    </div>
                                </div>
                            <?php } $temp++; ?>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="display">
                            <div class="number">
                                <h3 class="font-green-sharp">
                                    <span data-counter="counterup">
                                        <?php
                                            $details = isset($value[0]) ? $value[0] : [];
                                            $field_key = '';
                                            $field_id = '';
                                            $value = 0;
                                            $pre = '';                                                                                    
                                            if(!empty($details)){
                                                $field_key = property_exists($details, 'key') ? $details->key : '';
                                                if($field_key != '')
                                                {
                                                    $field_id = strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '_', $field_key));                                                                                            
                                                }                    
                                                $val = property_exists($details, 'val') ? $details->val : 0;
                                                $per = property_exists($details, 'per') ? $details->per : '';    
                                            }
                                        ?>                                    
                                        <span class="data_value" id="<?php echo $field_id; ?>">
                                            <?php echo $val; ?>
                                        </span>
                                        @if($per != '')
                                            <span class="data_per" id="data_per_<?php echo $field_id; ?>">{{ $per }}</span>
                                        @endif
                                        <div class="loader">Loading...</div>
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                    <span class="sr-only">76% progress</span>
                                </span>
                            </div>
                            <div class="status neww1">
                                <div class="status-title"> <a href="javascript:void(0);" id="name_wh_<?php echo $field_id; ?>" name = "similiar"><?php echo $field_key; ?> </a> </div>
                            </div>
                        </div>
                    <?php } ?>                    
                    </div>
                </div>
        <?php if ($count >= $rowNumber)
        { ?>
                
        </div>
            <?php } $count++; ?>
        <?php 
}
    } ?>
    
        </div>
        </div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs" id="bottomTabs">
                        @if(isset($tab_access['sales']) and $tab_access['sales'] == true)
                        <li class="ch_tabs">
                            <a href="#ff_list_tab" id="ff_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.sales_target')}}</a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="ff_list_tab">
                            <div id="sales_target_error" class="hideError" align="center"></div>
                            <div id="sales_target_table"><table id="sales_target" style="white-space: nowrap;"></table></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<!-- Custom Dashboard Style -->
<link href="{{ URL::asset('assets/global/css/dashboard-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
<!-- Custom Dashboard JS -->
<script src="{{ URL::asset('assets/global/scripts/sales-target.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>

    <!--<script>
        $(function(){
            // Custom Title for The Dashboard
            $(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('dashboard.dashboard_title.page_title')}}");
        });
        var socket = io('<?php echo env('SOCKET_IO') ?>');
        socket.on("dashboard-channel", function(message){
            let legal_entity_id = message.data.legal_entity_id;
            let le_wh_id = message.data.le_wh_id;
            if(legal_entity_id === $("#legal_entity_id").val() && le_wh_id==$("#wh_id").val()){
                console.log("Socket Updated with LeId "+legal_entity_id);
                var order_details = message.data.data;
                $.each(order_details, function (key2, dashboard) {
                    $.each(dashboard, function (key3, dashboardData) {
                        var key3 = dashboardData.key;
                        var val3 = dashboardData.val;
                        var per3 = dashboardData.per;
                        var test3 = key3.toLowerCase();
                        var temp3 = test3.replace(/[^A-Z0-9]/ig, "_");
                        $('#' + temp3).text(val3);
                        $('#data_per_' + temp3).text(per3);
                    });                                
                });
                $('#last_updated').text(message.data.time);
            }
        });
        $("#bottomTabs > li :first").addClass("active");
    </script>-->
@stop
@extends('layouts.footer')