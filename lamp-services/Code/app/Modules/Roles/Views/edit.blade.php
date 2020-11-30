@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{trans('roles.role_tab.role_edit')}} <span class="caption-helper">({{$row->name}})</span></div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
            </div>
            <div class="portlet-body">
                <div id="form-wiz" class="portlet-body">
                    <div class="tabbable-line">

                        <ul class="nav nav-tabs" id="test1">
                            <li class="active act1"><a href="#role" data-toggle="tab"style="cursor:default">{{trans('roles.role_tab.role')}}</a></li>
                            <li class="act2"><a href="#permis" data-toggle="tab" style="cursor:default">{{trans('roles.role_tab.role_permissions')}} </a></li>
                            <li class="act3"><a href="#users" data-toggle="tab" style="cursor:default">{{trans('roles.role_tab.role_users')}} </a></li>
                        </ul>

                        {{Form::open(array('url'=>'/roles/saveRole/0','method'=>'put', 'id' => 'role_step_1'))}}
                        <div class="tab-content">
                            <div class="tab-pane active" id="role">
                                <div class="row">
                                    <div class="col-md-6">
                                    
                                    
                                        <div class="form-group">
                                            <label class="control-label"> {{trans('roles.role_tab.parent_role')}} <span class="required">*</span></label>
                                            <select class="form-control select2me" name="inherit_role" id="inherit_role">
                                                <option value="">Please Select...</option>
                                                @if(isset($rolesList))
                                                    @foreach($rolesList as $role)
                                                        <option value="{{$role->role_id}}" <?php if($row->parent_role_id == $role->role_id){ ?> selected="true" <?php } ?> >{{$role->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">                                            
                                            <?php if(isset($row) && !empty($row)){?>
                                            <input type="hidden" id="roleId" name="roleId" value="{{$row->role_id}}">
                                            <?php } else{?>
                                            <input type="hidden" id="roleId" name="roleId">
                                            <?php } ?>
                                            <input type="hidden" name="manufacture_id" value="{{Session::get('customerId')}}">
                                            <label class="control-label">{{trans('roles.role_tab.role_name')}}<span class="required">*</span></label>
                                            <input type="text" class="form-control" placeholder="Role Name" name="role_name" id="role_name" value="{{$row->name}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"> {{trans('roles.role_tab.role_description')}} <span class="required">*</span></label>
                                            <textarea class="form-control" id="description" name="description" rows="3">{{$row->description}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('roles.role_tab.role_code')}}</label>
                                            <input class="form-control" type="text" <?php if(isset($row->short_code)) { echo 'value="'.$row->short_code.'"'; } ?> name="short_code" id="short_code"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Legal Entity</label>
                                                <select class="form-control select2me" name="legal_entity_id" id="legal_entity_id">
                                                    <option value=""></option>
                                                    @foreach($businessUnitsData as $buData)
                                                        <option value="{{$buData->legal_entity_id}}" {{ ($row->legal_entity_id == $buData->legal_entity_id) ? 'selected = "true"' : '' }}>{{$buData->display_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                 </div>
                                </div> 
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <input class="" type="checkbox" <?php if($row->is_support_role) { echo 'checked="checked"'; } ?> name="is_support_role" id="is_support_role"/>
                                            <sapn>{{trans('roles.role_tab.is_support_role')}}</sapn>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:96px;">
                                    <hr />
                                    <div class="col-md-12 text-center"> 
                                        <button class="btn green-meadow save1" type="submit" >Update</button>
                                        <a class="btn green-meadow next" href="javascript:void(0)" >Next</a>
                                    </div>                                    
                                </div>
                            </div>
                    {{Form::close()}}
                            <div class="tab-pane" id="permis">
                                <input type="hidden" id="hasChilds" value="{{ $hasChilds }}" />
                                 <div class="row">
                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                        
                                            @foreach($modules as $module)                                            
                                                <?php $moduleCode = $module->value; ?>
                                                @if(property_exists($module, 'child') && count($module->child) > 0)
                                                    <ul class="nav nav-tabs tabs-left" id="<?php echo $moduleCode; ?>">
                                                    <?php $i = ($module->value - 4000); ?>
                                                    @foreach($module->child as $moduleChild)
                                                        <?php $moduleName = property_exists($moduleChild, 'name') ? $moduleChild->name : ''; ?>
                                                        <?php $moduleParentId = property_exists($moduleChild, 'parent_id') ? $moduleChild->parent_id : ''; ?>
                                                        <?php $isMenu = property_exists($moduleChild, 'is_menu') ? $moduleChild->is_menu : ''; ?>
                                                        @if($moduleParentId == 0)
                                                        <?php if ($i == 1) { ?>
                                                            <li class="active"><a href="#tab_<?php echo $moduleCode; ?>_<?php echo $i; ?>" data-toggle="tab" style="padding-left:10px !important"> {{$moduleName}} </a></li>
                                                        <?php } else { ?>
                                                            <li><a href="#tab_<?php echo $moduleCode; ?>_<?php echo $i; ?>" data-toggle="tab" style="padding-left:10px !important"> {{$moduleName}} </a></li>
                                                        <?php } ?>
                                                        @endif    
                                                        <?php $i++; ?>
                                                    @endforeach
                                                    </ul>
                                                <hr/>
                                                @endif
                                            @endforeach
                                        
                                    </div>
                                    <div class="col-md-9 col-sm-9 col-xs-9">
                                        <div class="tab-content">                                            
                                            @foreach($modules as $module)
                                            <?php $moduleCode = $module->value; ?>
                                                @if(property_exists($module, 'child'))
                                                <?php $i = ($module->value - 4000); ?>
                                                @foreach($module->child as $moduleChild)
                                                    <?php $moduleName = property_exists($moduleChild, 'name') ? $moduleChild->name : ''; ?>
                                                    <?php $moduleParentId = property_exists($moduleChild, 'parent_id') ? $moduleChild->parent_id : ''; ?>
                                                    <?php $isMenu = property_exists($moduleChild, 'is_menu') ? $moduleChild->is_menu : ''; ?>
                                                    @if($moduleParentId == 0)
                                                    <?php if ($i == 1) { ?>
                                                        <div class="tab-pane active" id="tab_<?php echo $moduleCode; ?>_<?php echo $i; ?>">
                                                        <?php } else { ?>
                                                            <div class="tab-pane" id="tab_<?php echo $moduleCode; ?>_<?php echo $i; ?>">
                                                            <?php } ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="note note-success">
                                                                        <p>Please select which features are accessable to this role</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                       
                                                            
                                                            
                                                            
                                                            
<div class="row">
  <div class="col-md-12">
    <?php if(property_exists($moduleChild, 'child')) { ?>
    @if(property_exists($moduleChild, 'feature_id'))
    <div class="form-group">
      <label class="mt-checkbox"> 
        <input type="checkbox" value="{{$moduleChild->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild->feature_id}}" class="minimal {{$moduleChild->feature_id}}"  @if(in_array($moduleChild->
        feature_id,$row->feature_id)) checked="checked" @endif onchange="checkAll('{{$moduleChild->feature_id}}',this.checked);" /> 
        {{$moduleChild->name}}
        <span data-original-title="{{$moduleChild->name}} &nbsp;({{$moduleChild->feature_code}})" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span> <span></span> 
        </label>
    </div>
    @endif
    @foreach($moduleChild->child as $moduleChild1)
    <?php if(is_object($moduleChild1) && property_exists($moduleChild1, 'name')) { ?>
    <div class="form-group" >
      <label class="mt-checkbox" style="margin-left:30px;"> 
        <input type="checkbox" ref="{{$moduleChild->feature_id}}" value="{{$moduleChild1->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild1->feature_id}}" class="parsley-validated {{$module->value}} {{$moduleChild->feature_id}} parent" 
                                                                                               @if(in_array($moduleChild1->
        feature_id,$row->feature_id))checked="checked" @endif onchange="checkAll('{{$moduleChild1->feature_id}}',this.checked,'{{$moduleChild->feature_id}}');checkedParent('{{$moduleChild->feature_id}}',this.checked)"> 
        {{$moduleChild1->name}}
        <span data-original-title="{{$moduleChild1->name}} &nbsp;({{$moduleChild1->feature_code}})" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span> <span></span> 
        </label>
      <?php if(property_exists($moduleChild1, 'child')) { ?>
      @foreach($moduleChild1->child as $moduleChild2)
      <?php if(is_object($moduleChild2) && property_exists($moduleChild2, 'feature_id') && property_exists($moduleChild2, 'name')){ ?>
     <label class="mt-checkbox" style="margin-left:60px; display: inherit;">
          <input type="checkbox" ref="{{$moduleChild1->feature_id}}" value="{{$moduleChild2->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild2->feature_id}}"
                                                                                                       onchange="checksubchild('{{$moduleChild->feature_id}}',this.checked,'{{$moduleChild1->feature_id}}')" class="minimal {{$moduleChild->feature_id}} {{$moduleChild1->feature_id}} subchaild" @if(in_array($moduleChild2->
          feature_id,$row->feature_id)) checked="checked" @endif />{{$moduleChild2->name}}
        <span data-original-title="{{$moduleChild2->name}} &nbsp;({{$moduleChild2->feature_code}})" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span> <span></span>
        </label>
      
      <?php } ?>
      @endforeach 
      <!--<hr/>-->
      <?php }else{ ?>
      @if(property_exists($moduleChild1, 'feature_id') && property_exists($moduleChild1, 'value'))
      <div class="-list" id="child2-checkbox">
        <label class="mt-checkbox">
        <div class="checker" id="uniform-select_all"><span>
          <input type="checkbox" ref="{{$moduleChild->feature_id}}" value="{{$moduleChild1->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild1->feature_id}}" class="minimal {{$moduleChild->feature_id}}" @if(in_array($moduleChild->
          feature_id,$row->feature_id)) checked="checked" @endif /></span></div>
        <span>{{$moduleChild1->name}}</span> <span data-original-title="{{$moduleChild1->name}}" data-placement="top" class="badge bg-blue tooltips"> <i class="fa fa-question"></i> </span>
        </label>
      </div>
      @endif
      <?php } ?>
    </div>
    <?php } ?>
    @endforeach
    <?php }else{ ?>
    @if(property_exists($moduleChild, 'feature_id'))
   <div class="form-group">
      <label class="mt-checkbox">
        <input type="checkbox" value="{{$moduleChild->feature_id}}" name="feature_name[]"  id="feature_name{{$moduleChild->feature_id}}"  class="minimal {{$moduleChild->feature_id}}"  @if(in_array($moduleChild->
        feature_id,$row->feature_id)) checked="checked" @endif />
        {{$moduleChild->name}}
      <span data-original-title="{{$moduleChild->name}}" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span>
      <span></span> 
      </label>
    </div>
    @endif
    <?php } ?>
  </div>
</div>
                                                            <div class="row" style="margin-top:116px;">
                                                                <hr />
                                                                <div class="col-md-12 text-center"> 
                                                                    <a class="btn green-meadow back1" href="javascript:void(0)">Back</a>
                                                                    <a class="btn green-meadow save2" href="javascript:void(0)">Update</a>
                                                                    <a class="btn green-meadow" href="/roles/index">Cancel</a>
                                                                </div>
                                                            </div>
                                                        </div>                                                        
                                                    @endif 
                                                    <?php $i++; ?>
                                                @endforeach
                                                @endif 
                                            @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="users">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-12">
                                            <h3 class="form-section">{{trans('roles.role_tab.role_select_users')}}</h3>
                                            <div class="portlet light tasks-widget">
                                               
                                                <div class="row portlet-body">
                                                       
                                                        <table class="table table-striped table-bordered table-advance table-hover" id="sample_2">
                                                            <tr>
                                                                <td id="assignUser">

                                                                </td>
                                                            </tr>

                                                        </table>                                                                        
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-1 col-sm-12" style="position:relative; top:250px; padding-left: 30px">
                                            <a href="javascript:void(0)" class="btn btn-icon-only green moveRight"><i class="fa fa-angle-double-right"></i></a>
                                            <br /><br /><br />
                                            <a href="javascript:void(0)" class="btn btn-icon-only green moveLeft"><i class="fa fa-angle-double-left"></i></a>
                                        </div>


                                        <div class="col-md-6 col-sm-12">
                                            <h3 class="form-section">{{trans('roles.role_tab.role_assign_users')}}</h3>
                                            <div class="portlet light tasks-widget">
                                                
                                                <div class="row portlet-body">

                                                    
                                                        <table class="table table-striped table-bordered table-advance table-hover" id="sample_3">
                                                            <thead>
                                                                <tr>
                                                                    <th class="table-checkbox"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes"/></th>
                                                                    <th>&nbsp;</th>
                                                                    <th>User</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                                <tr class="odd gradeX" id="assignUsers">

                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top:0px;">
                                        <hr />
                                        <div class="col-md-12 text-center"> 
                                            <a class="btn green-meadow back2" href="javascript:void(0)">Back</a>
                                            <a class="btn green-meadow save3" href="javascript:void(0)" style="display:none">Done</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>  
        <input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">
        <input type="hidden" id="userdata" /> 
        <input type="hidden" id="userdata1" name="userids[]" value='<?php echo $secoundGridData;?>'/> 
    </div>
</div>
<div class="modal modal-scroll fade in" id="dialog" title="Confirmation Required" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    Do you want to update child roles?
</div>
@stop
@section('style')
<style>
.form-group {margin-bottom:0px !important;}
.portlet.light {padding: 12px 15px !important;}
.control-label {margin-top: 15px !important;}
.ui-button .ui-widget .ui-state-default .ui-corner-all .ui-button-text-only .ui-state-hover
{
    background-color: #333 !important;
}
.ui-dialog-buttonset button .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
    background-color: #e8e8e8 !important;
    color: #444 !important;
    font-size: 12px !important;
}
.ui-widget-overlay{
    background-color: #333 !important;
}
</style>
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $('#permis li').click(function(event){
        $('#permis li').removeClass('active');
    });
    $(function () {        
        var roleId='';
        roleId = $("#roleId").val();
        var usersData;                
        $.get('/roles/getiggridusers'+'?roleId='+roleId, function (response) {
            usersData = $('#userdata').val(response);
            ajaxCall();
            addAjaxCall();   
        });
        $("#dialog").dialog({
            autoOpen: false,
            modal: true
        });
        
        var user_array = new Array();
        var data = [];
        $('.moveRight').click(function() {
            var user_ids = [];
            var old_data = $.parseJSON($('#userdata1').val()) || [];
            var getrows = '';
            getrows = $('#sample_2').igGridSelection('selectedRows');            
            var data_View = $('#sample_2').data('igGrid').dataSource.dataView();          
            var userData=[];            
            for (var i = 0; i < getrows.length; i++) { 
                if(data_View[getrows[i].index])
                {
                    var user_id = data_View[getrows[i].index].user_id;
                    user_array.push(user_id);                
                    var dataDisplays = {};
                    dataDisplays["user_id"] = data_View[getrows[i].index].user_id;
                    dataDisplays["fullname"] = data_View[getrows[i].index].fullname;
                    dataDisplays["profile_pic"] = data_View[getrows[i].index].profile_pic;
                    userData.push(dataDisplays);  //string to json 
                    user_ids.push( dataDisplays["user_id"] );
                }                                
            }
            var new_data = old_data.concat(userData);
            var final_data = [],
                user_s_ids = [];
            $.each(new_data, function(id, val){
                if( user_s_ids.indexOf(val.user_id) != -1 ) return;
                user_s_ids.push(val.user_id);
                final_data.push(val);
            });
                
            var currentObj = $.parseJSON($('#userdata').val()),
                    currentNewObj = [];
                $.each(currentObj, function(i, v){
                    if( user_ids.indexOf( v.user_id ) != -1 ) return;                   
                   currentNewObj.push(v);
                });
                
                $('#userdata').val(JSON.stringify(currentNewObj));
                $('#userdata1').val(JSON.stringify(final_data));                
                addAjaxCall();
                ajaxCall();
        });
       $('.moveLeft').click(function() {
            var user_ids = [];
            var old_data = $.parseJSON($('#userdata').val());
            var getrows = '';
            getrows = $('#sample_3').igGridSelection('selectedRows');
            var data_View = $('#sample_3').data('igGrid').dataSource.dataView();          
            var userData=[];            
            for (var i = 0; i < getrows.length; i++) {                                
                if(data_View[getrows[i].index])
                {
                    var user_id = data_View[getrows[i].index].user_id;
                    user_array.push(user_id);                
                    var dataDisplays = {};
                    dataDisplays["user_id"] = data_View[getrows[i].index].user_id;
                    dataDisplays["fullname"] = data_View[getrows[i].index].fullname;
                    dataDisplays["profile_pic"] = data_View[getrows[i].index].profile_pic;
                    userData.push(dataDisplays);  //string to json 
                    user_ids.push( dataDisplays["user_id"] );
                }
            }            
            var new_data = old_data.concat(userData);
            var final_data = [],
                user_s_ids = [];
            $.each(new_data, function(id, val){
                if( user_s_ids.indexOf(val.user_id) != -1 ) return;
                user_s_ids.push(val.user_id);
                final_data.push(val);
            });
                
            var currentObj = $.parseJSON($('#userdata1').val()),
                    currentNewObj = [];
                $.each(currentObj, function(i, v){
                    if( user_ids.indexOf( v.user_id ) != -1 ) return;                   
                   currentNewObj.push(v);
                });               
                
                $('#userdata1').val(JSON.stringify(currentNewObj));
                $('#userdata').val(JSON.stringify(final_data));                
                addAjaxCall();
                ajaxCall();
        });
        
        var obj = $("#dragandrophandler");
        obj.on('dragenter', function (e)
        {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '2px solid #0B85A1');
        });
        obj.on('dragover', function (e)
        {
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('drop', function (e)
        {

            $(this).css('border', '2px dotted #0B85A1');
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            //We need to send dropped files to Server
            handleFileUpload(files, obj);
        });
        $(document).on('dragenter', function (e)
        {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('dragover', function (e)
        {
            e.stopPropagation();
            e.preventDefault();
            obj.css('border', '2px dotted #0B85A1');
        });
        $(document).on('drop', function (e)
        {
            e.stopPropagation();
            e.preventDefault();
        });

        $("#short_code").on('keydown', function(evt) {
            $(this).val(function (_, val) {
                return val.toUpperCase();
            });
        });
        
        //bootstrapValidator
        $('#role_step_1').bootstrapValidator({            
            message: 'This value is not valid',
//            feedbackIcons: {
//                valid: 'glyphicon glyphicon-ok',
//                invalid: 'glyphicon glyphicon-remove',
//                validating: 'glyphicon glyphicon-refresh'
//            },
            fields: {       
                inherit_role: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('roles.add_role_form.validate.parent_role')}}"
                        }
                    }
                },
                role_name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('roles.add_role_form.validate.role_name')}}"
                        },
                        regexp: {
                            regexp: /^[a-z\s]+$/i,
                            message: "{{trans('roles.add_role_form.validate.role_reg_string')}}"
                        }
                    }
                },
                description: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('roles.add_role_form.validate.role_description')}}"
                        }
                    }
                },
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();  
            var inherit_role = '';
            var updateroleId = $("#roleId").val();        
