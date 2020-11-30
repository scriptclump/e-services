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
                <div class="caption">{{trans('hsn.hsn_heads.caption')}}</div>                
                <div class="actions">
                	@if(isset($addPermission) and $addPermission)
	                	<a class="btn green-meadow" id="addNewHSN" href="#addHSN" data-toggle="modal">
		                    <i class="fa fa-plus-circle"></i>
		                    <span style="font-size:11px;"> {{trans('hsn.hsn_heads.add_hsn')}} </span>
	                    </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
            	<div role="alert" id="alertStatus"></div>
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="hsnListGrid"></table>
                        </div>                        
                    </div>
                </div>
                <!-- Edit Modal -->
				<div class="modal fade" id="editHSNModal" tabindex="-1" role="dialog" aria-labelledby="editHSNModalLabel" aria-hidden="true">
				    <div class="modal-dialog" role="document">
				        <div class="modal-content">
				            <div class="modal-header">
				                <h4 class="modal-title" id="editHSNModalLabel">{{trans('hsn.hsn_heads.edit_hsn')}}</h4>
				                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
				                <span aria-hidden="true">&times;</span>
				                </button>
				            </div>
				            <div class="modal-body">
				            	<div class="alert" role="alert" id="modalAlert"></div>
				                <form id="editHSNForm">
				                	<input type="hidden" name="_token" value="{{csrf_token()}}">
				                	<input type="hidden" name="edit_HSN_id" id="edit_HSN_id">
				                	@if(isset($taxDataPermission) and $taxDataPermission)
				                    <div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="Chapter">{{trans('hsn.side_heads.Chapter')}}</label>
						                        <input type="text" class="form-control" id="edit_Chapter" name="edit_Chapter" placeholder="{{trans('hsn.side_heads.Chapter')}}">
						                    </div>
				                		</div>
					                    <div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="ITC_HSCodes">{{trans('hsn.side_heads.ITC_HSCodes')}}</label>
						                        <input type="text" class="form-control" id="edit_ITC_HSCodes" name="edit_ITC_HSCodes">
						                    </div>
					                    </div>
				                    </div>
				                	<div class="row">
				                		<div class="col-lg-12">
				                			<div class="form-group">
						                        <label for="HSC_Desc">{{trans('hsn.side_heads.HSC_Desc')}}</label>
						                        <input type="text" class="form-control" id="edit_HSC_Desc" name="edit_HSC_Desc" placeholder="{{trans('hsn.side_heads.HSC_Desc')}}">
						                    </div>
				                		</div>
				                    </div>
				                    @else
				                    <div class="row">
				                		<div class="col-lg-6">
				                			<div class="form-group">
						                        <label for="Chapter">{{trans('hsn.side_heads.Chapter')}}</label>
						                        <strong id="edit_Chapter"></strong>
						                    </div>
				                		</div>
					                    <div class="col-lg-6">
					                		<div class="form-group">
						                        <label for="ITC_HSCodes">{{trans('hsn.side_heads.ITC_HSCodes')}}</label>
						                        <strong id="edit_ITC_HSCodes"></strong>
						                    </div>
					                    </div>
				                    </div>
				                	<div class="row">
				                		<div class="col-lg-12">
				                			<div class="form-group">
						                        <label for="HSC_Desc">{{trans('hsn.side_heads.HSC_Desc')}}</label>
						                        <strong id="edit_HSC_Desc"></strong>
						                    </div>
				                		</div>
				                    </div>
				                    @endif
				                    @if(isset($taxPercentPermission) and $taxPercentPermission)
				                    <div class="row">
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="tax_percent">{{trans('hsn.side_heads.tax_percent')}}</label>
						                        <input type="text" class="form-control" id="edit_tax_percent" name="edit_tax_percent" placeholder="{{trans('hsn.side_heads.tax_percent')}}">
						                    </div>
				                		</div>
				                		<div class="col-lg-2">
											<div class="form-check">
												{{trans('hsn.side_heads.is_active')}}
												<label class="switch">
													<input class="switch-input" type="checkbox" name="edit_is_active" id="edit_is_active">
													<span class="switch-label" data-on="Yes" data-off="No"></span>
													<span class="switch-handle"></span>
												</label>
											</div>
				                		</div>
				                    </div>
				                    @else
				                    <div class="row">
				                		<div class="col-lg-3">
				                			<div class="form-group">
						                        <label for="tax_percent">{{trans('hsn.side_heads.tax_percent')}}</label>
						                        <strong id="edit_tax_percent"></strong>
						                    </div>
				                		</div>
				                		<div class="col-lg-2">
											<div class="form-check">
												<label for="tax_percent">{{trans('hsn.side_heads.is_active')}}</label>
						                        <strong id="edit_is_active"></strong>
											</div>
				                		</div>
				                    </div>
				                    @endif
				            </div>
				            <div class="modal-footer">
				                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('hsn.hsn_heads.close')}}</button>
				                <button type="submit" id="saveHSNData" class="btn btn-primary">{{trans('hsn.hsn_heads.save')}}</button>
				            </div>
			                </form>
				        </div>
				    </div>
				</div>
            </div>
            <!-- Add Modal -->
			<div class="modal fade" id="addNewHSNModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
			    <div class="modal-dialog" role="document">
			        <div class="modal-content">
			            <div class="modal-header">
			                <h4 class="modal-title" id="addModalLabel">{{trans('hsn.hsn_heads.add_hsn')}}</h4>
			                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			                </button>
			            </div>
			            <div class="modal-body">
			            	<form id="addNewHSNForm">
			                	<input name="_token" type="hidden" value="{{csrf_token()}}">
			                	<div class="row">
			                		<div class="col-lg-6">
			                			<div class="form-group">
					                        <label for="Chapter">{{trans('hsn.side_heads.Chapter')}}</label>
					                        <input type="text" class="form-control" id="add_Chapter" name="add_Chapter" placeholder="{{trans('hsn.side_heads.Chapter')}}">
					                    </div>
			                		</div>
				                    <div class="col-lg-6">
				                		<div class="form-group">
					                        <label for="ITC_HSCodes">{{trans('hsn.side_heads.ITC_HSCodes')}}</label>
					                        <input type="text" class="form-control" id="add_ITC_HSCodes" name="add_ITC_HSCodes" placeholder="{{trans('hsn.side_heads.ITC_HSCodes')}}">
					                    </div>
				                    </div>
			                    </div>
			                	<div class="row">
			                		<div class="col-lg-9">
			                			<div class="form-group">
					                        <label for="HSC_Desc">{{trans('hsn.side_heads.HSC_Desc')}}</label>
					                        <input type="text" class="form-control" id="add_HSC_Desc" name="add_HSC_Desc" placeholder="{{trans('hsn.side_heads.HSC_Desc')}}">
					                    </div>
			                		</div>
			                		<div class="col-lg-3">
			                			<div class="form-group">
					                        <label for="tax_percent">{{trans('hsn.side_heads.tax_percent')}}</label>
					                        <input type="text" class="form-control" id="add_tax_percent" name="add_tax_percent" placeholder="{{trans('hsn.side_heads.tax_percent')}}">
					                    </div>
			                		</div>
			                    </div>
			                	<div class="row">
			                		<div class="col-lg-2">
										<div class="form-check">
											<label class="form-check-label"><br>
											<input type="checkbox"  checked="checked" id="add_is_active" name="add_is_active" class="form-check-input">
											{{trans('hsn.side_heads.is_active')}}
											</label>
										</div>
			                		</div>
			                    </div>
			            </div>
			            <div class="modal-footer">
			                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">{{trans('hsn.hsn_heads.close')}}</button>
			                <button type="submit" id="addHSNData" class="btn btn-primary">{{trans('hsn.hsn_heads.add')}}</button>
			            </div>
		                </form>
			        </div>
			    </div>
			</div>
        </div>
    </div>
