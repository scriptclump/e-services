@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('dashboard.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('dashboard.dashboard_heads.today_reports')}}</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
    <span style="color : #795548; float:right;">
        <i class="fa fa-clock-o" aria-hidden="true"> {{trans('dashboard.dashboard_heads.last_updated')}}: 
            <span id="last_updated">@if(isset($last_updated)){{$last_updated}}@endif</span>
        </i>
    <span>
</ul>
<div class="page-head">
    
    <div>
       
        <div style="width: 58%; float: left;">
            <div class="page-title">
                <h1>{{trans('dashboard.dashboard_heads.today_reports')}}</h1>  
            </div>
        </div>
        <div class="customDateArea" id="customDatesView">
            <div class="customDateWidth" style="width: 25% !important;">
                <div class="form-group" id="customDatePickerZone">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="form-control" name="fromDate" id="fromDate"/>
                        <span class="input-group-addon">to</span>
                        <input type="text" class="form-control" name="toDate" id="toDate"/>
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
                <option value="today">{{trans('dashboard.dashboard_time.today')}}</option>
                <option value="yesterday">{{trans('dashboard.dashboard_time.yesterday')}}</option>
                <option value="wtd">{{trans('dashboard.dashboard_time.wtd')}}</option>
                <option value="mtd">{{trans('dashboard.dashboard_time.mtd')}}</option>
                <option value="ytd">{{trans('dashboard.dashboard_time.ytd')}}</option>
                <option value="custom">{{trans('dashboard.dashboard_time.custom')}}</option>
            </select>
        </div>
    </div>
</div>

<div class="row" style="padding-bottom: 10px">
                  <div class="col-md-2">  
                      <input type="hidden" id="bu_id" value="{{$buid}}">
                      <select class="form-control select2me" id="dc_all_legalentity">
                        <option value=''>Please Select</option>
                     </select>
                  </div>
                  <div class="col-md-2">
                      <select class="form-control select2me" id="category">
                     </select>
                  </div>
                  <div class="col-md-2">
                     <select class="form-control select2me" id="manufacturer_le_ids">
                        <option value=''>Please Select Manufacturer</option>
                        @foreach($manufacturer['manufacturer'] as $key=>$value)
                        <option value="{{ $key }}" >{{$value}}</option>
                        @endforeach
                     </select>
                 </div>
                 <div class="col-md-2">
                     <select class="form-control select2me" id="brands_le_id">
                        <option value=''>Please Select Brand</option>
                       
                     </select>
                 </div>
                 <div class="col-md-2">
                     <select class="form-control select2me" id="product_group_ids">
                        <option value=''>Please Select Product Group</option>
                     </select>
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
                            <a href="#ff_list_tab" id="ff_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.ff_list')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['newCustomers']) and $tab_access['newCustomers'] == true)
                        <li class="ch_tabs">
                            <a href="#new_onboard_outlets_list_tab" id="new_onboard_outlets_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.new_outlets')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['selfOrders']) and $tab_access['selfOrders'] == true)
                        <li class="ch_tabs">
                            <a href="#self_orders_list_tab" id="self_orders_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.self_orders')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['delivery']) and $tab_access['delivery'] == true)
                        <li class="ch_tabs">
                            <a href="#delivery_list_tab" id="delivery_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.delivery')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['picking']) and $tab_access['picking'] == true)
                        <li class="ch_tabs">
                            <a href="#pickers_list_tab" id="pickers_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.pickers')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['verification']) and $tab_access['verification'] == true)
                        <li class="ch_tabs">
                            <a href="#verification_list_tab" id="verification_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.verification')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['shrinkage']) and $tab_access['shrinkage'] == true)
                        <li class="ch_tabs">
                            <a href="#shrinkage_list_tab" id="shrinkage_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.shrinkage')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['collections']) and $tab_access['collections'] == true)
                        <li class="ch_tabs">
                            <a href="#collections_list_tab" id="collections_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.collections')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['vehicles']) and $tab_access['vehicles'] == true)
                        <li class="ch_tabs">
                            <a href="#vehicles_list_tab" id="vehicles_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.vehicles')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['logistics']) and $tab_access['logistics'] == true)
                        <li class="ch_tabs">
                            <a href="#logistics_list_tab" id="logistics_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.logistics')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['inventory']) and $tab_access['inventory'] == true)
                        <li class="ch_tabs">
                            <a href="#inventory_list_tab" id="inventory_list" data-toggle="tab">{{trans('dashboard.dashboard_bottom_grid.inventory')}}</a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="ff_list_tab">
                            <div id="ff_list_error" class="hideError" align="center"></div>
                            <div id="ff_list_table"><table id="dashboard_ff_list" style="white-space: nowrap;"></table></div>
                        </div>
                        <div class="tab-pane" id="new_onboard_outlets_list_tab">
                            <div id="new_onboard_outlets_list_error" class="hideError" align="center"></div>
                            <div id="new_onboard_outlets_list_table"><table id="dashboard_new_onboard_outlets_list" style="white-space: nowrap;"></table></div>
                        </div>
                        <div class="tab-pane" id="self_orders_list_tab">
                            <div id="self_orders_list_error" class="hideError" align="center"></div>
                            <div id="self_orders_list_table"><table id="dashboard_self_orders_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="delivery_list_tab">
                            <div id="delivery_list_error" class="hideError" align="center"></div>
                            <div id="delivery_list_table"><table id="dashboard_delivery_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="pickers_list_tab">
                            <div id="pickers_list_error" class="hideError" align="center"></div>
                            <div id="pickers_list_table"><table id="dashboard_pickers_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="verification_list_tab">
                            <div id="verification_list_error" class="hideError" align="center"></div>
                            <div id="verification_list_table"><table id="dashboard_verification_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="shrinkage_list_tab">
                            <div id="shrinkage_list_error" class="hideError" align="center"></div>
                            <div id="shrinkage_list_table"><table id="dashboard_shrinkage_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="collections_list_tab">
                            <div id="collections_list_error" class="hideError" align="center"></div>
                            <div id="collections_list_table"><table id="dashboard_collections_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="vehicles_list_tab">
                            <div id="vehicles_list_error" class="hideError" align="center"></div>
                            <div id="vehicles_list_table"><table id="dashboard_vehicles_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="logistics_list_tab">
                            <div id="logistics_list_error" class="hideError" align="center"></div>
                            <div id="logistics_list_table"><table id="dashboard_logistics_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
                        </div>
                        <div class="tab-pane" id="inventory_list_tab">
                            <div id="inventory_list_error" class="hideError" align="center"></div>
                            <div id="inventory_list_table"><table id="dashboard_inventory_list" style="white-space: nowrap; table-layout: auto !important;"></table></div>
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
<style type="text/css">
    .textRightAlign {
        text-align:right !important;
    }
    .bu1{
    margin-left: 10px;
    font-size: 18px;
    color:#000000;
}
.bu2{
    margin-left: 20px;
    font-size: 16px;
    color:#1d1d1d;
}.bu3{
    margin-left: 30px;
    font-size: 15px;
    color:#3a3a3a;
}.bu4{
    margin-left: 40px;
    font-size: 14px;
    color:#535353;
}.bu5{
    margin-left: 50px;
    font-size: 13px;
    color: #6d6c6c;
}.bu6{
    margin-left: 60px;
    font-size: 11px;
    color:#868383;
}
</style>
<!-- Custom Dashboard JS -->
<script src="{{ URL::asset('assets/global/scripts/dashboard-custom.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>
<!-- <script src="http://localhost:3000/socket.io/socket.io.js"></script> -->


    <script>
        $(function(){
            // Custom Title for The Dashboard
            $(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('dashboard.dashboard_title.page_title')}}");
        });
        var socket = io('<?php echo env('SOCKET_IO') ?>');
        //var socket = io('http://localhost:3000');
        socket.on("dashboard-channel", function(message){
            let bu_id = message.data.bu_id;
            if(bu_id === $("#bu_id").val()){
                console.log("Socket Updated with BUId "+bu_id);
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
                //$('#test').text(message.data.test);
                $('#manufacturer_le_ids').select2("val", '');
                $('#brands_le_id').select2("val", '');
                $('#product_group_ids').select2("val", '');
            }
        });
        $("#bottomTabs > li :first").addClass("active");
    </script>
@stop
@extends('layouts.footer')