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
                <div class="caption"> {{trans('app_version.heading.index_page_title')}} </div>
                <div class="actions">
                	@if(isset($addPermission) and $addPermission)
	                	<a class="btn green-meadow" id="addNewAppVersion" href="#addAppVersion" data-toggle="modal">
		                    <i class="fa fa-plus-circle"></i>
		                    <span style="font-size:11px;"> {{trans('app_version.heading.add_app_version')}} </span>
	                    </a>
                    @endif
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
@if(isset($message))<script type="text/javascript">alert("{{ $message }}");</script> @endif
@if ($errors->has('message'))<script type="text/javascript">alert("{{ $errors->first('message') }}");</script> @endif
@if ($errors->has('version_number'))<script type="text/javascript">alert("{{ $errors->first('version_number') }}");</script> @endif
@if ($errors->has('released_date')) <script type="text/javascript">alert("{{ $errors->first('released_date') }}");</script>@endif
	<div class="modal modal-scroll fade in" id="addAppVersion" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
                <h4 class="modal-title" id="basicvalCode">{{trans('app_version.heading.add_app_version')}}</h4>
            </div>
            <div class="modal-body">
                <form id="addAppVersionForm" action="/appversion/addversion" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group" align="left">
                                	{{trans('app_version.modal.version_name')}}:
                                	<input type="text" class="form-control" name="version_name" required="required" value="{{ Input::old('version_name') }}">
                                	<input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" align="left">
                                    {{trans('app_version.modal.version_number')}}:
                                    <input type="text" class="form-control" name="version_number" required="required" value="{{ Input::old('version_number') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group" align="left">
                                	{{trans('app_version.modal.app_type')}}:
                                	<input type="text" name="app_type" required="required" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" align="left">
                                    {{trans('app_version.modal.released_date')}}:
                                    <input type="datetime-local" class="form-control" name="released_date" required="required" value="{{ Input::old('released_date') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="addNewAppVersionSubmit" class="btn green-meadow">{{trans('app_version.heading.add_app_version')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal-add app version -->
    <button data-toggle="modal" id="editAppVersion" class="btn btn-default" data-target="#basicvalCodeModal4" style="display: none" data-url="{{URL::asset('/appversion/editversion')}}"></button>
	<div class="modal fade" id="basicvalCodeModal4" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
	    <div class="modal-dialog wide">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
	                <h4 class="modal-title" id="basicvalCode4">{{trans('app_version.heading.edit_app_version')}}</h4>
	            </div>
	            <div class="modal-body">
		            <form id="updateAppVersionForm" action="/appversion/updateversion" class="text-center" method="post">
		            	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	                    <div id="editModalContent">
	                    	<div class="row">
		                        <div class="col-md-12" align="center">
		                            <div class="col-md-6">
		                                <div class="form-group" align="left">
                                        	{{trans('app_version.modal.version_name')}}:
		                                    <input type="text" class="form-control" name="version_name" id="edit_version_name" required="required">
		                                    <input type="hidden" name="version_id" id="edit_version_id">
		                                </div>
		                            </div>
		                            <div class="col-md-6">
		                                <div class="form-group" align="left">
                                        	{{trans('app_version.modal.version_number')}}:
	                                    	<input type="text" class="form-control" name="version_number" id="edit_version_number" required="required">
	                                	</div>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div class="col-md-12" align="center">
		                            <div class="col-md-6">
		                                <div class="form-group" align="left">
		                                    {{trans('app_version.modal.app_type')}}:
		                                    <input type="hidden" name="app_type" id="edit_hidden_app_type">
											<input type="text" class="form-control" id="edit_app_type" disabled="disabled">
		                                </div>
		                            </div>
		                            <div class="col-md-6">
		                                <div class="form-group" align="left">
		                                    {{trans('app_version.modal.released_date')}}:
		                                    <input type="datetime-local" name="released_date" id="edit_released_date" class="form-control" required="required">
		                        </div>
		                    </div>
		                    <hr/>
		                    <div class="row">
	                            <div class="col-md-12 text-center">
	                                <button type="submit" id="updateAppVersionSubmit" class="btn green-meadow">{{trans('app_version.heading.update_app_version')}}</button>
	                            </div>
		                    </div>
						</div>
					</form>
	            </div>
	        </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

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
@stop

