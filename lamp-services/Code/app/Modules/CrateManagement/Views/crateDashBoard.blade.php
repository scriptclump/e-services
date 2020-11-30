@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    Crate Dashboard
                </div>
                @if(isset($access) && $access==1)
                <a class="btn green-meadow" data-toggle="modal" data-target="#create_management" id="createmanagement" style="float: right;margin: 6px">Add</a> <span data-placement="top"></span> 
                @endif
                @if(isset($impertAccess) && $impertAccess==1)
                <a class="btn green-meadow" data-toggle="modal" data-target="#upload_create" id="upload_create_id" style="float: right;margin: 6px">Import Crate File</a> <span data-placement="top"></span>
                @endif
                @if(isset($transferAccess) && $transferAccess==1)
                <a class="btn green-meadow" data-toggle="modal" data-target="#transfer_create" id="transfer_create_id" style="float: right;margin: 6px">Crate Transfer</a> <span data-placement="top"></span>
                @endif
            </div>
            <div id="success_message_ajax"></div>
            <div class="portlet-body">
                <div>
                    <span class="caption-subject bold font-blue uppercase"> Filter By :</span>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="all_btn">All <span id="all_count"></span></a>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="137001_btn">Available <span id="137001_count"></span></a>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="137009_btn">Ready To Dispatch <span id="137009_count"></span></a>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="137002_btn">SIT DC to HUB <span id="137002_count"></span></a>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="137006_btn">Crate In HUB <span id="137006_count"></span></a>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="137003_btn">SIT HUB to DC <span id="137003_count"></span></a>&nbsp;&nbsp;&nbsp;
                    <a class="inactive"  id="137007_btn">Crate In DC <span id="137007_count"></span></a>
                </div>
                <div class="table-scrollable">
                    <table id="grid"></table>
                </div>
            </div>
            <div class="modal modal-scroll fade" id="crate_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel"><b>Crate Details</b></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet box">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label"><b>Order Id: </b>&nbsp;&nbsp;<span id="order_id"></span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label"><b>Order Code: </b>&nbsp;&nbsp;<span id="order_code"></span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label"><b>Order Date: </b>&nbsp;&nbsp;<span id="order_date"></span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label"><b>Warehouse Name: </b>&nbsp;&nbsp;<span id="wh_name"></span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label"><b>Hub Name: </b>&nbsp;&nbsp;<span id="hub_name"></span></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label"><b>Order Status: </b>&nbsp;&nbsp;<span id="order_status"></span></label>
                                                    </div>
                                                </div>
                                                <table id="container_table" class="table">
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="modal modal-scroll fade" id="crate_edit_Details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel"><b>Edit Crate Details</b></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet box">
                                        <div class="portlet-body">
                                        {{ Form::open(array('url' => 'cratemanagement/updatecreateCrate', 'id' => 'up_crateManagement_id'))}}
                                            <div class="row">
                                                <input type="hidden" name="crate_id" id='crate_id'>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                     <label class="control-label">Warehouse Name<span class=""></span></label>
                                                     <input type="text" name="warehouse_name_crate" id="warehouse_name_crate" class="form-control" readonly="readonly">
                                                  </div>
                                                </div>
                                                <div class="col-md-4">
                                                  <div class="form-group">
                                                     <label class="control-label">Warehouse Name<span class="required">*</span></label>
                                                     <select name = "up_le_wh_id" id="up_le_wh_id" class="form-control select2me">
                                                        <option value="">Please Select</option>
                                                        @foreach($warehouseName as $name)
                                                        <option value = "{{$name->le_wh_id}}">{{$name->name}}</option>
                                                        @endforeach              
                                                     </select>
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                               <div class="col-md-12 text-center">
                                                  <div class="form-group">
                                                     <button type="submit"  class="btn green-meadow">Submit</button>
                                                  </div>
                                               </div>
                                            </div>
                                            {{ Form::close() }}
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="create_management" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="management_grid_id">Add Crate Code</h4>
         </div>
         <div class="modal-body" id="create_management">
            <div class="row">
               <div class="col-md-12">
                  <div class="portlet box">
                     <div class="portlet-body">
                        {{ Form::open(array('url' => 'cratemanagement/createCrate', 'id' => 'add_management_id'))}}
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label class="control-label">Warehouse Name<span class="required">*</span></label>
                                 <select name = "warehouse_id" id="warehouse_id" class="form-control select2me">
                                    <option value="">Please Select</option>
                                    @foreach($warehouseName as $name)
                                    <option value = "{{$name->le_wh_id}}">{{$name->name}}</option>
                                    @endforeach              
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label class="control-label">Crate Code<span class="required">*</span></label>
                                 <input type="text" name="crate_code_val" id="crate_code_val" class="form-control" autocomplete="off" onkeydown="removecrateerrormsg();">
                                 <span id="crate_error" style="color:red"></span>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12 text-center">
                              <div class="form-group">
                                 <button type="submit"  class="btn green-meadow" id="add_management_submit">Submit</button>
                              </div>
                           </div>
                        </div>
                        {{ Form::close() }}                                        
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<div id="upload_create" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                 <h4 class="modal-title">Import Crate Code Template</h4>
            </div>
             <div class="modal-body">
                <br>
                <form id="download_pack_excel" action="/cratemanagement/downloadExcel">
                    <br>                   
                    <div class="row">
                        <div class="col-md-12 " align="center"> 
                            <button id="download_crate_code_temp" role="button" class="btn green-meadow">
                                Download Crate Code Template</button>
                            <p class="topmarg"></p>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center" align="center">
                        <form id='import_template_crate_code' action="{{ URL::to('/cratemanagement/uploadCrateCodeExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                            <div class="fileUpload btn green-meadow"> <span id="up_text"> Upload Crate Code Template</span>
                                <input type="file" class="form-control upload" name="upload_crate_code_template" id="upload_crate_code_template"/>
                            </div>
                            <span class="loader" id="packloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/><br/>
                            <p class="topmarg"></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_messages">Show errors</a>
