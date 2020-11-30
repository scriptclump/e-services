@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<link href="{{URL::to('assets/global/plugins/select2/select2.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
	.ui-iggrid-summaries-footer-text-container{
		font-size: 0.9em !important;
	    margin-left: 25px !important;
	    font-weight: bold !important;
	}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body" ng-app="Inventory" ng-controller="InventoryOOSReportCtrl">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">{{trans('outofstockreport.caption')}}</div>                
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
            	<div style="padding: 20px">
					<div class="row" style="background-color: #ffffff;">
						<div class="col-md-11"></div>
						<div class="col-md-1" style="padding-left: 67px;"><a href="javascript:void(0);" id="showFilter"><i class="fa fa-filter fa-lg"></i></a></div>
					</div>
					<div class="row" ng-init="loadOOSByPeriodChart('NULL','NULL','NULL',-1)" id="oos_report_filter" style="display: none;background-color: #ffffff;padding-bottom: 10px;">
						<div class="col-md-3">
							<label>{{trans('outofstockreport.filters.product_select')}}</label>
							<select id="productId" class="form-control select2me" multiple="multiple" ng-model="productId">
								<option value="0">{{trans('outofstockreport.filters.product_all')}}</option>
								<option ng-repeat="(key,value) in productIds" value="<%value.product_id%>"><%value.product_title%></option>
							</select>
						</div>
						<div class="col-md-3">
							<label>{{trans('outofstockreport.filters.date_select')}}</label>
							<select id="oos_report_date" ng-model="oos_report_date" class="form-control" style="padding-bottom: 10px;" ng-change="changeDate()">
								<option value="today" selected="selected">{{trans('outofstockreport.filters.date_today')}}</option>
								<option value="yesterday">{{trans('outofstockreport.filters.date_yesterday')}}</option>
								<option value="wtd">{{trans('outofstockreport.filters.date_wtd')}}</option>
								<option value="mtd">{{trans('outofstockreport.filters.date_mtd')}}</option>
								<option value="ytd">{{trans('outofstockreport.filters.date_ytd')}}</option>
								<option value="quarter">{{trans('outofstockreport.filters.date_quarter')}}</option>
								<option value="oos_report_date_range">{{trans('outofstockreport.filters.date_custom')}}</option>
						    </select>
						</div>
						<div ng-show="oos_report_date=='oos_report_date_range'">
							<div class="col-md-2">
								<label>{{trans('outofstockreport.filters.date_start')}}</label>
								<div class="input-icon input-icon-sm right">
								<i class="fa fa-calendar"></i>
								<input type="text" ng-model="startDate" name="startDate" id="startDate" class="form-control" value="">
								</div>
							 </div> 
							 <div class="col-md-2">
								<label>{{trans('outofstockreport.filters.date_end')}}</label>
								<div class="input-icon input-icon-sm right">
								<i class="fa fa-calendar"></i>
								<input type="text" ng-model="endDate" name="endDate" id="endDate" class="form-control" value="">
								</div>
							 </div> 
						</div>
						
						<button ng-click="loadOOSByPeriodChart(productId,startDate,endDate,oos_report_date)" class="btn green-meadow subBut" style="margin-top: 23px;">{{trans('outofstockreport.filters.submit')}}</button>
					</div>

					<div class="row tabs-below" style=" margin-bottom: 15px; ">
						<div class="alert alert-warning alert-dismissible" role="alert" id="alertDiv">
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						  <p id="errorMessage"></p>
						</div>
						<div class="col-md-12 tab-content" style="padding-bottom: 15px; background-color: #ffffff;">
							<div class="tab-pane"  id="oos_report_grid" style="overflow-y: auto; overflow-x:auto;height: 550px;padding: 10px;">
								<div id="ff_list_tab" style="display:none;"></div>
								<div id="table_tab">
									<table id="oosGrid"></table>
								</div>

							</div>
							<div class="tab-pane active" id="oos_report_chart" style="height: 550px;padding: 10px;">
								<div id="oos_line_chart"></div>						
							</div>
						</div>
						<div class="barChatCLass" style="border-top: 1px solid #eee;position: relative;">
							<div class="loader" loading></div>
	                        <ul class="nav" style="display: flex;float: right;padding-left: 12px;">
	                            <li class="custIcon "><a href="#oos_report_grid" data-toggle="tab" class="fa fa-th fa-lg" aria-hidden="true" title="Grid Data"></a></li>
	                            <li class="custIcon active"><a href="#oos_report_chart" data-toggle="tab" class="fa fa-line-chart fa-lg" aria-hidden="true" title="Line Chart"></a></li>
	                        </ul>
	                	</div>
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
	.headerAlignRight{
	 	text-align: right !important;
 	    padding-right: 5px;
	}
	.alignLeft{
	 	text-align: left;
	 	padding-left: 5px;
	}

