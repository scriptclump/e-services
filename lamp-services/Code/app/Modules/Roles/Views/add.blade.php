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
                <div class="caption" > {{trans('roles.role_tab.role_add')}} </div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
            </div>
            <div class="portlet-body">
                <div id="form-wiz" class="portlet-body">
                    <div class="tabbable-line">

                        <ul class="nav nav-tabs" id="test1">
                            <li class="active act1"><a href="javascript:void(0);" class='roletab' style="cursor:default">{{trans('roles.role_tab.role')}}</a></li>
                            <li class="act2"><a href="javascript:void(0);" class='permistab' style="cursor:default">{{trans('roles.role_tab.role_permissions')}} </a></li>
                            <li class="act3"><a href="javascript:void(0);" class="userstab"style="cursor:default">{{trans('roles.role_tab.role_users')}} </a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active main-tabs" id="role">
                                {{Form::open(array('url'=>'/roles/saveRole/0','method'=>'put', 'id' => 'role_step_1'))}}
                                <div class="row">
                                    <div class="col-md-6">
                                    
                                        <div class="form-group">
                                            <label class="control-label"> {{trans('roles.role_tab.parent_role')}} <span class="required">*</span></label>
                                            <div id="selectbox">
                                                <select class="form-control select2me" id="inherit_role" name="inherit_role" parsley-trigger="change" parsley-required="true" parsley-error-container="#selectbox"  onchange="getRole(this.value)">
                                                    <option value="">Please Select...</option>
                                                    @foreach($inheritRoles as $inheritRole)  
                                                        <option value="{{$inheritRole->role_id}}">{{$inheritRole->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" id="roleId" name="roleId">
                                            <label class="control-label"> {{trans('roles.role_tab.role_name')}} <span class="required">*</span></label>
                                            <input type="text" class="form-control select2" placeholder="Role Name" name="role_name" id="role_name" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"> {{trans('roles.role_tab.role_description')}} <span class="required">*</span></label>
                                            <textarea class="form-control select2" id="description" name="description" rows="3"></textarea>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{trans('roles.role_tab.role_code')}}</label>
                                            <input class="form-control" type="text" name="short_code" id="short_code"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <input class="" type="checkbox" name="is_support_role" id="is_support_role"/>
                                            <sapn>{{trans('roles.role_tab.is_support_role')}}</sapn>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:96px;">
                                    <hr />
                                    <div class="col-md-12 text-center">
                                        <button class="btn green-meadow save1" type="submit" name="Save & Continue">Save & Continue</button>
                                        <!--<a class="btn green-meadow save1" href="javascript:void(0)" >Save & Continue</a>-->
                                    </div>
                                </div>
                                {{Form::close()}}
                            </div>
                            <div class="tab-pane main-tabs" id="permis">

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
    <?php if (property_exists($moduleChild, 'child')) { ?>
    @if(property_exists($moduleChild, 'feature_id'))
    <div class="form-group">
        <label class="mt-checkbox"> {{$moduleChild->name}}
        <input type="checkbox" value="{{$moduleChild->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild->feature_id}}" class="checkbox minimal {{$moduleChild->feature_id}}" onchange="checkAll('{{$moduleChild->feature_id}}', this.checked);" />
      
        <span data-original-title="{{$moduleChild->name}} &nbsp;({{$moduleChild->feature_code}})" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span>
            <span></span>
        </label>
    </div>
    
    
    @endif
    @foreach($moduleChild->child as $moduleChild1)
    <?php if (is_object($moduleChild1) && property_exists($moduleChild1, 'name')) { ?>
    <div class="form-group">
    
     <label class="mt-checkbox" style="margin-left:30px;" id="child1-checkbox">   {{$moduleChild1->name}}  
          <input type="checkbox" ref="{{$moduleChild->feature_id}}" value="{{$moduleChild1->feature_id}}" name="feature_name[]"  id="feature_name{{$moduleChild1->feature_id}}" class="parsley-validated {{$module->value}} {{$moduleChild->feature_id}} parent" onchange="checkAll('{{$moduleChild1->feature_id}}', this.checked, '{{$moduleChild->feature_id}}')">
         
       <span data-original-title="{{$moduleChild1->name}} &nbsp;({{$moduleChild1->feature_code}})" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span>
         <span></span>
        </label>
      
      <?php if (property_exists($moduleChild1, 'child')) { ?>
      @foreach($moduleChild1->child as $moduleChild2)
      <?php if (is_object($moduleChild2) && property_exists($moduleChild2, 'feature_id') && property_exists($moduleChild2, 'name')) { ?>
      <label class="mt-checkbox" style="margin-left:60px; display: inherit;" id="child3-checkbox">{{$moduleChild2->name}}
      
     
          <input  type="checkbox" ref="{{$moduleChild1->feature_id}}" value="{{$moduleChild2->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild2->feature_id}}" 
                                                                                                class="minimal {{$moduleChild->feature_id}} {{$moduleChild1->feature_id}} subchaild" 
                                                                                                onchange="checksubchild('{{$moduleChild->feature_id}}', this.checked, '{{$moduleChild1->feature_id}}')"/>
          
        <span data-original-title="{{$moduleChild2->name}} &nbsp;({{$moduleChild2->feature_code}})" data-placement="top" class="badge bg-blue tooltips" style="position: static;"> <i class="fa fa-question"></i> </span>
        <span></span> 
        </label>
    
      <?php } ?>
      <?php // echo "<pre>";print_R($moduleChild2);die; ?>
      @endforeach 
      <!--<hr/>-->
      <?php } else { ?>
      @if(property_exists($moduleChild1, 'feature_id') && property_exists($moduleChild1, 'value'))
      <div class="mt-checkbox-list" id="child2-checkbox">
        <label class="mt-checkbox">
        <div class="checker" id="uniform-select_all"><span>
          <input type="checkbox" ref="{{$moduleChild1->feature_id}}" value="{{$moduleChild1->feature_id}}" name="feature_name[]" id="feature_name{{$moduleChild1->feature_id}}" class="minimal {{$moduleChild->feature_id}}" />
          </span></div>
        <span>{{$moduleChild1->name}}</span> <span data-original-title="{{$moduleChild1->name}}" data-placement="top" class="badge bg-blue tooltips"> <i class="fa fa-question"></i> </span>
        </label>
      </div>
      @endif
      <?php } ?>
    </div>
    <?php } ?>
    @endforeach
    <?php } else { ?>
    @if(property_exists($moduleChild, 'feature_id'))
    
    <div class="form-group">
        <label class="mt-checkbox">{{$moduleChild->name}}
		 <input type="checkbox" id="uniform-select_all" value="{{$moduleChild->feature_id}}" name="feature_name[]"  id="feature_name{{$moduleChild->feature_id}}"  class="minimal {{$moduleChild->feature_id}}" />
 <span data-original-title="{{$moduleChild->name}}" data-placement="top" class="badge bg-blue tooltips"  style="position: static;"> <i class="fa fa-question"></i> </span>
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
                                <div class="tab-pane main-tabs" id="users">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-12">
                                            <h3 class="form-section"> {{trans('roles.role_tab.role_select_users')}} </h3>
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

                                        <div class="col-md-1 col-sm-12" style="position:relative; top:250px; padding-left: 35px;">
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
                                            <a class="btn green-meadow save3" href="javascript:void(0)">Done</a>
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
        <input type="hidden" id="userdata1" name="userids[]"/> 
    </div>
</div>
@stop
@section('style')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.form-group {margin-bottom:0px !important;}
.portlet.light {padding: 12px 15px !important;}
.control-label {margin-top: 15px !important;}
</style>
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#permis li').click(function(event){
            $('#permis li').removeClass('active');
        });
        var usersData;
        $.get('/roles/getiggridusers', function (response) {
            usersData = $('#userdata').val(response);
            ajaxCall();
        });

        $("#short_code").on('keydown', function(evt) {
            $(this).val(function (_, val) {
                return val.toUpperCase();
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
                    enableRowNumbering: false,
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
                    pageSize: 4
                }
            ],
            primaryKey: 'user_id',
            width: '100%',
            height: '450px',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
        var getrows = $('#sample_2').igGridSelection('selectedRows');
        for (var i = 0; i < getrows.length; i++) {
            try {
                $('#sample_2').igGridSelection('deselectRow', getrows[i].index);
            } catch (e) {
                console.log('fail to unselect');
            }
        }
    }
    $(document).ready(function () {
        var user_array = new Array();
        var data = [];
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
            var parent_role_id = $("#inherit_role").val();
            var updateroleId = $("#roleId").val();
//            if ($("#inherit_role").val() != '') {
//                inherit_role = $("#inherit_role option:selected").text();
//            }
            var role_name = $("#role_name").val();
            var description = $("#description").val();
            var is_support_role = $('#is_support_role').is(':checked');
            if(is_support_role)
            {
                is_support_role = 1;
            }else{
                is_support_role = 0;
            }
            $.ajax({
                url: '/roles/saveRole/0',
                data: 'updateroleId=' + updateroleId + '&parent_role_id=' + parent_role_id + '&role_name=' + role_name + '&description=' + description + '&is_support_role=' + is_support_role + '&short_code=' + $("#short_code").val().toUpperCase(),
                type: 'get',
                success: function (data) {
                    if (data == 'exit') {
                        $('#flass_message').text("{{trans('roles.add_role_form.validate.role_exist')}}");
                        $('div.alert').show();
                        $('div.alert').removeClass('hide');
                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                        return false;
                    } else {
                        $('#flass_message').text("{{trans('roles.add_role_form.role_created')}}");
                        $('div.alert').show();
                        $('div.alert').removeClass('hide');
                        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                        $('html, body').animate({scrollTop: '0px'}, 500);
                        continueButton();
//                        $('a[href="#permis"]').tab('show');
                        $("#roleId").val(data);
                    }
                }
            });
        });
        $('.moveRight').click(function () {
            var user_ids = [];
            if ($('#userdata1').val() != '')
            {
                var old_data = $.parseJSON($('#userdata1').val()) || [];
            } else {
                var old_data = [];
            }
            var getrows = '';
            getrows = $('#sample_2').igGridSelection('selectedRows');
            var data_View = $('#sample_2').data('igGrid').dataSource.dataView();
            var userData = [];
            for (var i = 0; i < getrows.length; i++) {
                if (data_View[getrows[i].index])
                {
                    var user_id = data_View[getrows[i].index].user_id;
                    user_array.push(user_id);
                    var dataDisplays = {};
                    dataDisplays["user_id"] = [];
                    dataDisplays["user_id"] = data_View[getrows[i].index].user_id;
                    dataDisplays["fullname"] = data_View[getrows[i].index].fullname;
                    dataDisplays["profile_pic"] = data_View[getrows[i].index].profile_pic;
                    userData.push(dataDisplays);
                    user_ids.push(dataDisplays["user_id"]);
                }
            }
            var new_data = old_data.concat(userData);
            var final_data = [],
                    user_s_ids = [];
            $.each(new_data, function (id, val) {
                if (user_s_ids.indexOf(val.user_id) != -1)
                    return;
                user_s_ids.push(val.user_id);
                final_data.push(val);
            });
            var currentObj = $.parseJSON($('#userdata').val()),
                    currentNewObj = [];
            $.each(currentObj, function (i, v) {
                if (user_ids.indexOf(v.user_id) != -1)
                    return;
                currentNewObj.push(v);
            });
            $('#userdata').val(JSON.stringify(currentNewObj));
            $('#userdata1').val(JSON.stringify(final_data));
            addAjaxCall();
            ajaxCall();
        });
        $('.moveLeft').click(function () {
            var user_ids = [];
            var old_data = $.parseJSON($('#userdata').val());
            var getrows = '';
            getrows = $('#sample_3').igGridSelection('selectedRows');
            var data_View = $('#sample_3').data('igGrid').dataSource.dataView();
            var userData = [];
            for (var i = 0; i < getrows.length; i++) {
                if (data_View[getrows[i].index])
                {
                    var user_id = data_View[getrows[i].index].user_id;
                    user_array.push(user_id);
                    var dataDisplays = {};
                    dataDisplays["user_id"] = data_View[getrows[i].index].user_id;
                    dataDisplays["fullname"] = data_View[getrows[i].index].fullname;
                    dataDisplays["profile_pic"] = data_View[getrows[i].index].profile_pic;
                    userData.push(dataDisplays); //string to json 
                    user_ids.push(dataDisplays["user_id"]);
                }
            }
            var new_data = old_data.concat(userData);
            var final_data = [],
                    user_s_ids = [];
            $.each(new_data, function (id, val) {
                if (user_s_ids.indexOf(val.user_id) != -1)
                    return;
                user_s_ids.push(val.user_id);
                final_data.push(val);
            });
            var currentObj = $.parseJSON($('#userdata1').val()),
                    currentNewObj = [];
            $.each(currentObj, function (i, v) {
                if (user_ids.indexOf(v.user_id) != -1)
                    return;
                currentNewObj.push(v);
            });
            $('#userdata1').val(JSON.stringify(currentNewObj));
            $('#userdata').val(JSON.stringify(final_data));
            addAjaxCall();
            ajaxCall();
        });
        function addAjaxCall() {
            //  console.log($('#userdata1').val());
            $('#sample_3').igGrid({
                dataSource: $.parseJSON($('#userdata1').val()),
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
                        pageSize: 4
                    }
                ],
                primaryKey: 'user_id',
                width: '100%',
                height: '450',
                initialDataBindDepth: 0,
                localSchemaTransform: false
            });
            var getrows = $('#sample_3').igGridSelection('selectedRows');
            for (var i = 0; i < getrows.length; i++) {
                try {
                    $('#sample_3').igGridSelection('deselectRow', getrows[i].index);
                } catch (e) {
                    console.log('fail to unselect');
                }
            }

        }

        $('.adduser').on('click', function () {
            return !$('#Selectuser option:selected').remove().appendTo('#user_id');
            //            $(".dd-option-value:checkbox:checked").prop('checked', function(){
            //                    if($(this).checked)
            //                        alert($(this).val());
            //                });

        });
        $('.removeUser').on('click', function () {
            return !$('#user_id option:selected').remove().appendTo('#Selectuser');
        });
        $("#userForm").submit(function (event) {
            $("#popupContent").hide();
            $("#popupLoader").show();
            event.preventDefault();
            $.post('/roles/saveUser', $("#userForm").serialize(), function (response) {
                var res_arr = response.split('|');
                var data = $.parseJSON(res_arr[1]);
                if (res_arr[0] == 'success')
                {
                    alert('sucess');
                    $("#sample_2").igGrid("dataBind");
                    ajaxCall();
                    $("#popupLoader").hide();
                    $("#popupContent").show();
                    $('#user_id').append('<option value="' + data.user_id + '">' + data.firstname + ' ' + data.lastname + '</option>');
                    $('#user_id option').prop('selected', true);
                    $(".close").click();
                } else {
                    var Str = '';
                    $("#popupLoader").hide();
                    $("#popupContent").show();
                    if (data.customer_type != undefined) {
                        Str += data.customer_type + "<br>";
                    }
                    if (data.firstname != undefined) {
                        Str += data.firstname + "<br>";
                    }
                    if (data.lastname != undefined) {
                        Str += data.lastname + "<br>";
                    }
                    if (data.email_id != undefined) {
                        Str += data.email_id + "<br>";
                    }
                    if (data.password != undefined) {
                        Str += data.password + "<br>";
                    }
                    if (data.confirm_password != undefined) {
                        Str += data.confirm_password + "<br>";
                    }
                    if (data.phone_no != undefined) {
                        Str += data.phone_no + "<br>";
                    }
                    if (data.message != undefined) {
                        Str += data.message + "<br>";
                    }
                    $("#erroMsg").html(Str);
                }
            });
        });
        //$('#Selectuser').ddslick();

    });
    function getRole(id) {
        var token = $('#csrf-token').val();
        $.post('getRoleforInherit/' + id, {'_token' : token}, function (res) {
            var data = $.parseJSON(res);
            $('input:checkbox').removeAttr('checked');
            $("#opt01").prop('checked', true);
            var features = data[0].feature_id.split(',');
            for (var i = 0; i < features.length; i++) {
//                $("#feature_name" + features[i]).prop('checked', true);
//                $('input[value="'+features[i]+'"]').trigger('click');
                $('input[value="'+features[i]+'"]').prop('checked', true);
            }
            $('#customer_type').val(data[0].role_type).attr('selected', true);
            $("#customer_type1").val(data[0].role_type);
            $('.mfgName').show();
        });
    }


    function checkAll(clsId, state, parnId) {
        if (state) {
            $("#uniform-feature_name" + parnId).find('span').addClass('checked ');
            $("." + clsId).prop("checked", state).parent('span').addClass('checked');
            checkBoxChildCount(parnId);
        } else {
            var checkBoxCountFlag = checkBoxChildCount(parnId);
            if (checkBoxCountFlag === false) {
                $("#uniform-feature_name" + parnId).find('span').removeClass('checked');
            }
            $("." + clsId).prop("checked", state).parent('span').removeClass('checked');
        }
        checkBoxSubChildCount();
        checksubchild(parnId, state, clsId);
    }

    function checkBoxChildCount(parnId) {
        var index = 0;
        $('input:checkbox.' + parnId).each(function () {
            if (this.checked) {
                index++;
            }
        });
        if (index > 0) {
            return true;
        } else {
            return false;
        }
    }
    ;
    function checksubchild(parnId, status, chaild) {
        if (status) {
            $("#uniform-feature_name" + chaild).find('span').addClass('checked');
            $("#uniform-feature_name" + chaild).find('span').find('input').prop('checked', true);
            $("#uniform-feature_name" + parnId).find('span').addClass('checked ');
            checkBoxSubChildCount(chaild);
        } else {
            var checkedFlag = checkBoxSubChildCount(chaild);
            if (checkedFlag == false) {
                $("#uniform-feature_name" + chaild).find('span').find('input').prop('checked', false);
                $("#uniform-feature_name" + chaild).find('span').removeClass('checked');
                var index = 0;
                $('input:checkbox.parent').each(function () {
                    if (this.checked) {
                        index++;
                    }
                });
                if (index == 0) {
                    $("#uniform-feature_name" + parnId).find('span').removeClass('checked');
                }
            }

        }
    }

    function checkBoxSubChildCount(chaild) {
        var flag = 0;
        $('input:checkbox.' + chaild).each(function () {
            if (this.checked) {
                flag++;
            }
        });
        if (flag > 0) {
            return true;
        } else {
            return false;
        }
    }
    /*function checkedParent(id, state) {
     if (state == true) {
     $("#feature_name" + id).prop('checked', true);
     //$("#feature_name" + subid).chai('span').addClass('checked');
     }
     }/*
     function checkedParent1(id, subid, state) {
     if (state == true) {
     $("#feature_name" + id).prop('checked', true);
     $("#feature_name" + id).parent('span').addClass('checked');
     $("#feature_name" + subid).prop('checked', true);
     }
     }*/


    function getCustomerUser(id)
    {
        if (id == 7001) {
            $("#customer_type1").val(id);
            id = 0;
        }

        if (id > 0)
            $("#customer_id").val(id);
        $.get('getUserDetail/' + id, function (data) {

            var dataArr = $.parseJSON(data);
            var Sel = $('#Selectuser');
            Sel.empty()
            for (var i = 0; i < dataArr['users'].length; i++) {
                Sel.append('<option value="' + dataArr['users'][i].user_id + '">' + dataArr['users'][i].username + '</option>');
            }
            var Location = $("#location_id");
            for (var i = 0; i < dataArr['locations'].length; i++) {
                Location.append('<option value="' + dataArr['locations'][i].location_id + '">' + dataArr['locations'][i].location_name + '</option>');
            }
            var business_unit_id = $("#business_unit_id");
            for (var i = 0; i < dataArr['businessunits'].length; i++) {
                business_unit_id.append('<option value="' + dataArr['businessunits'][i].business_unit_id + '">' + dataArr['businessunits'][i].name + '</option>');
            }

        });
    }
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
    $(document).ready(function ()
    {
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
    });
    $(".save2").click(function ()
    {
//feature_name 
        var role_id = $("#roleId").val();
        var cheCkedValues = [];
        $('input[name="feature_name[]"]:checked').each(function (i, vl) {
            var id = $(this).val();
            cheCkedValues.push(id)
        });
        if (role_id != '')
        {
            $.ajax({
                url: '/roles/insertrolepermission',
                data: 'role_id=' + role_id + '&feature_name=' + cheCkedValues,
                typ: 'get',
                success: function (data)
                {
//                    $('a[href="#users"]').tab('show');
                    updateButton();
                }
            });
        }
    });
    $(".save3").click(function () {        
        var dataview = $('#sample_3').data('igGrid').dataSource.data();
        if(dataview.length == 0)
        {
            window.location.href = '/roles/index';
            return;
        }
        var role_id = $("#roleId").val();
        $('#flass_message').text("{{trans('roles.add_role_form.role_users')}}");
        $('div.alert').show();
        $('div.alert').removeClass('hide');
        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        $('html, body').animate({scrollTop: '0px'}, 800);
        window.setTimeout(function () {
            window.location.href = '/roles/index';
        }, 2000);
        var rows = '';
        rows = $('#sample_3').igGridSelection('selectedRows');        
        var roleuserIds = [];
        for (var i = 0; i < dataview.length; i++) {
            var user_id = dataview[i].user_id;
            roleuserIds.push(user_id);
        }

        if (role_id != '' && roleuserIds != '') {
            $.ajax({
                url: '/roles/insertusersrole',
                data: 'role_id=' + role_id + '&user_ids=' + roleuserIds,
                typ: 'get',
                success: function (data) {
                    $('#flass_message').text('Users for role added successfully');
                    $('div.alert').show();
                    $('div.alert').removeClass('hide');
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    window.setTimeout(function () {
                        window.location.href = '/roles/index';
                    }, 2000);
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
        var permissionIds = $(this).val();
        var token = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/roles/getpermissionids',
            data: 'roleId=' + permissionIds,
            type: 'get',
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    if (data[i] != '') {
//                        $(".mt-checkbox-list").find('#uniform-feature_name' + data[i]).find('span').addClass('checked');
                        var temp = $('input[value="'+data[i]+'"]').prop('checked');
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
    function continueButton() 
    {
        $('.act1').removeClass('active');
        $('.act2').addClass('active');
        $('.main-tabs').removeClass('active');
        $('#permis').addClass('active');
        $('#permis').show();
    }
    function updateButton() 
    {
        $('.act2').removeClass('active');
        $('.act3').addClass('active');
        $('.main-tabs').removeClass('active');
        $('#users').addClass('active');
        $('#users').show();
    }
    $(".back1").click(function () {
//        $('a[href="#role"]').tab('show');
        $('.act2').removeClass('active');
        $('.act1').addClass('active');
        $('.main-tabs').removeClass('active');
        $('#role').addClass('active');
        $('#role').show();
    });
    $(".back2").click(function () {
//        $('a[href="#permis"]').tab('show');
        $('.act3').removeClass('active');
        $('.act2').addClass('active');
        $('.main-tabs').removeClass('active');
        $('#permis').addClass('active');
        $('#permis').show();
    });
</script>
@stop