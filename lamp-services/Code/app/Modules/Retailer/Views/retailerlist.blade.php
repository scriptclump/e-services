@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<style>
    .alert-info {
        background-color: #00c0ef !important;
        border-color: #00c0ef !important;
        color: #fff !important;
    }
    .ui-iggrid-fixedcontainer-right{
        border-left: 1px solid #DCDCDC !important;
    }
    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table {
        border-width: 0!important;
        border-spacing: 0px!important;
    }
</style>

<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>

<div class="portlet-body">
    @if (Session::has('flash_message'))            
    <div class="alert alert-info">{{ Session::get('flash_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="float: right;">
            &times;
        </button>
    </div>
    @endif                    
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{trans('retailers.title.index_page_title')}} </div>
                <div class="tools">
                    <?php if(isset($buttonPermissions['RET006']) && $buttonPermissions['RET006'] == 1){ ?>
                    <button type="button" href="#exportCustomers" data-toggle="modal" class="btn green-meadow" id="exportExcel">{{trans('retailers.tab.export_excel')}}</button>
                    <?php } ?>
                    <?php if(isset($buttonPermissions['RET007']) && $buttonPermissions['RET007'] == 1){ ?>
                    <button type="button" href="#importCustomers" data-toggle="modal" class="btn green-meadow" id="importExcel">{{trans('retailers.tab.import_retailers')}}</button>
                    <?php } ?>
                    <?php if(isset($buttonPermissions['RET008']) && $buttonPermissions['RET008'] == 1){ ?>
                    <button type="button" class="btn green-meadow" id="sendSms">{{trans('retailers.tab.send_sms')}}</button>
                    <?php } ?>

                    @if(isset($creditlimitPermissions) && ($creditlimitPermissions == 1))

                    <a style = "height:32.88px" href="#" data-id="#" data-toggle="modal" data-target="#uploadCreditlimit" class="btn green-meadow">Upload Credit Limit</a>

                    @endif

                    @if(isset($creditlimitDownloadPermissions) && ($creditlimitDownloadPermissions == 1))

                    <a style = "height:32.88px" href="/creditLimitDonwload" class="btn green-meadow" id="">Download Credit Limit</a>

                    @endif
                   
                   
                    <span class="badge bg-blue">
                        <a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Click here for Fullscreen">
                            <i class="fa fa-question-circle-o"></i>
                        </a>
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style="height: 850px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                            <div class="table-responsive">
                                <span id="flass_message"></span>
                                <table id="retalir_list_grid"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">
<div class="modal modal-scroll fade in" id="exportCustomers" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
                <h4 class="modal-title" id="basicvalCode">{{trans('retailers.tab.export_retailers')}}</h4>
            </div>
            <div class="modal-body">
                <form id="exportCustomersForm" action="/retailers/exportCustomers" class="text-center" method="get">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="fdate" name="fdate" class="form-control" placeholder="From Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="tdate" name="tdate" class="form-control" placeholder="To Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span>{{trans('retailers.tab.export_note')}} 
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">{{trans('retailers.button.download')}} </button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-scroll fade in" id="importCustomers" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">{{trans('retailers.tab.import_retailers')}}</h4>
            </div>
            <div class="modal-body">
                <form id="importCustomersForm"  class="text-center" method="post"  enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="file" name="import_retailers" id="import_retailers" class="form-control" placeholder="Upload" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />                                        
                                    </div>
                                    <span class="form-group">{{trans('retailers.tab.import_retailers')}}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-md-12 text-center">
                                        <button  id="upload_retailer_file" class="btn green-meadow">{{trans('retailers.button.import')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                {{ Form::open(array('url' => 'retailers/importTemplate', 'id' => 'importTemplate'))}}
                  <div class="row" style="text-align: center;">
                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                       <div class="col-md-12">
                        <button type="submit" class="btn green-meadow" id="download-excel">Download Excel File</button>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
                    </div>
                 </div>
                 {{ Form::close() }}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



<div class="modal modal-scroll fade in" id="uploadCreditlimit" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Upload Credit Limit </h4>
            </div>
            <div class="modal-body">
                <form id="uploadCreditlimitForm" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="file" name="Creedit_Limit" id  = "Creedit_Limit" class="form-control" placeholder="Upload" />                                        
                                    </div>
                                    <span class="form-group"> </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                   
                                    <div class="col-md-12 text-center">
                                       <button type="button" id="upload_Creedit_Limit" class="btn green-meadow">{{trans('retailers.button.import')}}</button>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                {{ Form::open(array('url' => 'retailers/downloadCreditLimitTemplate', 'id' => 'downloadexcel'))}}
                  <div class="row" style="text-align: center;">
                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                       <div class="col-md-12">
                        <button type="submit" class="btn green-meadow" id="download-excel">Download Excel File</button>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp  
                    </div>
                 </div>
                 {{ Form::close() }}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



<!-- 
<div class="modal modal-scroll fade in" id="downloadCreditlimit" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="downloadCreditlimitpopup">x</button>
                <h4 class="modal-title" id="basicvalCode">Download Credit Limit </h4>
            </div>
            <div class="modal-body">
                <form action="" method="POST" onsubmit="return callTrigger()">
                    <div id="dimici_filter_div">
                        <div class="row">
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <div class="input-icon input-icon-sm right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" name="creditlimit_date_from" id="creditlimit_date_from" class="form-control start_date dp" value="" autocomplete="off" placeholder="{{ trans('gstReportLabels.from_date') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <div class="input-icon input-icon-sm right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" name="creditlimit_date_to" id="creditlimit_date_to" class="form-control end_date dp" value="" autocomplete="off" placeholder="{{ trans('gstReportLabels.to_date') }}">
                                    </div>
                                </div>                        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="text-align: center;">
                                    <input type="submit" value="{{ trans('gstReportLabels.submit') }}" class="btn green-meadow">
                                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                            </div>
                        </div>                      
                    </div>
            </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div> -->


<div class="modal modal-scroll fade in" id="sendSMSModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">{{trans('retailers.tab.send_sms')}}</h4>
            </div>
            <div class="modal-body">
                <form id="send_sms_form" action="/retailers/sendsms" class="text-center" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="$filter" id="filter_query" value="">
                    <input type="hidden" name="$orderby" id="orderby_query" value="">
                    <input type="hidden" name="pageSize" value="100000">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">                                
                                <label class="control-label">{{trans('retailers.tab.sms_message')}}<span class="required">*</span></label>
                                <textarea name="sms_message" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" id="send_sms_button" class="btn green-meadow">{{trans('retailers.button.send')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@stop
@section('style')

<style>
    .switch-label:before, .switch-label:after {
    left: 46% !important;
  
    }
    .switch {
        left: -15px !important;
    }
    .switch-label {
        font-size: 9px !important;
    }
    .centerAlignment{
        text-align : center;
    }
    .rightAlignment{
        text-align : right;
    }
    .ui-iggrid .ui-widget-content{
    border-spacing: 0px !important; 
    }
    .ui-widget-content a {
   line-height: 44px !important;
}
.ui-iggrid-fixedcontainer-right {
    border-left: 1px solid #e9ecf3 !important;
}
.slimScrollDiv{
    height: 550px !important;
}
</style>
@stop
@section('userscript')
@include('includes.ignite')
@include('includes.validators')
{{HTML::script('assets/global/plugins/igniteui/FileSaver.js')}}
{{HTML::script('assets/global/plugins/igniteui/Blob.js')}}
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script type="text/javascript">
function deleteLegalEntity(retid) {
    var decission = confirm("{{trans('retailers.windows.confirm_delete')}}");
    if (decission == true) {
        $.ajax({
            method: "GET",
            url: '/retailers/delete',
            data: "retId=" + retid,
            success: function (data) {
                console.log(data);
                //var data = $.parseJSON(data);
                console.log(data);
                if (data.status) {
                    $('#flass_message').text('Deleted successfully');
                    $("#retalir_list_grid").igGrid("dataBind");
                }else{
                    $('#flass_message').text('Unable to delete record');
                }
                $('div.alert').show();
                $('div.alert').removeClass('hide'); 
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 500);
            }
        });                 
    }
}
function blockLegalEntity(retid, isChecked, event) {
    if(!isChecked)
    {
        var decission = confirm("{{trans('retailers.windows.confirm_block')}}");
        isChecked = 0;
    }else{
        var decission = confirm("{{trans('retailers.windows.confirm_unblock')}}");
        isChecked = 1;
    }
    if (decission == true) {
        $.ajax({
            method: "GET",
            url: '/retailers/blockusers',
            data: "retId=" + retid+"&status="+isChecked,
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.status) {
                    $('#flass_message').text('Updated successfully');
                    $("#retalir_list_grid").igGrid("dataBind");
                }else{
                    $('#flass_message').text("{{trans('retailers.windows.unable_block')}}");
                }
                $('div.alert').show();
                $('div.alert').removeClass('hide'); 
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 500);
            }
        });                 
    }else{
        event.preventDefault();
    }
}
function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}
function getNextDay(select_date){
    select_date.setDate(select_date.getDate());
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth()+1),2)+'/'+zeroPad(setdate.getDate(),2)+'/'+setdate.getFullYear();
    return nextdayDate;
}
$(document).on('click', '.block_users', function (event) {
    var checked = $(this).is(":checked");
    var legalEntityId = $(this).val();
    blockLegalEntity(legalEntityId, checked, event);
});
    $(document).ready(function(){    
    $('#uploadfile').click(function(){
        $('#exportCustomers').find('button.close').trigger('click');
    });
    $('#upload_file').click(function(){
        $('#importCustomers').find('button.close').trigger('click');
    });
    $('#upload_Creedit_Limit').click(function(){
        $('#uploadCreditlimit').find('button.close').trigger('click');
    });

    $('#upload_retailer_file').click(function(){
        $('#importCustomers').find('button.close').trigger('click');
    });
    
    $('#fdate').datepicker({
        maxDate:0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var nextdayDate = getNextDay(select_date);
            $('#tdate').datepicker('option', 'minDate', nextdayDate);
        }
    });
    $('#tdate').datepicker({
        maxDate:0,
    });
        $('#retalir_list_grid').igGrid({
            dataSource: '/retailers/getRetailers',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            recordCountKey: 'totalCustomerCount',
            generateCompactJSONResponse: false,
            enableUTCDates: true, 
            renderCheckboxes: true,
            columns: [
                {headerText: "{{trans('retailers.grid.retailer_code')}}", key: 'le_code', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.grid.shop_name')}}", key: 'business_legal_name', dataType: 'string', width: '200px'},
                {headerText: "{{trans('retailers.form_fields.retailer_type')}}", key: 'legal_entity_type', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.form_fields.segment_type')}}", key: 'business_type', dataType: 'string', width: '100px'},
                {headerText: "{{trans('retailers.grid.name')}}", key: 'name', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.grid.warehouse')}}", key: 'DC', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.form_fields.volume_class')}}", key: 'volume_class', dataType: 'string', width: '100px', columnCssClass: "centerAlignment"},                       
                {headerText: "{{trans('retailers.form_fields.shutters')}}", key: 'No_of_shutters', dataType: 'number', width: '100px', columnCssClass: "centerAlignment"},
                {headerText: "{{trans('retailers.form_fields.other_suppliers')}}", key: 'suppliers', dataType: 'string', width: '200px'},
                {headerText: "{{trans('retailers.form_fields.smart_phone')}}", key: 'smartphone', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.form_fields.internet_availabilty')}}", key: 'network', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.grid.address')}}", key: 'address', dataType: 'string', width: '200px', height: '200px'},
                {headerText: "{{trans('retailers.grid.area_name')}}", key: 'area', dataType: 'string', width: '200px'},
                {headerText: "{{trans('retailers.grid.city')}}", key: 'city', dataType: 'string', width: '100px'},
                {headerText: "{{trans('retailers.grid.state')}}", key: 'state', dataType: 'string', width: '70px'},
                {headerText: "{{trans('retailers.grid.pincode')}}", key: 'pincode', dataType: 'number', width: '100px'},
                {headerText: "{{trans('retailers.grid.created_date')}}", key: 'created_at', dataType: 'date', width: '100px'},
                {headerText: "{{trans('retailers.grid.created_time')}}", key: 'created_time', dataType: 'string', width: '100px'},
                {headerText: "{{trans('retailers.form_fields.created_by')}}", key: 'created_by', dataType: 'string', width: '100px'},
                {headerText: "{{trans('retailers.grid.updated_date')}}", key: 'updated_at', dataType: 'date', width: '100px'},
                {headerText: "{{trans('retailers.grid.updated_time')}}", key: 'updated_time', dataType: 'string', width: '80px'},
                {headerText: "{{trans('retailers.form_fields.updated_by')}}", key: 'updated_by', dataType: 'string', width: '100px'},
                {headerText: "{{trans('retailers.grid.is_approved')}}", key: 'is_approved', dataType: 'string', width: '100px', columnCssClass: "centerAlignment"},
                {headerText: "{{trans('retailers.grid.orders')}}", key: 'orders', dataType: 'number', width: '100px', columnCssClass: "centerAlignment"},
                {headerText: "{{trans('retailers.form_fields.last_ordered_date')}}", key: 'last_order_date', dataType: 'date', width: '100px'},
                {headerText: "{{trans('retailers.grid.mobile')}}", key: 'mobile_no', dataType: 'string', width: '100px'},
                {headerText: "{{trans('retailers.grid.beat')}}", key: 'beat', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.grid.beat_rm_name')}}", key: 'beat_rm_name', dataType: 'string', width: '150px'},
                {headerText: "{{trans('retailers.grid.actions')}}", key: 'actions', dataType: 'string', width: '125px'},
                {headerText: "{{trans('retailers.grid.legal_entity_id')}}", key: 'legal_entity_id', dataType: 'number', width: '0px'}
            ],
            features: [
                {
                    name: "ColumnFixing",
                    fixingDirection: "right",
                    columnSettings: [
                        {columnKey: "le_code", allowFixing: false},
                        {columnKey: "DC", allowFixing: false},
                        {columnKey: "business_legal_name", allowFixing: false},
                        {columnKey: "legal_entity_type", allowFixing: false},
                        {columnKey: "business_type", allowFixing: false},
                        {columnKey: "volume_class", allowFixing: false},
                        {columnKey: "name",  allowFixing: false},
                        {columnKey: "No_of_shutters", allowFixing: false},
                        {columnKey: "area",  allowFixing: false},
                        {columnKey: "suppliers", allowFixing: false},
                        {columnKey: "address", allowFixing: false},
                        {columnKey: "network", allowFixing: false},
                        {columnKey: "smartphone", allowFixing: false},
                        {columnKey: "business_start_time", allowFixing: false},
                        {columnKey: "business_end_time", allowFixing: false},
                        {columnKey: "orders", isFixed: true, allowFixing: false},
                        {columnKey: "last_order_date", isFixed: true, allowFixing: false},
                        {columnKey: "created_at", allowFixing: false},
                        {columnKey: "created_by", allowFixing: false},
                        {columnKey: "created_time", allowFixing: false},
                        {columnKey: "updated_by", allowFixing: false},
                        {columnKey: "updated_at", allowFixing: false},
                        {columnKey: "updated_time", allowFixing: false},
                        {columnKey: "city", allowFixing: false},
                        {columnKey: "state", allowFixing: false},
                        {columnKey: "pincode", allowFixing: false},
                        {columnKey: "mobile_no", isFixed: true, allowFixing: false},
                        {columnKey: "beat", isFixed: true, allowFixing: false},
                        {columnKey: "beat_rm_name", isFixed: true, allowFixing: false},
                        {columnKey: "actions", isFixed: true, allowFixing: false},
                        {columnKey: "is_approved", allowFixing: false},
                        {columnKey: "legal_entity_id", allowFixing: false},
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'suppliers', allowFiltering: false},
                    {columnKey: 'network', allowFiltering: false},
                    {columnKey: 'smartphone', allowFiltering: false},
                    {columnKey: 'created_time', allowFiltering: false},
                    {columnKey: 'updated_time', allowFiltering: false},
                    {columnKey: 'is_approved', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                    {columnKey: 'updated_time', allowSorting: false},
                    {columnKey: 'created_time', allowSorting: false},
                    {columnKey: 'suppliers', allowSorting: false},
                    {columnKey: 'is_approved', allowSorting: false}
               ]
            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: "totalCustomerCount",
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Resizing",
            },
        ],
        primaryKey: 'legal_entity_id',
        width: '100%',
        height: '500px',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        type: 'remote',
        showHeaders: true,
        fixedHeaders: true,
        rendered: function (evt, ui) {
            $("#retalir_list_grid").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#retalir_list_grid").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();  
            $("#retalir_list_grid").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericonnextyear").closest("li").remove();
            $("#retalir_list_grid").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $(".ui-iggrid-filtericonnextmonth").each(function(){
                $(this).closest("li").remove();
            });     
            $(".ui-iggrid-filtericonlastmonth").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonthisyear").each(function(){
                $(this).closest("li").remove();
            });     
            $(".ui-iggrid-filtericonlastyear").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonnextyear").each(function(){
                $(this).closest("li").remove();
            });     
            $(".ui-iggrid-filtericonthismonth").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericondoesnotequal").each(function(){
                $(this).closest("li").remove();
            }); $(".ui-iggrid-filtericonstartswith").each(function(){
                $(this).closest("li").remove();
            }); $(".ui-iggrid-filtericonendswith").each(function(){
                $(this).closest("li").remove();
            }); $(".ui-iggrid-filtericondoesnotcontain").each(function(){
                $(this).closest("li").remove();
            });
        }
    });
    $("#export-button").click(function(){
//            $.ig.GridExcelExporter.exportGrid($("#retalir_list_grid"), {
//                fileName: "Customers",
//                worksheetName: "Customers",
//                tableStyle: "tableStyleLight13",
//                columnsToSkip: ["actions", "is_approved"]
//            });
        $.ajax({
            method: "GET",
            url: '/retailers/exportCustomers',
//                data: "retId=" + retid+"&status="+isChecked,
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.status) {
                    $('#flass_message').text();
                    $("#retalir_list_grid").igGrid("dataBind");
                }else{
                    $('#flass_message').text("{{trans('retailers.windows.unable_block')}}");
                }
                $('div.alert').show();
                $('div.alert').removeClass('hide'); 
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 500);
            }
        });
    });
    $("#importCustomersForm").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
    //        valid: 'glyphicon glyphicon-ok',
    //        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        }/*,
        fields: {
            import_retailers: {
                validators: {
                    file: {
                        extension: 'xls',
                        type: 'application/vnd.ms-excel',
                        maxSize: 2097152,   // 2048 * 1024
                        message: 'The selected file is not valid'                        
                    }
                }
            }
        }*/
    });

   $("#uploadCreditlimitForm").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
    //        valid: 'glyphicon glyphicon-ok',
    //        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            import_retailers: {
                validators: {
                    file: {
                        extension: 'xlsx',
                        type: 'application/vnd.ms-excel',
                        maxSize: 2097152,   // 2048 * 1024
                        message: 'The selected file is not valid'                        
                    }
                }
            }
        }
    });
   
    $("#send_sms_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
    //        valid: 'glyphicon glyphicon-ok',
    //        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            sms_message: {
                validators: {
                    notEmpty: {
                        message: "{{trans('retailers.form_validate.empty_sms')}}",
                    }
                }
            }
        }
    });
    $('#sendSms').click(function(){
        var encodedUrl = $("#retalir_list_grid").data("igGrid").dataSource._encodeUrl();
        var filterQuery = encodedUrl.$filter;
        var orderbyQuery = encodedUrl.$orderby;
        if(typeof filterQuery == 'undefined')
        {
            var decission = confirm("{{trans('retailers.windows.confim_all_users_sms')}}");
            if (decission == true) {
                $("#sendSMSModel").modal('show');
            }
        }else{
            $("#sendSMSModel").modal('show');
        }
        $('#filter_query').val(filterQuery);
        $('#orderby_query').val(orderbyQuery);
    });





    // import mfc credit limit code here

    $('#upload_Creedit_Limit').click(function () {
        token  = $("#csrf-token").val();
        var stn_Doc = $("#Creedit_Limit")[0].files[0];
        if (typeof stn_Doc == 'undefined')
        {
            alert("Please select file");
            return false;
        }
        var formData = new FormData();
        formData.append('creditlimit_data', stn_Doc);
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/retailers/uploadCreditlimit",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){
            console.log(data);
            $("#flass_message").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
            $(".alert-success").fadeOut(80000);
            $('div.alert').show();
            $('div.alert').removeClass('hide');
            $('div.alert').not('.alert-important').delay(30000).fadeOut(3500);
                
            }
        });
    });

    $('#upload_retailer_file').click(function () {
        token  = $("#csrf-token").val();
        var stn_Doc = $("#import_retailers")[0].files[0];
        if (typeof stn_Doc == 'undefined')
        {
            alert("Please select file");
            return false;
        }
        var formData = new FormData();
        formData.append('import_retailers', stn_Doc);
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/retailers/importRetailers",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){
                alert(data.message);
                $("#flass_message").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                $(".alert-success").fadeOut(80000);
                $('div.alert').show();
                $('div.alert').removeClass('hide');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                
            }
        });
    });


});

    // $(document).ready(function () {
    //     $('#creditlimit_date_from').datepicker({
    //         dateFormat: 'yy-mm-dd',
    //         maxDate: 0,
    //         onSelect: function () {
    //             var select_date = $(this).datepicker('getDate');
    //             var nextdayDate = getNextDay(select_date);
    //             $('#creditlimit_date_to').datepicker('option', 'minDate', nextdayDate);
    //         }
    //     });
    //     $('#creditlimit_date_to').datepicker({
    //         dateFormat: 'yy-mm-dd',
    //         maxDate: '+0D',
    //     });
    // });

    // function getNextDay(select_date) {
    //     select_date.setDate(select_date.getDate());
    //     var setdate = new Date(select_date);
    //     var nextdayDate = setdate.getFullYear() + '-' + zeroPad((setdate.getMonth() + 1), 2) + '-' + zeroPad(setdate.getDate(), 2);
    //     return nextdayDate;
    // }

    // function zeroPad(num, count) {
    //     var numZeropad = num + '';
    //     while (numZeropad.length < count) {
    //         numZeropad = "0" + numZeropad;
    //     }
    //     return numZeropad;
    // }

    // function callTrigger() {
    //     var token = $("#token_value").val();
    //             startDate = $("#creditlimit_date_from").val();
    //             endDate = $("#creditlimit_date_to").val();
    //             formData = new Array();
    //     formData.push({"startDate": startDate, "endDate": endDate});
    //     if (startDate == "")
    //     {
    //         alert("Please select from date");
    //         $("#creditlimit_date_from").focus();
    //         return false;
    //     }
    //     if (endDate == "")
    //     {
    //         alert("Please select to date");
    //         $("#creditlimit_date_to").focus();
    //         return false;
    //     }
    //     $('#downloadCreditlimitpopup').click();
    //     $.ajax({
    //         type: "GET",
    //         url: "/creditLimitDonwload?_token=" + token,
    //         data: "filterDetails=" + JSON.stringify(formData),
    //         success: function (data)
    //         {
    //             alert();
    //             console.log(data);
    //             $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>' + data + '</div></div>');
    //             $(".alert-success").fadeOut(40000);
    //             $("#creditlimit_date_from").val('');
    //             $("#creditlimit_date_to").val('');
    //         },
    //         error: function (data)
    //         {
    //             console.log(data);
    //         }
    //     });
    // }

    // $('#creditlimitbutton').click(function(){
    //           $("#creditlimit_date_from").val('');
    //           $("#creditlimit_date_to").val('');
    // });   

</script>

@stop
@extends('layouts.footer')
