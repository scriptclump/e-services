@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">{{trans('statecodes.statecode_heads.caption')}}</div>                
                <div class="actions">
                    @if(isset($addPermission) and $addPermission)
                        <a class="btn green-meadow" id="addNewState" href="#addState" data-toggle="modal">
                            <i class="fa fa-plus-circle"></i>
                            <span style="font-size:11px;"> {{trans('statecodes.statecode_heads.add_state')}} </span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div role="alert" id="alertStatus"></div>
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="stateListGrid"></table>
                        </div>                        
                    </div>
                </div>
                <!--edit modal-->
                <div class="modal fade" id="editStateModal" tabindex="-1" role="dialog" aria-labelledby="editStateModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="editStateModalLabel">{{trans('statecodes.statecode_heads.edit_state')}}</h4>
                                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert" role="alert" id="modalAlert"></div>
                                <form id="editStateForm">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="edit_scc_id" id="edit_scc_id">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="State_name">{{trans('statecodes.statecode_side_heads.State_name')}}</label>
                                                <select class="form-control select2me" id="edit_State_name"
                                                name="edit_State_name" style="margin-top: 6px"
                                                placeholder="{{trans('statecodes.statecode_side_heads.State_name')}}">
                                                <option value ="">--Please Select--</option>
                                                @foreach($statenameInfo as $state)
                                                    <option value = "{{$state['name']}}">{{$state['name']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="City_name">{{trans('statecodes.statecode_side_heads.City_name')}}</label>
                                                <input type="text" class="form-control" id="edit_City_name" name="edit_City_name" placeholder="{{trans('statecodes.statecode_side_heads.City_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="City_code">{{trans('statecodes.statecode_side_heads.City_code')}}</label>
                                                <input type="text" class="form-control" id="edit_City_code" name="edit_City_code" placeholder="{{trans('statecodes.statecode_side_heads.City_code')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="dc_inc_id">{{trans('statecodes.statecode_side_heads.dc_inc_id')}}</label>
                                                <input type="text" class="form-control" id="edit_dc_inc_id" name="edit_dc_inc_id" placeholder="{{trans('statecodes.statecode_side_heads.dc_inc_id')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="fc_inc_id">{{trans('statecodes.statecode_side_heads.fc_inc_id')}}</label>
                                                <input type="text" class="form-control" id="edit_fc_inc_id" name="edit_fc_inc_id" placeholder="{{trans('statecodes.statecode_side_heads.fc_inc_id')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="edit_latitude">Latitude</label>
                                                <input type="text" class="form-control" id="edit_latitude" name="edit_latitude" placeholder="Latitude">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="edit_longitude">Longitude</label>
                                                <input type="text" class="form-control" id="edit_longitude" name="edit_longitude" placeholder="Longitude">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-check">
                                                <label class="form-check-label"><br>
                                                <input type="checkbox"  checked="checked" id="edit_is_active" name="edit_is_active" class="form-check-input">
                                                {{trans('statecodes.statecode_side_heads.is_active')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('statecodes.statecode_heads.close')}}</button>
                                    <button type="submit" id="saveStateData" class="btn btn-primary">{{trans('statecodes.statecode_heads.save')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Modal -->
                <div class="modal fade" id="addNewStateModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="addModalLabel">{{trans('statecodes.statecode_heads.add_state')}}</h4>
                                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addNewStateForm">
                                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="State_name">{{trans('statecodes.statecode_side_heads.State_name')}}</label>
                                                <select class="form-control select2me" id="add_State_name"
                                                name="add_State_name" style="margin-top: 6px"
                                                placeholder="{{trans('statecodes.statecode_side_heads.State_name')}}">
                                                <option value ="">--Please Select--</option>
                                                @foreach($statenameInfo as $state)
                                                    <option value = "{{$state['name']}}">{{$state['name']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="State_code">{{trans('statecodes.statecode_side_heads.State_code')}}</label>
                                                <input type="text" class="form-control" id="add_State_code" name="add_State_code" placeholder="{{trans('statecodes.statecode_side_heads.State_code')}}">
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="City_name">{{trans('statecodes.statecode_side_heads.City_name')}}</label>
                                                <input type="text" class="form-control" id="add_City_name" name="add_City_name" placeholder="{{trans('statecodes.statecode_side_heads.City_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="City_code">{{trans('statecodes.statecode_side_heads.City_code')}}</label>
                                                <input type="text" class="form-control" id="add_City_code" name="add_City_code" placeholder="{{trans('statecodes.statecode_side_heads.City_code')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="dc_inc_id">{{trans('statecodes.statecode_side_heads.dc_inc_id')}}</label>
                                                <input type="text" class="form-control" id="add_dc_inc_id" name="add_dc_inc_id" placeholder="{{trans('statecodes.statecode_side_heads.dc_inc_id')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="fc_inc_id">{{trans('statecodes.statecode_side_heads.fc_inc_id')}}</label>
                                                <input type="text" class="form-control" id="add_fc_inc_id" name="add_fc_inc_id" placeholder="{{trans('statecodes.statecode_side_heads.fc_inc_id')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="add_latitude">Latitude</label>
                                                <input type="text" class="form-control" id="add_latitude" name="add_latitude" placeholder="Latitude">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="add_longitude">Longitude</label>
                                                <input type="text" class="form-control" id="add_longitude" name="add_longitude" placeholder="Longitude">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-check">
                                                
                                                <label class="form-check-label"><br>
                                                <input type="checkbox"  checked="checked" id="add_is_active" name="add_is_active" class="form-check-input">
                                                {{trans('statecodes.statecode_side_heads.is_active')}}
                                             </label>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('statecodes.statecode_heads.close')}}</button>
                                        <button type="submit" id="addStateData" class="btn btn-primary">{{trans('statecodes.statecode_heads.add')}}</button>
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
</div>
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .alignRight{
        text-align: right !important;
        padding: 10px 10px 10px 10px;
    }
    .actionsStyle{
        padding-left: 20px;
    }
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    function editStateRecord(id){
        $("#editStateModal").modal("show");
        $('#editStateModal').modal({backdrop:'static', keyboard:false});
        $.post('/legalentities/edit/'+id,function(response){
            if(response.status){
                $("#edit_scc_id").val(id);
                $("#edit_State_name").select2('val',response.state_name);
                $("#edit_City_name").val(response.city_name);
                $("#edit_City_code").val(response.city_code);
                $("#edit_dc_inc_id").val(response.dc_inc_id);
                $("#edit_fc_inc_id").val(response.fc_inc_id);
                $("#edit_latitude").val(response.latitude);
                $("#edit_longitude").val(response.longitude);
                $("#edit_is_active").prop('checked',(response.is_active) ? true : false);
            }
            else{
                $("#modalAlert").addClass("alert-danger").text("{{trans('statecodes.message.invalid')}}").show();
            }
        });
    }
    function deleteStateRecord(id) {
        var decision = confirm("Are you sure. Do you want to Delete it!");
        if(decision){
            $.post('/legalentities/delete/'+id,function(response){
                if(response.status){
                    $("#alertStatus").attr("class","alert alert-info").text("{{trans('statecodes.message.success_deleted')}}").show().delay(3000).fadeOut(350);
                    $('#stateListGrid').igGrid("dataBind");
                }
                else{
                    $("#alertStatus").attr("class","alert alert-danger").text("{{trans('statecodes.message.failed_deleted')}}").show().delay(3000).fadeOut(350);
                }
            });
        }
    }
    $(document).ready(function () 
    {
        $(function () {
            stateListGrid();
        });

        $('#addNewStateModal').on('hide.bs.modal', function () {
            $("#addNewStateForm").bootstrapValidator('resetForm', true);
            $("#add_is_active").prop('checked',true);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });

        $("#addNewState").click(function(){
            $("#addNewStateModal").modal("show");
            $("#addNewStateModal").modal({backdrop:'static', keyboard:false});
        });
        // Hiding the Alert on Page Load
        $("#modalAlert").hide();
        $("#alertStatus").hide();

        $("#modalClose").click(function(){
            $("#modalAlert").hide();
            $('#modalAlert').data('bs.modal',null); // this clears the BS modal data
            $("#edit_State_name").select2('val','');
            //$("#edit_State_code").attr('value','');
            $("#edit_City_name").attr('value','');
            $("#edit_City_code").attr('value','');
            $("#edit_dc_inc_id").attr('value','');
            $("#edit_fc_inc_id").attr('value','');
            $("#edit_latitude").attr('value','');
            $("#edit_longitude").attr('value','');
            $("#edit_is_active").val('');
        });
        function stateListGrid()
        {   
            $('#stateListGrid').igGrid({
                dataSource: '/legalentities/list',
                responseDataKey: 'Records',
                height:'100%',
                columns: [
                    {headerText: "{{trans('statecodes.statecode_side_heads.State_name')}}", key: "state_name", dataType: "string",  width: '12%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.State_code')}}", key: "state_code", dataType: "number",  width: '8%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.City_name')}}", key: "city_name", dataType: "string", width: '10%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.City_code')}}", key: "city_code", dataType: "string", width: '10%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.dc_inc_id')}}", key: "dc_inc_id",dataType: "number", width: '10%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.fc_inc_id')}}", key: "fc_inc_id", dataType: "number", width: '10%'},
                    {headerText: "Latitude", key: "latitude", dataType: "string", width: '10%'},
                    {headerText: "Longitude", key: "longitude", dataType: "string", width: '10%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.status')}}", key:"is_active", dataType: "string", width: '10%'},
                    {headerText: "{{trans('statecodes.statecode_side_heads.actions')}}", key:"actions", dataType: "string", width: '9%'}
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
        }
        // To Add New Record
        $('#addNewStateForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                add_State_name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('statecodes.validation_errors.State_name')}}"
                        }
                    }
                },
                add_City_name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('statecodes.validation_errors.City_name')}}"
                        },
                        remote: {
                            url: '/legalentities/validatecityname',
                            type: 'POST',
                            data: function (validator, $field, value) {
                                return  {
                                    edit_City_name: value
                                };
                            },
                            delay: 1000, // Send Ajax request every 1 seconds
                            message: "{{trans('statecodes.validation_errors.City_name_exist')}}"
                        }
                    }
                    
                },
                add_State_code: {
                    validators: {
                        regexp: {
                            regexp: '^[0-9]*$',
                            message: "{{trans('statecodes.validation_errors.State_code_isdigit')}}"
                        }
                    }
                },
                add_City_code: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('statecodes.validation_errors.City_code')}}"
                        },

                    }
                },
                add_dc_inc_id: {
                    validators: {
                        regexp: {
                            regexp: '^[0-9]*$',
                            message: "{{trans('statecodes.validation_errors.dc_inc_id_isdigit')}}"
                        }
                    }
                },
                add_fc_inc_id: {
                    validators: {
                       
                        regexp: {
                            regexp: '^[0-9]*$',
                            message: "{{trans('statecodes.validation_errors.fc_inc_id_isdigit')}}"
                        }
                    }
                },
                add_latitude: {
                    validators: {
                        regexp: {
                            regexp: '^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$',
                            message: "Please enter valid latitude!"
                        }
                    }
                },
                add_longitude: {
                    validators: {
                       
                        regexp: {
                            regexp: '^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$',
                            message: "Please enter valid longitude!"
                        }
                    }
                },
            }
        })
        .on('success.form.bv', function(event) {
            event.preventDefault();
            var newStateData = {
                state_name: $("#add_State_name").val(),
                //state_code: $("#add_State_code").val(),
                city_name: $("#add_City_name").val(),
                city_code: $("#add_City_code").val(),
                dc_inc_id: $("#add_dc_inc_id").val(),
                fc_inc_id: $("#add_fc_inc_id").val(),
                latitude: $("#add_latitude").val(),
                longitude: $("#add_longitude").val(),
                is_active: $("#add_is_active").prop('checked'),
            };
            var token=$("#_token").val();
            $.post('/legalentities/add',newStateData,function(response){
                $("#addNewStateModal").modal("hide");
                if(response.status){

                    $("#alertStatus").attr("class","alert alert-success").text("{{trans('statecodes.message.success_new')}}").show().delay(3000).fadeOut(350);
                    $('#stateListGrid').igGrid("dataBind");
                }
                else
                    $("#alertStatus").attr("class","alert alert-danger").text("{{trans('statecodes.message.failed_updated')}}").show().delay(3000).fadeOut(350);
            });            
        });
        $('#addNewState').click(function(){
           // When the modal is about to close, then the content will be reset...
            $("#add_State_name").select2("val",null);
        });
        // To Update the Editted Content
        //console.log('valid',)
        $('#editStateForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                edit_State_name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('statecodes.validation_errors.State_name')}}"
                        }
                    }
                },
                edit_City_name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('statecodes.validation_errors.City_name')}}"
                        },
                        remote: {
                            url: '/legalentities/validatecityname',
                            type: 'POST',
                            data: function (validator, $field, value) {
                                return  {
                                    edit_City_name: value,
                                    scc_id: $("#edit_scc_id").val()
                                };
                            },
                            delay: 1000, // Send Ajax request every 1 seconds
                            message: "{{trans('statecodes.validation_errors.City_name_exist')}}"
                        }
                    }
                },
                // edit_State_code: {
                //     validators: {
                //         regexp: {
                //             regexp: '^[0-9]*$',
                //             message: "{{trans('statecodes.validation_errors.State_code_isdigit')}}"
                //         }
                //     }
                // },
                edit_City_code: {
                    validators: {

                    }
                },
                edit_dc_inc_id: {
                    validators: {
                        regexp: {
                            regexp: '^[0-9]*$',
                            message: "{{trans('statecodes.validation_errors.dc_inc_id_isdigit')}}"
                        }
                    }
                },
                edit_fc_inc_id: {
                    validators: {
                        regexp: {
                            regexp: '^[0-9]*$',
                            message: "{{trans('statecodes.validation_errors.fc_inc_id_isdigit')}}"
                        }
                    }
                },
                edit_latitude: {
                    validators: {
                        regexp: {
                            regexp: '^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$',
                            message: "Please enter valid latitude!"
                        }
                    }
                },
                edit_longitude: {
                    validators: {
                        regexp: {
                            regexp: '^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$',
                            message: "Please enter valid longitude!"
                        }
                    }
                },
                edit_is_active: {
                    validators: {

                    }
                },
            }
        })
        .on('success.form.bv', function(event){
            event.preventDefault();
            var newStateData = {
                state_name: $("#edit_State_name").val(),
                //state_code: $("#edit_State_code").val(),
                city_name: $("#edit_City_name").val(),
                city_code: $("#edit_City_code").val(),
                dc_inc_id: $("#edit_dc_inc_id").val(),
                fc_inc_id: $("#edit_fc_inc_id").val(),
                latitude: $("#edit_latitude").val(),
                longitude: $("#edit_longitude").val(),
                is_active: $("#edit_is_active").prop('checked'),
                scc_id: $('#edit_scc_id').val()
            };
            $.post('/legalentities/update',newStateData,function(response){
                $("#editStateModal").modal("hide");
                console.log('response',response);
                if(response.status){
                    $("#alertStatus").attr("class","alert alert-success").text("{{trans('statecodes.message.success_updated')}}").show().delay(3000).fadeOut(350);
                    $('#stateListGrid').igGrid("dataBind");
                }
                else
                    $("#alertStatus").attr("class","alert alert-danger").text("{{trans('statecodes.message.failed_updated')}}").show().delay(3000).fadeOut(350);
            });            
        });
    });
</script>
@stop
@extends('layouts.footer')
