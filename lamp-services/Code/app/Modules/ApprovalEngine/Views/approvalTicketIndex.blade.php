@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Approval Flow List'); ?>
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget" style="height:650px;">
      <div class="portlet-title">
        <div class="caption"> 
          APPROVAL TICKETS | <span class="caption-subject bold font-blue uppercase"> FILTER BY :</span>
            <span class="caption-helper sorting">                                
                <a onclick="loadGridData('allTicketsTab')" style="font-size: 14px" id="allTicketsTab" data-toggle="tooltip"  title="All Tickets" >ALL
                (<span  id="allTicketsCount"  >0</span>)</a>&nbsp;
                <a onclick="loadGridData('openTicketsTab')" style="font-size: 14px" data-toggle="tooltip" id="openTicketsTab"  title="Open Tickets">Open Tickets
                (<span id="openTicketsCount"  >0</span>)</a>&nbsp;
                <a onclick="loadGridData('closedTicketsTab')" style="font-size: 14px" id="closedTicketsTab" data-toggle="tooltip" title="Closed Tickets" >Closed Tickets
                (<span id="closedTicketsCount">0</span>)</a>
            </span>
        </div>
      </div>

      <div class="portlet-body">

        <div class="row">
          <div class="col-md-6 pull-right text-right">
           
            <!-- <a href="/approvalworkflow/addapprovalstatus" class="btn green-meadow">Add Approval Workflow Data</a> -->
         
          </div>
        </div>

        

        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
        <div class="row">
          <div class="col-md-12">
            <div class="table-scrollable">
              <table id="approvalList"></table>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div>
</div>

         <div class="modal fade" id="view-history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>

                                </button>
                                <h4 class="modal-title" id="myModalLabel"> VIEW HISTORY</h4>
                            </div>
                            <div class="modal-body">

                                <!-- <div class="">
                                    <table class="table table-bordered  table-condensed flip-content" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody style="font-size: 11px !important">
                                            <tr>
                                                <th style="background:#fbfcfd" scope="row"><strong>TicketNumber:</strong></th>
                                                <td type = "text" id ="exp_history_code" name = "exp_history_code"></td>
                                                
                                            </tr>
                                            <tr>
                                                <th style="background:#fbfcfd" scope="row"><strong>TicketDetails:</strong></th>
                                                <td id ="subject_history" name = "subject_history"></td>

                                                
                                                
                                            </tr>
                                        </tbody>
                                    </table>     
                                </div> -->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                        <div class="tab-pane" id ="append_rows_details">
                                            <div class="row">
                                                <div class="col-lg-12 histhead" >  
                                                    <div class="col-md-2"> <b>Last User</b></div>
                                                    <div class="col-md-2" style="left: 58px"> <b>Assigned On</b></div> 
                                                    <div class="col-md-2" style="left: 45px"> <b>PreviousStatus</b></div>
                                                    <div class="col-md-2" style="left: 33px"> <b>Action</b></div>
                                                    <div class="col-md-2" style="left: 20px"> <b>Finalstatus</b></div>
                                                    <div class="col-md-2"> <b>Comment</b></div>  
                                                </div>
                                            </div>  
                                            <div id="historyContainer" style="height: 250px;overflow-x:hidden;overflow-y: scroll; margin-right: -15px;">
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
@stop
@section('userscript')

