@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message_ajax"></span>
<span id="failed_message_ajax"></span>


<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body">
	<div class="row">
	    <div class="col-md-12 col-sm-12">
	        <div class="portlet light tasks-widget">
	            <div class="portlet-title">
	                <div class="caption">Failed Order</div> 
	                <div class="actions"></div>
	            </div>
	            <div class="portlet-body">
	            	<div role="alert" id="alertStatus"></div>
	                <div class="row">
	                    <div class="col-md-12">                        
	                        <div class="table-responsive">
	                            <table id="failedorderGrid"></table>
	                        </div>                        
	                    </div>
	                </div>
	                <!-- Edit Modal -->
					<div class="modal fade" id="editfailedModal" tabindex="-1" role="dialog" aria-labelledby="editfailedModalLabel" aria-hidden="true">
					    <div class="modal-dialog" role="document">
					        <div class="modal-content">
					            <div class="modal-header">
					                <h4 class="modal-title" id="editfailedModalLabel">Edit Failed Order</h4>
					                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
					                   <span aria-hidden="true">&times;</span>
					                </button>
					            </div>
					            <div class="modal-body">
					            	<div class="alert" role="alert" id="modalAlert"></div>
					                <form id="editfailedorderform">
					                	<input type="hidden" name="_token" value="{{csrf_token()}}">
					                	<input type="hidden" name="edit_failed_order_id" id="edit_failed_order_id">
					                    <div class="row">
                                            <div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="legal_entity_id">Legal Entity Id</label>
							                        <input type="text" class="form-control" id="edit_legal_entity_id" readonly="readonly" name="edit_legal_entity_id" placeholder="Legal Entity Id">
							                    </div>
					                		</div>
						                    <div class="col-lg-6">
						                		<div class="form-group">
							                        <label for="order_date">Order Date</label>
							                        <input type="text" readonly="readonly" class="form-control" id="edit_order_date" name="edit_order_date">
							                    </div>
						                    </div>
					                    </div>
					                	<div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="order_code">Order Code</label>
							                        <input type="text" readonly="readonly" class="form-control" id="edit_order_code" name="edit_order_code" placeholder="Order Code">
							                    </div>
					                		</div>
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="updated_by">Updated By</label>
							                        <input type="text" readonly="readonly" class="form-control" id="edit_updated_by" name="edit_updated_by" placeholder="Updated By">
							                    </div>
					                		</div>
					                    </div>
					                    <div class="row">
					                		<div class="col-lg-12">
					                			<div class="form-group">
							                        <label for="order_data">Order Data</label>
						                            <textarea type="text"  rows="10"  class="form-control" id="edit_order_data" name="edit_order_data" placeholder="Order Data"></textarea>
							                    </div>
					                		</div>
					                    </div>
					                    <div class="row">
					                		<div class="col-lg-6">
					                			<div class="form-group">
							                        <label for="is_processed">Order Status</label>
							                        <select class="form-control select2me" id="edit_order_status" name="edit_order_status" style="margin-top: 6px" placeholder="Order Status">
	                                                    @foreach($statusInfo as $status)
	                        							  <option value = "{{$status['value']}}">{{$status['description']}}</option>
	                    								@endforeach
                                                    </select>
							                    </div>
					                		</div>
					                        <button type="button" id="savefailorderstatus" class="btn btn-primary"  style="margin-top: 23px">Save</button>		
					                    </div>
                                </div>
							            <div class="modal-footer">
							                <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">Close</button>
							                <button type="button" id="savefailorderdata" class="btn btn-primary">Place Order</button>
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
<script type="text/javascript">
    function editfailedorderRecord(id) {
 		$("#editfailedModal").modal("show");
 		$('#editfailedModal').modal({backdrop:'static', keyboard:false});
 		$.post('/dmapi/v2/fo/edit/'+id,function(response){
 			if(response.status){
 				$("#edit_failed_order_id").val(id);
 				$("#edit_order_data").text(response.order_data);
	 			$("#edit_order_date").val(response.order_date);
	 			$("#edit_order_code").val(response.order_code);
	 			$("#edit_updated_by").val(response.updated_by);
	 			$("#edit_order_status").val(response.processed);
	 			$("#edit_legal_entity_id").val(response.legal_entity_id);
 			}else{
 				$("#modalAlert").addClass("alert-danger").text("Invalid data! Please try again!").show();
 			}
 		});
 	}
 	$(document).ready(function () {
		$(function () {
    		failedorderGrid();
    		$('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonnextyear').parents('li').remove();
            $('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonthisyear').parents('li').remove();
            $('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonlastyear').parents('li').remove();
            $('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonnextmonth').parents('li').remove();
            $('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonthismonth').parents('li').remove();
            $('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonlastmonth').parents('li').remove();
            $('#failedorderGrid_dd_order_date').find('.ui-iggrid-filtericonnoton').parents('li').remove();
            $("#failedorderGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#failedorderGrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
            $("#failedorderGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
		});
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	      });
		// Hiding the Alert on Page Load
		$("#modalAlert").hide();
		$("#alertStatus").hide();

		$("#modalClose").click(function(){
			$("#modalAlert").hide();
			$('#modalAlert').data('bs.modal',null); // this clears the BS modal data
			$("#edit_order_data").attr('value','');
			$("#edit_order_date").attr('value','');
			$("#edit_order_code").attr('value','');
			$("#edit_updated_by").attr('value','');
            $("#edit_legal_entity_id").attr('value','');
			$("#edit_failed_order_id").val("");
			$("#editfailedorderform").bootstrapValidator('resetForm', true);
		});
		
    //FailedorderGrid();
		function failedorderGrid()
		{
	        $('#failedorderGrid').igGrid({
			    dataSource: '/dmapi/v2/fo/failedorderlist',
				responseDataKey: 'results',
			    height:'100%',
			    columns: [
			        {headerText: "Legal Entity Id", key: "legal_entity_id", dataType: "string",  width: '16%'},
					{headerText: "Order Data", key: "order_data", dataType: "string" ,template:"<div><input value='${order_data}' ></div>" ,width: '20%'},
					{headerText: "Order Date", key: "order_date", dataType: "date",width: '15%'},
					{headerText: "Order Code", key: 'order_code', dataType: "string", width: '20%'},
					{headerText: "Order Status", key: 'processed', dataType: "string", width: '18%'},
					{headerText: "Updated By", key: 'updated_by', dataType: "string", width: '20%'},
					{headerText: "Actions", key: 'actions', dataType: "string", width: '8%'}
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
                            {columnKey: 'actions', allowSorting: false},
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
	    $("#savefailorderdata").click(function(){
	    	var orderdata =  $('#edit_order_data').val();
            var failedid =  $('#edit_failed_order_id').val();
            var newplaceddata = {'edit_order_data':orderdata,'edit_failed_order_id':failedid};
            $('.spinnerQueue').show();
            $('.close').trigger('click');
			$.ajax({
				url:'/dmapi/v2/fo/placeorder',
				type:'post',
				data: newplaceddata,
				dataType:'json',
				success: function(data){
					$('.spinnerQueue').hide();
                    $('.close').trigger('click');
                    if(data.status == "success"){
	                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
	                    $(".alert-success").fadeOut(30000);
	                }else{
	                    $("#failed_message_ajax").html('<div class="alert alert-danger">'+data.message+'</div></div>' );
	                    $(".alert-danger").fadeOut(30000);
	                }
					$("#failedorderGrid").igGrid({dataSource: '/dmapi/v2/fo/failedorderlist'}).igGrid("dataBind");

				},
				error: function(data){
					alert("Server Error");
				}
	        });
	    });

	    $('#editfailedModal').on('hide.bs.modal', function () {
            $("#editfailedorderform").bootstrapValidator('resetForm', true);
            $('.modal-backdrop').remove();
        });

	    $("#savefailorderstatus").click(function(){
	    	var orderstatusdata ={
	    	                    order_status:$('#edit_order_status').val(),
	    	                    failed_order_id: $("#edit_failed_order_id").val(),
	    	};
			$.post('/dmapi/v2/fo/updatecomments',orderstatusdata,function(response){
				$("#editfailedModal").modal("hide");
				if(response.status){
                    $("#alertStatus").attr("class","alert alert-success").text("Updated the order status successfully").show().delay(3000).fadeOut(350);
					$('#failedorderGrid').igGrid("dataBind");
				}
				else{
					$("#alertStatus").attr("class","alert alert-danger").text("Failed to update the order status").show().delay(3000).fadeOut(350);
					$('#failedorderGrid').igGrid("dataBind");
				}
			});            
        });
	});
	
</script>
@stop
@extends('layouts.footer')
