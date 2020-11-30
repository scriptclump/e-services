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
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        padding: 8px !important;
    }
    .text-disabled:hover{
        color: #0000ff;
    }
    .text-disabled{
        color: #bdbdbd !important;
    }
    .label-enabled{
        background-color: #89C4F4 !important;
    }
    .label-disabled{
        background-color: #bdbdbd !important;
    }
    a[id$="Tab"] {
        text-decoration: none;
    }
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url(/img/ajax-loader.gif) center no-repeat #fff;
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
<span class="loader" id="loader" style="display:none;"><img src=""/></span>
<div class="box">    
    <div class="box-header">
    </div>
</div>
<div class="portlet-body">
    @if (Session::has('flash_message'))            
    <div class="alert alert-info">{{ Session::get('flash_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="float: right;">&times;</button></div>
    @endif                    
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{trans('users.users_tab.user_users')}} </div>
                <div class="col-md-3" style="margin-left:50%">
                    <div class="form-group" style="padding-top: 10px">
                    <input type="hidden" id="hidden_buid" name="hidden_buid" value='<?php if (isset($bu_id) && $bu_id!=''){ echo $bu_id;}else{ echo '';}?>'>
                        <select id="business_unit_id" name="business_unit_id" class="form-control business_unit_id select2me" ></select>
                    </div>
                </div>
                <div class="actions">
                    @if($redeemPermission)
                        <a href="#redeemModal" class="btn green-meadow" data-toggle="modal">
                            <span style="font-size:11px;">{{trans('users.users_form_fields.redeem_btn')}} </span>
                        </a>
                    @endif
                    @if($excelExportPermission)
                        <a href="/users/exportusers" class="btn green-meadow">
                            <span style="font-size:11px;">{{trans('users.users_tab.user_export')}} </span>
                        </a>
                    @endif
                    @if($addPermission)
                        <a href="/users/addusers" class="btn green-meadow">
                            <i class="fa fa-plus-circle"></i>
                            <span style="font-size:11px;"> {{trans('users.users_tab.user_add')}} </span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="caption">
                            <span class="caption-subject bold font-blue" style="font-size: 15px"> Filter By :</span>
                            <span class="caption-helper sorting">                                
                                <a onclick="loadGridData('allUsersTab')" style="font-size: 14px" id="allUsersTab" data-toggle="tooltip"  title="All Users" >{{trans('users.users_form_fields.all_users_list')}}
                                (<span  id="allUsersCount"  >0</span>)</a>&nbsp;
                                <a onclick="loadGridData('activeUsersTab')" style="font-size: 14px" data-toggle="tooltip" id="activeUsersTab"  title="Active Users">{{trans('users.users_form_fields.active_users_list')}}
                                (<span id="activeUsersCount"  >0</span>)</a>&nbsp;
                                <a onclick="loadGridData('inActiveUsersTab')" style="font-size: 14px" id="inActiveUsersTab" data-toggle="tooltip" title="Inactive Users" >{{trans('users.users_form_fields.inactive_users_list')}}
                                (<span id="inActiveUsersCount">0</span>)</a>
                            </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row" style="padding: 7px 0px 0px 0px;">
                    <div class="col-md-12">
                        <div role="alert" id="alertStatus"></div>              
                        <div class="table-responsive">
                            <div id="stillLoadingMsg">{{trans('users.users_form_fields.still_loading_msg')}}</div>
                            <table id="userId"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
<div class="modal fade" id="AssignChildrenModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{trans('users.users_tab.user_manager')}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <p>{{trans('users.users_tab.user_manager_text')}}<br></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <!-- #newUserToAssign -->
                        {{trans('users.users_tab.user_manager_select')}}<br>
                        <div id="userLevelUsers">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br>
                        <input type="button" name="assignChildUserSubmitButton" id="assignChildUserSubmitButton" class="btn green-meadow" value="Assign">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="wizardCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">{{trans('users.users_tab.user_create')}}</h4>
            </div>
            <div class="modal-body" id="popupLoader" align="center" style="display: none">
                <img src="/img/ajax-loader.gif" >
            </div>
            <div class="modal-body" id="userDiv"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    <button data-toggle="modal" id="edit" class="btn btn-default" data-target="#wizardCodeModal" style="display: none"></button>
</div><!-- /.modal -->   

<div class="modal fade" id="redeemModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="basicvalCode">{{trans('users.users_form_fileds.users_cash_back_redeem')}}</h4>
            </div>
            <div class="modal-body">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active"><a data-toggle="tab" href="#exportRedeem">{{trans('users.users_form_fileds.users_cash_back_export')}}</a></li>
                        <li><a data-toggle="tab" href="#importRedeem">{{trans('users.users_form_fileds.users_cash_back_import')}}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="exportRedeem" class="tab-pane fade in active">
                        {{ Form::open(array('url' => '/users/exportredeem', 'id' => 'redeemExportForm'))}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('users.users_form_fileds.users_cash_back_fromdate')}} <span class="required">*</span></label>
                                            <div class='input-group date' id='fromDatePicker'>
                                                <input type="text" id="fromDate" name="fromDate" class="form-control" placeholder="From Date">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('users.users_form_fileds.users_cash_back_todate')}} <span class="required">*</span></label>
                                            <div class='input-group date' id='toDatePicker'>
                                                <input type="text" id="toDate" name="toDate" class="form-control" placeholder="To Date">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" align="center">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('users.users_form_fileds.users_cash_back_export')}}</label>
                                            <input type="submit" name="exportRedeemButton" id="exportRedeemButton" class="form-control btn green-meadow" value="{{trans('users.users_form_fileds.users_cash_back_export')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                        </div>
                        <div id="importRedeem" class="tab-pane fade">
                            {{ Form::open(array('url' => '/users/importredeem', 'id' => 'redeemImportForm', 'files' => true))}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('users.users_form_fileds.users_cash_back_import_file')}} <span class="required">*</span></label>
                                            <input type="file" id="importFile" name="importFile" accept=".xls" class="form-control">
                                            <span class="form-group">Accepted format .xls only</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('users.users_form_fileds.users_cash_back_import')}}</label>
                                            <input type="submit" name="importRedeemButton" class="form-control btn green-meadow" value="{{trans('users.users_form_fileds.users_cash_back_import')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    <button data-toggle="modal" id="edit" class="btn btn-default" data-target="#wizardCodeModal" style="display: none"></button>
