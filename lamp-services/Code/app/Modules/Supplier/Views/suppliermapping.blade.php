@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Supplier Manufacturer Mapping'); ?>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/suppliers">Suppliers</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Supplier Manufacturer Mapping</li>
        </ul>
    </div>
</div>
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="portlet light tasks-widget" style="height:650px;">
		  <div class="portlet-title">
		    <div class="caption"> 
		     	Supplier Manufacturer Mapping
		    </div>
            <div class="actions">
            	@if($supplierMapAccess == 1)
					<button  id="addsuppliermapping" class="btn green-meadow">Map Supplier</button>
				@endif
            </div>
		  </div>

		  <div class="portlet-body">
		    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
		    <div class="row">
		      <div class="col-md-12">
		        <div class="table-scrollable">
		          <table id="supplierMapGrid"></table>
		        </div>
		      </div>
		    </div>  
		  </div>
		</div>
	</div>
</div>
<div class="modal fade" id="addsuppliermap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Map New Supplier</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
                    {{ Form::open(array('url' => '#', 'id' => 'add_new_supplier_form'))}}

                    <div class="col-md-12">                        
                        <div class="form-group">
                            <select name="supp_name" id="supp_name" class="form-control select2me" placeholder="Select Supplier">
                                <option value="">Please Select Supplier</option>
                                @foreach ($filter_options['suppler_list'] as $supp_data)
                                <option value="{{ $supp_data->legal_entity_id }}">{{ $supp_data->business_legal_name }}</option>
                                @endforeach
                            </select>
                        </div>                        
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <select name="manf_name" id="manf_name" class="form-control select2me" placeholder="Select Manufacture">
                                <option value="">Please Select Manufacturer</option>
                                @foreach ($filter_options['manfacturer_name'] as $manf_id => $manf_name)
                                <option value="{{ $manf_id }}">{{ $manf_name }}</option>
                                @endforeach
                            </select>
                        </div>                        
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <select name="dc_name" id="dc_name" class="form-control select2me dc_reset" placeholder="Select Warehouse">
                                <option value="">Please Select Warehouse</option>
                                @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="map_id" id="supplier_map_id" value="">
                </div>
                <div class="row">
        			<hr/>
            		<div class="col-md-12 text-center"> 
               			<button type="button" class="btn green-meadow btnn" id="map_sup_button" onclick="addNewMap()">Submit
               			</button>
               		</div>
               	</div>
				{{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@stop
@section('userscript')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#supplierMapGrid').igGrid({
	    dataSource: '/suppliers/mappinggrid',
	    responseDataKey: 'results',
	    columns: [
	        {headerText: 'Legal Entity', key: 'legal_entity_name', dataType: 'string', },            
	        {headerText: 'Manufacturer', key: 'manf_name', dataType: 'string', },
	        {headerText: 'Warehouse', key: 'le_wh_name', dataType: 'string', },
	        {headerText: 'Status', key: 'status_type', dataType: 'string', },
	        {headerText: 'Actions', key: 'actions', dataType: 'string', },
	    ],
	    
	    features: [
	    {
	        name: "Filtering",
	        type:"remote",
	        allowFiltering: true,
	        caseSensitive: false,
	        columnSettings: [
                {columnKey: 'legal_entity_name', allowFiltering: true },
                {columnKey: 'manf_name', allowFiltering: true },   
                {columnKey: 'le_wh_name', allowFiltering: true },                
                {columnKey: 'actions', allowFiltering: false },
                {columnKey: 'status_type', allowFiltering: false },
            ]
	    }, 
	    {
	         name: 'Sorting',
	         type: "local",
	     },
	     {
	        name : 'Paging',
	        type: "local",
	        pageSize : 25,
	    }
	    ],
	    primaryKey: 'product_id',
	    width:'100%',
	    height:'500px',
	    initialDataBindDepth: 1,
	});
});


var token  = $("#csrf-token").val();
$("#addsuppliermapping").click(function(){
    $('#addsuppliermap').modal('toggle');
	$("#map_sup_button").html("Submit");
	$("#supplier_map_id").val('');
});

$(document).on('click', '.change_sup_active_status', function (event) {
    var checked = $(this).is(":checked");
    var map_id = $(this).val();
    changeStatus(map_id,checked);

});
function changeStatus(map_id,status){
	$.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/suppliers/changemapstatus/'+map_id+'/'+status,
        success: function (respData)
        {
        	var data = respData;
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
            $(".alert-success").fadeOut(20000);
    		$("#supplierMapGrid").igGrid("dataBind");
            
        },
        error: function (response) {
        	alert("Technical Error!");
        }
    });
}
function addNewMap(){
	var frmData = $('#add_new_supplier_form').serialize();
	var map_id = $("#supplier_map_id").val();
	var url = '/suppliers/addsuppliermap';

	if($('#supp_name').val() == ""){
        alert("Select Supplier!");
        return false;
    }

    if($('#manf_name').val() == ""){
        alert("Select Manufacturer!");
        return false;
    }

    if($('#dc_name').val() == ""){
        alert("Select Warehouse!");
        return false;
    }

 	if(map_id!="")
		url = '/suppliers/updatesuppliermap';
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: url,
        data: frmData,
        success: function (respData)
        {
        	var data = respData;
        	if(data.status == 1){
	            $('#addsuppliermap').modal('toggle');
	            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
	            $(".alert-success").fadeOut(20000);
	    		$("#supplierMapGrid").igGrid("dataBind")
	    	}else{
	    		alert(data.message);
	    	}
            
        },
        error: function (response) {
        	alert("Technical Error!");
            $('#addsuppliermap').modal('toggle');
        }
    });
}	

$('#addsuppliermap').on('hidden.bs.modal', function (e) {
	$("#manf_name").select2('val',"");
    $("#supp_name").select2('val',"");
    $("#dc_name").select2('val',"");
});

function getSupplierMap(id){
	$.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/suppliers/getsupppliermapdata/'+id,
        success: function (respData)
        {
        	var data = respData;
            $("#manf_name").select2('val',data.manf_name);
            $("#supp_name").select2('val',data.supp_name);
            $("#dc_name").select2('val',data.le_wh_name);
			$("#supplier_map_id").val(id);
            $('#addsuppliermap').modal('toggle');
			$("#map_sup_button").html("Update");

        },
        error: function (response) {
        	alert("Technical Error!");
            $('#addsuppliermap').modal('toggle');
        }
    });
}




function deletesuppliermapping(map_id){

	$.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/suppliers/deletesuppliermap/'+map_id,
        success: function (respData)
        {
        	var data = respData;
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
            $(".alert-success").fadeOut(20000);
    		$("#supplierMapGrid").igGrid("dataBind");
            
        },
        error: function (response) {
        	alert("Technical Error!");
        }
    });
}
</script>
@stop
