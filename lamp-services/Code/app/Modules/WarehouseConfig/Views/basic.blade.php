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
	                <div class="caption">{{trans('warehouse.warehouse_heads.caption')}}</div>                
	                <div class="actions">
	                	@if(isset($AddWarehousePermission) and $AddWarehousePermission)
		                	<a class="btn green-meadow" id="addNewWarehouse" href="#addWarehouseConfig" data-toggle="modal">
			                    <i class="fa fa-plus-circle"></i>
			                    <span style="font-size:11px;"> {{trans('warehouse.warehouse_heads.add_warehouse')}} </span>
		                    </a>
		                @endif    
	                </div>
	            </div>
	            <div class="portlet-body">
	            	<div role="alert" id="alertStatus"></div>
	                <div class="row">
	                    <div class="col-md-12">                        
	                        <div class="table-responsive">
	                            <table id="warehouseListGrid"></table>
	                        </div>                        
	                    </div>
	                </div>
	                <!-- Edit Modal -->
					<div class="modal fade" id="editWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="editWarehouseModalLabel" aria-hidden="true">
					    <div class="modal-dialog" role="document">
					        <div class="modal-content">
					            <div class="modal-header">
					                <h4 class="modal-title" id="editWarehouseModalLabel">{{trans('warehouse.warehouse_heads.edit_warehouse')}}</h4>
					                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
					                   <span aria-hidden="true">&times;</span>
					                </button>
					            </div>
					            <div class="modal-body">
					            	<div class="alert" role="alert" id="modalAlert"></div>
					                <form id="editWarehouseForm">
					                	<input type="hidden" name="_token" value="{{csrf_token()}}">
					                	<input type="hidden" name="edit_pjp_pincode_area_id" id="edit_pjp_pincode_area_id">
					                    <div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="pjp_name">{{trans('warehouse.side_heads.pjp_name')}}<span class="required" aria-required="true">*</span></label>
								                    <input type="text" class="form-control" id="edit_pjp_name" name="edit_pjp_name">
							                    </div>
					                		</div>
						                    <div class="col-lg-6">
						                		<div class="form-group">
							                        <label for="days">{{trans('warehouse.side_heads.days')}}</label>
							                        <input type="text" class="form-control" id="edit_days" name="edit_days">
							                    </div>
						                    </div>
					                    </div>
					                    <div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="le_wh_id">{{trans('warehouse.side_heads.le_wh_id')}}<span class="required" aria-required="true">*</span></label>
							                        <select class="form-control select2me" id="edit_le_wh_id" name="edit_le_wh_id" style="margin-top: 6px" placeholder="{{trans('warehouse.side_heads.le_wh_id')}}" onChange="loadRmId('edit')">
									                    <option value = "">--Please Select--</option>
	                                                    @foreach($warehouseInfo as $display)
	                        							  <option value = "{{$display['le_wh_id']}}">{{$display['display_name']}}</option>
	                    								@endforeach
                    							    </select>
							                    </div>
					                		</div>
					                		<div class="col-lg-6">
						                		<div class="form-group">
							                        <label for="pincode">{{trans('warehouse.side_heads.pincode')}}
							                        <span class="required" aria-required="true">*</span></label>
							                        <input type="text" class="form-control" id="edit_pincode" name="edit_pincode">
							                    </div>
						                    </div>
					                    </div>
					                    <div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="spoke_id">{{trans('warehouse.side_heads.spoke_id')}}<span class="required" aria-required="true">*</span></label>
							                        <select class="form-control select2me" id="edit_spoke_id" name="edit_spoke_id" placeholder="{{trans('warehouse.side_heads.spoke_id')}}" >
							                        <option value = "">--Please Select--</option>
                                                   <!--  @foreach($spokeInfo as $sp)
                                                       <option value = "{{$sp['spoke_id']}}">{{$sp['spoke_name']}}</option>
                    								@endforeach -->
							                        </select>	
							                    </div>
					                		</div>
					                    </div>
					                    <div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="rm_id">{{trans('warehouse.side_heads.rm_id')}}<span class="required" aria-required="true">*</span></label>
							                        <select class="form-control select2me" id="edit_rm_id" name="edit_rm_id" style="margin-top: 6px" placeholder="{{trans('warehouse.side_heads.rm_id')}}">
								                    <option value = "">--Please Select--</option>
                                                     @foreach($usersInfo as $user)
                        							<option value = "{{$user['user_id']}}">{{$user['firstname']}}</option>
                    								@endforeach
                                                </select>
							                    </div>
					                		</div>
					                    </div>
					            </div>
							            <div class="modal-footer">
							                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('warehouse.warehouse_heads.close')}}</button>
							                <button type="submit" id="saveWarehouseData" class="btn btn-primary">{{trans('warehouse.warehouse_heads.save')}}</button>
							            </div>
				                    </form>
					        </div>
					    </div>
					</div>
	            </div>
	            <!-- Add Modal -->
				<div class="modal fade" id="addNewWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
				    <div class="modal-dialog" role="document">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title" id="addModalLabel">{{trans('warehouse.warehouse_heads.add_warehouse')}}</h4>
				                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
				                  <span aria-hidden="true">&times;</span>
				                </button>
				            </div>
				            <div class="modal-body">
				            	<form id="addNewWarehouseForm">
				                	<input name="_token" type="hidden" value="{{csrf_token()}}">
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="pjp_name">{{trans('warehouse.side_heads.pjp_name')}}<span class="required" aria-required="true">*</span></label>
						                        <input type="text" class="form-control" id="add_pjp_name" name="add_pjp_name" placeholder="{{trans('warehouse.side_heads.pjp_name')}}">
						                    </div>
				                		</div>
					                    <div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="days">{{trans('warehouse.side_heads.days')}}</label>
						                        <input type="text" class="form-control" id="add_days" name="add_days" placeholder="{{trans('warehouse.side_heads.days')}}">
						                    </div>
					                    </div>
				                    </div>
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="le_wh_id">{{trans('warehouse.side_heads.le_wh_id')}}<span class="required" aria-required="true">*</span></label>
						                        <select class="form-control select2me" id="add_le_wh_id" name="add_le_wh_id" style="margin-top: 6px" placeholder="{{trans('warehouse.side_heads.le_wh_id')}}" onChange="loadRmId('add')">
								                    <option value = "">--Please Select--</option>
                                                     @foreach($warehouseInfo as $display)
                        							<option value = "{{$display['le_wh_id']}}">{{$display['display_name']}}</option>
                    								@endforeach
                    						    </select>
						                    </div>
				                		</div>
				                		<div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="pincode">{{trans('warehouse.side_heads.pincode')}}<span class="required" aria-required="true">*</span></label>
						                        <input type="text" class="form-control" id="add_pincode" name="add_pincode" placeholder="{{trans('warehouse.side_heads.pincode')}}">
						                    </div>
					                    </div>
				                	</div>
                                    <div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="spoke_id">{{trans('warehouse.side_heads.spoke_id')}}<span class="required" aria-required="true">*</span></label>
						                        <select class="form-control select2me" id="add_spoke_id" name="add_spoke_id" placeholder="{{trans('warehouse.side_heads.spoke_id')}}">
						                        	<option value = "">--Please Select--</option>
						                        </select>
						                    </div>
				                		</div>
				                	</div>
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="rm_id">{{trans('warehouse.side_heads.rm_id')}}<span class="required" aria-required="true">*</span></label>
						                        <select class="form-control select2me" id="add_rm_id" name="add_rm_id" style="margin-top: 6px" placeholder="{{trans('warehouse.side_heads.rm_id')}}">
								                    <option value = "">--Please Select--</option>
                                                     @foreach($usersInfo as $user)
                        							  <option value = "{{$user['user_id']}}">{{$user['firstname']}}</option>
                    								@endforeach
                                                </select>
						                    </div>
				                		</div>
				                	</div>
				            </div>
						            <div class="modal-footer">
						                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('warehouse.warehouse_heads.close')}}</button>
						                <button type="submit" id="addWarehouseData" class="btn btn-primary">{{trans('warehouse.warehouse_heads.add')}}</button>
						            </div>
			                    </form>
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
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
	.alignRight{
		text-align: right !important;
		padding: 10px 10px 10px 10px;
	}
	.actionsStyle{
		padding-left: 20px;
	}
    .fileinput-filename {
    display: table-column !important;
    }
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    function editWarehouseRecord(id) {
 		$("#editWarehouseModal").modal("show");
 		$('#editWarehouseModal').modal({backdrop:'static', keyboard:false});
 		$.post('/beatconfig/edit/'+id,function(response){
 			if(response.status){
 				$("#edit_pjp_pincode_area_id").val(id);
 				$("#edit_pjp_name").val(response.pjp_name);
	 			$("#edit_days").val(response.days);
	 			$("#edit_pincode").val(response.pincode);
	 			$("#edit_spoke_id").html("");
	 			$("#edit_rm_id").html("");
 				$("#edit_le_wh_id").select2('val',response.le_wh_id);
 				$('#edit_rm_id').append(`<option value=''>Please select</option>`);
 				response['users'].forEach(function(data){
 					$('#edit_rm_id').append('<option value='+data['user_id']+'>'+data['firstname']+'</option>')
 				});
 				$('#edit_spoke_id').append(`<option value=''>Please select</option>`);
 				$("#edit_rm_id").select2('val',response.rm_id);

 				response['spokes'].forEach(function(data){
 					$('#edit_spoke_id').append('<option value='+data['spoke_id']+'>'+data['spoke_name']+'</option>');
 				});
 				$("#edit_spoke_id").select2('val',response.spoke_id);

 			}else{
 				$("#modalAlert").addClass("alert-danger").text("{{trans('warehouse.message.invalid')}}").show();
 			}
 		});
 	}
 	function deleteWarehouseRecord(id) {
 		var decision = confirm("Are you sure. Do you want to Delete it!");
 		if(decision){
 			$.post('/beatconfig/delete/'+id,function(response){
 				if(response.status){
 					$("#alertStatus").attr("class","alert alert-info").text("{{trans('warehouse.message.success_deleted')}}").show().delay(3000).fadeOut(350);
 					$('#warehouseListGrid').igGrid("dataBind");
 				}
 				else
	 				$("#alertStatus").attr("class","alert alert-danger").text("{{trans('warehouse.message.failed_deleted')}}").show().delay(3000).fadeOut(350);
 			});
 		}
 	}
 	$(document).ready(function () {
		$(function () {
    		warehouseListGrid();
    		$('#warehouseListGrid_dd_le_wh_id').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
    		$('#warehouseListGrid_dd_rm_id').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
		});

		$('#addNewWarehouseModal').on('hide.bs.modal', function () {
            $("#addNewWarehouseForm").bootstrapValidator('resetForm', true);
            $("#add_rm_id").select2("val", "");
            $("#add_le_wh_id").select2("val", "");
            $("#add_spoke_id").select2("val", "");
        });

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	      });

		$("#addNewWarehouse").click(function(){
			$("#addNewWarehouseModal").modal("show");
			$("#addNewWarehouseModal").modal({backdrop:'static', keyboard:false});
		});
		// Hiding the Alert on Page Load$("#edit_spoke_id").select2
		$("#modalAlert").hide();
		$("#alertStatus").hide();

		$("#modalClose").click(function(){
			$("#modalAlert").hide();
			$('#modalAlert').data('bs.modal',null); // this clears the BS modal data
			$("#edit_pjp_name").attr('value','');
			$("#edit_days").attr('value','');
			$("#edit_pincode").attr('value','');
            $("#edit_rm_id").select2('val','');
			$("#edit_le_wh_id").select2('val','');
			$("#edit_spoke_id").select2('val','');
		});
		
    //warehouseListGrid();
		function warehouseListGrid()
		{
	        $('#warehouseListGrid').igGrid({
			    dataSource: '/beatconfig/list',
				responseDataKey: 'Records',
			    height:'100%',
			    columns: [
			        {headerText: "{{trans('warehouse.side_heads.le_wh_id')}}", key: 'le_wh_id', dataType: "string", width: '10%'},
			        {headerText: "{{trans('warehouse.side_heads.spoke_id')}}", key: 'spoke_id', dataType: "string", width: '10%'},
					{headerText: "{{trans('warehouse.side_heads.rm_id')}}", key: 'rm_id', dataType: "string", width: '10%'},
					{headerText: "{{trans('warehouse.side_heads.pjp_name')}}", key: "pjp_name", dataType: "string", columnCssClass:"alignLeft",headerCssClass:"alignLeft", width: '15%'},
					{headerText: "{{trans('warehouse.side_heads.days')}}", key: 'days', dataType: "string", columnCssClass:"aligncentre",headerCssClass:"alignLeft", width: '15%'},
					{headerText: "{{trans('warehouse.side_heads.pincode')}}", key: "default_pincode", dataType: "string", width: '15%'},
					{headerText: "Retailers", key: 'total_outlets', dataType: "number", width: '5%'},
					{headerText: "{{trans('warehouse.side_heads.actions')}}", key: 'actions', dataType: "string", width: '8%'}
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
			        }
			    ]
			});	
	    }  
		$('#addNewWarehouseForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                add_pjp_name: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.pjp_name')}}"
                        },
                    }
                },
                add_days: {
                    validators: {
                    }
                },
                add_pincode: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.pincode')}}"
                        },
                        regexp: {
                            regexp: '^[1-9][0-9]{5}$',
                            message: "{{trans('warehouse.validation_errors.valid_pincode')}}"
                        },
                    }
                },
                add_rm_id: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.rm_id')}}"
                        },
                    }
                },
                add_le_wh_id: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.le_wh_id')}}"
                        },
                    }
                },
                add_spoke_id: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.spoke_id')}}"
                        },
                    }
                }
            }
        })
		.on('success.form.bv', function(event) {
        	event.preventDefault();
			var newWarehouseData = {
				pjp_name: $("#add_pjp_name").val(),
				days: $("#add_days").val(),
				pincode: $("#add_pincode").val(),
				rm_id: $("#add_rm_id").val(),
				le_wh_id: $("#add_le_wh_id").val(),
				spoke_id: $("#add_spoke_id").val(),
			};
			var token=$("#_token").val();
			$.post('/beatconfig/add',newWarehouseData,function(response){
				$("#addNewWarehouseModal").modal("hide");
				if(response.status){
					$("#addNewWarehouseForm").bootstrapValidator('resetForm', true);
                    $("#alertStatus").attr("class","alert alert-success").text("{{trans('warehouse.message.success_new')}}").show().delay(3000).fadeOut(350);
					$('#warehouseListGrid').igGrid("dataBind");
				}
				else{
					$("#addNewWarehouseForm").bootstrapValidator('resetForm', true);
					$("#alertStatus").attr("class","alert alert-danger").text("{{trans('warehouse.message.failed_updated')}}").show().delay(3000).fadeOut(350);
				}
			});        
        });

	    $('#editWarehouseForm')
	    .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                edit_pjp_name: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.pjp_name')}}"
                        },
	                 }
                },
                edit_days: {
                    validators: {
	                }
                },
                edit_pincode: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.pincode')}}"
                        },
                        regexp: {
                            regexp: '^[1-9][0-9]{5}$',
                            message: "{{trans('warehouse.validation_errors.valid_pincode')}}"
                        },
	                 }
                },
                edit_rm_id: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.rm_id')}}"
                        },
	                }
                },
                edit_le_wh_id: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.le_wh_id')}}"
                        },
	                }
                },
                edit_spoke_id: {
                    validators: {
                    	notEmpty: {
                            message: "{{trans('warehouse.validation_errors.spoke_id')}}"
                        },
	                }
                }
            }
        })
         .on('success.form.bv', function(event) {
        	event.preventDefault();
			var newWarehouseData = {
				pjp_pincode_area_id: $("#edit_pjp_pincode_area_id").val(),
				pjp_name: $("#edit_pjp_name").val(),
				days: $("#edit_days").val(),
				pincode: $("#edit_pincode").val(),
				rm_id: $("#edit_rm_id").val(),
				le_wh_id: $("#edit_le_wh_id").val(),
				spoke_id: $("#edit_spoke_id").val(),
			};
			$.post('/beatconfig/update',newWarehouseData,function(response){
				$("#editWarehouseModal").modal("hide");
				if(response.status){
					$("#alertStatus").attr("class","alert alert-success").text("{{trans('warehouse.message.success_updated')}}").show().delay(3000).fadeOut(350);
					$('#warehouseListGrid').igGrid("dataBind");
				}
				else
					$("#alertStatus").attr("class","alert alert-danger").text("{{trans('warehouse.message.failed_updated')}}").show().delay(3000).fadeOut(350);
			});            
        });
	});
    $('#add_le_wh_id').change(function(){
	    let token  = $("#csrf-token").val();
	    $('#add_spoke_id').html('');
	    $('#loaddata').show();
	    $.ajax({
	        type:"GET",
	        headers: {'X-CSRF-TOKEN':token},
	        url:"/beatconfig/display/"+$('#add_le_wh_id').val(),
	        success: function(result){
	            $('#add_spoke_id').html('');
	            if(result.status){
	            	$('#add_spoke_id').append(`<option value=''>Please select</option>`);
	                result['data'].forEach(function(data){
	                    $('#add_spoke_id').append(`<option value=${data['spoke_id']}>${data['spoke_name']}</option>`);
	                });
	                $('#loaddata').hide();
	              
	            }else{
	                $('#loaddata').hide();
	            }
	        }
        });
    });
    $('#edit_le_wh_id').change(function(){
	    let token  = $("#csrf-token").val();
	    $('#edit_spoke_id').html('');
	    $("#edit_spoke_id").select2('val','');
	    //$("#edit_pjp_name").attr('value','');


	    $('#loaddata').show();
	    $.ajax({
	        type:"GET",
	        headers: {'X-CSRF-TOKEN':token},
	        url:"/beatconfig/display/"+$('#edit_le_wh_id').val(),
	        success: function(result){
	        	$('#edit_spoke_id').html('');
	            if(result.status){
	            	$('#edit_spoke_id').append(`<option value=''>Please select</option>`);
	                result['data'].forEach(function(data){
	                    $('#edit_spoke_id').append(`<option value=${data['spoke_id']}>${data['spoke_name']}</option>`);
	                });
	                $('#loaddata').hide();
	            }else{
	                $('#loaddata').hide();
	            }
	        }
        });
    });
    function loadRmId(type)
	{
        let token  = $("#csrf-token").val();
	    if(type=="add")
	    var le_wh_id=$('#add_le_wh_id').val();
		else if(type=="edit")
	    var le_wh_id=$('#edit_le_wh_id').val();
	    $.ajax({
	        type:"GET",
	        headers: {'X-CSRF-TOKEN':token},
	        url:"/beatconfig/access/"+le_wh_id,
	        success: function(result){
	        	if(type=="add"){
	        		$("#add_rm_id").html("");
	        		$('#add_rm_id').append(`<option value=''>Please select</option>`);
		            result['users'].forEach(function(data){
	 					$('#add_rm_id').append('<option value='+data['user_id']+'>'+data['firstname']+'</option>') 
		            });
		        }else if(type=="edit"){
		        	$("#edit_rm_id").html("");
		        	$('#edit_rm_id').append(`<option value=''>Please select</option>`);
		        	result['users'].forEach(function(data){
	 					$('#edit_rm_id').append('<option value='+data['user_id']+'>'+data['firstname']+'</option>') 
		            });
		        }
	            $('#loaddata').hide();
	        }
        });
    }

</script>
@stop
@extends('layouts.footer')