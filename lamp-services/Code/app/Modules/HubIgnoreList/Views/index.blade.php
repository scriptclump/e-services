@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="alert alert-info hide" id="errorMessage">
    <button type="button" class="close" aria-hidden="true">&times;</button>
    <div class="flash_message_class" id="flash_message"></div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
            <input id="token_value" type="hidden" name="_token" value="{{Session::token()}}">
                <div class="caption"> {{trans('hubignorelist.hubignorelist_index.ignorelist_caption')}}</div>                
                <div class="actions">
                	@if(isset($addPermission) and $addPermission)
	                	<a class="btn green-meadow" id="addHubIgnoreListButton" href="#addHubIgnoreList" data-toggle="modal" data-backdrop="static">
		                    <i class="fa fa-plus-circle"></i>
		                    <span style="font-size:11px;"> {{trans('hubignorelist.hubignorelist_index.ignorelist_add')}} </span>
	                    </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="hubIgnoreListGrid"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="addHubIgnoreList" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" id="closeModal" aria-hidden="true">�</button>
                <h4 class="modal-title" id="basicvalCode"> 
                    {{trans('hubignorelist.hubignorelist_index.ignorelist_add')}}
                </h4>
            </div>
            <div class="modal-body">
                <form id="addNewHubIgnoreListForm" class="text-center" method="post">
                    <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                    <div class="row">
                        <div class="col-md-6" align="Left">
                             {{trans('hubignorelist.hubignorelist_form_fields.ref_type')}}: <br/>
                            <select class="form-control select2me" id="ignoreRefType" name="ignoreRefType">
                                <option></option>
                                <option value="manufacturer">Manufacturer</option>
                                <option value="brand">Brand</option>
                                <!-- <option value="product">Product</option> -->
                            </select>
                        </div>
                        <div class="col-md-6" align="Left" id="manufacturerList">
                            @if(isset($manufacturerInfo))
                            {{trans('hubignorelist.hubignorelist_form_fields.manufacturer')}}<small>(s)</small>: <br/>
                            <select class="form-control select2me" id="manufacturer_id" name="manufacturer_id[]" multiple="multiple">
                                <option></option>
                                    @foreach($manufacturerInfo as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                            @endif
                        </div>
                        <div class="col-md-6" align="Left" id="brandList">
                            @if(isset($brandsInfo))
                            {{trans('hubignorelist.hubignorelist_form_fields.brand')}}<small>(s)</small>: <br/>
                            <select class="form-control select2me"  multiple="multiple" id="brand_id" name="brand_id[]">
                                <option></option>
                                    @foreach($brandsInfo as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" align="Left">
                            {{trans('hubignorelist.hubignorelist_form_fields.scope_type')}}: <br/>
                            <select class="form-control select2me" id="ignoreScopeType" name="ignoreScopeType">
                                <option></option>
                                <option value="dc">Dc</option>
                                <option value="hub">Hub</option>
                                <option value="spoke">Spoke</option>
                                <option value="beat">Beat</option>
                            </select>
                        </div>
                        <div class="col-md-6" align="Left" id="dcList">
                            @if(isset($dcInfo))
                            {{trans('hubignorelist.hubignorelist_form_fields.dc')}}<small>(s)</small>:<br/>
                            <select class="form-control select2me" id="dc_id" name="dc_id[]" multiple="multiple">
                                <option></option>
                                    @foreach($dcInfo as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                            @endif
                        </div>
                        <div class="col-md-6" align="Left" id="hubList">
                            @if(isset($hubInfo))
                            {{trans('hubignorelist.hubignorelist_form_fields.hub')}}<small>(s)</small>: <br/>
                                <select class="form-control select2me" id="hub_id" name="hub_id[]" multiple="multiple">
                                <option></option>
                                    @foreach($hubInfo as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                            @endif
                        </div>
                        <div class="col-md-6" align="Left" id="beatList">
                            @if(isset($beatInfo))
                            {{trans('hubignorelist.hubignorelist_form_fields.beat')}}<small>(s)</small>: <br/>
                            <select class="form-control select2me" id="beat_id" name="beat_id[]" multiple="multiple">
                                <option></option>
                                    @foreach($beatInfo as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                            @endif
                        </div>
                        <div class="col-md-6" align="Left" id="spokeList">
                            @if(isset($spokeInfo))
                            {{trans('hubignorelist.hubignorelist_form_fields.spoke')}}<small>(s)</small>: <br/>
                            <select class="form-control select2me" id="spoke_id" name="spoke_id[]" multiple="multiple">
                                <option></option>
                                    @foreach($spokeInfo as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="center">
                            Click: <br/>
                            <input type="submit" class="btn green-meadow" value="{{trans('hubignorelist.hubignorelist_form_fields.submit')}}" id="hubIgnoreListSubmit">
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal-add app version -->
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
{{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function deleteHubIgnoreListById(id)
    {
        var dec = confirm("Are you sure you want to Delete ?");
        if (dec == true)
        {
            $.ajax({
                url: '/ignorelist/deleteHubIgnoreListById/' + id,
                type: 'GET',
                success: function (result)
                {
                    if (result.status == 1) {
                        document.getElementsByClassName("flash_message_class")[0].innerHTML = "Successfully Deleted!";
                        document.getElementById("errorMessage").className = document.getElementById("errorMessage").className.replace(/hide/,'');
// document.getElementById("").className = 
                        $("#hubIgnoreListGrid").igGrid("dataBind");      // For Grid Refresh;
                    } else {
                        document.getElementsByClassName("flash_message_class")[0].innerHTML = result;
                    }
                },
                error: function (err) {
                    console.log('Error: ' + err);
                }
            });
        }
    }
    $(document).ready(function() {

        $("#hubIgnoreListGrid").igGrid({
            responseDataKey: "Records",
            dataSource: "/ignorelist/viewhubignorelist",
            columns: [
                { headerText: "Scope Type", key: "scope_type", dataType: "string" },
                { headerText: "Scope Name", key: "scope_name", dataType: "string" },
                { headerText: "Reference Type", key: "ref_type", dataType: "string" },
                { headerText: "Reference Name", key: "ref_name", dataType: "string" },
                { headerText: "Actions", key: "action", dataType: "string" }
            ],
            features: [
            {
                name: "Filtering",
                type: "local",
                columnSettings: [
                    {columnKey: 'action', allowFiltering: false},
                    ]     
            },
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'action', allowFiltering: false},
                    ]     
            },
            {
                name : 'Paging',
                type: "local",
                pageSize : 10
            }
        ]
        });

        $("#manufacturerList, #brandList, #productList").hide();
        $("#dcList, #hubList, #beatList, #spokeList").hide();

        $('.close').click(function(){
            $('.alert').hide();
        });

        $('#closeModal').click(function(){
            // When the modal is about to close, then the content will be reset...
            $("#ignoreRefType, #ignoreScopeType").select2("val",null);
            $("#manufacturer_id, #brand_id, #product_id").select2("val",null);
            $("#dc_id, #hub_id, #beat_id, #spoke_id").select2("val",null);

            $("#manufacturerList, #brandList, #productList").hide();
            $("#dcList, #hubList, #beatList, #spokeList").hide();
        });

        $('#ignoreRefType').change(function(){
            var ignoreRefType = $('#ignoreRefType option:selected').attr('value');
            $("#manufacturerList, #brandList, #productList").hide();
            $("#manufacturer_id, #brand_id, #product_id").select2("val",null);
            $("#"+ignoreRefType+"List").show();
        });

        $('#ignoreScopeType').change(function(){
            var ignoreScopeType = $('#ignoreScopeType option:selected').attr('value');
            $("#dcList, #hubList, #beatList, #spokeList").hide();
            $("#dc_id, #hub_id, #beat_id, #spoke_id").select2("val",null);
            $("#"+ignoreScopeType+"List").show();
        });

    $('#addNewHubIgnoreListForm')
        .find('[name="ignoreRefType"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'ignoreRefType');
            })
            .end()
        .find('[name="ignoreScopeType"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'ignoreScopeType');
            })
            .end()
        .find('[name$="manufacturer_id"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'manufacturer_id[]');
            })
            .end()
        .find('[name$="brand_id"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'brand_id[]');
            })
            .end()
        .find('[name="dc_id"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'dc_id');
            })
            .end()
        .find('[name="hub_id"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'hub_id');
            })
            .end()
        .find('[name="beat_id"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'beat_id');
            })
            .end()
        .find('[name="spoke_id"]')
            .change(function(e) {
                $('#addNewHubIgnoreListForm').formValidation('revalidateField', 'spoke_id');
            })
            .end()
        .formValidation({
            framework: 'bootstrap',
            excluded: ':hidden',
            fields: {
                ignoreRefType: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.ref_type')}}",
                        }
                    }
                },
                ignoreScopeType: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.scope_type')}}",
                        }
                    }
                },
                'manufacturer_id[]': {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.manufacturer')}}",
                        },
                        callback: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.manufacturer')}}",
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('manufacturer_id[]').val();
                                return (options != null && options.length >= 1);
                            }
                        }   
                    }
                },
                'brand_id[]': {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.brand')}}",
                        },
                        callback: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.brand')}}",
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('brand_id[]').val();
                                return (options != null && options.length >= 1);
                            }
                        }
                    }
                },
                'dc_id[]': {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.dc')}}",
                        },
                        callback: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.dc')}}",
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('dc_id[]').val();
                                return (options != null && options.length >= 1);
                            }
                        }
                    }
                },
                'hub_id[]': {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.hub')}}",
                        },
                        callback: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.hub')}}",
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('hub_id[]').val();
                                return (options != null && options.length >= 1);
                            }
                        }
                    }
                },
                'beat_id[]': {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.beat')}}",
                        },
                        callback: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.beat')}}",
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('beat_id[]').val();
                                return (options != null && options.length >= 1);
                            }
                        }
                    }
                },
                'spoke_id[]': {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.spoke')}}",
                        },
                        callback: {
                            message: "{{trans('hubignorelist.hubignorelist_form_validate.spoke')}}",
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                var options = validator.getFieldElements('spoke_id[]').val();
                                return (options != null && options.length >= 1);
                            }
                        }
                    }
                }
            }
        })
        .on('success.form.fv', function(e) {
            // Prevent form submission
            e.preventDefault();

            var $form = $(e.target);
            $.ajax({
                url: '/ignorelist/addnewhubignorelist',
                type: 'POST',
                data: $form.serialize(),
                success: function(result) {
                    // Below 3 lines are to display the result message
                    $("#flash_message").html(result.message);
                    $('div.alert').removeClass('hide'); 
                    $('div.alert').show(); 

                    $("#hubIgnoreListGrid").igGrid("dataBind");     // Refresh the Grid

                    $("#ignoreRefType, #ignoreScopeType").select2("val",null);
                    $("#manufacturer_id, #brand_id, #product_id").select2("val",null);
                    $("#dc_id, #hub_id, #beat_id, #spoke_id").select2("val",null);

                    $("#manufacturerList, #brandList, #productList").hide();
                    $("#dcList, #hubList, #beatList, #spokeList").hide();

                    $("#addHubIgnoreList").modal('hide');   // Close the Add Modal
                }
            });
        });

    });
</script>
@stop
@extends('layouts.footer')