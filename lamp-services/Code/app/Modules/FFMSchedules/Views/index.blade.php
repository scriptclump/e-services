@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light tasks-widget">
        <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="portlet-title">
                <div class="caption">Sales Team Schedules</div>
                <div class="actions">
                    <div class="row" style="margin-right: 5px;">
                        @if($addPermission)
                            <a class="btn green-meadow" id="addNewSchedule" href="#addSchedule" data-toggle="modal">
                                <i class="fa fa-plus-circle"></i>
                                <span style="font-size:11px;"> Add New Schedule </span>
                            </a>
                        @endif
                        @if($importPermission)
                          <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document1" class="btn green-meadow">Import</a>
                        @endif
                        @if($exportPermission)
                            <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document2" class="btn green-meadow">Export</a>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                <table id="ffm_pjp_grid"></table>
                            </div>
                        </div>
                    </div>
                @if($addPermission)
                    <!-- Add Modal -->
                <div class="modal fade" id="addNewScheduleModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="addModalLabel">Add Schedule</h4>
                                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addNewScheduleForm">
                                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ffm_name">FFM Name<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <select class="form-control select2me" placeholder="FFM Name" id="add_ffm_id" style="margin-top: 6px" name ="add_ffm_id" onchange="getwarehouse()">
                                                    <option value ="" selected="selected">--Please Select--</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="dc_code">DC/FC</label>
                                                <select class="form-control select2me" placeholder="DC/FC" id="add_dc_code" name="add_dc_code" style="margin-top: 6px">
                                                <option value ="" selected="selected">--Please Select--</option>
                                                @foreach($dcs as $dc)
                                                    <option value = "{{$dc['le_wh_id']}}">{{$dc['display_name']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="city_code">City<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <input type="text" class="form-control auto-comp" name="add_city_code" id="add_city_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pincode">Pincode<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <select class="form-control select2me" id="add_pincode"
                                                name="add_pincode" style="margin-top: 6px" placeholder ="Pincode">
                                                <option value ="">--Please Select--</option>
                                                @foreach($pincodes as $pin)
                                                    <option value = "{{$pincodes['pincode']}}">{{$pincodes['pincode']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="date">From Date<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <div class="input-icon right" style="width: 100%" >
                                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                                    <input type="text" class="form-control" name="from_date" id="from_date" autocomplete="Off" placeholder="From Date" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="date">To Date<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <div class="input-icon right" style="width: 100%" >
                                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                                    <input type="text" class="form-control" name="to_date" id="to_date" autocomplete="Off" placeholder="To Date" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="mobile_no">Mobile Number<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <input type="text" class="form-control" id="add_mobile_no" name="add_mobile_no" placeholder="Mobile No">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!-- <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('statecodes.statecode_heads.close')}}</button> -->
                                        <button type="submit" id="addScheduleData" class="btn btn-primary">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($editPermission)
                <!-- edit modal -->
                <div class="modal fade" id="editSchedule" tabindex="-1" role="dialog" aria-labelledby="editScheduleLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="editScheduleLabel">Edit</h4>
                                <button type="button" id="editClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert" role="alert" id="modalAlert"></div>
                                <form id="editScheduleForm">
                                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                                    <input type="hidden" name="edit_fpd_id" id="edit_fps_id">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ffm_name">FFM Name</label>
                                                <input type="text" class="form-control" id="edit_ffm_name" name="edit_ffm_name" disabled="on" placeholder="FFM Name">
                                                <input type="hidden" class="form-control" id="edit_ffm_id" name="edit_ffm_id">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="dc_code">DC/FC</label>
                                                <select class="form-control select2me" id="edit_dc_code"
                                                name="edit_dc_code" style="margin-top: 6px" placeholder="DC/FC">
                                                <option value ="">--Please Select--</option>
                                                @foreach($dcs as $dc)
                                                    <option value = "{{$dc['le_wh_id']}}">{{$dc['display_name']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="city_code">City<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <input type="text" class="form-control auto-comp" name="edit_city_code" id="edit_city_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pincode">Pincode<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <select class="form-control select2me" id="edit_pincode" name="edit_pincode" style="margin-top: 6px" placeholder="Pincode">
                                                <option value ="">--Please Select--</option>
                                                @foreach($pincodes as $pin)
                                                    <option value = "{{$pin['default_pincode']}}">{{$pin['pjp_name']}}&nbsp({{$pin['default_pincode']}})</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="date">Date<span class="required" style="color:red;" aria-required="true">*</span></label>
                                                <div class="input-icon right" style="width: 100%" >
                                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                                    <input type="text" class="form-control" name="edit_date" id="edit_date" autocomplete="Off" placeholder="Date" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!-- <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal"></button> -->
                                        <button type="submit" id="saveSchedule" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($exportPermission)
                <!-- Export Modal -->
                <div class="modal fade" id="upload-document2" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exportModalLabel">Export Schedules</h4>
                                <button type="button" id="exportClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="exportScheduleForm">
                                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="ffm_name">FFM Name</label>
                                                <select class="form-control select2me" id="ffm_id" name="ffm_id" style="margin-top: 6px"
                                                placeholder="FFM Name">
                                                <option value ="" selected="selected">--Please Select--</option></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="date">From Date</label>
                                                <div class="input-icon right" style="width: 100%" >
                                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                                    <input type="text" class="form-control" name="fdate" id="fdate" autocomplete="Off" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label for="date">To Date</label>
                                                <div class="input-icon right" style="width: 100%" >
                                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                                    <input type="text" class="form-control" name="tdate" id="tdate" autocomplete="Off" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!-- <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('statecodes.statecode_heads.close')}}</button> -->
                                        <a href="ffmschedules/exportffmschedules" id="exportScheduleData" class="btn green-meadow btnn range_info" >Export</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@if($importPermission)
<div class="modal fade" id="upload-document1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">FFM Daily Schedules</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-body">
                                {{ Form::open(array('url' => 'ffmschedules/downloadexcelforffmschedules', 'id' => 'downloadexcel-slabpricing'))}}
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="form-group">
                                            <button type="submit" class="btn green-meadow" id="download-excel">Download Schedules Template</button>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                                {{ Form::open(['id' => 'ffm_schedules']) }}
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                    <input type="file" name="upload_schedulesfile" id="upload_schedulesfile" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button"  class="btn green-meadow" id="schedules-upload-button">Upload Schedules Template</button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
  
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .fa-times{ color: red !important;}
    .jqx-widget-header {
        background: #f2f2f2 !important;
    }
    .fc-field {
        cursor:pointer !important;
    }
    .up-down{width:264px !important; margin:3px !important;}
    .portlet.light > .portlet-title > .tools {
        padding:0px !important;
    }
    .portlet > .portlet-title > .tools > a {
        height: auto !important;
    }
    .has-feedback label~.form-control-feedback {
        top: 40px !important;
        right:10px !important;
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
    }
    .fa-plus{font-size: 14px !important;}
    .fa-pencil{font-size: 14px !important;}
    .fa-trash-o{font-size: 14px !important;}
    .preview {
      display: block;
      max-width:100px;
      max-height:100px;
      width: auto;
      height: auto;
    }
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!-- select 2 drop down -->
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
<!-- <script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script> --> 
<script src="/js/helper.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/category/form-wizard-create-category.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('script')
@include('includes.validators')

<script>
    jQuery(document).ready(function() {
        FormWizard.init();
    });
</script>
<script type="text/javascript">
    $('#add_city_code').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "/ffmschedules/getcitynames",
                dataType: "json",
                data: {
                    dc:$('#add_dc_code').val(),
                    city : $('#add_city_code').val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            var add_city_code = ui.item.label;
            var city_code = ui.item.city_code;
            $('#add_city_code').val(city_code);
            getpincodes();
        }
    });
    $('#edit_city_code').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "/ffmschedules/getcitynames",
                dataType: "json",
                data: {
                    dc:$('#edit_dc_code').val(),
                    city : $('#edit_city_code').val()
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            var edit_city_code = ui.item.label;
            var city_code = ui.item.city_code;
            $('#edit_city_code').val(city_code);
            getpin();
        }
    });
    $('#edit_dc_code').on('change',function(){
        $('#edit_city_code').val('');
        $('#edit_pincode').select2('val','');$('#edit_pincode').html('');
        $('#editScheduleForm').bootstrapValidator('revalidateField', 'edit_city_code');
        $('#editScheduleForm').bootstrapValidator('revalidateField', 'edit_pincode');
    });
    $('#add_dc_code').on('change',function(){
        $('#add_city_code').val('');
        $('#add_pincode').select2('val','');$('#add_pincode').html('');
    });
    $(document).ready(function (){
        $( "#from_date" ).datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            onSelect: function(selected) {
                $('#addNewScheduleForm').bootstrapValidator('revalidateField', 'from_date');
                $("#to_date").datepicker("option","minDate", selected)
            }
         });
        $( "#to_date" ).datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: 0,
            onSelect: function(selected) {
                $('#addNewScheduleForm').bootstrapValidator('revalidateField', 'to_date');
                $("#to_date").datepicker("option","maxDate", selected)
            }            
        });
        $( "#fdate" ).datepicker({
            dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
                $('#addNewScheduleForm').bootstrapValidator('revalidateField', 'fdate');
                $("#tdate").datepicker("option","minDate", selected)
            }
        });
        $( "#tdate" ).datepicker({
            dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
                $('#addNewScheduleForm').bootstrapValidator('revalidateField', 'tdate');
            }
        });
        $( "#edit_date" ).datepicker({  minDate: 0,dateFormat: 'dd-mm-yy' });
        $('#ffm_pjp_grid').igGrid({
                dataSource: '/ffmschedules/getschedules',
                responseDataKey: 'Records',
                height:'100%',
                columns: [
                    {headerText: "FFM Name", key: "ff_name", dataType: "string",  width: '14%'},
                    {headerText: "Mobile number", key: "mobile_no", dataType: "string",  width: '12%'},
                    {headerText: "Date", key:"date", dataType: "date", width: '10%', format: 'dd-M-yyyy'},
                    {headerText: "Business Unit", key: "le_wh_id", dataType: "string", width: '10%'},
                    {headerText: "State", key: "state", dataType: "string", width: '10%'},
                    {headerText: "City", key: "city", dataType: "string", width: '14%'},
                    {headerText: "Pincode", key: "pincode",dataType: "string", width: '10%'},
                    {headerText: "Actions", key:"actions", dataType: "string", width: '9%'}
                ],
                features: [
                    {
                        name: "Filtering",
                        mode: "simple",
                        columnSettings: [
                            {columnKey: 'actions', allowFiltering: false},
                        ]
                    },
                    {
                        name: "Sorting",
                        type: "remote",
                        persist: false,
                        columnSettings: [
                            {columnKey: 'actions', allowFiltering: false},
                        ],
                    },
                    {
                        name: 'Paging',
                        type: 'remote',
                        pageSize: 10,
                        recordCountKey: 'TotalRecordsCount',
                        pageIndexUrlKey: "page",
                        pageSizeUrlKey: "pageSize"
                    },
            
                    {
                        name: "Resizing",
                    }]
            });
        $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/ffmschedules/getFFMList',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#add_ffm_id").html(rs);
                $("#ffm_id").html(rs);
            }
        });
        $('#upload-document2').on('hide.bs.modal', function () {
            $("#ffm_id").select2("val", "");
            $('#fdate').datepicker('setDate', null);
            $('#tdate').datepicker('setDate', null);
        });
        $('#addNewScheduleModal').on('hide.bs.modal', function () {
            $("#addNewScheduleForm").bootstrapValidator('resetForm', true);
            $("#add_ffm_id").select2("val", "");
            $("#add_dc_code").select2("val","");
            $("#add_city_code").val("");
            $("#add_pincode").select2("val","");
            $('#add_mobile_no').val("");
            $('#from_date').datepicker('setDate', null);
            $('#to_date').datepicker('setDate', null);
        });
        $('#editSchedule').on('hide.bs.modal', function () {
            $("#editScheduleForm").bootstrapValidator('resetForm', true);
            $("#edit_dc_code").select2("val","");
            $("#edit_city_code").val("");
            $("#edit_pincode").select2("val","");
            $('#edit_mobile_no').val("");
            $('#edit_date').val("");
        });
        $("#modalClose").click(function(){
            $("#modalAlert").hide();
            $('#modalAlert').data('bs.modal',null); // this clears the BS modal data
        });
        $("#editClose").click(function(){
            $("#modalAlert").hide();
            $('#modalAlert').data('bs.modal',null); // this clears the BS modal data
        });
        $("#exportClose").click(function(){
            $("#modalAlert").hide();
            $('#modalAlert').data('bs.modal',null);
        });
        $("#schedules-upload-button").click(function () {
            token  = $("#csrf-token").val();
            var stn_Doc = $("#upload_schedulesfile")[0].files[0];
            var formData = new FormData();
            formData.append('schedules_data', stn_Doc);
            formData.append('test', "sample");
            $.ajax({
                type: "POST",
                headers: {'X-CSRF-TOKEN': token},
                url: "/ffmschedules/uploadffmschedules",
                data: formData,
                processData: false,
                contentType: false,
                success: function (data){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
                    $(".alert-success").fadeOut(20000)
                }
            });
            $('#upload-document1').modal('toggle');
        });
        $('#upload-document1').on('hidden.bs.modal', function (e) {
            $("#upload_schedulesfile").val("");
        });

        $("#exportScheduleData").click(function() {
            var fdate = $("#fdate").val();
            var tdate = $("#tdate").val();
            var ff_id = $("#ffm_id").val();
            $("#exportScheduleData").attr("href", "/ffmschedules/exportffmschedules?fdate=" + fdate+"&tdate="+tdate+"&ff_id="+ff_id);
            if(fdate == '' || tdate =='') {
                alert('please select start date and end date');
                return false;
            }else if(ff_id == '' || ff_id == null){
                alert('please select a FFM');
                return false; 
            }else if(tdate < fdate){
                alert('To date should be greater than from date');
                return false;
            }else
                return true;
        });

    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#addNewSchedule").click(function(){
            $("#addNewScheduleModal").modal("show");
            $("#addNewScheduleModal").modal({backdrop:'static', keyboard:false});
        });
    function editSchedule(id){
        $.post('/ffmschedules/edit/'+id,function(response){
            if(response){
                $("#editSchedule").modal("show");
                $('#editSchedule').modal({backdrop:'static', keyboard:false});
                var warehouse = $('#edit_dc_code');
                warehouse.find('option').remove().end();
                warehouse.append($('<option></option>').val("").html("--Please Select--"));
                for(var i=0; i<response['le_wh_id']['le_wh_id'].length; i++){
                    warehouse.append(
                        $('<option></option>').val(response['le_wh_id']['le_wh_id'][i].le_wh_id).html(response['le_wh_id']['le_wh_id'][i].display_name)
                    );
                }

                var pincode = $('#edit_pincode');
                pincode.find('option').remove().end();
                for(var i=0; i<response['pincodes'].length; i++){
                    pincode.append(
                        $('<option></option>').val(response['pincodes'][i].pincode).html(response['pincodes'][i].pincode)
                    );
                }

                $("#edit_fps_id").attr('value',response['result'].fps_id);
                $("#edit_ffm_name").attr('value',response['result'].ff_name);
                $("#edit_ffm_id").attr('value',response['result'].ff_id);
                $("#edit_pincode").select2('val',response['result'].pincode);
                $("#edit_city_code").val(response['result'].city);
                $("#edit_dc_code").select2('val',response['result'].le_wh_id);
                $("#edit_date").val(response['result'].date);                
            }
            else{
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Failed to edit schedule!</div></div>' );
                $(".alert-danger").fadeOut(20000);
            }
        });
    }
    function getpin(){
    $("#edit_pincode").select2('val','');$("#edit_pincode").html("");
    var city = $("#edit_city_code").val();
    city = city=="" ? "0" : city;
    token  = $("#csrf-token").val(); 
    $('#edit_pincode').val('');
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/ffmschedules/getpincodes?city='+city,
        success: function( data ) {
                if(data){
                    $('#edit_pincode').select2('val','');$('#edit_pincode').html('');
                    var pincodes = $('#edit_pincode');
                    pincodes.find('option').remove().end();
                    pincodes.append('<option value="">Please Select</option>');
                    for(var i=0; i<data.length; i++){
                        pincodes.append(
                            $('<option></option>').val(data[i].pincode).html(data[i].pincode)
                        );
                    }
                }
                $('#edit_pincode').val('');
                 document.getElementById('edit_pincode').disabled = false;
            }
        });
    }

    function getpincodes(){
        $("#add_pincode").select2('val','');$("#add_pincode").html("");
        var city = $("#add_city_code").val();
        city = city=="" ? "0" : city;
        token  = $("#csrf-token").val(); 
        $('#add_pincode').val('');
        // prepare the ajax call to get the brand information
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/ffmschedules/getpincodes?city='+city,
            success: function( data ) {
                    $('#add_pincode').select2('val','');$('#add_pincode').html('');
                    if(data){
                        var pincode = $('#add_pincode');
                        pincode.find('option').remove().end();
                        for(var i=0; i<data.length; i++){
                            pincode.append(
                                $('<option></option>').val(data[i].pincode).html(data[i].pincode)
                            );
                        }
                    }
                    $('#add_pincode').val('');
                }
        });
    }

    function getwarehouse(){
        var ffm = $("#add_ffm_id").val();
        ffm = ffm=="" ? "0" : ffm;
        token  = $("#csrf-token").val(); 
        $('#add_dc_code').val('');
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/ffmschedules/getwarehouse/'+ffm,
            success: function( data ) {
                $('#add_dc_code').select2('val','');$('#add_dc_code').html('');
                $('#add_city_code').val('');$('#add_city_code').html('');
                $('#add_pincode').select2('val','');$('#add_pincode').html('');
                if(data['le_wh_id']){
                    var warehouse = $('#add_dc_code');
                    warehouse.find('option').remove().end();
                    warehouse.append($('<option></option>').val('').html("--Please Select---"));
                    for(var i=0; i<data['le_wh_id'].length; i++){
                        warehouse.append(
                            $('<option></option>').val(data['le_wh_id'][i].le_wh_id).html(data['le_wh_id'][i].display_name)
                        );
                    }
                }
                $('#add_mobile_no').val(data['mobile_no']);
                document.getElementById('add_mobile_no').readOnly = true;
                $('#add_dc_code').val('');
            }
        });
    }
    $('#addNewScheduleForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                add_ffm_id: {
                    validators: {
                        notEmpty: {
                            message: "Please enter ffm name!"
                        }
                    }
                },
                add_city_code: {
                    validators: {
                        notEmpty: {
                            message: "Please select a city!"
                        }
                    }
                },
                add_pincode: {
                    validators: {
                        notEmpty: {
                            message: "Please select a pincode!"
                        }
                    }
                },
                from_date: {
                    validators: {
                        notEmpty: {
                            message: "Please select from date!"
                        }
                    }
                },
                to_date: {
                    validators: {
                        notEmpty: {
                            message: "Please select to date!"
                        }
                    }
                },
            }
        })
        .on('success.form.bv', function(event) {
            event.preventDefault();
            var newScheduleData = {
                ffm_id: $("#add_ffm_id").val(),
                city_code: $("#add_city_code").val(),
                le_wh_id: $("#add_dc_code").val(),
                pincode: $("#add_pincode").val(),
                from_date: $("#from_date").val(),
                to_date: $("#to_date").val(),
                mobile_no: $("#add_mobile_no").val()
            };
            var token=$("#_token").val();
            $.post('/ffmschedules/add',newScheduleData,function(response){
                $("#addNewScheduleModal").modal("hide");
                $("#add_ffm_id").val('');
                if(response.status){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Schedule is added!</div></div>' );
                    $(".alert-success").fadeOut(20000);
                    $('#ffm_pjp_grid').igGrid("dataBind");
                }
                else{
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Failed to add schedule!</div></div>' );
                    $(".alert-danger").fadeOut(20000);
                }
            });            
        });
    
    $('#editScheduleForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                edit_city_code: {
                    validators: {
                        notEmpty: {
                            message: "Please select a city!"
                        }
                    }
                },
                edit_pincode: {
                    validators: {
                        notEmpty: {
                            message: "Please select a pincode!"
                        }
                    }
                },
                edit_date: {
                    validators: {
                        notEmpty: {
                            message: "Please select a date!"
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(event){
            event.preventDefault();
            var newData = {
                fps_id: $("#edit_fps_id").val(),
                ffm_name: $("#edit_ffm_name").val(),
                ffm_id: $("#edit_ffm_id").val(),
                state_code: $("#edit_state_code").val(),
                city_code: $("#edit_city_code").val(),
                pincode: $("#edit_pincode").val(),
                le_wh_id: $("#edit_dc_code").val(),
                date: $("#edit_date").val()
            };
            $.post('/ffmschedules/update',newData,function(response){
                $("#editSchedule").modal("hide");
                if(response.status == 1){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Updated!</div></div>' );
                    $(".alert-success").fadeOut(20000);
                    $('#ffm_pjp_grid').igGrid("dataBind");
                }
                else if(response.status == 2){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Schedule already exists!</div></div>' );
                    $(".alert-danger").fadeOut(20000);
                }
                else{
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Failed to update!</div></div>' );
                    $(".alert-danger").fadeOut(20000);
                }
            });            
        });
    function deleteSchedule(id) {
        var decision = confirm("Are you sure. Do you want to Delete it!");
        if(decision){
            $.post('/ffmschedules/delete/'+id,function(response){
                if(response.status){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-info">Deleted!</div></div>' );
                    $(".alert-info").fadeOut(20000);
                    $('#ffm_pjp_grid').igGrid("dataBind");
                }
                else{
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Failed to delete!</div></div>' );
                    $(".alert-danger").fadeOut(20000);
                }
            });
        }
    }
</script>

<script type="text/javascript">
    $(function () {
        $('#ffm_pjp_grid_container').find('.ui-iggrid-filtericonendswith').parents('li').remove();
        $('#ffm_pjp_grid_container').find('.ui-iggrid-filtericondoesnotcontain').parents('li').remove();
        $('#ffm_pjp_grid_container').find('.ui-iggrid-filtericonequals').parents('li').remove();
        $('#ffm_pjp_grid_container').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonnextyear').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonthisyear').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonlastyear').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonnextmonth').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonthismonth').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonlastmonth').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonnoton').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonafter').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonbefore').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericontoday').parents('li').remove();
        $('#ffm_pjp_grid_dd_date').find('.ui-iggrid-filtericonyesterday').parents('li').remove();
    });

</script>    
@stop   