@section('script') 
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script type="text/javascript">
	function editAppVersion(id)
	{
		$.get('/appversion/editversion/' + id, function (response) {
            // $("#editModalContent").html(response);
            console.log(response);
            // Assiging Values to the Edit Modal
            $("#edit_app_type").val(response.app_type);
            $("#edit_version_id").val(response.version_id);
            $("#edit_version_name").val(response.version_name);
            $("#edit_version_number").val(response.version_number);
            $("#edit_released_date").val(response.released_date);
            $("#edit_hidden_app_type").val(response.app_type);

            $("#editAppVersion").click();
        });
	}
	$(document).ready(function ()
	{
		$(function () {
			$(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('app_version.heading.index_page_title')}}");
    		appVersionGrid();
		});
		
		function appVersionGrid()
		{
			var token = $("#token_value").val();
		    $.ajax({
		        url:"/appversion/versionlist?_token=" + token,
		        type:"POST",
		        dataType:"json",
		        success:function(data)
		        {
		        	$('#appVersionInfoGrid').igTreeGrid({

					    dataSource: data,
					    autoGenerateColumns: false,
					    primaryKey: "version_id",
				        foreignKey: "childs",
				        initialExpandDepth: 0,
					    height:"100%",
					    
					     columns: [
							{headerText: "{{trans('app_version.grid.app_tree')}}", key: "version_id", width: "5%", dataType: "number", template: "<div class='hideContent'></div>"},
			                {headerText: "{{trans('app_version.grid.app_type')}}", key: 'app_type', dataType: 'string', width: '15%'},
							{headerText: "{{trans('app_version.grid.version_name')}}", key: 'version_name', dataType: 'string', width: '20%', columnCssClass: 'rightAlignText', headerCssClass: 'rightAlignText'},
							{headerText: "{{trans('app_version.grid.version_number')}}", key: 'version_number', dataType: 'string', width: '15%', columnCssClass: 'rightAlignText', headerCssClass: 'rightAlignText'},
							{headerText: "{{trans('app_version.grid.released_date')}}", key: 'released_date', dataType: 'string', width: '10%', columnCssClass: 'rightAlignText', headerCssClass: 'rightAlignText'},
							{headerText: "{{trans('app_version.grid.actions')}}", key: 'actions', dataType: 'string', width: '5%'}
			            ],
						features: [
					        {
					            name: "Filtering",
					            columnSettings: [
				            		{columnKey: 'version_id', allowFiltering: false},
				                    {columnKey: 'actions', allowFiltering: false},
					            ]
					        },
					        {
					            name: 'Sorting',
					            columnSettings: [
					            	{columnKey: 'version_id', allowFiltering: false},
					                {columnKey: 'actions', allowFiltering: false},
					            ],
					        },
					        {
					            name: 'Paging',
					            pageSize: 10,
					            mode: 'allLevels'
					        }]
					});

		        }
			});		
	 	}

		/*$('#addNewAppVersionSubmit').click(function(){
			event.preventDefault();
			var content = $("#addAppVersionForm").serialize();
		    $.ajax({
		        url: '/appversion/addversion',
		        data: content,
		        type: 'post',
		        success: function (response) {
		            var data = $.parseJSON(response);
		            alert(data.version_number);
		            // if (data.status) {
		            //     $("#getuserid").val(data.user_id);
		            //     $("#email_error").html('');
		            //     $('a[href="#tab22"]').tab('show');
		            //     $('#email_id').prop('readonly', true);
		            // }
		        }
		    });
		});*/
	});
</script>
@stop
@extends('layouts.footer')
