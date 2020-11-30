@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<!-- Used to display header and lastUPdated date  -->
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('purchasedashboard.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('purchasedashboard.dashboard_heads.today_reports')}}</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
    <span style="color : #795548; float:right;">
        <i class="fa fa-clock-o" aria-hidden="true"> {{trans('purchasedashboard.dashboard_heads.last_updated')}}: 
            <span id="last_updated">@if(isset($last_updated)){{$last_updated}}@endif</span>
        </i>
    <span>
</ul>
<!-- End header text -->

<div class="page-head">
<!-- Used to display page title and date drop down -->
    <div>
    <div style="width: 58%; float: left;">
            <div class="page-title">
                <h1>{{trans('purchasedashboard.dashboard_heads.today_reports')}}</h1>  
            </div>
    </div>
<!-- Start Date filter -->
    <div class="customDateArea" id="customDatesView">
        <div class="customDateWidth" style="width: 25% !important;">
            <div class="form-group" id="customDatePickerZone">
                <div class="input-daterange input-group" id="datepicker">
                    <input type="text" class="form-control" name="fromDate" id="fromDate" autocomplete="Off"/>
                    <span class="input-group-addon">to</span>
                    <input type="text" class="form-control" name="toDate" id="toDate" autocomplete="Off"/>
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
            <option value="today">{{trans('purchasedashboard.dashboard_time.today')}}</option>
            <option value="yesterday">{{trans('purchasedashboard.dashboard_time.yesterday')}}</option>
            <option value="wtd">{{trans('purchasedashboard.dashboard_time.wtd')}}</option>
            <option value="mtd">{{trans('purchasedashboard.dashboard_time.mtd')}}</option>
            <option value="last_month">{{trans('purchasedashboard.dashboard_time.lastMonth')}}</option>
            <option value="ytd">{{trans('purchasedashboard.dashboard_time.ytd')}}</option>
            <option value="custom">{{trans('purchasedashboard.dashboard_time.custom')}}</option>
        </select>
    </div>
    </div>
<!-- End -->
</div>

<!-- Start of different filters like Bu , categories , products , saleds type -->
<div class="row" style="padding-top: 5px; padding-bottom :10px;">
                 <!-- Bussiness Unit  -->
                <div class="col-md-2">  
                    <input type="hidden" id="bu_id" value="{{$buid}}">
                    <select class="form-control select2me" id="dc_all_legalentity">
                    <option value=''>Select Business Unit</option>
                    </select>
                </div>

                <!-- categories filter -->
                <div class="col-md-2">
                    <select class="form-control select2me" id="category">
                    <option value=''>Select categories</option>
                    </select>
                </div>
                <!-- End of date filter -->

                <!-- start  Manufactures filter  -->

                <div class="col-md-2">
                    <select class="form-control select2me" id="manufacturer_le_ids">
                    <option value=''>Select Manufacturer</option>
                    @foreach($manufacturer['manufacturer'] as $key=>$value)
                    <option value="{{ $key }}" >{{$value}}</option>
                    @endforeach
                    </select>
                </div>
                <!-- End of manufactures filter -->

                <!-- start of brand filter(based on selected value we were calling procedure to get dashboard values )  -->
                <div class="col-md-2">
                    <select class="form-control select2me" id="brands_le_id">
                    <option value=''>Select Brand</option>
                    
                    </select>
                </div>
                <!-- ENd brand filter -->

                <!-- Start of product group filter ( based on select value we were calling procedure to get value for dashboard ) -->
                <div class="col-md-2">
                     <select class="form-control select2me" id="product_group_ids">
                        <option value=''>Select Product Group</option>
                     </select>
                </div>
                 <!-- End of product group filter  -->

                 <!-- Start of salesOption filter  (Based on access we were displaying this filter) -->

                 @if(isset($primarysalesaccess) && $primarysalesaccess==1)
                 <div class="col-md-2">
                     <select class="form-control select2me" id="primary_secondary_sales">
                        <option value=''>Please Select</option>
                        <option value='1' @if ($primary_secondary_sales == "1") {{ 'selected' }} @endif>All</option>
                        <option value='2' @if ($primary_secondary_sales == "2") {{ 'selected' }} @endif>DC</option>
                        <option value='3' @if ($primary_secondary_sales == "3") {{ 'selected' }} @endif>FC</option>
                        
                     </select>
                 </div>
                 @endif
             
                 <!-- ENd of salesType filter -->
</div>

<!-- Supplier  -->
<div class=" pull-right" style="width: 14% !important;padding-bottom :10px;">  
    <select class="form-control select2me" id="supplier">
        <option value=''>Select Supplier</option>
        @foreach($supplier as $key)
        <option value="{{ $key['legal_entity_id'] }}" >{{$key['business_legal_name']}}</option>
        @endforeach
        
    </select>
</div>
<!-- End of filters  -->





<!-- Block of code used to display value in dashboard  -->
<div class="row">
    <div class="item active">
        <?php 
            $rowNumber = 6;
            $count = 0;
            if(isset($PCDData))
            foreach ($PCDData as $key => $value)
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
                                        
                                    </span>
                                </h3>
                                    <div class="progress-info">
                                        <div class="status">
                                            <div class="status-title"> <a href="javascript:void(0);" id="<?php echo $field_id; ?>"><?php echo $field_key; ?> </a> </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                            <?php if($temp == 1) { ?>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
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
                                   
                                </span>
                            </div>
                            <div class="status">
                                <div class="status-title"> <a href="javascript:void(0);" id="<?php echo $field_id; ?>"><?php echo $field_key; ?> </a> </div>
                            </div>
                        </div>
                    <?php } ?>                    
                    </div>
                </div>
        <?php if ($count >= $rowNumber)
        { ?>
                
        </div>
            <?php } $count++; ?>
        <?php } ?>
    
        </div>