<style type="text/css">

    .fa-link{color:#3598dc !important;}
    .caption-subject{font-size: 12px !important;}

    .ui-iggrid-results{
        height: 30px !important;
    }

    .timeline-badge {
        height: 80px;
        padding-right: 30px;
        position: relative;
        width: 80px;
        z-index: 1111 !important;
    }

    .timline_style .timeline-badge-userpic {
        border-radius: 30px !important;
    }

    .timeline::before {
        background: #f5f6fa none repeat scroll 0 0;
        bottom: 0;
        content: "";
        display: block;
        margin-left: 54px;
        position: absolute;
        top: 0;
        width: 4px;
        top:62px !important;
    }
    .timeline-body {
        font-weight: 600;
        margin-bottom: -9px !important;
        margin-left: 75px !important;
        margin-top: -63px !important;
    }
    .timeline {
        margin-bottom: 0px !important;
    }
    .modal-dialog{
        width:81% !important;
    }
    .timeline_last {
        margin: 0 0 30px;
        padding: 0;
        position: relative;
    }
.sorting a:active{text-decoration:none !important;}
.active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
.inactive{text-decoration:none !important; color:#676767 !important;}
</style>

<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />


@extends('layouts.footer')
<script type="text/javascript">

    $(function(){
        var token=$('#csrf-token').val();
        loadGridData('openTicketsTab');
    });
    function showTab(initial) {
            $.ajax({
                url: "/approvalworkflow/getuserticketcount",
                type: 'GET',
                dataType:"json",
                success: function (response)
                {
                    //$("#stillLoadingMsg").hide();
                    if(initial){
                        // Updating Users Count
                        $("#allTicketsCount").text(response.allTicketsCount);
                        $("#openTicketsCount").text(response.openTicketsCount);
                        $("#closedTicketsCount").text(response.closedTicketsCount);
                        countNumbers();
                    }
                },
            });
        }
    function countNumbers() {
            $('.count').each(function () {
                $(this).prop('Counter',0).animate({
                        Counter: $(this).text()
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                });
            }
   
    function loadGridData(selectedId){
        showTab(true);

        $("#approvalList").igGrid({
            dataSource: '/approvalworkflow/approvalticketgrid?showTab='+selectedId,
            type: "JSON",
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            recordCountKey: 'TotalRecordsCount',
            generateCompactJSONResponse: false, 
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
                { headerText: "Ticket No", key: "TicketNumber", dataType: "string", width: "10%" },
                { headerText: "Ticket Details", key: "TicketDetails", dataType: "string", width: "30%" },
                { headerText: "Assigned To", key: "TicketPendingOn", dataType: "string", width: "15%" },
                { headerText: "Assigned On", key: "created_at", dataType: "date", width: "10%", template: "${AssignDate}"  },            
                { headerText: "Status", key: "TicketStatus", dataType: "string", width: "10%" },
                { headerText: "Comment", key: "awf_comment", dataType: "string", width: "20%" },
                { headerText: "Actions", key: "CustomAction", dataTpe: "string", width: "7%"},
            ],
            features: [
                {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                        {columnKey: 'CustomAction', allowSorting: false },
                        {columnKey: 'created_at', allowSorting: true },
                        {columnKey: 'TicketNumber', allowSorting: true },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'PreviousStatus', allowFiltering: false },
                        {columnKey: 'TicketStatusAsPerRole', allowFiltering: false },
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'awf_comment', allowFiltering: false },
                        {columnKey: 'TicketStatus', allowFiltering: false },
                    ]
                },
                {
                    name: 'Paging',
                    type: 'remote',
                    pageSize: 20,
                    recordCountKey: 'TotalRecordsCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize",
                     
                }
                
            ],
            primaryKey: 'awf_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,

      });
        if(selectedId=='allTicketsTab'){
                $("#allTicketsTab").removeClass('inactive');
                $("#openTicketsTab").removeClass('active');
                $("#closedTicketsTab").removeClass('active');
                $("#allTicketsTab").addClass('active');
                $("#openTicketsTab").addClass('inactive');
                $("#closedTicketsTab").addClass('inactive');
                }else if(selectedId=='openTicketsTab'){
                    $("#openTicketsTab").removeClass('inactive');
                    $("#allTicketsTab").removeClass('active');
                    $("#closedTicketsTab").removeClass('active');
                    $("#openTicketsTab").addClass('active');
                    $("#closedTicketsTab").addClass('inactive');
                    $("#allTicketsTab").addClass('inactive');
                }else if(selectedId=='closedTicketsTab'){
                    $("#closedTicketsTab").removeClass('inactive');
                    $("#openTicketsTab").removeClass('active');
                    $("#allTicketsTab").removeClass('active');
                    $("#closedTicketsTab").addClass('active');
                    $("#allTicketsTab").addClass('inactive');
                    $("#openTicketsTab").addClass('inactive');
                }

    }

    function viewhistory(type,id){
        $('#view-history').modal('toggle');
        $("#historyContainer").empty();
        token  = $("#csrf-token").val(); 
       // alert(id);
        $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "GET",
                url: '/approvalworkflow/approvalHistorygrid/'+type+"/"+id,
                success: function( data ) { 
                    var refType =data['historyHTML'];
                    //append to the table
                    $('#historyContainer').append(refType);     
                }        
        });
    }

</script>
@stop

@extends('layouts.footer')


