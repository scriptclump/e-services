@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.css" /> 
<style type="text/css">
    label {
        padding-bottom: 0px !important;
    }
    a {
        color: #444;
    }
    .checkbox, .radio {
        margin-top: 0px !important;
    }
    #myproperlabel{
        margin-left: 20px;
    }
    .glyphicon-remove, .glyphicon-ok{top:120px !important;} 
</style>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height:680px;">
            <div class="portlet-title">
                <div class="caption">
                 {{trans('features.tab.index')}}
                </div>
<!--                 <input class="form-control" id="feature_search" type="text" style="width:20%;margin-left: 3%;margin-left: 7%;margin-top: 10px;">
 -->                @if($addFeature)
                    <a href="javascript:void(0)" class="btn green-meadow pull-right" data-toggle="modal" data-target="#basicvalCodeModal" ><span style="font-size:11px;">{{trans('features.tab.add')}}</span></a>
                @endif
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="{{trans('features.tab.fullscreen_msg')}}"><i class="fa fa-question"></i></a></span> </div>
            </div>
            <div class="portlet-body">
                <div id="form-wiz" class="portlet-body">
                    
                    <div id="treeGrid"></div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Modal - Popup for ADD -->

    <div class="modal modal-scroll fade in" id="basicvalCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog wide">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">ï¿œ</button>
                    <h4 class="modal-title" id="basicvalCode">{{trans('features.tab.add')}}</h4>
                </div>
                <div class="modal-body">        
                    {{ Form::open(array('url' => 'rbac/store','data-url' => 'rbac/store','id' => 'addfeature' )) }}
                    {{ Form::hidden('_method','POST') }}    
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="master_lookup_id">{{trans('features.modal.module_name')}}*</label>
                                <select name="master_lookup_id" id="master_lookup_id" class="form-control">
                                    <option value="0">{{trans('features.modal.default_select')}}</option>
                                    @foreach($modules as $module)
                                    <option value="{{$module->module_id}}">{{$module->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">{{trans('features.modal.feature_name')}}*</label>
                                <input type="text" id="name" name="name" placeholder="{{trans('features.modal.feature_name')}}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="feature_code">{{trans('features.modal.feature_name')}}*</label>
                                <input type="text" id="feature_code" name="feature_code" placeholder="{{trans('features.modal.feature_code')}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">

                                <label for="add_parent_id">{{trans('features.modal.parent_name')}}</label>
                                <select name="add_parent_id" id="add_parent_id" class="form-control">
                                    <option value="-1">{{trans('features.modal.default_select')}}</option>
                                    <option value="0">{{trans('features.modal.main_parent')}}</option>  
                                    @foreach($parents as $parent) @if($parent->parent_id==null)
                                    <option value="{{$parent->feature_id}}">{{$parent->featurename}}</option>
                                    @else
                                    <option value="{{$parent->feature_id}}">--{{$parent->featurename}}</option>
                                    @endif @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="icon">{{trans('features.modal.icon')}}</label>
                                <input type="text" id="icon" name="icon" value="" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="url">{{trans('features.modal.url')}}</label>
                                <input type="text" id="url" name="url" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sort_order">{{trans('features.modal.sort_order')}}*</label>
                                <select name="sort_order" id="sort_order" class="form-control">
                                    <option value="0">{{trans('features.modal.default_select')}}</option>
                                    <?php for ($i = 10; $i <= 1000; ) { ?>
                                        <option value="<?php echo $i; ?>">
                                            <?php echo $i; $i = $i+10; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputEmail">{{trans('features.modal.description')}}</label>
                                <textarea class="form-control" id="description" value="" name="description" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="wiki_url">{{trans('features.modal.wiki_url')}}</label>
                                <input type="text" id="wiki_url" name="wiki_url" value="" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="wiki_description">{{trans('features.modal.wiki_description')}}</label>
                                <textarea class="form-control" id="wiki_description" value="" name="wiki_description" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputEmail">{{trans('features.modal.status')}}</label>
                                <div id="myproperlabel">
                                    <div class="checkbox">
                                        <input type="checkbox" id="opt01" id="is_active" name="is_active" parsley-group="mygroup" parsley-trigger="change" parsley-required="true" parsley-mincheck="2" parsley-error-container="#myproperlabel .last" class="parsley-validated" checked="true">
                                        <label for="opt01">{{trans('features.modal.is_active')}}</label>
                                    </div>
                                </div>
                                <div id="myproperlabel">
                                    <div class="checkbox">
                                        <input type="checkbox" id="opt02" name="is_menu" parsley-group="mygroup" parsley-trigger="change" parsley-required="true" parsley-mincheck="2" parsley-error-container="#myproperlabel .last" class="parsley-validated" />
                                        <label for="opt02">{{trans('features.modal.is_menu')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="form-group">
                                {{ Form::submit('Add', array('class' => 'btn btn-primary','id'=>'addfeaturebutton')) }}
                            </div>
                        </div>

                    </div>
                    {{Form::close()}}
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="basicvalCodeModal1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog wide">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                    <h4 class="modal-title" id="basicvalCode">{{ trans('features.tab.edit') }}</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(array('url' => 'update','data-url' => 'update/','id' => 'editfeature')) !!}
                    {!! Form::hidden('_method','POST') !!}  
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="master_lookup_id">{{trans('features.modal.module_name')}}*</label>
                                <select name="master_lookup_id"  id="master_lookup_id" class="form-control">

                                    <option value="0">Please choose</option> 
                                    @foreach($modules as $module)
                                    <option value="{{$module->module_id}}">{{$module->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="text">{{trans('features.modal.feature_name')}}*</label>
                                <input type="text" id="name" name="name" placeholder="{{trans('features.modal.feature_name')}}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="text">{{trans('features.modal.feature_code')}}*</label>
                                <input type="text" id="feature_code" name="feature_code" placeholder="{{trans('features.modal.feature_code')}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">

                                <label for="edit_parent_id">{{ trans('features.modal.parent_name') }}</label>
                                <select name="edit_parent_id"  id="edit_parent_id" class="form-control">
                                    <option value="-1">{{trans('features.modal.default_select')}}</option>
                                    <option value="0">{{trans('features.modal.main_parent')}}</option>
                                    @foreach($parents as $parent) @if($parent->parent_id==null)
                                    <option value="{{$parent->feature_id}}">{{$parent->featurename}}</option>
                                    @else
                                    <option value="{{$parent->feature_id}}">--{{$parent->featurename}}</option>
                                    @endif @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="icon">{{trans('features.modal.icon')}}</label>
                                <input type="text" id="icon" name="icon" value="" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="url">{{trans('features.modal.url')}}</label>
                                <input type="text" id="url" name="url" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sort_order">{{trans('features.modal.sort_order')}}*</label>
                                <select name="sort_order" id="sort_order" class="form-control">
                                    <option value="0">{{trans('features.modal.default_select')}}</option>
                                    <?php for ($i = 10; $i <= 1000; ) { ?>
                                        <option value="<?php echo $i; ?>">
                                            <?php echo $i; $i = $i+10; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="description">{{trans('features.modal.description')}}</label>
                                <textarea class="form-control" id="description" value="" name="description" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="wiki_url">{{trans('features.modal.wiki_url')}}</label>
                                <input type="text" id="wiki_url" name="wiki_url" value="" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="wiki_description">{{trans('features.modal.wiki_description')}}</label>
                                <textarea class="form-control" id="wiki_description" value="" name="wiki_description" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Roles Feature<span class="required">*</span></label>
                                <select class="form-control chosen" multiple="true"  name="role_feature_id[]" id="role_feature_id">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="form-group">
                                {!! Form::submit('Update', array('class' => 'btn btn-primary', 'id' => 'update_button')) !!}
                            </div>
                        </div>

                    </div>                                            
                    {!!Form::close()!!}              
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- /.Editmodal -->

    <!-- Modal - Popup for Verify User Password while deleting -->
    <div class="modal fade" id="verifyUserPassword" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">ï¿œ</button>
                    <h4 class="modal-title" id="basicvalCode">{{trans('features.modal.enter_password')}}</h4>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="form-group col-sm-12">
                            <label class="col-sm-2 control-label" for="BusinessType">{{trans('features.modal.password')}}*</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon addon-red"><i class="fa fa-flag-checkered"></i></span>
                                    <input type="password" id="verifypassword" name="passwordverify" class="form-control">
                                    <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel-btn">{{trans('features.modal.cancel')}}</button>
                    <button type="button" id="save-btn" class="btn btn-success">{{trans('features.modal.submit')}}</button>
                </div>                
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    @stop    
    @section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.css" /> 
    <link rel="stylesheet" href="https://jqwidgets.com/public/jqwidgets/styles/jqx.base.css" /> 
    <link rel="stylesheet" href="https://jqwidgets.com/public/jqwidgets/styles/jqx.energyblue.css" />
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


    #errorsList li{
        display:none;
    }
    
   .jqx-input-group .jqx-input-group-addon{
        position: initial;
        height: 22px!important;
        width: 200px;
        margin: 4px;
        bottom:160px;
    }
    .jqx-rc-r-energyblue{
        margin-left: 20px!important;
        margin-top: 0px !important;
    }
    .jqx-input.jqx-input-energyblue.jqx-rc-l.jqx-rc-l-energyblue.jqx-input-group-addon.jqx-input-group-addon-energyblue.jqx-widget.jqx-widget-energyblue.jqx-widget-content.jqx-widget-content-energyblue{
        margin-left: 136px!important;
.   }

    .jqx-rc-all.jqx-rc-all-energyblue.jqx-widget.jqx-widget-energyblue.jqx-input-group.jqx-input-group-energyblue{
        position: absolute !important;
/*        overflow-y: auto;
*/    }
    .filtercolumns{
        z-index: 100!important;
    }
    .jqx-fill-state-normal.jqx-fill-state-normal-energyblue.jqx-rc-r.jqx-rc-r-energyblue.jqx-input-group-addon.jqx-input-group-addon-energyblue{
        width: 25px!important;
    }
</style>
{{HTML::style('css/switch-custom.css')}}
@stop
    @section('userscript')

    <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>  
    @include('includes.validators')    
    @include('includes.jqx')
    <script type="text/javascript">
        $(document).ready(function ()

        {
            $("#role_feature_id").chosen({width: "100%"});  
            $(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('features.features_title.index_page_title')}}");
            ajaxCall();
            makePopupAjax($('#basicvalCodeModal'));
            makePopupEditAjax($('#basicvalCodeModal1'), 'feature_id');
            $(".jqx-input-group-addon").attr("autocomplete", "off");

            /*$('#feature_search').keyup(function(){
                var search_text = $('#feature_search').val();
                var dataString = { 'text': search_text, '_token' : $('#csrf_token').val() };
                if(search_text==''){

                }else{
                    ajaxCall();
                }
            })*/

        });  
            $("#editfeature").submit( function(event) {
                // Code to Update Roles of the Selected Feature
                var roleFeatureResponse = [];
                $.each($(".chosen-choices li"), function(){            
                    roleFeatureResponse.push(parseInt($("#role_feature_id option:contains('"+$(this).text()+"')").val()));
                });
                
                $(this).append('<input name="rolesForFeature" style="display:none;" value="'+JSON.stringify(roleFeatureResponse)+'">');

                $(this).bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        master_lookup_id: {
                            validators: {
                                callback: {
                                    message: 'Please choose Module',
                                    callback: function (value, validator, $field) {
                                        var options = $('[name="master_lookup_id"]').val();
                                        return (options != 'Please choose');
                                    }
                                },
                                notEmpty: {
                                    message: 'Module is required.'
                                }
                            }
                        },
                        name: {
                            validators: {
                                notEmpty: {
                                    message: 'Feature Name is required.'
                                }
                            }
                        },
                        feature_code: {
                            validators: {
                                notEmpty: {
                                    message: 'Feature Code is required.'
                                }
                            }
                        },
                        sort_order: {
                            validators: {
                                callback: {
                                    message: "Sort order is mandatory",
                                    callback: function(value, validator) {
                                        return value > 0;
                                    }
                                }
                            }
                        }
                    }
                }).on('success.form.bv', function (event) {
                    return false;
                });
		return true;
            });  

        function ajaxCall()
        {
            
            $('.loderholder').show();
            $.ajax(
                    {
                        url: "getdata",
                        success: function (result)
                        {
                            var employees = result;
                            // prepare the data
                            var source =
                                    {
                                        datatype: "json",
                                        datafields: [
                                            {name: 'modulename', type: 'string'},
                                            {name: 'featurename', type: 'string'},
                                            {name: 'featurecode', type: 'string'},
                                            {name: 'sort_order', type: 'string'},
                                            {name: 'is_menu', type: 'string'},
                                            {name: 'status', type: 'string'},
                                            {name: 'actions', type: 'string'},
                                            {name: 'children', type: 'array'},
                                            {name: 'expanded', type: 'bool'}
                                        ],
                                        hierarchy:
                                                {
                                                    root: 'children'
                                                },
                                        id: 'feature_id',
                                        localData: employees
                                    };
                            var dataAdapter = new $.jqx.dataAdapter(source);
                            // create Tree Grid
                            $("#treeGrid").jqxTreeGrid(
                                    {
                                        width: "100%",
                                        source: dataAdapter,
                                        sortable: true,
                                        //autoheight: true,
                                        //autowidth: true,
                                        filterable: true,
                                        theme: 'energyblue',

                                        columns: [
                                            {text: "{{trans('features.modal.module_name')}}", datafield: 'modulename', filterable: true, width: "10%"},
                                            {text: "{{trans('features.modal.feature_name')}}", datafield: 'featurename', filterable: true, width: "30%"},
                                            {text: "{{trans('features.modal.feature_code')}}", datafield: 'feature_code', width: "20%"},
                                            {text: "{{trans('features.modal.sort_order')}}", datafield: 'sort_order', width: "10%"},
                                            {text: "{{trans('features.modal.is_menu')}}", datafield: 'is_menu', width: "10%"},
                                            {text: "{{trans('features.modal.status')}}", datafield: 'is_active', width: "10%"},
                                            {text: "{{trans('features.modal.actions')}}", datafield: 'actions', width: "10%"}
                                        ]
                                    });
                              $('#treeGrid').on('filter', function (event) {
                                var filterGroups = event.args.filters;
                                var filterInfo = "";
                                for (var x = 0; x < filterGroups.length; x++) {
                                    var filterDataField = filterGroups[x].datafield;
                                    var filterGroup = filterGroups[x].filter;
                                    var filterOperator = filterGroup.operator;
                                    var filters = filterGroup.getfilters();

                                    filterInfo += "\nData Field: " + filterDataField;
                                    filterInfo += "\nOperator: " + filterOperator;

                                    for (var m = 0; m < filters.length; m++) {
                                        var filter = filters[m];
                                        var value = filter.value;
                                        var condition = filter.condition;
                                        var operator = filter.operator;
                                        var type = filter.type;
                                        filterInfo += "\nCondition " + m + ": " + condition;
                                        filterInfo += "\nValue " + m + ": " + value;
                                        filterInfo += "\nFilter Type " + m + ": " + type;
                                    }
                                }
                             });

                        }
                    });
                    $('.loderholder').hide();
        }

        function deleteEntityType(feature_id)
        {
            var dec = confirm("{{trans('features.delete.confirm')}}");
            if (dec == true)
                $('#verifyUserPassword').modal('show');
            $('#verifyUserPassword button#cancel-btn').on('click', function (e) {
                e.preventDefault();
                //console.log('clicked cancel');
                $('#verifyUserPassword').modal('hide');
            });
            $('#verifyUserPassword button#save-btn').on('click', function (e) {
                e.preventDefault();
                //console.log('cliked submit');
                var userPassword = $.trim($('#verifyUserPassword input').val());
                var token = $("#csrf_token").val();
                if (userPassword == '') {
                    alert('Field is required');
                    return false;
                } else
                    $.ajax({
                        url: 'deletefeature/' + feature_id,
                        data: {'_token' : token, 'password' : userPassword},
                        type: 'POST',
                        success: function (result)
                        {

                            if (result.status == 1) {   
                                alert("{{trans('features.delete.success')}}");
                                
                                // This line is used to delete, single feature from the list
                                $('[id="edit_parent_id"] option[id="'+ result.deleted_parent_id +'"]').remove();
                                $('[id="add_parent_id"] option[id="'+ result.deleted_parent_id +'"]').remove();
                                
                                ajaxCall();
//                                location.reload();
                                //window.location.href = '/customer/editcustomer/'+manufacturerId;
                                $('#verifyUserPassword').modal('hide');
                            } else {
                                alert(result);
                            }
                        },
                        error: function (err) {
                            console.log('Error: ' + err);
                        },
                        complete: function (data) {
                            console.log(data);
                        }
                    });
            });
        }
        function deleteParent(feature_id)
        {
            var dec = confirm("{{trans('features.delete.with_parent')}}");
            if (dec == true)
                $('#verifyUserPassword').modal('show');
            $('#verifyUserPassword button#cancel-btn').on('click', function (e) {
                e.preventDefault();
                //console.log('clicked cancel');
                $('#verifyUserPassword').modal('hide');
            });
            $('#verifyUserPassword button#save-btn').on('click', function (e) {
                e.preventDefault();
                //console.log('cliked submit');
                var userPassword = $.trim($('#verifyUserPassword input').val());
                var token = $("#csrf_token").val();
                if (userPassword == '') {
                    alert('Field is required');
                    return false
                } else
                    $.ajax({
                        url: 'deleteParentfeature/' + feature_id,
                        data: {'_token' : token, 'password' : userPassword},
                        type: 'POST',
                        success: function (result)
                        {

                            if (result.status == 1) {    
                                alert("{{trans('features.delete.success')}}");

                                // The below Code is to Delete the Features from the parent_id select option. 
                                // When they are deleted by the user, they are no longer needed in the parent_id select..
                                var featuresIds = result.featuresList.split(',');
                                var i;
                                for (i = 0; i < featuresIds.length; ++i) {
                                    //console.log(featuresIds[i])
                                    $('[id="edit_parent_id"] option[value="'+ featuresIds[i] +'"]').remove(); 
                                    $('[id="add_parent_id"] option[value="'+ featuresIds[i] +'"]').remove(); 
                                }

                                ajaxCall();
//                                location.reload();
                                //window.location.href = '/customer/editcustomer/'+manufacturerId;
                                $('#verifyUserPassword').modal('hide');
                            } else {
                                alert(result);
                            }
                        },
                        error: function (err) {
                            console.log('Error: ' + err);
                        },
                        complete: function (data) {
                            console.log(data);
                        }
                    });
            });
        }


        $('#verifyUserPassword').on('hide.bs.modal', function () {
            console.log('hide bs modal');
            $(this).find('button#cancel-btn').off('click');
            $(this).find('button#save-btn').off('click');
            $(this).find('input').val('');
        });
        function getModuleId(moduleid, parentid) {
            $('#master_lookup_id').val(moduleid);
            $('#parent_id').val(parentid);
        }
        $(document).ready(function () {
            $('#addfeature').bootstrapValidator({
                //live: 'disabled',
                message: "{{trans('features.validation.default')}}",
                feedbackIcons: {
//                    valid: 'glyphicon glyphicon-ok',
//                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    master_lookup_id: {
                        validators: {
                            callback: {
                                message: "{{trans('features.validation.module_select')}}",
                                callback: function (value, validator, $field) {
                                    var options = $('[name="master_lookup_id"]').val();
                                    return (options != "{{trans('features.modal.default_select')}}");
                                }
                            },
                            notEmpty: {
                                message: "{{trans('features.validation.module_required')}}",
                            }
                        }
                    },
                    name: {
                        validators: {
                            notEmpty: {
                                message: "{{trans('features.validation.feature_name_required')}}",
                            }
                        }
                    },
                    feature_code: {
                        validators: {
                            notEmpty: {
                                message: "{{trans('features.validation.feature_code_required')}}",
                            }
                        }
                    },
                    sort_order: {
                        validators: {
                            callback: {
                                message: "{{trans('features.validation.sort_order_required')}}",
                                callback: function(value, validator) {
                                    return value > 0;
                                }
                            }
                        }
                    }
                }
            }).on('success.form.bv', function (event) {
                ajaxCallPopup($('#addfeature'));
                return false;
            }).validate({
                submitHandler: function (form) {
                    return false;
                }
            });

        });
 
        $('#basicvalCodeModal').on('hide.bs.modal', function () {

            console.log('Add - resetForm');    
            $('#addfeature').bootstrapValidator('resetForm', true);
            $('#addfeature')[0].reset();
            $('#addfeaturebutton').removeClass('disabled');
            $('#addfeaturebutton').removeAttr('disabled');
        });
        $('#basicvalCodeModal1').on('hide.bs.modal', function () {

            console.log('Edit - resetForm');   
            $('#editFeature').bootstrapValidator('resetForm', true);
            $('#editfeature')[0].reset();
            $('#role_feature_id option').remove().trigger('chosen:updated');
            $('#update_button').removeClass('disabled');
            $('#update_button').removeAttr('disabled');
        });

        $(document).ready(function () {
            $('#editfeature').bootstrapValidator({
                //live: 'disabled',
                message: "{{trans('features.validation.default')}}",
                feedbackIcons: {
//                    valid: 'glyphicon glyphicon-ok',
//                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    master_lookup_id: {
                        validators: {
                            callback: {
                                message: "{{trans('features.validation.module_select')}}",
                                callback: function (value, validator, $field) {
                                    var options = $('[name="master_lookup_id"]').val();
                                    return (options != "{{trans('features.modal.default_select')}}");
                                }
                            },
                            notEmpty: {
                                message: "{{trans('features.validation.module_required')}}",
                            }
                        }
                    },
                    name: {
                        validators: {
                            notEmpty: {
                                message: "{{trans('features.validation.feature_name_required')}}",
                            }
                        }
                    },
                    feature_code: {
                        validators: {
                            notEmpty: {
                                message: "{{trans('features.validation.feature_code_required')}}",
                            }
                        }
                    },
                    sort_order: {
                        validators: {
                            callback: {
                                message: "{{trans('features.validation.sort_order_required')}}",
                                callback: function(value, validator) {
                                    return value > 0;
                                }
                            }
                        }
                    }
                }
            }).on('success.form.bv', function (event) {
                return false;
            });

        });
        function updateIsMenu(featureId)
        {
            $('.loderholder').show();
            if(featureId > 0)
            {
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: '/rbac/updatemenu',
                    type: 'POST',
                    data: {'featureId' : featureId, 'status': $('.'+featureId).prop('checked')},
                    dataType: 'JSON',
                    success: function (data) {
                        if(data)
                        {
                            ajaxCall();
                        }
                    },
                    error: function (response) {

                    }
                });
            }
            $('.loderholder').hide();
        }
        function updateIsActive(featureId)
        {
            $('.loderholder').show();
            if(featureId > 0)
            {
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: '/rbac/updateactive',
                    type: 'POST',
                    data: {'featureId' : featureId, 'status': $('.'+featureId+'_is_active').prop('checked')},
                    dataType: 'JSON',
                    success: function (data) {
                        if(data)
                        {
                            ajaxCall();
                        }
                    },
                    error: function (response) {

                    }
                });
            }
            $('.loderholder').hide();
        }
        
        /*Helper Code*/
        function makePopupAjax($el) {
            var $form = $el.find('form');
            $form.validate();
        }

        function makePopupEditAjax($el, primaryKey) {
            $el.on('shown.bs.modal', function (e) {
                var url = $(e.relatedTarget).data('href'),
                        $this = $(this),
                        $form = $this.find('form'),
                        key = primaryKey || 'id';
                $.get(url, function (data) {
                    $.each(data, function (i, v) {
                        if (i == key) {
                            $form.attr('action', function () { 
                                return $(this).data('url') + v;
                            });
                        }
                        if(i == "role_feature_id[]"){
                            $("#role_feature_id").append(v).trigger('chosen:updated');
                        }
                        var el = $form.find('[name="' + i + '"]');
                        if (el.length && el[0].type.toLowerCase() == 'checkbox') {
                            if(v)
                            {
                                el.prop('checked', true);
                                el.filter('[value=' + v + ']').prop('checked', true);
                            }else{
                                el.prop('checked', false);
                                el.filter('[value=' + v + ']').prop('checked', true);
                            }                            
                            return;

                        }
                        
                        if(i=='parent_id'){
                            $('#edit_parent_id').select2().select2('val',v);
                        }else{
                        el.val(v);
                        }
                    });
                });
                $form.validate();
            });
        }

        function ajaxCallPopup($form) {
            $.post($form.attr('action'), $form.serialize(), function (data) {
                if (data.status === true) {
                    $form.closest('.modal').modal('hide'); 
                    $form[0].reset();
                    if ($('.jqxgrid').lenth && $.fn.jqxGrid)
                        $('.jqxgrid').jqxGrid('refresh');

                    // add new Parent and to the existing parents list
                    $('[id="add_parent_id"]').append("<option value='"+ data.new_parent_id +"' >--"+ data.new_parent_value +"</option>");
                    $('[id="edit_parent_id"]').append("<option value='"+ data.new_parent_id +"' >--"+ data.new_parent_value +"</option>");

                    alert('' + data.message);
                    postData(data);
                } else {
                    alert('' + data.message);
                }
            });
        }

        function postData(data)
        {
            console.log('we are in helper.js');
            ajaxCall();
//            location.reload();
        }

        $(function () {
            $.validator.setDefaults({
                onfocusout: function (element) {
                    $(element).valid();
                },
                submitHandler: function (form) {
                    var $form = $(form);
                    ajaxCallPopup($form);
                },
                errorPlacement: function (error, element) {
                    element.closest('.form-group').append(error);
                },
                unhighlight: function (element, errorClass, validClass) {
                    if ($(element).hasClass('optional') && $(element).val() == '') {
                        $(element).removeClass('error valid');
                    } else {
                        $(element).removeClass('error').addClass('valid');
                    }
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.2/chosen.jquery.min.js"></script> 
<style type="text/css">.loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%;    }
    .error{color: red;}
</style>

<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
    @stop
