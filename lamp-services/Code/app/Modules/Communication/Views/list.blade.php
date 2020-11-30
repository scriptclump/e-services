@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Message Templates </div>                
                <div class="actions">
                    @if($add_button_access)
                    <a href="#add_message_template" data-toggle="modal" class="btn green-meadow">
                        <i class="fa fa-plus-circle"></i>
                        <span style="font-size:11px;"> Send New Message </span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">                        
                        <span id="success_message"></span>
                    </div>
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="messages"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add_message_template" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">{{ trans('communication.filters.heading_1') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="portlet light tasks-widget" style="height: auto;">
                            <span id="error_message"></span>
                            <div class="portlet-body">
                                <form id="communication_form" action="/communication/senddata" method="post">	
                                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}" />
                                    <div id="filters">
                                        <div class="row">
                                            <div class="col-md-3">                        
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('communication.form.dc')}} </label>
                                                    <select name="dc_name[]" id="dc_name" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.form.dc') }}">
                                                        @if(!empty($dc_data))
                                                        @foreach ($dc_data as $dc_data)
                                                        <option value="{{ $dc_data->le_wh_id }}">{{ $dc_data->lp_wh_name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">                        
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('communication.form.hub')}} </label>
                                                    <select name="hubs[]" id="hub" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.form.hub') }}">

                                                    </select>
                                                </div>                        
                                            </div>
                                            <div class="col-md-3">                        
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('communication.form.beat')}} </label>
                                                    <select name="beats[]" id="beats" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.form.beat') }}">

                                                    </select>
                                                </div>                        
                                            </div>
                                            <div class="col-md-3">                        
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('communication.form.role')}}<span class="required">*</span> </label>
                                                    <select name="roles[]" id="role" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.form.role') }}">
                                                        @if(!empty($role_list))
                                                        @foreach ($role_list as $role_data)
                                                        <option value="{{ $role_data->role_id }}">{{ $role_data->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>                        
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="row">
                                            <div class="col-md-12">                        
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('communication.form.message')}}<span class="required">*</span></label>
                                                    <textarea name="message" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{trans('communication.form.message_type')}}<span class="required">*</span></label>
                                                    <select name="message_type[]" id="message_type" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.form.please_select') }}">
                                                        <option value="sms">{{trans('communication.form.send_sms')}}</option>
                                                        <option value="push">{{trans('communication.form.push_notification')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    @if($add_button_access)
                                                    <button type="submit" class="btn green-meadow" id="submit_message_form">Submit</button>
                                                    @endif
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
@section('style')
<style media="all">
    .switch { 
        border-radius:18px;
        cursor:pointer;
        height:22px;
        margin-top:6px!important;
        padding:3px;
        position:relative;
        vertical-align:top;
        width:50px
    }
    .switch-input { 
        position:absolute;
        top:0;
        left:0;
        opacity:0
    }
    .switch-label{position:relative;display:block;height:inherit;font-size:10px;text-transform:uppercase;
                 background:#a8a8a8;border-radius:inherit;width:43px}
    .switch-label:before,.switch-label:after{position:absolute;top:50%;margin-top:-.5em;line-height:1;
          -webkit-transition:inherit;-moz-transition:inherit;-o-transition:inherit;transition:inherit}
    .switch-label:before{content:attr(data-off);right:5px;color:#aaa;text-shadow:0 1px rgba(255,255,255,.5)}
    .switch-label:after{content:attr(data-on);left:5px;color:#fff;text-shadow:0 1px rgba(0,0,0,.2);opacity:0}
    .switch-input:checked ~ .switch-label {background:#03a75b;width:43px}.switch-input:checked
    ~ .switch-label:before {opacity:0}.switch-input:checked ~ .switch-label:after {opacity:1}
    .switch-handle{position:absolute;top:4px;left:4px;width:20px;height:20px;
                  background:linear-gradient(to bottom,#fff 40%,#f0f0f0);
                  background-image:-webkit-linear-gradient(top,#fff 40%,#f0f0f0);border-radius:100%}
    .switch-input:checked ~ .switch-handle {left:25px}.switch-label,
    .switch-handle{transition:All .3s ease;-webkit-transition:All .3s ease;-moz-transition:All .3s ease;
                  -o-transition:All .3s ease}
    .ui-widget-content a {
        color: #5b9bd1!important;
    }
</style>
{{HTML::style('css/switch-custom.css')}}
@stop
@section('script') 
@include('includes.ignite')
{{HTML::script('assets/global/plugins/select2/select2.min.js') }}
@include('includes.validators')
<script type="text/javascript">
$(document).ready(function ()
{
    $('#messages').igGrid({
        dataSource: "/communication/getallmessages",
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        recordCountKey: 'totalMessageCount',
        columns: [
            {headerText: 'Message Text', key: 'message', dataType: 'string', width: '25%'},
            {headerText: 'Message Type', key: 'message_type', dataType: 'string', width: '10%'},
            {headerText: '#Mobile', key: 'count_mobile_numbers', dataType: 'int', width: '10%'},
            {headerText: '#Sent SMS', key: 'sms_sent_count', dataType: 'int', width: '10%'},
            {headerText: '#Sent Push', key: 'push_sent_count', dataType: 'int', width: '10%'},
            {headerText: 'Created By', key: 'created_by_name', dataType: 'string', width: '20%'},
            {headerText: 'Created Date', key: 'created_at', dataType: 'string', width: '10%'},
            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '5%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
//                    {columnKey: 'notify_rm', allowFiltering: false},
                    {columnKey: 'count_mobile_numbers', allowFiltering: false},
                    {columnKey: 'sms_sent_count', allowFiltering: false},
                    {columnKey: 'push_sent_count', allowFiltering: false},
                    {columnKey: 'created_at', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: true,
                columnSettings: [
//                    {columnKey: 'notify_rm', allowSorting: false},
                    {columnKey: 'actions', allowSorting: false}
                ]
            },
            {
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 20,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote',
                initialDataBindDepth: 0,
                localSchemaTransform: false,
                showHeaders: true,
                fixedHeaders: true
            }
        ],
        primaryKey: '_id',
        width: '100%',
        height: '570px',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        type: 'remote',
        showHeaders: true,
        fixedHeaders: true
    });
    $('#dc_name').change(function () {
        var dc_name = $(this).val();
        var token = $("#token_value").val();
        if (dc_name != null && dc_name.length != 0)
        {
            $.ajax({
                type: "GET",
                url: "/communication/gethubs?_token=" + token + "&dc_id=" + dc_name,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function () {
                    $('#loader').show();
                },
                complete: function () {
                    $('#loader').hide();
                },
                success: function (responseData)
                {
                    console.log(responseData);
                    if (responseData.status) {
                        $('#hub').empty();
                        $.each(responseData.response, function () {
                            $('#hub').append(
                                    $("<option></option>").text(this.lp_wh_name).val(this.le_wh_id));
                        });
                    } else {
                        $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>' + responseData.message + '</div></div>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });
    $('#hub').change(function () {
        var hub_name = $(this).val();
        var token = $("#token_value").val();
        if (hub_name != null && hub_name.length != 0)
        {
            $.ajax({
                type: "GET",
                url: "/communication/getbeats?_token=" + token + "&hub_id=" + hub_name,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function () {
                    $('#loader').show();
                },
                complete: function () {
                    $('#loader').hide();
                },
                success: function (responseData)
                {
                    console.log(responseData);
                    if (responseData.status) {
                        $('#beats').empty();
                        $.each(responseData.response, function () {
                            $('#beats').append(
                                    $("<option></option>").text(this.pjp_name).val(this.pjp_pincode_area_id));
                        });
                    } else {
                        $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>' + responseData.message + '</div></div>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });
    $("#communication_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            message: {
                validators: {
                    notEmpty: {
                        message: "{{trans('communication.form.error_message')}}"
                    }
                }
            },
            "roles[]": { 
                validators: {
                    notEmpty: {                      
                        message: "{{trans('communication.form.role')}}"
                    }
                }
            },
            "message_type[]": { 
                validators: {
                    choice: {
                        min: 1,
                        max: 2,
                        message: "{{trans('communication.form.error_message_type')}}"
                    }
                }
            }
        }
    }).on('success.form.bv', function (event) {
        event.preventDefault();
        var datastring = '';
        var datastring = $("#communication_form").serialize();
        console.log(datastring);
        $.ajax({
            url: '/communication/senddata',
            data: datastring,
            type: 'post',
            success: function (response) {
                console.log(response);
                var data = $.parseJSON(response);
                console.log(data);
                if(data.status == 1)
                {
                    $("#error_message").hide();
                    $('#add_message_template').modal('hide');
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>' + data.message + '</div></div>').delay(3000).fadeOut(350).show();
                    $("#messages").igGrid("dataBind");
                }else{
                    $("#success_message").hide();
                    $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>' + data.message + '</div></div>').delay(3000).fadeOut(350).show();
                }
            }
        });
    });
    $("#add_message_template").on("hidden.bs.modal", function () {
        $("#error_message").html('');
        $("#dc_name").select2("val", "");
        $("#hub").select2("val", "");
        $("#beats").select2("val", "");
        $("#role").select2("val", "");
        $("#message_type").select2("val", "");
        $('[name="message"]').val('');
        $('#submit_message_form').removeClass('disabled');
        $('#submit_message_form').removeAttr('disabled');
    });
});
</script>
@stop
@extends('layouts.footer')