@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Notification Templates </div>                
                <div class="actions">
                    @if(isset($add_permission) and $add_permission)
                    <a href="#add_notification_template" data-toggle="modal" class="btn green-meadow">
                        <i class="fa fa-plus-circle"></i>
                        <span style="font-size:11px;"> Add Template </span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div role="alert" id="alertStatus"></div>
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="notifications"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="add_notification_template" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Add Notification Template</h4>
            </div>
            <div class="modal-body">
                <form id="addNotificationTemplate" action="/notification/addtemplate" class="text-center" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Notification Code <span class="required">*</span></label>
                                <input class="form-control" type="text" name="notification_code" value="" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Notification Message <span class="required">*</span></label>
                                <textarea class="form-control" type="text" name="notification_message" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Recipient By Role </label>
                                <select class="form-control select2me" id="notificaiton_recipient_roles" name="notificaiton_recipient_roles[]" multiple="true">
                                    @if(isset($roles))
                                        @foreach($roles as $role)
                                            <option value="{{ $role->role_id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Recipient By Users</label>
                                <select class="form-control select2me" id="notificaiton_recipient_users" name="notificaiton_recipient_users[]" multiple="true">
                                    @if(isset($users))
                                        @foreach($users as $userData)
                                            <option value="{{ $userData->user_id }}">{{ $userData->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Recipient By Legal Entity</label>
                                <select class="form-control select2me" id="notificaiton_recipient_legal_entities" name="notificaiton_recipient_legal_entities[]" multiple="true">
                                    @if(isset($legal_entities))
                                        @foreach($legal_entities as $legalEntitiesData)
                                            <option value="{{ $legalEntitiesData->legal_entity_id }}">{{ $legalEntitiesData->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn green-meadow">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="update_notification_template" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">Update Notification Template</h4>
            </div>
            <div class="modal-body">
                <form id="updateNotificationTemplate" action="/notification/updatetemplate" class="text-center" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="notification_template_id" value="" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Notification Code <span class="required">*</span></label>
                                <input class="form-control" type="text" name="notification_code" value="" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Notification Message <span class="required">*</span></label>
                                <textarea class="form-control" type="text" id="notification_message" name="notification_message" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Recipient By Role </label>
                                <select class="form-control select2me" id="notificaiton_recipient_roles" name="notificaiton_recipient_roles[]" multiple="true">
                                    @if(isset($roles))
                                        @foreach($roles as $role)
                                            <option value="{{ $role->role_id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Recipient By Users</label>
                                <select class="form-control select2me" id="notificaiton_recipient_users" name="notificaiton_recipient_users[]" multiple="true">
                                    @if(isset($users))
                                        @foreach($users as $userData)
                                            <option value="{{ $userData->user_id }}">{{ $userData->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                
                                <label class="control-label">Recipient By Legal Entity</label>
                                <select class="form-control select2me" id="notificaiton_recipient_legal_entities" name="notificaiton_recipient_legal_entities[]" multiple="true">
                                    @if(isset($legal_entities))
                                        @foreach($legal_entities as $legalEntitiesData)
                                            <option value="{{ $legalEntitiesData->legal_entity_id }}">{{ $legalEntitiesData->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn green-meadow">Update</button>
                        </div>
                    </div>
                </form>
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
    loadGrid();
    $("#addNotificationTemplate").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
    //        valid: 'glyphicon glyphicon-ok',
    //        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            notification_code: {
                validators: {
                    notEmpty: {
                        message: "Notification Code is required."
                    },
                    remote: {
                        headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                        url: '/notification/validatecode',
                        type: 'POST',
                        data: function (validator, $field, value) {
                            return  {
                                notification_code: value
                            };
                        },
                        delay: 2000, // Send Ajax request every 2 seconds
                        message: "Code already exists, please choose another"
                    }
                }
            },
            notification_message: {
                validators: {
                    notEmpty: {
                        message: "Notification Message is required."
                    }
                }
            }
        }

    }).on('success.form.bv', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        var token=$("#_token").val();
        $.post('/notification/addtemplate',data,function(response){
            $("#add_notification_template").modal("hide");
            if(response.status){
                $("#addNotificationTemplate").bootstrapValidator('resetForm', true);
                $("#alertStatus").attr("class","alert alert-success").html(response.message).show().delay(3000).fadeOut(350);
                $('#notifications').igGrid("dataBind");
            }else{
                $("#addNotificationTemplate").bootstrapValidator('resetForm', true);
                $("#alertStatus").attr("class","alert alert-danger").html(response.message).show().delay(3000).fadeOut(350);
                $('#notifications').igGrid("dataBind");
            }
        });            
    });

    $('#add_notification_template').on('hide.bs.modal', function () {
        $("#addNotificationTemplate").bootstrapValidator('resetForm', true);
        $("#notification_message").val("");
        $("#notificaiton_recipient_users").select2("val", "");
        $("#notificaiton_recipient_roles").select2("val", "");
        $("#notificaiton_recipient_legal_entities").select2("val", "");
        $("#notification_code").val("");

    });

    $("#updateNotificationTemplate").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
    //        valid: 'glyphicon glyphicon-ok',
    //        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            notification_code: {
                validators: {
                    notEmpty: {
                        message: "Notification Code is required."
                    },
                    remote: {
                        headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                        url: '/notification/validatecode',
                        type: 'POST',
                        data: function (validator, $field, value) {
                            return  {
                                notification_code: value,
                                notification_template_id: $('[name="notification_template_id"]').val()
                            };
                        },
                        delay: 2000, // Send Ajax request every 2 seconds
                        message: "Code already exists, please choose another"
                    }
                }
            },
            notification_message: {
                validators: {
                    notEmpty: {
                        message: "Notification Message is required."
                    }
                }
            }
        }
    }).on('success.form.bv', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.post('/notification/updatetemplate',data,function(response){
            $("#update_notification_template").modal("hide");
            if(response.status){
                $("#alertStatus").attr("class","alert alert-success").html(response.message).show().delay(3000).fadeOut(350);
                $('#notifications').igGrid("dataBind");
            }else{
                $("#alertStatus").attr("class","alert alert-danger").html(response.message).show().delay(3000).fadeOut(350);
                $('#notifications').igGrid("dataBind");
            }
        });            
    });
});
function loadGrid()
{
    $('#notifications').igGrid({
        dataSource: "/notification/templates",
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        recordCountKey: 'totalNotificationCount',
        columns: [
            {headerText: 'Notification Template Id', key: 'notification_template_id', dataType: 'int', width: '0%'},
            {headerText: 'Notification Code', key: 'notification_code', dataType: 'string', width: '10%'},
            {headerText: 'Notification Message', key: 'notification_message', dataType: 'string', width: '20%'},
            {headerText: 'Recipents By Role', key: 'roles_list', dataType: 'string', width: '20%'},
            {headerText: 'Recipents By Users', key: 'users_list', dataType: 'string', width: '20%'},
            {headerText: 'Recipents By Legal Entities', key: 'legal_entity_list', dataType: 'string', width: '20%'},
            {headerText: 'Notify RM', key: 'notify_rm', dataType: 'string', width: '10%'},
            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '10%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'notify_rm', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: true,
                columnSettings: [
                    {columnKey: 'notify_rm', allowSorting: false},
                    {columnKey: 'actions', allowSorting: false}
                ]
            },
            {
                name: 'Paging',
                type: "local",
                pageSize: 10
            }
        ],
        primaryKey: 'notification_template_id',
        width: '100%',
        height: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        showHeaders: true,
        fixedHeaders: true
    });
}
function notifyRm(templateId)
{
    if(templateId > 0)
    {
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/notification/notifyrm',
            type: 'POST',
            data: {'template_id' : templateId, 'status': $('.'+templateId).prop('checked')},
            dataType: 'JSON',
            success: function (data) {
                loadGrid();
            },
            error: function (response) {

            }
        });
    }
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function deleteEntityType(templateId) 
{
    if(templateId > 0){
        var decision = confirm("Are you sure you want to delete it!");
        if(decision){
            $.post('/notification/deletetemplate/'+templateId,function(response){
                if(response == 1){
                    $("#alertStatus").attr("class","alert alert-info").text("Notification successfully deleted!").show().delay(3000).fadeOut(350);
                    $('#notifications').igGrid("dataBind");
                }else{
                    $("#alertStatus").attr("class","alert alert-danger").text("Notification failed to delete. Try again!").show().delay(3000).fadeOut(350);
                    $('#notifications').igGrid("dataBind");
                }
            });
        }
    }
}

