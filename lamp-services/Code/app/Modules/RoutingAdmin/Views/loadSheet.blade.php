<!doctype html>
<html ng-app="sheetdwnload">

<head>
    <meta charset="utf-8">
    <title>LoadSheet</title>

    <style>
       
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body ng-controller="loadSheetCtrl" class="ng-cloak">

    <div id="loadsheet" class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <div id="editor"></div>
                            <td class="title">
                                <img src="https://portal.ebutor.com/assets/admin/layout/img/logo.png">
                            </td>

                            <button type="button" id="print" class="btn pull-right print" ng-click="generatePDF()"><span class="glyphicon glyphicon-print"></span> Print</button>
                            <table class="table">
								<tr id="maindiv" class="heading">
                                    <td>Total Orders:</td>
                                    <td> <%orders.coordinates_data.length %></td>
                                    <td>Created On: </td>
                                    <td> <%date | date : mm-dd-yy%></td>
                                    <td>Delivery Executive</td>
                                    <td> <span ng-hide="de_name!=null">Not Available</span> <%de_name%> </td>
                                </tr>
                                <tr class="information">
                                    <td>Vehicle No</td>
                                    <td><span id="vehicle_no"> <%vehicleinfo.vehicle_number%></span></td>
                                    <td>Consignment weight</td>
                                    <td><span id="max_load"> <%vehicleinfo.consignmentWeight%> m³</span></td>
                                    <td>Vehicle Maximum Load</td>
                                    <td><span id="consignment"><%vehicleinfo.vehicle_max_load%> m³</span></td>
                                </tr>
                                <tr class="information">
                                    <td>Vehicle Utilisation</td>
                                    <td><span id="consignment"><% 100 - (((vehicleinfo.vehicle_max_load - vehicleinfo.consignmentWeight)/vehicleinfo.vehicle_max_load * 100)) | number:2  %>% </span></td>
                                    <td>Vehicle Crates Capacity</td>
                                    <td><span id="consignment"><% vehicleinfo.vehicle_max_load/0.060 | number:0  %> </span></td>
                                    <td>Crates Loaded</td>
                                    <td><span><%totalCrates%></span></td>
                                </tr>
                            </table>
							<hr></hr>
                            <table cellpadding="0" cellspacing="0" class="table" border-bottom="1px solid" border-top="1px solid">

                                <thead style="font-weight: bold;">

                                    <td class="information" style="width: 60px;">
                                        Sl No.
                                    </td>
                                    <td class="information" style="width: 145px;">
                                        Order Code
                                    </td>
                                    <td class="information" style="width: 150px;">
                                       Beat
                                    </td>

                                    <td class="information">
                                        #Crates
                                    </td>

                                    <td class="information">
                                        #Cartons
                                    </td>

                                    <td class="information">
                                        #Bags
                                    </td>

                                    <td class="information">
                                        Invoice Amount
                                    </td>

                                    <td class="information" style="width: 420px;">
                                        Crate's Barcode
                                    </td>

                                    

                                </thead>
                                <tbody >
	                                <tr ng-repeat="(cokey,coval) in coordinates | orderBy:'coval.coordinates.order_code':true">
	                                    <th class="information" ng-bind="cokey+1"></th>
	                                    <td class="information"><%coval.coordinates.order_code%></td>
                                        <td class="information"><%coval.coordinates.beat%></td>
                                        <td class="information" style="text-align: right;"><%coval.coordinates.crates_info.crates_count%></td>
                                        <td class="information" style="text-align: right;"><%coval.coordinates.other_info[0].cfc_count%></td>
                                        <td class="information" style="text-align: right;"><%coval.coordinates.other_info[0].bag_count%></td>
                                        <td class="information" style="text-align: right;">
                                            <%coval.coordinates.invoice_amount | currency : '' : 2%>
                                        </td>
	                                    <td class="information">
		                                    <span ng-repeat="(subkey,subval) in coval.coordinates.crates_info.crates">
		                                    <%subval%><font ng-show="!$last">,</font> 
		                                    </span>
		                                    <span ng-show="coval.coordinates.crates_info.crates_count==0">---</span>
                                        </td>
	                                </tr>
                                    </tbody>
                                </tbody>

                            </table>
                        <div ng-if="unassigned">
                            <table cellpadding="0" cellspacing="0" class="table">

                                <thead style="font-weight: bold;">

                                    <td class="information" style="width: 120px;">
                                        Sl No.(unassigned)
                                    </td>
                                    <td class="information" style="width: 145px;">
                                        Order Code
                                    </td>
                                    <td class="information" style="width: 180px;">
                                       Beat
                                    </td>

                                    <td class="information" style="margin-left: 10px!important">
                                        #Crates
                                    </td>

                                    <td class="information" style="margin-left: 10px!important">
                                        Crate's Barcode
                                    </td>

                                    

                                </thead>
                                <tbody>
                                    <tr ng-repeat="(cokey,coval) in unassigned">
                                        <th class="information"><%cokey+1%></th>
                                        <td class="information">
                                            <%coval.coordinates.order_code%>
                                        </td>
                                        <td class="information">
                                            <%coval.coordinates.beat%>
                                        </td>
                                        <td class="information">
                                            <%coval.coordinates.crates_info.crates_count%>
                                        </td>
                                        <td class="information">
                                            <span ng-repeat="(subkey,subval) in coval.coordinates.crates_info.crates">
                                            <%subval%><br>
                                            </span>
                                            <span ng-show="coval.coordinates.crates_info.crates_count==0">---</span></td>
                                        

                                    </tr>
                                </tbody>
                            </table>
                        </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <script src="https://kendo.cdn.telerik.com/2016.3.914/js/kendo.all.min.js"></script>

    <script type="text/javascript" charset="utf-8" async defer>
        var app = angular.module('sheetdwnload', [], function($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');

        });

        app.controller('loadSheetCtrl', function($scope, $http) {
            $.urlParam = function(name) {
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                if (results == null) {
                    return null;
                } else {
                    return results[1] || 0;
                }
            }
            $scope.date = new Date();
            $scope.generatePDF = function() {
                    kendo.drawing.drawDOM($("#loadsheet")).then(function(group) {
                        kendo.drawing.pdf.saveAs(group, "Loadsheet PDF.pdf");
                    });
                }
                //$.urlParam()
            $scope.orders = angular.fromJson('<?php echo json_encode($json);?>');
            $scope.de_name = $scope.orders.de_name;
            $scope.vehicleinfo = $scope.orders.vehicleInfo;
            $scope.coordinates = $scope.orders.coordinates_data;
            console.log($scope.coordinates);
            if ($scope.orders.unassigned != undefined) {
                $scope.unassigned = $scope.orders.unassigned.coordinates_data;
            }
            $scope.coordinates.reverse();
            let coords = $scope.coordinates.filter(object=>{
                if(object)
                    return object;
            })
            $scope.coordinates =coords;
            //$scope.coordinates.splice(-1,1);
            $scope.totalCrates = 0;
            angular.forEach($scope.coordinates , function(val, key) {
                $scope.totalCrates += parseInt(val.coordinates.crates_info.crates_count);
            })

        });
        app.filter('roundup', function() {
            return function(value) {
                return Math.ceil(value);

            }
        })
    </script>

</body>

</html>