//            if ($("#inherit_role").val() != '') {
//                inherit_role = $("#inherit_role option:selected").text(); //$( "#myselect option:selected" ).text()       
//            }
            parent_role_id = $("#inherit_role").val();
            var role_name = $("#role_name").val();
            var short_code = $("#short_code").val();
            var description = $("#description").val();
            var legalentity =$("#legal_entity_id").val();
            var is_support_role = $('#is_support_role').is(':checked');
            if(is_support_role)
            {
                is_support_role = 1;
            }else{
                is_support_role = 0;
            }
            $.ajax({
            url: '/roles/updaterole/0',
            data: 'legal_entity_id=' + legalentity +'&updateroleId=' + updateroleId + '&parent_role_id=' + parent_role_id + '&role_name=' + role_name + '&description=' + description + '&is_support_role=' + is_support_role + '&short_code=' + $("#short_code").val().toUpperCase(),
            type: 'get', 
             success:function(data) {                
                if(data =='exit')  {
                    $('#flass_message').text("{{trans('roles.edit_role_form.role_exist')}}"); 
                    $('div.alert').show(); 
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);  
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    return false;
                } else {
                    $('#flass_message').text("{{trans('roles.edit_role_form.role_update')}}"); 
                    $('div.alert').show(); 
                    $('div.alert').removeClass('hide'); 
                    //$('div.alert').not('.alert-important').delay(3000).fadeOut(350);     
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    $('a[href="#permis"]').tab('show');
                    $("#roleId").val(data);
                }
            }
        });            
     });   
     $('input[type="checkbox"]').click(function(event){
	var refe = $(this).attr('ref');
        if(!$(this).prop('checked')){
            if(refe > 0){
                var temp = $('input[ref="'+refe+'"]:checked').length;
                if(temp == 0){
                    if($('#feature_name'+refe).prop('checked'))
                    {
                        $('#feature_name'+refe).prop('checked', false);
                        var ref2 = $('#feature_name'+refe).attr('ref');
                        var temp2 = $('input[ref="'+ref2+'"]:checked').length;
                        if(temp2 == 0){
                            if($('#feature_name'+ref2).prop('checked'))
                            {
                                $('#feature_name'+ref2).prop('checked', false);
                            }
                        }
                    }
                }
            }
        }else{
            if(!$('#feature_name'+refe+':checked').length)
            {
                $('#feature_name'+refe).prop('checked', true);
                var ref2 = $('#feature_name'+refe).attr('ref');
                if(!$('#feature_name'+ref2+':checked').length)
                {
                    $('#feature_name'+ref2).prop('checked', true);						
                }
            }	
        }
    });
  });
    
    function ajaxCall() {
        /*var myName = arguments.callee.toString();
        myName = myName.substr('function '.length);
        myName = myName.substr(0, myName.indexOf('('));       
        console.log(myName);*/
        $('#sample_2').igGrid({
            dataSource: $.parseJSON($('#userdata').val()),
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'result',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: '', key: 'profile_pic', dataType: 'string', width: '15%'},
                {headerText: 'User', key: 'fullname', dataType: 'string', width: '20%', columnCssClass: "addrolegridfont"},
                {headerText: '', key: 'user_id', dataType: 'number', hidden: true},
            ],
            features: [
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'profile_pic', allowFiltering: false}
                    ]
                },
                {
                    name: "RowSelectors",
                    enableCheckBoxes: true,
                    enableRowNumbering: false
                },
                {
                    name: 'Selection',
                    multipleSelection: true
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                },
                 {

                    name: 'Paging',
                     type: "local",
                    pageSize: 10
                }
                
            ],
            primaryKey: 'user_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
        
        var getrows = $('#sample_2').igGridSelection('selectedRows');
        for (var i = 0; i < getrows.length; i++) {
            try{
            $('#sample_2').igGridSelection('deselectRow', getrows[i].index);
        }catch(e){ console.log('fail to unselect'); }
        }
    }
    
    function addAjaxCall() {                    
        $('#sample_3').igGrid({
            dataSource: $.parseJSON($('#userdata1').val()),
            autoGenerateColumns: false, 
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'iteams',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: '', key: 'profile_pic', dataType: 'string', width: '15%'},
                {headerText: 'User', key: 'fullname', dataType: 'string', width: '20%', columnCssClass: "addrolegridfont"},
                {headerText: '', key: 'user_id', dataType: 'number', hidden: true},
            ],
            features: [
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'profile_pic', allowFiltering: false}
                    ]
                },
                {
                    name: "RowSelectors",
                    enableCheckBoxes: true,
                    enableRowNumbering: false
                },
                {
                    name: 'Selection',
                    multipleSelection: true
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                },
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 10
                }
            ],
            primaryKey: 'user_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
        var getrows = $('#sample_3').igGridSelection('selectedRows');    
        for (var i = 0; i < getrows.length; i++) {
            try{
            $('#sample_3').igGridSelection('deselectRow', getrows[i].index);
        }catch(e){ console.log('fail to unselect'); }
        }
    }
    
    function checkAll(clsId, state, parnId) {           
        if(state) {    
            $("#uniform-feature_name"+parnId).find('span').addClass('checked ');
            $("." + clsId).prop("checked", state).parent('span').addClass('checked');
            checkBoxChildCount(parnId);
        } else {
            var checkBoxCountFlag = checkBoxChildCount(parnId);              
            if(checkBoxCountFlag === false) {                  
                $("#uniform-feature_name"+parnId).find('span').removeClass('checked');                                
            }            
            $("." + clsId).prop("checked", state).parent('span').removeClass('checked');            
        }                
        checkBoxSubChildCount();
        checksubchild(parnId, state, clsId);
    }
    
    function checkBoxChildCount(parnId) {
        //alert(parnId)
        var index = 0;
        $('input:checkbox.'+parnId).each(function () {
            if(this.checked) {
                index++;                
            }            
        });         
        if(index > 0) {
            return true;
        } else {
             return false;
        }
    };
   
   function checksubchild(parnId, status, child) {     
       if(status) {
           $("#uniform-feature_name"+child).find('span').addClass('checked');
           $("#uniform-feature_name"+child).find('span').find('input').prop('checked', true);
           $("#uniform-feature_name"+parnId).find('span').addClass('checked ');
           checkBoxSubChildCount(child)
           
       } else {
           var checkedFlag = checkBoxSubChildCount(child);             
           if(checkedFlag==false) {                                             
           $("#uniform-feature_name"+child).find('span').find('input').prop('checked', false);
           $("#uniform-feature_name"+child).find('span').removeClass('checked');            
            var index = 0;
            $('input:checkbox.parent').each(function () {
            if(this.checked) {
                index++;
             }            
            });        
            if(index==0) {
               $("#uniform-feature_name"+parnId).find('span').removeClass('checked');
           }
        }
        
      }
   }
    
    function checkBoxSubChildCount(child){
        var flag = 0;
        $('input:checkbox.'+child).each(function () {
            if(this.checked){
                flag++;
            }
        });        
        if(flag > 0){
            return true;
        }else{
             return false;
        }
    }
   function checkedParent(id, state) {
        if (state == true) {
            $("#feature_name" + id).prop('checked', true);
            //$("#feature_name" + subid).chai('span').addClass('checked');
        }else{
//            $("#feature_name" + id).prop('checked', false);
        }
    }
    /* function checkedParent1(id, subid, state) {
        if (state == true) {
            $("#feature_name" + id).prop('checked', true);
            $("#feature_name" + id).parent('span').addClass('checked');
            $("#feature_name" + subid).prop('checked', true);
        }
    }*/
    function sendFileToServer(formData, status)
    {
        var uploadURL = "/roles/uploadProfilePic"; //Upload URL
        var token = $("#csrf-token").val();
        var extraData = {}; //Extra Data.
        var jqXHR = $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            xhr: function () {
                var xhrobj = $.ajaxSettings.xhr();
                if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function (event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
                return xhrobj;
            },
            url: uploadURL,
            type: "POST",
            contentType: false,
            processData: false,
            cache: false,
            data: formData,
            success: function (data) {
                status.setProgress(100);
                //$("#status1").append("Data from Server:"+data+"<br>");    
                $("#profile_picture").val(data);
            }
        });
        status.setAbort(jqXHR);
    }

    var rowCount = 0;
    function createStatusbar(obj)
    {
        rowCount++;
        var row = "odd";
        if (rowCount % 2 == 0)
            row = "even";
        this.statusbar = $("<div class='statusbar " + row + "' ></div>");
        this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
        this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
        this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
        this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
        obj.after(this.statusbar);
        this.setFileNameSize = function (name, size)
        {
            var sizeStr = "";
            var sizeKB = size / 1024;
            if (parseInt(sizeKB) > 1024)
            {
                var sizeMB = sizeKB / 1024;
                sizeStr = sizeMB.toFixed(2) + " MB";
            } else
            {
                sizeStr = sizeKB.toFixed(2) + " KB";
            }

            this.filename.html(name);
            this.size.html(sizeStr);
        }
        this.setProgress = function (progress)
        {
            var progressBarWidth = progress * this.progressBar.width() / 100;
            this.progressBar.find('div').animate({width: progressBarWidth}, 10).html(progress + "%&nbsp;");
            if (parseInt(progress) >= 100)
            {
                this.abort.hide();
            }
        }
        this.setAbort = function (jqxhr)
        {
            var sb = this.statusbar;
            this.abort.click(function ()
            {
                jqxhr.abort();
                sb.hide();
            });
        }
    }
    function handleFileUpload(files, obj)
    {
        for (var i = 0; i < files.length; i++)
        {
            var fd = new FormData();
            fd.append('file', files[i]);
            var status = new createStatusbar(obj); //Using this we can set progress.
            status.setFileNameSize(files[i].name, files[i].size);
            sendFileToServer(fd, status);
        }
    }
  


    $(".save2").click(function ()
    {
        //feature_name         
        var hasChilds = parseInt($('#hasChilds').val());
        var updateChilds = 0;
        if(hasChilds)
        {
            $("#dialog").dialog({
                buttons : {
                    "Yes" : function() {
                        updateChilds = 1;
                        savePermission(updateChilds);
                        $(this).dialog("close");
                    },
                    "No" : function() {
                        savePermission(updateChilds);
                        $(this).dialog("close");
                    }
                }
            });
            $("#dialog").dialog("open");
        }else{
            savePermission(updateChilds);  
        }
    });


    $(".save3").click(function ()
    {        
        var role_id = $("#roleId").val();
        var rows = '';
        rows = $('#sample_3').igGridSelection('selectedRows');
        var dataview = $('#sample_3').data('igGrid').dataSource.data();        
        var roleuserIds = [];
        for (var i = 0; i < dataview.length; i++) {
            var user_id = dataview[i].user_id;
            roleuserIds.push(user_id);
        }
        if (role_id != '')
        {
            $.ajax({
                url: '/roles/insertusersrole',
                data: 'role_id=' + role_id + '&user_ids=' + roleuserIds,
                typ: 'get',
                success: function (data) {                   
                    $('#flass_message').text("{{trans('roles.edit_role_form.role_users')}}"); 
                    $('div.alert').show(); 
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);                    
                    $('html, body').animate({scrollTop: '0px'}, 500);
                        window.setTimeout(function(){
                        window.location.href='/roles/index';
                    },2000);                    
                }
            });
        }
    });

    $("#inherit_role").on("change", function ()
    {
        $('[type="checkbox"]').each(function(event){
            if($(this).prop('checked'))
            {
//               $(this).trigger('click'); 
               $(this).prop('checked', false); 
            }
        });
        $(".mt-checkbox-list").find(".checker").find("span").removeClass('checked')
        var prmissionIds = $(this).val();
        var token = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/roles/getpermissionids',
            data: 'roleId=' + prmissionIds,
            type: 'get',
            success: function (data) {

                for (var i = 0; i < data.length; i++) {
                    if (data[i] != '') {
//                        $(".mt-checkbox-list").find('#uniform-feature_name' + data[i]).find('span').addClass('checked')
                        var temp = $('input[value="'+data[i]+'"]').is(':checked');
                        if(!temp)
                        {
//                            $('input[value="'+data[i]+'"]').trigger('click');
                            $('input[value="'+data[i]+'"]').prop('checked', true);
                        }
                    }

                }
            }
        });
    });

    $(".back1").click(function () {
        $('a[href="#role"]').tab('show');
    });

    $(".back2").click(function () {
        $('a[href="#permis"]').tab('show');
    });
    $(".next").click(function () {
        $('a[href="#permis"]').tab('show');
    });
    
    function savePermission(updateChilds)
    {
        var role_id = $("#roleId").val();
        var cheCkedValues = [];
        $('a[href="#users"]').tab('show');
        $('input[name="feature_name[]"]:checked').each(function (i, vl) {
            var id = $(this).val();
            cheCkedValues.push(id)
        });
        if (role_id != '')
        {
            $.ajax({
                url: '/roles/insertrolepermission',
                data: 'role_id=' + role_id + '&feature_name=' + cheCkedValues + '&update_childs=' + updateChilds,
                type: 'get',
                success: function (data)
                {
                    console.log(data);
                }
            });
        }
    }
</script>
@stop