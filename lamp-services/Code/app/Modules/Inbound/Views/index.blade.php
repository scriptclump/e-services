<?php
$url = $_SERVER['REQUEST_URI'];
//echo $url;
$urldata = explode('/', $url);
$statuses = array('Pending', 'Completed', 'Cancelled', '');
//$filter = $urldata[count($urldata)-1];
//echo $urldata;
?>
@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<style type="text/css">
    .actionss{padding-left: 22px !important;}
    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}
</style>
<div id="cancle_loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height:650px;">
            <div class="portlet-title">
                <div class="caption">
                    MANAGE INBOUND
                </div>
                <div class="tools">
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">

                    <span class="badge bg-blue"><a  class="fullscreen" data-toggle="tooltip" title="Hi, This is help Tooltip!" style="color:#fff;"><i class="fa fa-question"></i></a></span>



                </div>
            </div>
            <div class="portlet-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="caption">

                            <span class="caption-subject bold font-blue uppercase"> Filter By :</span>
                            <span class="caption-helper sorting">
                                <a href="javascript:;" onclick = "filterdata('All')" class="<?php if (!in_array('Pending', $urldata) && !in_array('Completed', $urldata) && !in_array('Cancelled', $urldata)) { ?> active <?php } else { ?>inactive <?php } ?>">All<span id="allrecords-count"></span></a> &nbsp;&nbsp;
                                <a href="javascript:;" onclick = "filterdata('Pending')" class="<?php if (in_array('Pending', $urldata)) { ?> active <?php } else { ?>inactive <?php } ?>">Pending<span id="allrecords-pending-count"></span></a> &nbsp;&nbsp;
                                <a href="javascript:;" onclick = "filterdata('Completed')" class="<?php if (in_array('Completed', $urldata)) { ?> active <?php } else { ?>inactive <?php } ?>">Completed<span id="allrecords-completed-count"></span></a> &nbsp;&nbsp;
                                <a href="javascript:;" onclick = "filterdata('Cancelled')" class="<?php if (in_array('Cancelled', $urldata)) { ?> active <?php } else { ?>inactive <?php } ?>">Cancelled<span id="allrecords-cancelled-count"></span></a> &nbsp;&nbsp;
                                <!-- <a href="#" class="inactive">Date</a>  -->
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 pull-right text-right">
                        <a href="/inbound/add" class="btn green-meadow">Create Inbound</a>
                        <a href="" class="btn green-meadow">Upload Consigment</a>
                    </div>
                </div>

                <div class="table-scrollable">

    <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Consignment Detail</h4>
      </div>
      <div class="modal-body">
        
        <div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Consignment Id</label>
<input type="text" id="inward_request_Id" class="form-control" placeholder="178" disabled>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">STN Number</label>
<input type="text" id="stn_number" class="form-control" placeholder="STN1234" disabled>
</div>
</div>
</div>


<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Pickup Address</label>
<textarea rows="4" id="warehouse_name" class="form-control" placeholder="Sunera Technoligies, Nsl Arena" disabled>
</textarea>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Delhivery Address</label>
<textarea rows="4" id="deliveryaddress" class="form-control" placeholder="Sunera Technoligies, Nsl Arena" disabled>
</textarea>
</div>
</div>


                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="scroller" style="height: 250px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
                                                <div id='loadingmessage' style='display:none'>
                                                    <img src='http://fbelpdev.ebutor.com/assets/admin/layout/img/loading.gif'/> </div>

                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-advance table-hover" id="details">
                                                        <thead>
                                                            <tr>
                                                                <th>S.NO</th>
                                                                <th>Seller SKU</th>
                                                                <th>Image</th>
                                                                <th>Quantity</th>
                                                                <th>Name</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="bodydata"></tbody>
                                                    </table>

                                                </div>
                                            </div>


                                        </div>
                                    </div>







                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                </div>
                            </div>
                        </div>
                    </div>


                    <table id="grid"></table>
                    <table id="filtered-data"></table>

                    <!-- 
                    <div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                    <table class="table table-striped table-advance table-hover">
                    <thead>
                    <tr>
                    
                    <th>Consigment ID</th>
                    <th>Created Date</th>
                    <th>Quantity</th>
                    <th>Drop Schedule</th>
                    <th>Consigment Status</th>
                    <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>Cons-7878675646</td>
                    <td>Feb 02, 2016</td>
                    <td>40</td>
                    <td>Not Schedule</td>
                    <td>Created</td>
                    <td class="actionss"><code><i class="fa fa-times"></i></code></td>
                    </tr>
                    
                    </tbody>
                    </table>
                    </div>
                    -->

                </div>

            </div>
        </div>
        <!-- END PORTLET-->
    </div>

</div>



@stop