</div><!-- /.modal -->   

@stop

@section('style')
<style type="text/css">
.fa-pencil {
    color: #3598dc !important;
}
.fa-trash-o {
    color: #3598dc !important;
}
.glyphicon-refresh {
    color: #3598dc !important;
}
.ui-iggrid-results {
    bottom: -3px !important;
}
.sorting a{ list-style-type:none !important;text-decoration:none !important;font-size: 12px;}
.sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting a:active{text-decoration:none !important;}
.active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
.inactive{text-decoration:none !important; color:#676767 !important;}

</style>
@stop

@section('script') 
@include('includes.ignite')
@include('includes.validators') 
@include('includes.jqx')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">

$(document).ready(function (){      
    $('#fromDatePicker, #toDatePicker').datetimepicker({ format: "DD/MM/YYYY",maxDate: moment().add(0, 'days') });
        
        $.ajaxSetup({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()}
        });
        
        // Route Handler
        $("[id$='Tab']").click(function(){
            if($(this).context.className == "text-disabled"){
                var selectedId = $(this).context.id;
                $('.text-enabled').each(function(i, obj) {
                    $("#"+$(this).context.id).attr("class","text-disabled");
                    $("#"+$(this).context.id.slice(0,-3)+"Count").attr("class","label label-disabled");
                });
                $("#"+selectedId).attr("class","text-enabled");
                $("#"+selectedId.slice(0,-3)+"Count").attr("class","label label-enabled");
               }
        });
        
        $('#business_unit_id').change(function(){
            loadGridData('activeUsersTab');
        })

    $(function(){
        var token=$('#csrf-token').val();
        var hidden_buid= 0;
        $.ajax({
            type:'get',
            headers: {'X-CSRF-TOKEN': token},
            url:'/users/getbu',
            success: function(res){
                res.forEach(data=>{
                    $('#business_unit_id').append(data);
                });
                if(res ==''){
                    $('#business_unit_id').select2('val',-1);
                    loadGridData('activeUsersTab');
                }else{
                    $('#business_unit_id').select2('val',hidden_buid);
                    loadGridData('activeUsersTab');
                }
            }
        });
    });
});
    /*==== Delete Legeal Entitys ====*/
    // Function to Animate Count of the Numbers in the UI
        function countNumbers() {
            $('.count').each(function () {
                $(this).prop('Counter',0).animate({
                        Counter: $(this).text()
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                });
            }
    function showTab(initial) {
            $.ajax({
                url: "/users/usersCount?bu_id="+$("#business_unit_id").val(),
                type: 'POST',
                dataType:"json",
                success: function (response)
                {
                    $("#stillLoadingMsg").hide();
                    if(initial){
                        // Updating Users Count
                        $("#allUsersCount").text(response.TotalRecordsCount);
                        $("#activeUsersCount").text(response.activeUsersCount);
                        $("#inActiveUsersCount").text(response.inActiveUsersCount);
                        countNumbers();
                    }
                },
            });
        }
    function loadGridData(selectedId){
                showTab(true);
                $('#userId').igGrid({
                    dataSource: '/users/usersList?showTab='+selectedId+'&bu_id='+$("#business_unit_id").val(),
                    type: "JSON",
                    //initialDataBindDepth: 0,
                    enableUTCDates: true,
                    autoGenerateColumns: false,
                    mergeUnboundColumns: false,
                    responseDataKey: "results",
                    generateCompactJSONResponse: false,
                    enableUTCDates: true,
                    width: "100%",
                    height: "100%",
                    columns: [
                        {headerText: 'Profile PIC', key: 'profile_picture', dataType: 'string', width: '10%'},
                        {headerText: 'Name', key: 'full_name', dataType: 'string', width: '24%'},
                        {headerText: 'Role', key: 'rolename', dataType: 'string', width: '20%'},
                        {headerText: 'Reporting Manager', key: 'reporting_manager', dataType: 'string', width: '20%'},
                        {headerText: 'Email ID', key: 'email_id', dataType: 'string', width: '25%'},
                        {headerText: 'Mobile No', key: 'mobile_no', dataType: 'string', width: '12%'},
                        {headerText: 'Emp ID', key: 'emp_code', dataType: 'string', width: '9%'},
                        {headerText: 'OTP', key: 'otp', dataType: 'string', width: '10%'},
                        {headerText: 'Active', key: 'is_active', dataType: 'string', width: '7%'},
                        {headerText: 'Impersonate', key: 'impersonate', dataType: 'string', width: '12%'},
                        {headerText: 'Actions', key: 'actions', dataType: 'string', width: '10%'}
                    ],
                    features:  [
                                    {
                            name: "Sorting",
                            type: "remote",
                            columnSettings: [
                            {columnKey: 'profile_picture', allowSorting: false },
                            {columnKey: 'is_active', allowFiltering: false },
                            {columnKey: 'impersonate', allowFiltering: false },
                            {columnKey: 'actions', allowSorting: false },
                             ]
                        },
                        {
                            name: "Filtering",
                            type: "remote",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings: [
                            {columnKey: 'profile_picture',allowFiltering: false },
                            {columnKey: 'is_active', allowFiltering: false },
                            {columnKey: 'impersonate', allowFiltering: false },
                            {columnKey: 'actions', allowFiltering: false },
                                
                            ]
                        },
                        { 
                           
                            name: 'Paging',
                            type: 'remote',
                            pageSize: 10,
                            recordCountKey: 'TotalRecordsCount',
                            pageIndexUrlKey: "page",
                            pageSizeUrlKey: "pageSize"
                             
         
                        }
            
                       ],                
                    primaryKey: "user_id",
                    width: '100%',
                    height: '100%',
                    initialDataBindDepth: 0,
                   // localSchemaTransform: false,
                });
                if(selectedId=='allUsersTab'){
                $("#allUsersTab").removeClass('inactive');
                $("#activeUsersTab").removeClass('active');
                $("#inActiveUsersTab").removeClass('active');
                $("#allUsersTab").addClass('active');
                $("#activeUsersTab").addClass('inactive');
                $("#inActiveUsersTab").addClass('inactive');
                }else if(selectedId=='activeUsersTab'){
                    $("#activeUsersTab").removeClass('inactive');
                    $("#allUsersTab").removeClass('active');
                    $("#inActiveUsersTab").removeClass('active');
                    $("#activeUsersTab").addClass('active');
                    $("#inActiveUsersTab").addClass('inactive');
                    $("#allUsersTab").addClass('inactive');
                }else if(selectedId=='inActiveUsersTab'){
                    $("#inActiveUsersTab").removeClass('inactive');
                    $("#activeUsersTab").removeClass('active');
                    $("#allUsersTab").removeClass('active');
                    $("#inActiveUsersTab").addClass('active');
                    $("#allUsersTab").addClass('inactive');
                    $("#activeUsersTab").addClass('inactive');
                }
  
            }
    function deleteEntityType(user_id,deleteType) {
        var decission = confirm("Are you sure you want to "+deleteType.charAt(0).toUpperCase() + deleteType.slice(1)+".");
        // console.log("decission "+decission);
        if (decission == true) {
            $.ajax({
                method: "GET",
                url: '/users/deleteUser',
                data: "userId=" + user_id +"&deleteType="+deleteType,
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status) {
                        $('#flash_message').text();
                        $("#userId").igGrid("dataBind");
                    }else if(data.modal){
                        $("#userLevelUsers").html(data.userLevelUsers);
                        $('#AssignChildrenModal').modal('show');
                    }else
                        $('#flash_message').text('Unable to Delete user, please contact admin.');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    window.location.assign('/users/index');
                }
            });                 
        }
    }

    /*==== Activate/Inactivate Users ====*/
    $(document).on('click', '.block_users', function (event) {
        var checked = $(this).is(":checked");
        var userId = $(this).val();
        blockLegalEntity(userId, checked, event);
    });

    function blockLegalEntity(userId, isChecked, event) {
        if(!isChecked)
        {
            var decission = confirm("Are you sure you want to In-Active the user.");
            isChecked = 0;
        }else{
            var decission = confirm("Are you sure you want to Active the user.");
            isChecked = 1;
        }
        event.preventDefault();
        if (decission == true) {
            $.ajax({
                method: "GET",
                url: '/users/blockuser',
                data: "userId=" + userId+"&status="+isChecked,
                success: function (response) {
                    console.log(response);
                    var data = $.parseJSON(response);
                    console.log(data);
                    if (data.status) {
                        $('#flash_message').text();
                        $('div.alert').show();
                        $('div.alert').removeClass('hide'); 
                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                        $('html, body').animate({scrollTop: '0px'}, 500);
                        alert('Successfully Updated');
                        window.location.assign('/users/index');
                    }else if(data.modal){
                        $("#userLevelUsers").html(data.userLevelUsers);
                        $('#AssignChildrenModal').modal('show');
                    }else{
                        alert('Unable to make changes, please contact admin.');
                    }
                },
                statusCode: {
                    500: function() {
                      alert("Sorry! you cannot Active/Inactive for this user.");
                    }
                }
            });                 
        }
    }

    $(document).on('click', '#assignChildUserSubmitButton', function (event) {
        var userId = $("#newUserToAssign").val();
        var oldUserId = $("#oldUserId").val();
        $.ajax({
            method: "GET",
            url: '/users/assignChildUserToParentUser',
            data: "userId=" + userId +'&oldUserId=' + oldUserId,
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.status) {
                    alert("The Reporting Manager had been Updated");
                    window.location.assign('/users/index');
                    /*$("#userId").igGrid("dataBind");*/
                }else
                    alert("Unable to Update the Reporting Manager. Please try again");
                $('#AssignChildrenModal').modal('hide');
            }
        });
    });

    $("#addUser").click(function () {
        $.get($(this).attr('data-url'), function (response) {
            $("#basicvalCode").html('Create New User');
            $("#userDiv").html(response);
        });
    });
    $("#addNewUser").click(function () {
        //alert($(this).attr('data-url'));
        $.get($(this).attr('data-url'), function (response) {
            $("#basicvalCode").html('Add New User');
            $("#userDiv").html(response);
        });
    });

    $("#exportRedeemButton").click(function () {
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        if(fromDate != '' || toDate != ''){
            window.setTimeout(function () {
                $("#redeemModal").modal('toggle');
            }, 2000);
        }
    });
    
    /*==== Edit Legeal Entitys ====*/
    function editUser(id) {
        $.get('/users/edit/' + id, function (response) {
            $("#basicvalCode").html('Edit User');
            $("#edit").click();
            $("#userDiv").html(response);
        });
    }

    function switchUser(id) {
        $.ajax({
            url: "/users/switchUser/" + id,
            data: id,
            type: "POST",
            success: function (result)
            {
                location.reload();
                location.href = '/';
            }
        });
    }
    
    $(document).ready(function () {
        window.setTimeout(function () {
            $(".alert").hide();
        }, 3000);
        $("#userId_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#userId_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#userId_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#userId_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#userId_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();  
        $('#redeemModal').on('hidden.bs.modal', function() {
            $('#redeemExportForm').formValidation('resetForm', true);
            $('#redeemImportForm').formValidation('resetForm', true);
        });

        $('#redeemExportForm')
        .formValidation({
            framework: 'bootstrap',
            excluded: ':disabled',
            fields: {
                fromDate: {
                    validators: {
                        notEmpty: {
                            message: '{{trans("users.users_form_validate.user_redeem_fromdate_required")}}'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            max: 'toDate',
                            message: '{{trans("users.users_form_validate.user_redeem_fromdate_invalid")}}'
                        }
                    }
                },
                toDate: {
                    validators: {
                        notEmpty: {
                            message: '{{trans("users.users_form_validate.user_redeem_todate_required")}}'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            min: 'fromDate',
                            message: '{{trans("users.users_form_validate.user_redeem_todate_invalid")}}'
                        }
                    }
                }
            }
        });

        $('#redeemImportForm')
        .formValidation({
            framework: 'bootstrap',
            fields: {
                fromDate: {
                    validators: {
                        notEmpty: {
                            message: '{{trans("users.users_form_validate.user_redeem_import_file_required")}}'
                        },
                        file: {
                            extension: '.xls',
                            type: 'application/vnd.ms-excel',
                            message: '{{trans("users.users_form_validate.user_redeem_import_file_invalid")}}'
                        }
                    }
                }
            }
        });

    });
    
    function impersonateusers(user_id) {
        $.post('/users/impersonateusers?user_id='+user_id,function(response){
            if(response.status){
                $("#alertStatus").attr("class","alert alert-success").html(response.message).show().delay(3000).fadeOut(350);
                location.reload();
            }else {
                $("#alertStatus").attr("class","alert alert-danger").html(response.message).show().delay(3000).fadeOut(350);
               location.reload();
            }
        
        });
    }

</script>
@stop