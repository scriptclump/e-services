@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message_ajax"></span>
<div class="row">
<div class="col-md-12">
<div class="portlet light tasks-widget">
    <div class="portlet-title">
        <div class="caption">Leave Manage
        </div>
         <div class="col-md-10">
            <input type="hidden" name="hr_access" id="hr_access" value="{{$Hraccess}}">
            <div id="leave_info" style="display: none">
                <span style ="color: red;float: right;margin-top: 10px;" id="leave_count"><strong>@foreach($leavetypes['current_leave_count'] as $types)
                <span value = "">{{$types->leave_type}}(s)-> {{$types->no_of_leaves}} &nbsp;&nbsp;</span>
                @endforeach</strong>
                </span>
            </div>
        </div>
        <div class="tools">
        <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
        </div>
    </div>
    <form  action ="" method="POST" id = "apply_leav_manage" name = "apply_leav_manage">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
        <div class="portlet-body">
            @if($Hraccess)
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="select_report" class="control-label">Employee Name</label>
                        <select id="name" name="name" class="form-control select2me" onChange="loademploydata(this);">
                        <option value ="">Please Select</option>
                        @foreach($employee as $value)
                        <option value="{{$value['emp_code']}}">{{$value['firstname']."[".$value['emp_code']."]"}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="select_report" class="control-label">Type</label>
                        <select id="leave_type" name="leave_type" class="form-control" onChange="loadthedatalogin(this);">
                        <option value ="">Please Select</option>
                        @foreach($leavetypes['leave_type'] as $key => $types)
                            <option value = "{{$key}}">{{$types}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="select_report" class="control-label">From Date</label>
                            <input type="text" class="form-control " name="from_date" id="from_date" placeholder="From Date" autocomplete="off">
                            <input type ="hidden" name ="employ_id" id ="employ_id" value ="<?php echo $employid;?>">
                            <input type ="hidden" name ="emp_group_id" id ="emp_group_id" value ="<?php echo $emp_group_id;?>">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token()}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="select_report" class="control-label">To Date</label>
                        <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date" autocomplete="off"> 
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="select_report" class="control-label" id="reason">Reason</label>
                        <label for="select_report" class="control-label" id="holiday" style="display: none">Holiday</label>
                        <select id="optional_leave" name="optional_leave" class="form-control" style="display: none">                        
                         <option value ="">Please Select</option>
                        @foreach($leavetypes['leave_holiday_type'] as $key =>$leave_types)
                            <option value = "{{$key}}">{{$leave_types}}</option>
                        @endforeach
                        </select>
                        <select id="normal_leave" name="normal_leave" class="form-control" >
                        <option value ="">Please Select</option>
                        @foreach($leavetypes['leave_reason_type'] as $key =>$leave_types)
                            <option value = "{{$key}}">{{$leave_types}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="select_report" class="control-label">Emergency Contact</label>
                        <input type ="text" id ="emergency_number" name ="emergency_number" class="form-control">
                    </div>
                </div>
                <div class="loginfromhomesection" style="display:none">
                    @include('HrmsEmployees::sections/loginfromhome')
                </div>
            </div>
                <!-- Loading each section for all option -->
               

                
                <div class="col-md-3">
                    <div class="form-group genra">
                        <button type="submit"class="btn green-meadow" style ="margin-top: -97px;
                        margin-left: 1090px;">Apply</button>
                    </div>
                </div>
            
            </div>
        </div>
    </form>
    <table class="table table-striped table-bordered table-advance table-hover" id="leave_history_details">  
    </table>  
    <div id="leave_history_no_data"></div>
</div>    
</div>
</div>


@stop
@section('userscript')
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>

<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/bootstrap_framework.min.js') }}" type="text/javascript"></script>

 <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>

 <script src="{{ URL::asset('assets/global/plugins/clockface/js/clockface.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

@extends('layouts.footer')
<style>
 
</style>
<script>


    $(document).ready(function(){
        dates();
        $access = $('#hr_access').val();
        leaveInfoData($access);
    });



    $('#apply_leav_manage').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            from_date: {
                validators: {
                    notEmpty: {
                        message: 'From Date is required',
                    },
                }
            },
            to_date: {
                validators: {
                    notEmpty: {
                        message: ' To Date is required',
                    },
                }
            },
            normal_leave: {
                validators: {
                    notEmpty: {
                        message: 'Reason is required',
                    },
                }
            },
            optional_leave: {
                validators: {
                    notEmpty: {
                        message: 'Holiday is required',
                    },
                }
            },
            emergency_number: {
                validators: {
                    notEmpty: {
                        message: "Enter Contact Number."
                    },
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    } 
                }
            },
            leave_type: {
                validators: {
                    notEmpty: {
                        message: 'Leave Type is required',
                    },
                }
            },
        }

    }).on('success.form.fv', function(event){
    event.preventDefault();
        console.log($('#apply_leav_manage').serialize());
        console.log($('#from_date').val());
        console.log($('#to_date').val());
        if($('#leave_type').val()==148007){
            if($('#from_date').val()!=$('#to_date').val()){
                alert('At a time you can apply only one Login from home')
            }else{
               applyLeave();
            }
        }else{
            applyLeave();
        }        
    });
    function dates(){
        var start = new Date();
        var end = new Date(new Date().setYear(start.getFullYear() + 1));
        $("#from_date").datepicker("remove");
        $("#to_date").datepicker("remove");
        if($('#emp_group_id').val() == 1){
            $('#from_date').datepicker({
                clearBtn: true,
                daysOfWeekDisabled: [0,6],
                endDate: end,
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('changeDate',function(){
                stDate = new Date($(this).val());
                $('#to_date').datepicker('setStartDate', stDate);
            });
            $('#to_date').datepicker({
                clearBtn: true,
                daysOfWeekDisabled: [0,6],
                startDate: start,
                endDate: end,
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('changeDate', function () {
                $('#from_date').datepicker('setEndDate', new Date($(this).val()));
            });
        }else{
            $('#from_date').datepicker({
                clearBtn: true,
                daysOfWeekDisabled: [0],
                endDate: end,
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('changeDate',function(){
                stDate = new Date($(this).val());
                $('#to_date').datepicker('setStartDate', stDate);
            });
             $('#to_date').datepicker({
                clearBtn: true,
                daysOfWeekDisabled: [0],
                startDate: start,
                endDate: end,
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).on('changeDate', function () {
                $('#from_date').datepicker('setEndDate', new Date($(this).val()));
            });
        }
    }
    function applyLeave(){
        var frmData = $('#apply_leav_manage').serialize();
        var token = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/employee/empapplyleave',
            type: "post",
            data: frmData,
            beforeSend:function(){
            $('[class="loader"]').show();
            $(".overlay").show();
            },
            complete:function(){
            $('[class="loader"]').hide();
            $(".overlay").hide();
            },
            success:function(respData)
            {
                $("#name").select2("val",null);
                $("#from_date").val("");
                $("#to_date").val("");
                $("#reason").val("");
                $("#emergency_number").val("");
                $("#leave_type").val("");
                console.log('**',respData);
                if(respData == 'Success'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Leave Applied Successfully<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }
                else if(respData == 'failed'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Leave Rejected successfully<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }
                else if(respData == 'you have already applied leaves for the following dates'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You have already applied leave for the following dates<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }
                else if(respData == 'causual error'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You are trying to apply more causual leave(s) than available<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }
                else if(respData == 'sick error'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You are trying to apply more sick leave(s) than available<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }
                else if(respData == 'optional error'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You are trying to apply more optional leave(s) than available<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }else if(respData == 'not an optional'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">No Optional Holiday on selected dates<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You are trying to apply more optional leave(s) than availabe<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                }else if(respData == 'lfh error'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You are not allowed to apply Login from home on this date!<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                }
                window.setTimeout(function(){window.location.reload()}, 5000);
            }
        });
    }

    function LeaveWithdraw(leavetype){
        var token = $("#csrf-token").val();
        var leave_withdraw = confirm("Are you sure you want to withdraw leave ?"), self = $(this);
        if(leave_withdraw == true){
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "POST",
                url: '/employee/withdrawleave/' + leavetype,
                success: function (respData){
                    alert("Leave Withdrawn Successfully");
                    window.location.reload();
                }
            });
        }
    }
    $(document).ready(function(){
        reloadGridData();
    });
    function loadthedatalogin(){
        var leavetype = $("#leave_type").val();
        if(leavetype == '148007'){
            $('.loginfromhomesection').show();
        }
        else{
            $('.loginfromhomesection').hide();
        }
        if(leavetype == '148005'){
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'from_date', false);
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'to_date', false);
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'normal_leave', false);
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'optional_leave', true);
            $('#apply_leav_manage').bootstrapValidator('revalidateField', 'from_date');
            $('#apply_leav_manage').bootstrapValidator('revalidateField', 'to_date');
            //$('#apply_leav_manage').bootstrapValidator('revalidateField', 'normal_leave');
            //$('#apply_leav_manage').bootstrapValidator('revalidateField', 'optional_leave');

            $('#from_date').val('');
            $('#to_date').val('');
            $("#from_date").prop("disabled", true);
            $("#to_date").prop("disabled", true);
            $("#holiday").css("display","block");
            $("#reason").css("display","none");
            $("#optional_leave").css("display","block");
            $('#normal_leave').val('');
            $("#normal_leave").css("display","none");

        }else{
            $("#from_date").prop("disabled", false);
            $("#to_date").prop("disabled", false); 
            $('#optional_leave').val('');
            $("#holiday").css("display","none");
            $("#reason").css("display","block");           
            $("#optional_leave").css("display","none");
            $("#normal_leave").css("display","block");

            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'from_date', true);
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'to_date', true);
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'normal_leave', true);
            $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'optional_leave', false);//$('#apply_leav_manage').bootstrapValidator('revalidateField', 'from_date');
            //$('#apply_leav_manage').bootstrapValidator('revalidateField', 'to_date');
            //$('#apply_leav_manage').bootstrapValidator('revalidateField', 'normal_leave');
            //$('#apply_leav_manage').bootstrapValidator('revalidateField', 'optional_leave');

        }
    }
    function loademploydata(){
        var token = $("#csrf-token").val();
        var employcode = $("#name").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/employee/employeedata/' + employcode,
            data: employcode,
            success:function(leavetypes){
                $("#leave_type").html('');
                $("#normal_leave").html('');
                $("#optional_leave").html('');
                $("#optional_leave").val('');
                $("#normal_leave").val('');
                $("#leave_type").val('');
                if(leavetypes){
                    $("#leave_type").append("<option value=''>Please Select</option>");
                    $("#normal_leave").append("<option value=''>Please Select</option>");
                    $("#optional_leave").append("<option value=''>Please Select</option>");
                    $.each(leavetypes['leave_type'],function(index,item) {
                        $("#leave_type").append("<option value='"+index+"'>"+item+"</option>")
                    });
                    $.each(leavetypes['leave_reason_type'],function(index,item) {
                        $("#normal_leave").append("<option value='"+index+"'>"+item+"</option>")
                    });
                    $.each(leavetypes['leave_holiday_type'],function(index,item) {
                        $("#optional_leave").append("<option value='"+index+"'>"+item+"</option>")
                    });
                    $("#employ_id").attr('value', leavetypes['emp_id']);
                    $("#emp_group_id").attr('value',leavetypes['emp_group_id']);
                    dates();
                    $('#leave_count').html('');
                    let current_leave = leavetypes.current_leave_count;
                    let leave_data = '<strong>';
                    current_leave.forEach(leave=>{
                        leave_data = leave_data +`<span>${leave.leave_type}(s) -> ${leave.no_of_leaves}  </span>`
                    });
                    leave_data =leave_data+"</strong>";
                    $('#leave_count').html(leave_data);
                    reloadGridData();
                    leaveInfoData(0);
                }
            }
        });
    }
    $('#from_date').change(function(){
        $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'from_date', true);
        $('#apply_leav_manage').bootstrapValidator('revalidateField', 'from_date');
    });

    $('#to_date').change(function(){
        $('#apply_leav_manage').bootstrapValidator('enableFieldValidators', 'to_date', true);
        $('#apply_leav_manage').bootstrapValidator('revalidateField', 'to_date');

    });

    function reloadGridData(){
        $("#leave_history_details").igGrid({
            dataSource: '/employee/getappliedleaves/' + $("#employ_id").val(),
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results", 
            width: "100%",
            height: "100%",
            columns: [
                {headerText:"From Date", key: 'from_date', dataType: "date",format:"date",width: '20%'},
                {headerText:"To Date", key: 'to_date', dataType: "date",format:"date", width: '20%'},
                {headerText:"No Of Days", key: 'no_of_days', dataType: "string", width: '15%'},
                {headerText:"Type", key: 'leave_type', dataType: 'string', width: '20%'},           
                {headerText:"Reason", key: 'reason', dataType: 'string', width: '20%'}, 
                {headerText:"Emergency Contact", key:'contact_number', dataType: 'string', width: '20%'},
                {headerText: "Status", key:'status', dataType: 'string', width: '20%'},
                {headerText: "Action", key:'CustomAction', dataType: 'string', width: '15%'}  
            ],
            features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'leave_type', allowSorting: false },
                    {columnKey: 'reason', allowSorting: false },
                    {columnKey: 'status', allowSorting: false },
                    {columnKey: 'CustomAction', allowSorting: false },
                   
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'leave_type', allowFiltering: false },
                        {columnKey: 'reason', allowFiltering: false },
                        {columnKey: 'status', allowFiltering: false },
                        {columnKey: 'CustomAction', allowFiltering: false },
                     
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    pageIndexUrlKey: 'page', 
                    pageSizeUrlKey: 'pageSize', 
                    pageSize: 10,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'remote' 
                },
                {
                    name: "Resizing",
                }
            ],
            primaryKey: 'prmt_tmpl_Id',
            width: '100%',
            height: '500',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            rendered: function (evt, ui){
                igGridHideOption();
            }
            
        });
    }

function igGridHideOption(){
    $("#leave_history_details_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
    $("#leave_history_details_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
    $("#leave_history_details_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
    $("#leave_history_details_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
    $("#leave_history_details_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
    $("#leave_history_details_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
    $("#leave_history_details_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
    $("#leave_history_details_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
    $("#leave_history_details_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
    $("#leave_history_details_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
    $("#leave_history_details").find(".ui-iggrid-filtericonnoton").closest("li").remove();
    // $("#leave_history_details").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
    //$("#leave_history_details").find(".ui-iggrid-filtericonendswith").closest("li").remove();
    $("#leave_history_details_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
}
function leaveInfoData(flag){
    if(flag == 1){
        $('#leave_info').css('display','none');
    }else{
        $('#leave_info').css('display','block');
    }
}

</script>
@stop
@extends('layouts.footer')