@section('userscript')
<style type="text/css">
    #loadingmessage{ z-index: 9999999999 !important; position: relative; top: 50% !important; left: 50% !important;}
    
    /*/ Absolute Center Spinner /*/
    .loading {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /*/ Transparent Overlay /*/
    .loading:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }

    /*/ :not(:required) hides these rules from IE9 and below /*/
    .loading:not(:required) {
        /*/ hide "loading..." text /*/
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /*/ Animation /*/

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
</style>
<style>
.numericAlignment {text-align: center;}
</style>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>

@extends('layouts.footer')

<script>
    $(function () {
        var token = $("#take").val();

        $.ajax({
            url: "/inbound/getAllCountHere?_token=" + token,
            type: "POST",
            dataType: "json",
            success: function (data)
            {
                $("#allrecords-count").html(" [" + data.allRecordscount + "]");
                $("#allrecords-cancelled-count").html(" [" + data.allCancelRecordscount + "]");
                $("#allrecords-completed-count").html(" [" + data.allCompletedRecordscount + "]");
                $("#allrecords-pending-count").html(" [" + data.allPendingRecordscount + "]");
            },
        });

        var url = window.location.href;
        var urlArr = url.split("/");
        var status = urlArr[5];
        if (status == null)
        {
            status == "";
        }

        //$("#grid").find(".ui-iggrid-filtericondoesnotcontain").closest("span").remove();

        $("#grid").igGrid({
            dataSource: '/inbound/searchorderwise/' + status,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            width: "100%",
            columns: [
                {headerText: "Consigment ID", key: "PrimaryKEY", dataType: "number", width: "15%"},
                {headerText: "Created Date", key: "createdate", dataType: "date", width: "25%", format: "MM/dd/yyyy"},
                {headerText: "Quantity", key: "TotalQuantity", dataType: "number", width: "10%", columnCssClass: "numericAlignment"},
                {headerText: "Drop Schedule", key: "updatedate", dataType: "date", width: "25%"},
                {headerText: "Consigment Status", key: "request_status", dataType: "string", width: "25%"},
                {headerText: "Action", key: "action", dataType: "string", width: "10%"}
            ],
            features: [
                {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                        {columnKey: 'action', allowSorting: false},
                        {columnKey: 'TotalQuantity', allowSorting: false},
                        {columnKey: "PrimaryKEY", allowSorting: true, currentSortDirection: "descending"}

                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'action', allowFiltering: false},
                        {columnKey: 'TotalQuantity', allowFiltering: false},
                    ]
                },
                {
                    recordCountKey: 'TotalRecordsCount',
                    chunkIndexUrlKey: 'page',
                    chunkSizeUrlKey: 'pageSize',
                    chunkSize: 10,
                    name: 'AppendRowsOnDemand',
                    loadTrigger: 'auto',
                    type: 'remote'
                }

            ],
            primaryKey: 'inbound_request_id',
            width: '100%',
                    height: '320px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            //Removing filter columns in Grid
            rendered: function (evt, ui) {
                $("#grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();    
                $("#grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();
                $("#grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();
                $("#grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
                $("#grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
                $("#grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();
                $("#grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();





                }

        });


    });

// $("#grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();      


    function Cancel_inward_request(id)
    {

        var confirmation_alert = confirm("Do you want to cancel this AGN request?");

        if (confirmation_alert == true)
        {
            var token = $("#take").val();
        $.ajax({

            type:"POST",
            url:"/inbound/cancelrequest?_token=" + token,
            data:"id="+id,
            beforeSend: function () {
                $('#cancle_loadingmessage').addClass('loading');
            },
            complete: function () {
                $('#cancle_loadingmessage').removeClass('loading');
            },
            success:function(data)
            {
                $("#grid").igGrid("dataBind");
            }
        });
         }
         else
         {
            return false;
        }
    }

    function filterdata(status)
    {
        if (status == 'All')
        {
            window.location = "/inbound/index";
        }
        else{
            window.location = "/inbound/index/"+status;
        }

    }

     $(document).on("click", ".inwarddetails", function () {
     var inwardId = $(this).data('id');
     var token = $("#take").val();
     // $('#loadingmessage').show();
     $.ajax({
        url:"/inbound/getInwardDetails?_token=" + token,
        type:"POST",
        dataType:"json",
        data:"inwardId="+inwardId,
        beforeSend: function(){
            $("#inward_request_Id").attr("placeholder", "");
            $("#stn_number").attr("placeholder", "");  
            $("#scheduling_id").attr("placeholder", "");  
            $("#warehouse_name").attr("placeholder", "");
            $("#deliveryaddress").attr("placeholder", "");
            $('#loadingmessage').show();
        },
        complete: function(){
            $('#loadingmessage').hide();
        },
        success:function(data)
        {
            var returnedData = jQuery.parseJSON(JSON.stringify(data));
            console.log(returnedData[0]['inbound_product_details']);
            $("#inward_request_Id").attr("placeholder", returnedData[0].inbound_request_id);
            $("#stn_number").attr("placeholder", returnedData[0].stn);  
            //$("#scheduling_id").attr("placeholder", returnedData[0].scheduling_id);  
            $("#deliveryaddress").attr("placeholder", returnedData[0].ware_name[0]['address1']+","+returnedData[0].ware_name[0]['address2']+","+returnedData[0].ware_name[0]['city']);
            $("#warehouse_name").attr("placeholder", returnedData[0].pickup_address[0]['address1']+","+returnedData[0].pickup_address[0]['address2']+","+returnedData[0].pickup_address[0]['city']);
            var i = 1;
            $.each(returnedData[0]['inbound_product_details'], function(key,value) {
                    //alert(value.product_name);
                var tabledata = "<tr><td align='center'>"+i+"</td><td align='center'>"+value.seller_sku+"</td><td> <img align='center' height='20' width='20' src='"+value.product_image+"'> </td><td align='center'>"+value.product_quantity+"</td><td align='center'>"+value.product_name+"</td></tr>";
                i++;
                $("#details tbody").append(tabledata);
            });



            }
        });


    });

    $('#myModal').on('hidden.bs.modal', function () {
        $(this).find("#details tbody").html(' ');

    });

</script>

@stop
@extends('layouts.footer')