<div id="import_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <table class='cratecode_success_msg'>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="transfer_create" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                 <h4 class="modal-title">Crate Transfer</h4>
            </div>
             <div class="modal-body">
                <form id="download_cratetrans" action="/cratemanagement/downloadCrate">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p id="err_msg" style="color:#d65b60; display: none;">Please select a warehouse</p>
                                <label class="control-label">From Warehouse<span class="required">*</span></label>
                                <select name = "from_le_wh_id" id="from_le_wh_id" class="form-control select2me">
                                    <option value="">Please Select</option>
                                    @foreach($warehouseName as $name)
                                        <option value = "{{$name->le_wh_id}}">{{$name->name}}</option>
                                    @endforeach              
                                </select>
                            </div>
                        </div>
                    </div>                 
                    <div class="row">
                        <div class="col-md-12 " align="center"> 
                            <button id="download_crate_transfer_temp" role="button" class="btn green-meadow">
                                Download Crate Transfer Template</button>
                            <p class="topmarg"></p>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center" align="center">
                        <form id='import_template_crate_transfer' action="{{ URL::to('/cratemanagement/uploadCrateTransferExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                          <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                                <p id="to_err_msg" style="color:#d65b60; display: none;">Please select the warehouses</p>
                                <label class="control-label">To Warehouse<span class="required">*</span></label>
                                <select name = "to_le_wh_id" id="to_le_wh_id" class="form-control select2me">
                                    <option value="">Please Select</option>
                                    @foreach($warehouseName as $name)
                                        <option value = "{{$name->le_wh_id}}">{{$name->name}}</option>
                                    @endforeach              
                                </select>
                            </div>
                        </div></div>
                            <div class="fileUpload btn green-meadow"> <span id="up_text"> Upload Crate Transfer Template</span>
                                <input type="file" class="form-control upload" name="upload_crate_transfer_template" id="upload_crate_transfer_template"/>
                            </div>
                            <span class="loader" id="packloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/><br/>
                            <p class="topmarg"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_cp_messages">Show errors</a>