</div>
</div>
<style>
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

 	function editHSNRecord(id) {
 		$("#editHSNModal").modal("show");
 		$('#editHSNModal').modal({backdrop:'static', keyboard:false});
 		$.post('/hsn/edit/'+id,function(response){
 			if(response.status){
 				$("#edit_HSN_id").attr('value',id);
 				@if(isset($taxDataPermission) and $taxDataPermission)
	 				$("#edit_Chapter").attr('value',response.Chapter);
	 				$("#edit_ITC_HSCodes").attr('value',response.ITC_HSCodes);
	 				$("#edit_HSC_Desc").attr('value',response.HSC_Desc);
 				@else
	 				$("#edit_Chapter").text(response.Chapter);
	 				$("#edit_ITC_HSCodes").text(response.ITC_HSCodes);
	 				$("#edit_HSC_Desc").text(response.HSC_Desc);
 				@endif
 				@if(isset($taxPercentPermission) and $taxPercentPermission)
	 				$("#edit_tax_percent").attr('value',response.tax_percent);
	 				$("#edit_is_active").prop('checked',(response.is_active) ? true : false);
 				@else
	 				$("#edit_tax_percent").text((response.tax_percent == null) ? "" : response.tax_percent);
	 				$("#edit_is_active").text((response.is_active) ? "Active" : "In-Active");
 				@endif
 			}
 			else{
 				$("#modalAlert").addClass("alert-danger").text("{{trans('hsn.message.invalid')}}").show();
 			}
 		});
 	}

 	function deleteHSNRecord(id) {
 		var decision = confirm("Are you sure. Do you want to Delete it!");
 		if(decision){
 			$.post('/hsn/delete/'+id,function(response){
 				if(response.status){
 					$("#alertStatus").attr("class","alert alert-info").text("{{trans('hsn.message.success_deleted')}}").show().delay(3000).fadeOut(350);
 					$('#hsnListGrid').igGrid("dataBind");
 				}
 				else
	 				$("#alertStatus").attr("class","alert alert-danger").text("{{trans('hsn.message.failed_deleted')}}").show().delay(3000).fadeOut(350);
 			});
 		}
 	}

	$(document).ready(function () {
		$(function () {
    		hsnListGrid();
		});

		$('#addNewHSNModal').on('hide.bs.modal', function () {
            $("#addNewHSNForm").bootstrapValidator('resetForm', true);
            $("#add_is_active").prop('checked',true);
        });

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	      });

		$("#addNewHSN").click(function(){
			$("#addNewHSNModal").modal("show");
			$("#addNewHSNModal").modal({backdrop:'static', keyboard:false});
		});

		// $("#addHSNData").click(function(){
			
		// });

		// Hiding the Alert on Page Load
		$("#modalAlert").hide();
		$("#alertStatus").hide();

		$("#modalClose").click(function(){
			$("#modalAlert").hide();
			$('#modalAlert').data('bs.modal',null); // this clears the BS modal data
			$("#edit_Chapter").attr('value','');
			$("#edit_ITC_HSCodes").attr('value','');
			$("#edit_HSC_Desc").attr('value','');
			$("#edit_tax_percent").attr('value','');
			$("#edit_is_active").val('');
		});
		
		function hsnListGrid()
		{
			$('#hsnListGrid').igGrid({
        	    dataSource: '/hsn/list',
				responseDataKey: 'Records',
			    height:'100%',
			    columns: [
					{headerText: "{{trans('hsn.side_heads.Chapter')}}", key: "Chapter", dataType: "string", columnCssClass:"alignRight",headerCssClass:"alignRight", width: '5%'},
					{headerText: "{{trans('hsn.side_heads.ITC_HSCodes')}}", key: 'ITC_HSCodes', dataType: "string", columnCssClass:"alignRight",headerCssClass:"alignRight", width: '15%'},
					{headerText: "{{trans('hsn.side_heads.HSC_Desc')}}", key: 'HSC_Desc', dataType: "string", width: '25%'},
					{headerText: "{{trans('hsn.side_heads.tax_percent')}}", key: 'tax_percent', columnCssClass:"alignRight", headerCssClass:"alignRight", dataType: "double", width: '5%'},
					{headerText: "{{trans('hsn.side_heads.status')}}", key: 'is_active', dataType: "string", width: '5%'},
					{headerText: "{{trans('hsn.side_heads.actions')}}", key: 'actions', dataType: "string", width: '8%'}
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
	 	$('#addNewHSNForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                add_Chapter: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hsn.validation_errors.Chapter')}}"
                        }
                    }
                },
                add_ITC_HSCodes: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hsn.validation_errors.ITC_HSCodes')}}"
                        },
		                regexp: {
		                    regexp: '^[0-9]*$',
		                    message: "{{trans('hsn.validation_errors.ITC_HSCodes_isdigit')}}"
		                },
		                remote: {
		                    url: '/hsn/validatehsncode',
		                    type: 'POST',
		                    data: function (validator, $field, value) {
		                        return  {
		                            edit_ITC_HSCodes: value
		                        };
		                    },
		                    delay: 1000, // Send Ajax request every 1 seconds
		                    message: "{{trans('hsn.validation_errors.ITC_HSCodes_exist')}}"
		                }
                    }
                },
                add_HSC_Desc: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hsn.validation_errors.HSC_Desc')}}"
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(event) {
        	event.preventDefault();
			var newHsnData = {
				Chapter: $("#add_Chapter").val(),
				ITC_HSCodes: $("#add_ITC_HSCodes").val(),
				HSC_Desc: $("#add_HSC_Desc").val(),
				tax_percent: $("#add_tax_percent").val(),
				is_active: $("#add_is_active").prop('checked'),
			};
			$.post('/hsn/add',newHsnData,function(response){
				$("#addNewHSNModal").modal("hide");
				if(response.status){

					$("#alertStatus").attr("class","alert alert-success").text("{{trans('hsn.message.success_updated')}}").show().delay(3000).fadeOut(350);
					$('#hsnListGrid').igGrid("dataBind");
				}
				else
					$("#alertStatus").attr("class","alert alert-danger").text("{{trans('hsn.message.failed_updated')}}").show().delay(3000).fadeOut(350);
			});            
        });

		// To Update the Editted Content
        $('#editHSNForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                edit_Chapter: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hsn.validation_errors.Chapter')}}"
                        }
                    }
                },
                edit_ITC_HSCodes: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hsn.validation_errors.ITC_HSCodes')}}"
                        },
		                regexp: {
		                    regexp: '^[0-9]*$',
		                    message: "{{trans('hsn.validation_errors.ITC_HSCodes_isdigit')}}"
		                },
		                remote: {
		                    url: '/hsn/validatehsncode',
		                    type: 'POST',
		                    data: function (validator, $field, value) {
		                        return  {
		                            edit_ITC_HSCodes: value,
		                            hsn_id: $("#edit_HSN_id").val()
		                        };
		                    },
		                    delay: 1000, // Send Ajax request every 1 seconds
		                    message: "{{trans('hsn.validation_errors.ITC_HSCodes_exist')}}"
		                }
                    }
                },
                edit_HSC_Desc: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('hsn.validation_errors.HSC_Desc')}}"
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(event) {
        	event.preventDefault();
			var newHsnData = {
				HSN_id: $("#edit_HSN_id").val(),
				Chapter: $("#edit_Chapter").val(),
				ITC_HSCodes: $("#edit_ITC_HSCodes").val(),
				HSC_Desc: $("#edit_HSC_Desc").val(),
				tax_percent: $("#edit_tax_percent").val(),
				is_active: $("#edit_is_active").prop('checked'),
			};
			$.post('/hsn/update',newHsnData,function(response){
				$("#editHSNModal").modal("hide");
				if(response.status){

					$("#alertStatus").attr("class","alert alert-success").text("{{trans('hsn.message.success_old')}}").show().delay(3000).fadeOut(350);
					$('#hsnListGrid').igGrid("dataBind");
				}
				else
					$("#alertStatus").attr("class","alert alert-danger").text("{{trans('hsn.message.failed_old')}}").show().delay(3000).fadeOut(350);
			});            
        });
	});
</script>
@stop
@extends('layouts.footer')