</div>
<!-- End of block -->

<!-- <div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs" id="bottomTabs">
                        @if(isset($tab_access['poOrderDetails']) and $tab_access['poOrderDetails'] == true)
                        <li class="ch_tabs">
                            <a href="#po_order_details_list_tab" id="po_order_details_list" data-toggle="tab">{{trans('purchasedashboard.dashboard_bottom_grid.po_order_details')}}</a>
                        </li>
                        @endif
                        @if(isset($tab_access['grnDetails']) and $tab_access['grnDetails'] == true)
                        <li class="ch_tabs">
                            <a href="#grn_details_list_tab" id="grn_details_list" data-toggle="tab">{{trans('purchasedashboard.dashboard_bottom_grid.grn_details')}}</a>
                        </li>
                        @endif
                         @if(isset($tab_access['inventoryDetails']) and $tab_access['inventoryDetails'] == true)
                        <li class="ch_tabs">
                            <a href="#inventory_details_list_tab" id="inventory_details_list" data-toggle="tab">{{trans('purchasedashboard.dashboard_bottom_grid.inventory_details')}}</a>
                        </li>
                        @endif
                        <div class="actions" style="margin-right:10px; "> 
                            <a type="button" id="salesexport" class="btn green-meadow pull-right">Export Po Details</a> 
                        </div>
                       <form id="sales_details_export" action="/stockist/getexportdetails" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="exportbuId" id="exportbuId" value={{$buid}}>
                            <input type="hidden" name="fromDate" id="fromDate_export" value="">
                            <input type="hidden" name="toDate" id="toDate_export" value="">
                            <input type="hidden" name="filter_date" id="filterData_export" value="today">
                            <input type="hidden" name="exportflag" id='exportflag' value="2">

                       </form>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="po_order_details_list_tab">
                            <div id="po_order_details_list_error" class="hideError" align="center"></div>
                            <div id="po_order_details_list_table"><table id="purchasedashboard_po_order_details_list" style="white-space: nowrap;"></table></div>
                        </div>
                        <div class="tab-pane" id="grn_details_list_tab">
                            <div id="grn_details_list_error" class="hideError" align="center"></div>
                            <div id="grn_details_list_table"><table id="purchasedashboard_grn_details_list" style="white-space: nowrap;"></table></div>
                        </div>
                        <div class="tab-pane" id="inventory_details_list_tab">
                            <div id="inventory_details_list_error" class="hideError" align="center"></div>
                            <div id="inventory_details_list_table"><table id="purchasedashboard_inventory_details_list" style="white-space: nowrap;"></table></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- Block of code used to display data in grip format -->
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="ch_tabs active">
                            <a href="#ff_list_tab" id="ff_list" data-toggle="tab">{{trans('purchasedashboard.dashboard_bottom_grid.Po_details')}}</a>
                        </li>
                         <div class="actions" style="margin-right:10px; "> 
                            <a type="button" id="salesexport" class="btn green-meadow pull-right">Export Po Details</a> 
                        </div>
                       <form id="sales_details_export" action="/purchase/getexportdetails" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="exportbuId" id="exportbuId" value={{$buid}}>
                            <input type="hidden" name="fromDate" id="fromDate_export" value="">
                            <input type="hidden" name="toDate" id="toDate_export" value="">
                            <input type="hidden" name="filter_date" id="filterData_export" value="">
                            <input type="hidden" name="exportflag" id='exportflag' value="2">

                       </form>
                        <!-- Removed Invertory Tab UI Button here -->
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="ff_list_tab">
                            <div id="ff_list_error" class="hideError" align="center"></div>
                            <div id="ff_list_table"><table id="dashboard_purchase_list" style="white-space: nowrap;"></table></div>
                        </div>
                       
                        <!-- Removed Invertory Tab UI Area here -->
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of grid feature -->
@stop