<div id="import_cp_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <table class='cratetransfer_success_msg'>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @stop
    @section('userscript')
    @include('includes.validators')
    <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
    <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
    <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
    <!-- Ignite UI Required Combined CSS Files -->
    <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
    <!--Ignite UI Required Combined JavaScript Files-->
    <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>


    <style>
        .inactive {
            font-size:14px;
        }
        th {
            background-color:#d3d3d3;
            color: white;
        } 
    </style>

    @extends('layouts.footer')

    <script>
    $("#download_crate_transfer_temp").click(function(e){
        e.preventDefault();
        $("#err_msg").hide();
        var from_le_wh_id = $("#from_le_wh_id").val();
        if(from_le_wh_id == 0 || from_le_wh_id == null)
        {
            $("#err_msg").show();
        }else{
            $("#download_cratetrans").submit();
        }
    });
    $('#upload_crate_transfer_template').change(function(e){
    $('#import_template_crate_transfer').submit();
    });

  $('#import_template_crate_transfer').submit(function(e){
    e.preventDefault();
    var csrf_token = $('#csrf-token').val();
    var formData = new FormData($(this)[0]);
    var url = $(this).attr('action');
    $.ajax({
      headers: {'X-CSRF-TOKEN': csrf_token},
      url: url,
      type: 'POST',
      data: formData,
      async: false,
      beforeSend: function(xhr) {
      },
      success: function (data) {
        $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
        $(".alert-success").fadeOut(20000);
        window.setTimeout(function(){window.location.reload()}, 10000);
      },
      cache: false,
      contentType: false,
      processData: false
    });
    $('#transfer_create').modal('toggle');
  });
  $('#transfer_create').on('hidden.bs.modal', function () {
        $('#from_le_wh_id').select2('val', '');
        $('#to_le_wh_id').select2('val', '');    
      });

    $('#crate_details').on('shown.bs.modal', function (e){
        var cratecode = $(e.relatedTarget).data('cratecode');

        $.ajax({
            url: '/cratemanagement/cratedetails',
            type: 'POST',
            data: {'crate_code': cratecode},
            success: function (response) {
                $.each(response, function (key, val) {
                    $("#order_id").html(val.gds_order_id);
                    $("#order_code").html(val.order_code);
                    $("#order_date").html(val.order_date);
                    $("#wh_name").html(val.wh_name);
                    $("#hub_name").html(val.hub_name);
                    $("#order_status").html(val.order_status);
                    
                    $("#container_table").html("");
                    for (var conkey in val.containers) {
                        $("#container_table").append('<tr><td style = "background-color:#d3d3d3; color: black"><b>Container Barcode:</b> ' + val.containers[conkey].container_barcode +'</td>\n\
                        <td style = "background-color:#d3d3d3; color: black"><b>Crate Status:</b> ' + val.containers[conkey].status + '</td>\n\
                        <td style = "background-color:#d3d3d3; color: black"><b>Weight:</b> ' + val.containers[conkey].weight / 1000 + ' kg</td></tr>\n\
                        <tr><td colspan = "3"><table id = "itable_' + conkey + '" class = "table"></table></td></tr>');
                        
                        $("#itable_" + conkey).html('<tr><td><b>Product Title</b></td><td><b>Mrp</b></td><td><b>Quantity</b></td></tr>');
                        var productsList = val.containers[conkey].products;
                        for(var prodKey in productsList){
                            $("#itable_" + conkey).append('<tr><td>' + productsList[prodKey].product_title + '</td><td>' + productsList[prodKey].mrp + '</td><td>' + productsList[prodKey].picked_qty + '</td></tr>');
                        }
                    }
                });
            }
        });

    });
    
    $(document).ready(function () {
        display("137001");
        activeTab("137001_btn");

        $("#all_btn").click(function () {
            activeTab("all_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("all");
        });

        $("#137001_btn").click(function () {
            activeTab("137001_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("137001");
        });

        $("#137009_btn").click(function () {
            activeTab("137009_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("137009");
        });
        
        $("#137002_btn").click(function () {
            activeTab("137002_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("137002");
        });
        
        $("#137006_btn").click(function () {
            activeTab("137006_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("137006");
        });
        
        $("#137003_btn").click(function () {
            activeTab("137003_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("137003");
        });
        
        $("#137007_btn").click(function () {
            activeTab("137007_btn");
            
            if ($.trim($("#grid").html()) != '') {
                $("#grid").igGrid("destroy");
            }

            display("137007");
        });
    });

    function display(tabId){
        statusCount();
        
        var columns;
        if(tabId === "all" || tabId === "137001" || tabId === "137009" || tabId === "137001" || tabId === "137007"){
            columns = [
                {headerText: "Crate Code", key: "crate_code", dataType: "string", width: "120px"},
                {headerText: "Status", key: "status", dataType: "string", width: "90px"},
                {headerText: "Transaction Status", key: "transaction_status", dataType: "string", width: "140px"},
                {headerText: "Warehouse Name", key: "warehouse_name", dataType: "string", width: "120px"},
                {headerText: "Last Order Id", key: "last_order_id", dataType: "number", width: "100px"},
                {headerText: "Last Order Code", key: "last_order_code", dataType: "string", width: "120px"},
                {headerText: "Last Order Status", key: "last_order_status", dataType: "string", width: "160px"},
                {headerText: "Picker Name", key: "picker_name", dataType: "string", width: "150px"},
                {headerText: "Delivery Executive", key: "de_name", dataType: "string", width: "150px"},
                {headerText: "Actions", key: "actions", dataType: "string", width: "80px"}
            ];
        } else if(tabId === "137006") {
            columns = [
                {headerText: "Crate Code", key: "crate_code", dataType: "string", width: "120px"},
                {headerText: "Status", key: "status", dataType: "string", width: "90px"},
                {headerText: "Transaction Status", key: "transaction_status", dataType: "string", width: "140px"},
                {headerText: "Hub Name", key: "hub_name", dataType: "string", width: "120px"},
                {headerText: "Last Order Id", key: "last_order_id", dataType: "number", width: "100px"},
                {headerText: "Last Order Code", key: "last_order_code", dataType: "string", width: "120px"},
                {headerText: "Last Order Status", key: "last_order_status", dataType: "string", width: "160px"},
                {headerText: "Picker Name", key: "picker_name", dataType: "string", width: "150px"},
                {headerText: "Delivery Executive", key: "de_name", dataType: "string", width: "150px"},
                {headerText: "Actions", key: "actions", dataType: "string", width: "80px"}                              
            ];
        } else {
            columns = [
                {headerText: "Crate Code", key: "crate_code", dataType: "string", width: "120px"},
                {headerText: "Status", key: "status", dataType: "string", width: "90px"},
                {headerText: "Transaction Status", key: "transaction_status", dataType: "string", width: "140px"},
                {headerText: "Last Order Id", key: "last_order_id", dataType: "number", width: "100px"},
                {headerText: "Last Order Code", key: "last_order_code", dataType: "string", width: "120px"},
                {headerText: "Last Order Status", key: "last_order_status", dataType: "string", width: "160px"},
                {headerText: "Picker Name", key: "picker_name", dataType: "string", width: "150px"},
                {headerText: "Delivery Executive", key: "de_name", dataType: "string", width: "150px"},
                {headerText: "Actions", key: "actions", dataType: "string", width: "80px"}
            ];
        }
        
        $("#grid").igGrid({
            primaryKey: "ordercode",
            dataSource: '/cratemanagement/getbytransactionstatus?type=' + tabId,
            columns: columns,
            responseDataKey: "results",
            features: [
                {
                    name: "Sorting",
                    sortingDialogContainment: "window"
                },
                {
                    name: 'Paging',
                    type: 'local',
                    pageSize: 10
                },
                {
                    name: "Filtering", 
                    allowFiltering: true,
                    type: "local",
                    mode: "simple",
                    columnSettings: [
                        {columnKey: "actions", allowFiltering: false}
                    ]
                }
            ],
            width: '100%',
            height: '500px'
        });
    }

    function statusCount(){
        $.ajax({
            url: '/cratemanagement/statuscount',
            type: 'GET',
            success: function (response) {
                $("#all_count").html("(" + response.all_crates + ")");
                $("#137001_count").html("(" + response.available + ")");
                $("#137009_count").html("(" + response.rtd + ")");
                $("#137002_count").html("(" + response.sit_dc_hub + ")");
                $("#137006_count").html("(" + response.crate_in_hub + ")");
                $("#137003_count").html("(" + response.sit_hub_dc + ")");
                $("#137007_count").html("(" + response.crate_in_dc + ")");
            }
        });
    }
    
    function activeTab(tabId){
        if(tabId === "all_btn"){
            $("#all_btn").css("color", "#5b9bd1");
            $("#137001_btn").css("color", "#999");
            $("#137009_btn").css("color", "#999");
            $("#137002_btn").css("color", "#999");
            $("#137006_btn").css("color", "#999");
            $("#137003_btn").css("color", "#999");
            $("#137007_btn").css("color", "#999");
        } else if(tabId === "137001_btn"){
            $("#all_btn").css("color", "#999");
            $("#137001_btn").css("color", "#5b9bd1");
            $("#137009_btn").css("color", "#999");
            $("#137002_btn").css("color", "#999");
            $("#137006_btn").css("color", "#999");
            $("#137003_btn").css("color", "#999");
            $("#137007_btn").css("color", "#999");
        } else if(tabId === "137009_btn"){
            $("#all_btn").css("color", "#999");
            $("#137001_btn").css("color", "#999");
            $("#137009_btn").css("color", "#5b9bd1");
            $("#137002_btn").css("color", "#999");
            $("#137006_btn").css("color", "#999");
            $("#137003_btn").css("color", "#999");
            $("#137007_btn").css("color", "#999");
        } else if(tabId === "137002_btn"){
            $("#all_btn").css("color", "#999");
            $("#137001_btn").css("color", "#999");
            $("#137009_btn").css("color", "#999");
            $("#137002_btn").css("color", "#5b9bd1");
            $("#137006_btn").css("color", "#999");
            $("#137003_btn").css("color", "#999");
            $("#137007_btn").css("color", "#999");
        } else if(tabId === "137006_btn"){
            $("#all_btn").css("color", "#999");
            $("#137001_btn").css("color", "#999");
            $("#137009_btn").css("color", "#999");
            $("#137002_btn").css("color", "#999");
            $("#137006_btn").css("color", "#5b9bd1");
            $("#137003_btn").css("color", "#999");
            $("#137007_btn").css("color", "#999");
        } else if(tabId === "137003_btn"){
            $("#all_btn").css("color", "#999");
            $("#137001_btn").css("color", "#999");
            $("#137009_btn").css("color", "#999");
            $("#137002_btn").css("color", "#999");
            $("#137006_btn").css("color", "#999");
            $("#137003_btn").css("color", "#5b9bd1");
            $("#137007_btn").css("color", "#999");
        } else if(tabId === "137007_btn"){
            $("#all_btn").css("color", "#999");
            $("#137001_btn").css("color", "#999");
            $("#137009_btn").css("color", "#999");
            $("#137002_btn").css("color", "#999");
            $("#137006_btn").css("color", "#999");
            $("#137003_btn").css("color", "#999");
            $("#137007_btn").css("color", "#5b9bd1");
        }
    }
    $('#add_management_id').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
         warehouse_id: {
                validators: {
                    notEmpty: {
                        message: 'Select Warehouse Name'
                    },
                }
            },
        crate_code_val: {
            validators: {
                notEmpty: {
                    message: 'Please Enter Crate Code'
                },

            }
        },
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#add_management_id').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/cratemanagement/createCrate',
        data: frmData,
        dataType:'json',
        success: function (respData){
            console.log(respData.status);
            console.log(respData.message);
            if(respData.status==200) {
                $('#add_management_id').formValidation('resetForm', true);            
                $('.close').trigger('click');
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData.message+'</div></div>');
                $(".alert-success").fadeOut(1000);
                display("137001");
                activeTab("137001_btn");
                location.reload();
         }else if(respData.status==404){
            document.getElementById("crate_error").innerHTML=respData.message;//css('display','block');
            $('#add_management_submit').removeAttr('disabled');
            $('#add_management_submit').removeClass("disabled");
         }else{
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Something Went Wrong Please Try Again</div></div>');
            $(".alert-success").fadeOut(1000);
         }   
        }
    });
});

$('#createmanagement').on('click',function(){
    $('#add_management_id').formValidation('resetForm', true);
    $("#add_management_id")[0].reset();
    $('#warehouse_id').select2('val','');            
});

$('#crate_edit_Details').on('shown.bs.modal', function (e){
    var token  = $("#csrf-token").val();
    var crate_id = $(e.relatedTarget).data('crateid');
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        url: '/cratemanagement/crateeditdetails',
        type: 'POST',
        data: {'crate_id': crate_id},
            success: function (response) {
            $.each(response, function (key, val) {
                $("#crate_id").val(val.crate_id);
                $("#status").val(val.status);
                $("#warehouse_name_crate").val(val.warehouse_name);
                $("#le_wh_id").val(val.le_wh_id);
                });
            }
    });
});