</style>
@stop

@section('script') 
@include('includes.ignite')
{{HTML::script('https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.js')}}
{{HTML::script('assets/global/scripts/angular-fusioncharts.min.js')}}
{{HTML::script('https://static.fusioncharts.com/code/latest/fusioncharts.js')}}
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
{{HTML::script('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js')}}
{{HTML::script('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js')}}
{{HTML::script('assets/global/plugins/select2/select2.min.js')}}
{{HTML::script('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js')}}
{{HTML::script('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js')}}
<script type="text/javascript">
	$(document).ready(function () {
		$(function () {
    		$("#productId").select2('val', '0');
		});
		
		$('#startDate, #endDate').datepicker({
	        autoclose: true,
	    	format: "dd/mm/yyyy",
	        endDate: "today",
	        maxDate: 0,
	        todayHighlight: true
	    });

	    $("#alertDiv").hide();

		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	      });

		$("#showFilter").click(function(){
			$("#oos_report_filter").toggle("fast",function(){});
		});

	});
</script>
<script type="text/javascript">
	var app = angular.module('Inventory', [],function($interpolateProvider){
		$interpolateProvider.startSymbol('<%');
		$interpolateProvider.endSymbol('%>');
	});

	app.controller('InventoryOOSReportCtrl', function ($scope,$http,$filter){
		
		$scope.changeDate = function () {
			$scope.startDate = $scope.endDate = null;
		}

		$scope.productIds = angular.fromJson(<?php echo $productsData;?>);

		$scope.loadOOSByPeriodChart = function(productId,startDate,endDate,flag){

			if(flag == "oos_report_date_range"){
				startDate = $scope.startDate.split("/")[2]+"-"+$scope.startDate.split("/")[0]+"-"+$scope.startDate.split("/")[1];
				endDate = $scope.endDate.split("/")[2]+"-"+$scope.endDate.split("/")[0]+"-"+$scope.endDate.split("/")[1];
			}
			
			var requestPayload = {
				productId: $("#productId").val(),
				startDate: startDate,
				endDate: endDate,
				flag: flag,
			};
			
			$scope.stackCheckingDataset = [];
			$scope.stackCheckingCategoryList = [];
			var oosRepObj = {};
			var req=$http.post('/inventory/getOOSReportChartData',requestPayload);
			req.success(function(response){

				if(response.status == 1 && response.chartResult && response.gridResult){
					
					// Chart Preparation Recipe - Begin
					angular.forEach(response.chartResult, function(values, keys){
        				for (var i = 0; i <= values.length-1; i++) {
        					oosRepObj[values[i].name]  = new Array();
    					}
        			});
        			angular.forEach(response.chartResult, function(values, keys){
    					$scope.stackCheckingCategoryList.push({"label": keys});
    					angular.forEach(values, function(seriesValues, seriesKeys){
    						var temp = JSON.stringify(seriesValues.values, null, 4).replace(/[{""}]/g, '');
							temp = temp.replace(/[\[\]']+/g, '<br/>');
							temp = temp.replace(/,/g, '<br/>');
							temp = temp.replace(/tool_tip: null/g, '');
							temp = temp.replace(/count_value/g, 'Value');
							temp = temp.replace(/count/g, 'Count');
    						oosRepObj[seriesValues.name].push({
								'value' :seriesValues.sale_loss,
								tooltext: seriesValues.name +
									"{br}{br}OOS:" + seriesValues.OOS +
									"{br}Sale Loss:" + seriesValues.sale_loss +
									"{br} " + temp,
    						});
    					});
        			});
        			for(let k in oosRepObj){
        				$scope.stackCheckingDataset.push({
        					"seriesname": k,
	                		"data": oosRepObj[k]
	        			});
	        		}
					// multi series column
					var stackCheckingProperties = {
						type: 'msline',
				        renderAt: 'oos_line_chart',
				        width: '100%',
				        height: '550',
				        dataFormat: 'json',
				        dataSource: {
				            "chart": {
				                //"numberPrefix": "₹",
				                showvalues: "0",
				                rotateValues: "1",
	                			placeValuesInside: "1",
	                			caption: 'Out of Stock Report',
	                			yaxisname: 'Value',
	                			xaxisname: 'Date',
				                exportEnabled: "1",
				        		exportFormats: "PNG=Export as PNG|PDF=Export as PDF|XLS=Export as CSV",
	    						exportTargetWindow: "_self",
	    						toolTipBorderColor: "#666666",
						        toolTipBgColor: "#666666",
						        toolTipColor: "#ffffff"/*,
						        legendScrollBgColor : "#cccccc",
	            				legendScrollBarColor: "#999999"*/
				                //plottooltext: "$seriesname <br/>Count: $datavalue <br/>Value: $displayValue"
				            },
				            "categories": [
				                {
				                    "category": $scope.stackCheckingCategoryList
				                }
				            ],
				            "dataset": $scope.stackCheckingDataset
						}
					}
					var checkingStackedChart = new FusionCharts(stackCheckingProperties);
					checkingStackedChart.render();

					// Chart Recipe - End
					// igGrid Recipe - Begin

					var reg = /^-?\d+\.?\d*$/;
					var dateReg = /^\d{4}\-\d{2}\-\d{2}$/;
					var oosGridHeader = [];
					var oosGridSummaries = [];
					var oosGridDefs = [];
					var oosGridDefsTest = [];
					var columnKeys = [];
					angular.forEach(response.gridResult[0],function(val,key){
						
						var result = reg.test(val);
                        var width = 150;
                        var format = "";
        				var	columnCssClass = "";        					
        				var	headerCssClass = "";
						var type = typeof(val);
        				
        				var dateResult = dateReg.test(val);

        				if(type === "number" || result){
        					
        					columnCssClass = "alignRight";
							headerCssClass = "headerAlignRight";
							width = 100;

        					if(key == "Product_Id" || key == "Mobile_No"){
        						type = 'string';
        						format = '';
        					}else{
        						type = 'number';
								format = "0.00";
        					}

        				}else if(dateResult){
							type = "date";
							format = "dd/MM/yyyy";
                            width = 100;
                            columnCssClass = "alignLeft";
        					headerCssClass = "";
        				}
						// Summaries Cols
        				if(type == "number" && key != "MRP" && key != "Product_Id" && key != "Mobile_No"){
        					oosGridSummaries.push({
			                    columnKey: key,
			                    allowSummaries: true,
			                    summaryOperands: [{
			                        "rowDisplayLabel": "",
			                        "type": "SUM",
			                        "active": true
			                    }]
			                });
        				}else{
        					oosGridSummaries.push({
			                    columnKey: key,
			                    allowSummaries: false,
			                });
        				}
        				var headerText = key;
						headerText = headerText.replace(/_/g, " ");
						oosGridDefs.push({ 
							headerText:headerText, key: key, dataType: type, columnCssClass: columnCssClass, width: width, headerCssClass: headerCssClass,format:format});
				        oosGridDefsTest.push({ name:key, type: type, columnCssClass: columnCssClass, width: width, headerCssClass: headerCssClass,format:format});
				        var colTemop = { columnKey: key, columnCssClass: columnCssClass, width: width, headerCssClass: headerCssClass, allowSummaries: true, summaryOperands:oosGridSummaries };
						columnKeys.push(colTemop);
					});
					// Ignite Grid For OOS Report
				$(function () {
					var f = true, ds, schema;

					schema = new $.ig.DataSchema("array", {
						fields: oosGridDefsTest,
					});
					ds = new $.ig.DataSource({
						dataSource: response.gridResult,
						schema: schema,
						filtering: {
							type: "local"
						}
					}).dataBind();
					createSimpleFilteringGrid(f, ds);
				});

				function createSimpleFilteringGrid(f, ds) {
					var features = [
							{
								name: "Paging",
								type: "local",
								pageSize: 10
							},
							{
				                name: "Sorting",
				                type: "local",
				                persist: true
				            },
				            {
				            	name: "Summaries",
				            	columnSettings:  oosGridSummaries, 
        						defaultDecimalDisplay: 0
				            },
				            {
				            	name: 'Resizing',
				            }
					];

					if (f) {
						features.push({
							name: "Filtering",
							type: "local",
							mode: "simple",
							filterDialogContainment: "window",
							columnSettings: columnKeys
						});
					}
				if ($("#filterByText").length &&  $("#searchField").data("igTextEditor")) {
						$("#filterByText").igTextEditor("destroy");
						$("#filterByText").remove();
						$("#searchLabel").remove();
				}
				if ($("#oosGrid").data("igGrid")) {
					$("#oosGrid").igGrid("destroy");
				}
				$("#oosGrid").igGrid({
					autoGenerateColumns: true,
					height: "500px",
					width: "100%",
					columns: oosGridDefs,
					dataSource: response.gridResult,
					features: features,
					dataRendered: function() {
						oosGridDefs.forEach(function(item,index){
							if(item.dataType == "number"){
								$("#oosGrid_summaries_footer_row_icon_container_sum_"+item.key).remove();
								var numberText = $("#oosGrid_summaries_footer_row_text_container_sum_"+item.key).text();
								$("#oosGrid_summaries_footer_row_text_container_sum_"+item.key).text(numberText.substr(3));
							}
						});
			        },
				});
			}
				}else if(response.status == 0){
					$("#alertDiv").show();
					$("#errorMessage").text(response.message);
					$("#alertDiv").delay(3000).fadeOut(350);
				}
			});
		}
	});
</script>
@stop
@extends('layouts.footer')