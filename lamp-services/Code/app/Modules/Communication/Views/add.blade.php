@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
@if(Session::has('flash_message'))
<div class="alert alert-info">
    <a class="close" data-dismiss="alert">×</a>
    {!!Session::get('flash_message')!!}
</div>
@endif
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">{{ trans('communication.filters.heading_1') }}</div>
            </div>
            <div class="portlet-body">
                <form id="communication_form" action="/communication/senddata" method="post">	
                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}" />
                    <div id="filters">
                        <div class="row">
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <label class="control-label">{{trans('communication.form.dc')}} </label>
                                    <select name="dc_name[]" id="dc_name" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.filters.all') }}">
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
                                    <select name="hubs[]" id="hub" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.filters.all') }}">

                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <label class="control-label">{{trans('communication.form.beat')}} </label>
                                    <select name="beats[]" id="beats" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.filters.all') }}">

                                    </select>
                                </div>                        
                            </div>
                            <div class="col-md-3">                        
                                <div class="form-group">
                                    <label class="control-label">{{trans('communication.form.role')}} </label>
                                    <select name="roles[]" id="role" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.filters.all') }}">
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
                                    <label class="control-label">{{trans('communication.form.message_type')}}</label>
                                    <select name="message_type[]" id="message_type" class="form-control select2me" multiple="multiple" placeholder="{{ trans('communication.form.please_select') }}">
                                        <option value="sms">{{trans('communication.form.send_sms')}}</option>
                                        <!-- option value="push">{{trans('communication.form.push_notification')}}</option -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn green-meadow">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@include('includes.validators')
<script type="text/javascript">
$(document).ready(function () {
    $('#dc_name').change(function () {
        var dc_name = $(this).val();
        var token = $("#token_value").val();
        if (dc_name.length != 0)
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
        if (hub_name.length != 0)
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
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>' + data.message + '</div></div>').show();
                }else{
                    $("#success_message").hide();
                    $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>' + data.message + '</div></div>').show();
                }
            }
        });
    });
});
</script>
@stop
@extends('layouts.footer')