$('#up_crateManagement_id').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
         up_le_wh_id: {
                validators: {
                    notEmpty: {
                        message: 'Select Warehouse Name'
                    },
                }
            },
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#up_crateManagement_id').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/cratemanagement/updatecreateCrate',
        data: frmData,
        dataType:"json",
        success: function (respData){
            if(respData.status==200) {
                $('#up_le_wh_id').select2('val','');
                $('#up_crateManagement_id').formValidation('resetForm', true);            
                $('.close').trigger('click');
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Succesfully Updated</div></div>');
                $(".alert-success").fadeOut(3000);
                display("137001");
                activeTab("137001_btn");
                location.reload();
         }else{
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Something Went Wrong Please Try Again</div></div>');
            $(".alert-success").fadeOut(3000);
         }   
        }
    });
});

$('#crate_edit_Details').on('hidden.bs.modal', function () {
       $('#up_le_wh_id').select2('val','');            

});

$('#createmanagement').on('click', function () {
        $('#add_management_id').formValidation('resetField', 'crate_code_val');
});
function removecrateerrormsg(){
    document.getElementById("crate_error").innerHTML='';//css('display','none');
}

$("#download_template_button_code").click(function(e){
    e.preventDefault();
    $("#download_crate_form").submit();
});


    $('#upload_crate_code_template').change(function(e){
    $('#import_template_crate_code').submit();
    });


    $('#import_template_crate_code').submit(function(e){
    e.preventDefault();
    var csrf_token = $('#csrf-token').val();
    var formData = new FormData($(this)[0]);
    //$('#pimloader').show();
    var url = $(this).attr('action');
    $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            beforeSend: function(xhr) {
            //$('#pimloader').show();
            },
            success: function (data) {
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
                $(".alert-success").fadeOut(20000)   
            },
            cache: false,
            contentType: false,
            processData: false
    });
    $('#upload_create').modal('toggle');
    });

</script>
</div>
</body>
</html>

@stop
@extends('layouts.footer')