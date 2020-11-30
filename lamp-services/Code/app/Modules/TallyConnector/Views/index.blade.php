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
	                <div class="caption">{{trans('tally.tally_heads.caption')}}</div>                
	                <div class="actions">
	                	@if(isset($AddTallyPermission) and $AddTallyPermission)
		                	<a class="btn green-meadow" id="addNewTally" href="#addTallyConnector" data-toggle="modal">
			                    <i class="fa fa-plus-circle"></i>
			                    <span style="font-size:11px;"> {{trans('tally.tally_heads.add_tally')}} </span>
		                    </a>
		                @endif
	                </div>
	            </div>
	            <div class="portlet-body">
	            	<div role="alert" id="alertStatus"></div>
	                <div class="row">
	                    <div class="col-md-12">                        
	                        <div class="table-responsive">
	                            <table id="tallyListGrid"></table>
	                        </div>                        
	                    </div>
	                </div>
	                <!-- Edit Modal -->
					<div class="modal fade" id="editTallyModal" tabindex="-1" role="dialog" aria-labelledby="editTallyModalLabel" aria-hidden="true">
					    <div class="modal-dialog" role="document">
					        <div class="modal-content">
					            <div class="modal-header">
					                <h4 class="modal-title" id="editTallyModalLabel">{{trans('tally.tally_heads.edit_tally')}}</h4>
					                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
					                   <span aria-hidden="true">&times;</span>
					                </button>
					            </div>
					            <div class="modal-body">
					            	<div class="alert" role="alert" id="modalAlert"></div>
					                <form id="editTallyForm">
					                	<input type="hidden" name="_token" value="{{csrf_token()}}">
					                	<input type="hidden" name="edit_sync_id" id="edit_sync_id">
					                    <div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="cost_centre">{{trans('tally.side_heads.cost_centre')}}</label>
						                        <input type="text" class="form-control" id="edit_cost_centre" name="edit_cost_centre" placeholder="{{trans('tally.side_heads.cost_centre')}}">
						                        <p id="edit_cost_center_message" style="color:#a94442"></p>
							                    </div>
					                		</div>
						                    <div class="col-lg-6">
						                		<div class="form-group">
							                        <label for="cost_centre_group">{{trans('tally.side_heads.cost_centre_group')}}</label>
							                        <input type="text" class="form-control" id="edit_cost_centre_group" name="edit_cost_centre_group">
							                    </div>
						                    </div>
					                    </div>
					                	<div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="sync_url">{{trans('tally.side_heads.sync_url')}}</label>
							                        <input type="text" class="form-control" id="edit_sync_url" name="edit_sync_url" placeholder="{{trans('tally.side_heads.sync_url')}}">
							                    </div>
					                		</div>
					                    </div>
					                    <div class="row">
					                		<div class="col-lg-2">
												<div class="form-check">
													<label class="form-check-label"><br>
													   <input type="checkbox"  checked="checked" id="edit_is_active" name="edit_is_active" class="form-check-input">
													   {{trans('tally.side_heads.is_active')}}
													</label>
												</div>
					                		</div>
				                        </div>
					            </div>
							            <div class="modal-footer">
							                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('tally.tally_heads.close')}}</button>
							                <button type="submit" id="saveTallyData" class="btn btn-primary">{{trans('tally.tally_heads.save')}}</button>
							            </div>
				                    </form>
					        </div>
					    </div>
					</div>
	            </div>
	            <!-- Add Modal -->
				<div class="modal fade" id="addNewTallyModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
				    <div class="modal-dialog" role="document">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title" id="addModalLabel">{{trans('tally.tally_heads.add_tally')}}</h4>
				                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
				                  <span aria-hidden="true">&times;</span>
				                </button>
				            </div>
				            <div class="modal-body">
				            	<form id="addNewTallyForm">
				                	<input name="_token" type="hidden" value="{{csrf_token()}}">
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="cost_centre">{{trans('tally.side_heads.cost_centre')}}</label>
						                        <input type="text" class="form-control" id="add_cost_centre" name="add_cost_centre" placeholder="{{trans('tally.side_heads.cost_centre')}}">
						                    	<p id="add_cost_center_message" style="color:#a94442"></p>
						                    </div>
				                		</div>
					                    <div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="cost_centre_group">{{trans('tally.side_heads.cost_centre_group')}}</label>
						                        <input type="text" class="form-control" id="add_cost_centre_group" name="add_cost_centre_group" placeholder="{{trans('tally.side_heads.cost_centre_group')}}">
						                    </div>
					                    </div>
				                    </div>
				                	<div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="sync_url">{{trans('tally.side_heads.sync_url')}}</label>
						                        <input type="text" class="form-control" id="add_sync_url" name="add_sync_url" placeholder="{{trans('tally.side_heads.sync_url')}}">
						                    </div>
				                		</div>
				                	</div>
				                	<div class="row">
				                		<div class="col-lg-2">
											<div class="form-check">
												<label class="form-check-label"><br>
												   <input type="checkbox"  checked="checked" id="add_is_active" name="add_is_active" class="form-check-input">
												   {{trans('tally.side_heads.is_active')}}
												</label>
											</div>
				                		</div>
				                    </div>
				            </div>
						            <div class="modal-footer">
						                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('tally.tally_heads.close')}}</button>
						                <button type="submit" id="addTallyData" class="btn btn-primary">{{trans('tally.tally_heads.add')}}</button>
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
<script type="text/javascript">
    function editTallyRecord(id) {
 		$("#editTallyModal").modal("show");
 		$('#editTallyModal').modal({backdrop:'static', keyboard:false});
 		$.post('/tallyconnector/edit/'+id,function(response){
 			if(response.status){
 				$("#edit_sync_id").val(id);
 				$("#edit_cost_centre").val(response.cost_centre);
	 			$("#edit_cost_centre_group").val(response.cost_centre_group);
	 			$("#edit_sync_url").val(response.sync_url);
 				$("#edit_is_active").prop('checked',(response.is_active) ? true : false);
 				$("#edit_cost_centre").css("border-color","#e5e5e5");
 				$("#edit_cost_center_message").hide();
 			}else{
 				$("#modalAlert").addClass("alert-danger").text("{{trans('tally.message.invalid')}}").show();
 			}
 		});
 	}
    
 	function deleteTallyRecord(id) {
 		var decision = confirm("Are you sure. Do you want to Delete it!");
 		if(decision){
 			$.post('/tallyconnector/delete/'+id,function(response){
 				if(response.status){
 					$("#alertStatus").attr("class","alert alert-info").text("{{trans('tally.message.success_deleted')}}").show().delay(3000).fadeOut(350);
 					$('#tallyListGrid').igGrid("dataBind");
 				}
 				else
	 				$("#alertStatus").attr("class","alert alert-danger").text("{{trans('tally.message.failed_deleted')}}").show().delay(3000).fadeOut(350);
 			});
 		}
 	}
 	$(document).ready(function () {
		$(function () {
    		tallyListGrid();
		});

		$('#addNewTallyModal').on('hide.bs.modal', function () {
            $("#addNewTallyForm").bootstrapValidator('resetForm', true);
            $("#add_cost_centre").val("");
            $("#add_is_active").prop('checked',true);
            $("#add_cost_center_message").hide();
           	$('#add_cost_centre').css('border-color','#e5e5e5');
           	$("#addTallyData").attr("disabled",false);
        });

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	      });

		$("#addNewTally").click(function(){
			$("#addNewTallyModal").modal("show");
			$("#addNewTallyModal").modal({backdrop:'static', keyboard:false});
		});
		// Hiding the Alert on Page Load
		$("#modalAlert").hide();
		$("#alertStatus").hide();

		$("#modalClose").click(function(){
			$("#modalAlert").hide();
			$('#modalAlert').data('bs.modal',null); // this clears the BS modal data
			$("#edit_cost_centre").attr('value','');
			$("#edit_cost_centre_group").attr('value','');
			$("#edit_sync_url").attr('value','');
			$("#edit_is_active").val('');
			$("#editTallyForm").bootstrapValidator('resetForm', true);
			$("#saveTallyData").attr("disabled",false);
		});
		
    //tallyListGrid();
		function tallyListGrid()
		{
			message='';
	        $('#tallyListGrid').igGrid({
			    dataSource: '/tallyconnector/list',
				responseDataKey: 'Records',
			    height:'100%',
			    columns: [
					{headerText: "{{trans('tally.side_heads.cost_centre')}}", key: "cost_centre", dataType: "string", columnCssClass:"alignLeft",headerCssClass:"alignLeft", width: '15%'},
					{headerText: "{{trans('tally.side_heads.bu_name')}}", key: "bu_name", dataType: "string", columnCssClass:"alignLeft",headerCssClass:"alignLeft", width: '15%'},
					{headerText: "{{trans('tally.side_heads.cost_centre_group')}}", key: 'cost_centre_group', dataType: "string", columnCssClass:"aligncentre",headerCssClass:"alignLeft", width: '10%'},
					{headerText: "{{trans('tally.side_heads.sync_url')}}", key: 'sync_url', dataType: "string", width: '15%'},
					{headerText: "{{trans('tally.side_heads.status')}}", key: 'is_active', dataType: "string", width: '10%'},
					{headerText: "{{trans('tally.side_heads.actions')}}", key: 'actions', dataType: "string", width: '8%'}
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
		$('#addNewTallyForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                add_cost_centre_group: {
                    validators: {
                       
                    }
                },
                add_cost_centre: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('tally.validation_errors.cost_centre')}}"
                        }
                    }
                },
                add_sync_url: {
                    validators: {
                        
                    }
                }
            }
        })
		.on('success.form.bv', function(event) {
        	event.preventDefault();
			var newTallyData = {
				cost_centre: $("#add_cost_centre").val(),
				cost_centre_group: $("#add_cost_centre_group").val(),
				sync_url: $("#add_sync_url").val(),
				is_active: $("#add_is_active").prop('checked'),
			};
			var token=$("#_token").val();
			$.post('/tallyconnector/add',newTallyData,function(response){
				$("#addNewTallyModal").modal("hide");
				if(response.status){
                    $("#alertStatus").attr("class","alert alert-success").html(response.message).show().delay(3000).fadeOut(350);
					$('#tallyListGrid').igGrid("dataBind");
				}else {
					$("#alertStatus").attr("class","alert alert-danger").html(response.message).show().delay(3000).fadeOut(350);
				}
			});            
        });  
	    $('#editTallyForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
            	add_cost_centre_group: {
                    validators: {
                       
                    }
                },
                edit_cost_centre: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('tally.validation_errors.cost_centre')}}"
		                }
                    }
                },
                add_sync_url: {
                    validators: {
                        
                    }
                },
                add_sync_url: {
                    validators: {
                       
                    }
                }   
            }
        })
        .on('success.form.bv', function(event) {
        	event.preventDefault();
			var newTallyData = {
				sync_id: $("#edit_sync_id").val(),
				cost_centre: $("#edit_cost_centre").val(),
				cost_centre_group: $("#edit_cost_centre_group").val(),
				sync_url: $("#edit_sync_url").val(),
				is_active: $("#edit_is_active").prop('checked'),
			};
			$.post('/tallyconnector/update',newTallyData,function(response){
				$("#editTallyModal").modal("hide");
				if(response.status){
					$("#alertStatus").attr("class","alert alert-success").html(response.message).show().delay(3000).fadeOut(350);
					$('#tallyListGrid').igGrid("dataBind");
				}else{
					$("#alertStatus").attr("class","alert alert-danger").html(response.message).show().delay(3000).fadeOut(350);
				}
			});            
        });
	});
	$('#add_cost_centre').keyup(function(){
		if($('#add_cost_centre').val() == ""){
		    $('#add_cost_center_message').css('display','none');
    	}
		if($('#add_cost_centre').val()){
			validateCostcenter(1);
		}else{
			$('#add_cost_center_message').css('display','none');
        }
	});
    $('#edit_cost_centre').keyup(function(){
    	if($('#edit_cost_centre').val() == ""){
			$('#edit_cost_center_message').css('display','none');
    	}
    	if($('#edit_cost_centre').val()){
			validateCostcenter(2);
		}else{
			$('#edit_cost_center_message').css('display','none');
        }
	});
	function validateCostcenter(type){
		if(type==1){
			var tallycode =  $('#add_cost_centre').val();
			var input = {'add_cost_centre':tallycode};
		}else{
			var tallycode =  $('#edit_cost_centre').val();
			var edit_sync_id =  $('#edit_sync_id').val();
			var input = {'edit_cost_centre':tallycode,'edit_sync_id':edit_sync_id};
		}
		$.ajax({
			url:'/tallyconnector/validatetallycode',
			type:'post',
			data: input,
			success: function(res){
				res=JSON.parse(res);
				if(res.valid){
                    if(type == 1){
						$("#addTallyData").attr("disabled",false);
						$('#add_cost_center_message').css('display','none');
						$('#add_cost_centre').css('border-color','#d6e9c6');
	                }else{
	                	$("#saveTallyData").attr("disabled",false);
	                    $('#edit_cost_center_message').css('display','none');
	                    $('#edit_cost_centre').css('border-color','#d6e9c6');
	                }
				}else{
                    if(type == 1){
						$('#add_cost_center_message').css('display','block');
						$('#add_cost_centre').css('border-color','#a94442');
                    	$('#add_cost_center_message').html(res.message);
                    	$("#addTallyData").attr("disabled",true);
	                }else{
	                	$("#edit_cost_center_message").show();
	                	$('#edit_cost_center_message').css('display','block');
						$('#edit_cost_centre').css('border-color','#a94442');
                    	$('#edit_cost_center_message').html(res.message);
                    	$("#saveTallyData").attr("disabled",true);
	                }
                }
			}
        });
	}
</script>
@stop
@extends('layouts.footer')
