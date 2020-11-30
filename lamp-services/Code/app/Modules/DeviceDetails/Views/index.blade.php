@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="portlet-body">
    @if (Session::has('flash_message'))            
    <div class="alert alert-info">{{ Session::get('flash_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="float: right;">&times;</button></div>
    @endif                    
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
            <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="caption"> {{trans('device_dtls.heading.index_page_title')}} </div>
               
               <div class="col-md-3">
                    <div class="form-group">
                        <select id = "warehouse_id"  name = "warehouse_id" class="form-control select2me"  style="margin-top: 8px;">
                            <option value =" ">--Select WareHouse--</option>
                          
                             @foreach($wareHouses as $wareHouses)
                             @if($wareHouses->lp_wh_name!='' && $wareHouses->lp_wh_name!=null)
                            <option value = "{{$wareHouses->le_wh_id}}">{{$wareHouses->lp_wh_name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div> 

                <div class="col-md-3">
                    <div class="form-group">
                        <select id = "hubs_id"  name = "hubs_id" class="form-control select2me" style="margin-top: 8px;" >
                            <option value ="0">--Select Hub--</option>
                            
                        </select>
                    </div>
                  </div> 
                <div class="col-md-3">
                    <div class="form-group">
                        <select id = "beats_id"  name = "beats_id" class="form-control select2me" style="margin-top: 8px;">
                            <option value ="0">--Select Beats--</option>
                            
                        </select>
                    </div>
                </div> 
                <div class="col-md-2">
                	<div class="form-group">
                		<button type="button" class="form-control" style="margin-top: 6px;" id="search_dclist" name="search_dclist">
                        <span class="glyphicon glyphicon-search"></span> Search
                        </button>
                	</div>
                </div>
                
                
            </div>

            
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="appVersionInfoGrid"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</div>
<style>
	.rightAlignText{
		text-align: right !important;
	}
	.hideContent{
		display: none;
	}
	.ownWarning{
		color:red;
	}
</style>
<link href="{{ URL::asset('assets/global/plugins/select2/select2.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('script') 
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	
	$(document).ready(function ()
	{
		$(function () {
			$(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('device_dtls.heading.index_page_title')}}");

            var warehouse_id = $('#warehouse_id').val();
            if($('#hubs_id').val()!=''){
            var hubs_id = $('#hubs_id').val();
            }else{
                var hubs_id='';
            }
            if($('#beats_id').val()!=''){
            var beats_id = $('#beats_id').val();
            var urlsource='/devicedetails/devicedetailslist?%24filter=hub_id+eq'+hubs_id+' and pjp_pincode_area_id+eq'+beats_id;
           }else{
            var urlsource="/devicedetails/devicedetailslist";
            var beats_id='';
           }
    		
		        	$('#appVersionInfoGrid').igGrid({
                      
					    dataSource: '/devicedetails/devicedetailslist?%24fillter=hub_id+eq'+hubs_id+' and pjp_pincode_area_id+eq'+beats_id,
                        initialDataBindDepth: 0,
                        autoGenerateColumns: false,
                        mergeUnboundColumns: false,
                        generateCompactJSONResponse: false,
                        responseDataKey: "results", 
                        enableUTCDates: true, 
                        width: "100%",
                         height: "100%",
					     columns: [
					        {headerText: "{{trans('device_dtls.grid.username')}}", key: 'firstname', dataType: 'string', width: '15%'},
					        {headerText: "{{trans('device_dtls.grid.shopname')}}", key: 'b_name', dataType: 'string', width: '15%'},
					        {headerText: "{{trans('device_dtls.grid.mobile')}}", key: 'mobile_no', dataType: 'string', width: '15%'},
							{headerText: "{{trans('device_dtls.grid.device_id')}}", key: "device_id", width: "10%", dataType: "string"},
			                {headerText: "{{trans('device_dtls.grid.registration_id')}}", key: 'registration_id', dataType: 'string', width: '15%'},
							
			            ],
						features: [
						{
						    name:'Filtering',
						    type: "remote",
                            mode: "simple",
                            allowFiltering: true,
                            filterDialogContainment: "window",
                            columnSettings: [
                             {columnKey: 'firstname', allowFiltering: true },
                             {columnKey: 'b_name', allowFiltering: true },
                             {columnKey: 'mobile_no', allowFiltering: true },
                             {columnKey: 'device_id', allowFiltering: true },
                             {columnKey: 'registration_id', allowFiltering: true },
                             
                    ]
                    },
                    { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging',
                    loadTrigger: 'auto',
                    type: 'local'
                     }
					        ]
					});

		});
		
		/*function appVersionGrid()
		{
			 		
	 	}*/

		
	});


	 $("#warehouse_id").change(function() {
	 	
    var token = $('#csrf-token').val();
    var warehouse_id = $('#warehouse_id').val();
    if(warehouse_id>0){
        $.ajax({
               headers: {'X-CSRF-TOKEN': token},
		        url:"/devicedetailswarehouse",
		        type:"POST",
		        data: 'warehouseid='+warehouse_id,
		        success:function(data){
                
           $("#hubs_id").html(data);     

		        }
        });
    }
    
    });


     $("#hubs_id").change(function() {
        
    var token = $('#csrf-token').val();
    var hubs_id = $('#hubs_id').val();
    if(hubs_id>0){
        $.ajax({
               headers: {'X-CSRF-TOKEN': token},
                url:"/devicedetailshubs",
                type:"POST",
                data: 'hubsid='+hubs_id,
                success:function(data){
                
           $("#beats_id").html(data);     

                }
        });
    }
    
    });



    $("#search_dclist").click(function(){

     
     var token = $('#csrf-token').val();
    var warehouse_id = $('#warehouse_id').val();
    var hubs_id = $('#hubs_id').val();
    var beats_id = $('#beats_id').val();
    if(warehouse_id>0 && hubs_id>0 && beats_id>0){
        $("#appVersionInfoGrid").igGrid({dataSource: '/devicedetails/devicedetailslist?%24fillter=hub_id+eq'+hubs_id+' and pjp_pincode_area_id+eq'+beats_id});

     }
    });

	
</script>

@stop
@extends('layouts.footer')