function editEntityType(templateId)
{
    if(templateId > 0)
    {
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/notification/edittemplate',
            type: 'POST',
            data: {'template_id' : templateId},
            dataType: 'JSON',
            success: function (data) {
                $('#updateNotificationTemplate').find('select').each(function(){
                    var tempFieldName = $(this).attr('id');
                    if(data.hasOwnProperty(tempFieldName))
                    {
                        $.each(data, function(key, value){
                            if(tempFieldName == key && value && value != '')
                            {
                                var values = value.split(',');
//                                $.each(values,function(i){
//                                    $('#updateNotificationTemplate select[name="'+key+'"]').select2("val", values[i]);
//                                });
                                $('#updateNotificationTemplate select[id="'+key+'"]').val(values).trigger("change");
                            }
                        });
                    }
                });
                $('#updateNotificationTemplate').find('textarea').each(function(){
                    var tempFieldName = $(this).attr('name');
                    if(data.hasOwnProperty(tempFieldName))
                    {
                        $.each(data, function(key, value){
                            if(tempFieldName == key && value && value != '')
                            {
                                $('#updateNotificationTemplate textarea[name="'+key+'"]').val(value);
                            }
                        });
                    }
                });
                $('#updateNotificationTemplate').find('input').each(function()
                {
                    if($(this).is(":text"))
                    {
                        var tempFieldName = $(this).attr('name');
                        if(data.hasOwnProperty(tempFieldName))
                        {
                            $.each(data, function(key, value){
                                if(tempFieldName == key && value)
                                {
//                                    $('#updateNotificationTemplate').val(value);
                                    $('#updateNotificationTemplate input[name="'+key+'"]').val(value);
                                }
                            });
                        }
                    }else if($(this).is(":hidden")){
                        var tempFieldName = $(this).attr('name');
                        if(data.hasOwnProperty(tempFieldName))
                        {
                            $.each(data, function(key, value){
                                if(tempFieldName == key)
                                {
//                                    $(this).val(value);
                                    $('#updateNotificationTemplate input[name="'+key+'"]').val(value);
                                }
                            });
                        }
                    }
                });
                $('#update_notification_template').modal('show');
            },
            error: function (response) {

            }
        });
    }
}

$('#update_notification_template').on('hide.bs.modal', function () {
    $('#updateNotificationTemplate').bootstrapValidator('resetForm', true); 
    $('#updateNotificationTemplate input[name="notification_template_id"]').val(0);
    $('#updateNotificationTemplate input[name="notification_code"]').val('');
    $('#updateNotificationTemplate textarea[id="notification_message"]').val('');
    $('#updateNotificationTemplate select[id="notificaiton_recipient_roles"]').select2().select2('val',0);
    $('#updateNotificationTemplate select[id="notificaiton_recipient_users"]').select2().select2('val',0);
    $('#updateNotificationTemplate select[id="notificaiton_recipient_legal_entities"]').select2().select2('val',0);
});
</script>
@stop
@extends('layouts.footer')