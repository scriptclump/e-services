@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<style type="text/css" media="screen">
	.collapse {
		display: block; 
	}
	.dcselect{
		margin-bottom: 20px!important;
	}
	
</style>
<div ng-app="plunker"  ng-controller="AccordionDemoCtrl">
	<select class="btn green-meadow dcselect">
  <option value="volvo">Alwal DC</option>
  <option value="saab">---</option>
  <option value="opel">---</option>
  <option value="audi">---</option>
</select>
		<accordion>
			<accordion-group ng-repeat="group in groups" heading="<%group.title%>" is-open="group.open">
		<div class="constrained" style="overflow-y:scroll;height:400px; border: 1px solid lightgray;">

			<table  class="table table-striped" id="div-to-update" infinite-scroll="myPagingFunction(upiData)"  infinite-scroll-distance="3" infinite-scroll-parent="true" infinite-scroll-disabled="busyLoadingData" infinite-scroll-container='".constrained"' >
				<thead>
					<tr>
						<th ng-click="sortType = 'orderId'; sortReverse = !sortReverse">Vehicle 
							<span ng-show="sortType == 'orderId' && !sortReverse" class="fa fa-caret-down"></span>
							<span ng-show="sortType == 'orderId' && sortReverse" class="fa fa-caret-up"></span></th>
							<th ng-click="sortType = 'customerVpa'; sortReverse = !sortReverse">Capacity
								<span ng-show="sortType == 'customerVpa' && !sortReverse" class="fa fa-caret-down"></span>
								<span ng-show="sortType == 'customerVpa' && sortReverse" class="fa fa-caret-up"></span></th>
								<th ng-click="sortType = 'amount'; sortReverse = !sortReverse">Crates
									<span ng-show="sortType == 'amount' && !sortReverse" class="fa fa-caret-down"></span>
									<span ng-show="sortType == 'amount' && sortReverse" class="fa fa-caret-up"></span></th>
									<th ng-click="sortType = 'unqTxnId'; sortReverse = !sortReverse">TransactionId
										<span ng-show="sortType == 'unqTxnId' && !sortReverse" class="fa fa-caret-down"></span>
										<span ng-show="sortType == 'unqTxnId' && sortReverse" class="fa fa-caret-up"></span></th>
										<th ng-click="sortType = 'createdAt'; sortReverse = !sortReverse">CreatedAt
											<span ng-show="sortType == 'createdAt' && !sortReverse" class="fa fa-caret-down"></span>
											<span ng-show="sortType == 'createdAt' && sortReverse" class="fa fa-caret-up"></span></th>
											<th ng-click="sortType = 'updatedAt'; sortReverse = !sortReverse">UpdatedAt
												<span ng-show="sortType == 'updatedAt' && !sortReverse" class="fa fa-caret-down"></span>
												<span ng-show="sortType == 'updatedAt' && sortReverse" class="fa fa-caret-up"></span></th>
												<th ng-click="sortType = 'status'; sortReverse = !sortReverse">Status
													<span ng-show="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
													<span ng-show="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span></th>
													<th >
													</th>

												</tr>
												<tr>
													<th>
														<input  placeholder="search for OrderId" class="input-sm form-control" type="search" ng-model="c1"/>
													</th>
													<th>
														<input  placeholder="search for customer" class="input-sm form-control" type="search" ng-model="c5"/>
													</th>
													<th>
														<input  placeholder="search for Amount" class="input-sm form-control" type="search" ng-model="c2"/>
													</th>
													<th>
														<input  placeholder="search for TransactionId" class="input-sm form-control" type="search" ng-model="c3"/>
													</th>
													<th>

													</th>
													<th>

													</th>
													<th>
														<input  placeholder="search for status" class="input-sm form-control" type="search" ng-model="c4"/>
													</th>
												</tr>

											</thead>
											<div>


												<tbody>
													<tr >
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>														
														
														</tr>

												</tbody>

											</div>

										</table>
										
										</div>

									
			</accordion-group>    
		</accordion>
		
	</div>
	<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular.js"></script>
	<script src="http://angular-ui.github.com/bootstrap/ui-bootstrap-tpls-0.2.0.js"></script>
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">

	<script  type="text/javascript">
		var app = angular.module('plunker', ['ui.bootstrap'],function($interpolateProvider){
			$interpolateProvider.startSymbol('<%');
			$interpolateProvider.endSymbol('%>');
		});


		function AccordionDemoCtrl($scope) {

			$scope.groups = [
			{
				title: "Ramanthapur Hub",
				content: "Dynamic Group Body - 1",
				open: false
			},
			{
				title: "Uppal Hub",
				content: "Dynamic Group Body - 2",
				open: false
			}
			];

			$scope.addNew = function() {
				$scope.groups.push({
					title: "New One Created",
					content: "Dynamically added new one",
					open: false
				});
			}

		}

	</script>

	@stop
	@extends('layouts.footer')