@section('style')
<!-- Custom Dashboard Style -->
<link href="{{ URL::asset('assets/global/css/dashboard-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')

<!-- Css styling  -->
<style type="text/css">
.textRightAlign {
    text-align:right !important;
}
.textCenterAlign {
    text-align:center; !important;
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

.ui-iggrid .ui-iggrid-tablebody td {
    padding: 10px;
    font-size: 12px !important;
    font-weight: normal;
}
</style>

<!-- Custom Dashboard JS -->
<script src="{{ URL::asset('assets/global/scripts/purchase-custom.js') }}" type="text/javascript"></script>
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
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<!-- Used to customize dropdown -->
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />

<!-- Cdn used for socket io implementation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>

<script>
    $(function(){
        // Custom Title for The Dashboard
        $(document).attr("title", "{{trans('purchasedashboard.dashboard_title.company_name')}} - {{trans('purchasedashboard.dashboard_title.page_title')}}");
    });

    // Used to get updated buid
    $(function(){
        // Custom Title for The Dashboard
        var token=$('#csrf-token').val();
        var buid=$('#bu_id').val(); 
        $.ajax({
        type:'get',
        headers: {'X-CSRF-TOKEN': token},
        url:'/stockist/getbu',
        success: function(res){
            res.forEach(data=>{
                $('#dc_all_legalentity').append(data);
            });
            $("#dc_all_legalentity").select2("val", buid);
        }

    });
    });
    

    // Specific function is used for export feature 
    $("#salesexport").click(function(){

    var csrf_token = $('#csrf-token').val();
    var buid = $('#exportbuId').val();
    var flag = $('#exportflag').val();
    var filter_date = $('#dashboard_filter_dates').val();
    var todate=$("#toDate").val();
    var fromdate=$("#fromDate").val();
    $('#filterData_export').val(filter_date);
    /*if(){

    }else{*/
        $('#sales_details_export').submit();
    //} 
    })
    //End of export feature
    
    // $("#bottomTabs > li :first").addClass("active");
</script>

@stop
@extends('layouts.footer')   