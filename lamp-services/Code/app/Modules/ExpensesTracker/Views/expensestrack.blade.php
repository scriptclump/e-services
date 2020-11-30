@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Expenses Tracker'); ?>
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="portlet-title">
                <div class="caption">Expenses Tracker Dashboard</div>
                
                <div class="form-group text-right" style="margin-top:10px;">
                        @if($directExpAccess == '1')
                            <a target="_blank" href="/expensestracker/directexpenses" class="btn green-meadow" >Direct Expenses</a>
                        @endif
                        @if($downloadAccess == '1') 
                        <button type="submit" class="btn green-meadow" id="download_exp">Download Expenses</button>
                        @endif
                </div>
                
            </div>        
            
            @if($dashboardAccess == '1')
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="expensesgrid"></table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- This Is for Approve Expenses  -->
        <div class="modal fade" id="update-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Expenses Data</h4>

                    </div>
                    <div class="modal-body" id="modal_padding">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                        {{ Form::open(array('url' => '', 'id' => 'expense_view_data'))}}
                                        <div class="row">
                                        <table class="table table-bordered  table-condensed flip-content" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody style="font-size: 11px !important" id="#main_table">
                                                <tr>
                                                    <th width="5%" style="background:#fbfcfd" class="col-md-1"scope="row"><strong>Code:</strong></th>
                                                    <td width="25%" type = "text" id ="exp_code" name = "exp_code"></td>
                                                    <th width="5%" style="background:#fbfcfd" scope="row"><strong>User :</strong></th>
                                                    <td width="45%" type = "text" id ="submitteb_expenses_td"><span style="float: left; width: 190px;" id="Submitteb_expenses"></span><span id="update_business_unit"></span></td>
                                                    <th width="10%" style="background:#fbfcfd" scope="row"><strong>Request For:</strong></th>
                                                    <td width="10%" type = "text" id ="FlowType" name = "FlowType"></td>

                                                </tr>
                                                <tr>
                                                    <th style="background:#fbfcfd" scope="row"><strong>Subject:</strong></th>
                                                    <td type = "text" id ="subject" name = "subject">
                                                    </td>
                                                    <th style="background:#fbfcfd" scope="row"><strong>Date:</strong></th>
                                                    <td type = "text" id ="date" name = "date"></td>
                                                    <!-- <th style="background:#fbfcfd" scope="row"></th> -->
                                                    <th style="background:#fbfcfd"  scope="row"><strong>Approved Amount:</strong></th>
                                                    <td type = "text" id ="ApprovalAmount" name = "ApprovalAmount"></td>

                                                </tr>
                                            </tbody>
                                        </table>
                                            <input type = "hidden" id ="ExpensesMainID" name = "ExpensesMainID" class="form-control">
                                            <input type = "hidden" id ="CurrentStatusID" name="CurrentStatusID" class="form-control">
                                            <input type = "hidden" id ="UserId" name="UserId" value="{{ Session::get('userId') }}"class="form-control">
                                            <input type = "hidden" id ="RequestFlowType" name = "RequestFlowType">
                                            <input type = "hidden" id ="Approval_amount" name = "Approval_amount">

                                        </div>
                                        <div class="row voucher_table">
                                            <div class="col-md-12">
                                            <div class="scroller" style="height: 200px; max-height: 300px; border: 1px solid; " data-always-visible="1" data-rail-visible1="0" data-handle-color="#96999c">
                                                <table class="table table-striped table-bordered table-hover table-advance fixed_headers" id = "expenses_details" name = "expenses_details[]">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40%;">Type</th>
                                                        <th style="width: 20%;">Description</th>
                                                        <th style="width: 15%;">Req Amt</th>
                                                        <th style="width: 15%;">Appr Amt</th>
                                                        <th style="width: 15%;">Proof </th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                </table>
                                            </div>                        
                                            </div>
                                        </div> 
                                        <br>

                                        @if($userAccess == '1')
                                        <div id="tallyDropdown">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Tally Ledger Name</label>
                                                        <select name = "TallyLedgerName" id="TallyLedgerName" class="form-control select2me">
                                                            <option value = "">--Please select--</option>
                                                        <@foreach($getTallyDetails as $tallyData)
                                                            <option value = "{{$tallyData->tlm_name}}">{{$tallyData->tlm_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- this html code refer to approval status -->
                                        <div id="apprFlagSection" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Approval Status</label>
                                                        <select id ="NextStatusID" name="NextStatusID" class="form-control">   
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                <div class="form-group">
                                                        <label class="control-label">Comment</label>
                                                        <textarea type="text" id ="Comment" name="Comment" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                <button type="submit" class="btn green-meadow" id="price-save-button">Update Expenses</button>
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


            <!-- View History expenses -->
            <div class="modal fade" id="view-upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>

                                </button>
                                <h4 class="modal-title" id="myModalLabel"> VIEW HISTORY  EXPENSES</h4>
                            </div>
                            <div class="modal-body">

                                <div class="">
                                        <table class="table table-bordered  table-condensed flip-content" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody style="font-size: 11px !important">
                                                <tr>
                                                    <th style="background:#fbfcfd" scope="row"><strong>Code:</strong></th>
                                                    <td type = "text" id ="exp_history_code" name = "exp_history_code"></td>
                                                    <th style="background:#fbfcfd" scope="row"><strong>Submitted By:</strong></th>
                                                    <td type = "text" id ="Submitteb_history_expenses" name = "Submitteb_history_expenses"></td>
                                                    
                                                    <th style="background:#fbfcfd" scope="row"><strong>Request For:</strong></th>
                                                    <td type = "text" id ="FlowType_history" name = "FlowType_history"></td>

                                                </tr>
                                                <tr>
                                                    <th style="background:#fbfcfd" scope="row"><strong>Subject:</strong></th>
                                                    <td type = "text" id ="subject_history" name = "subject_history">
                                                    <input type = "hidden" id ="ExpensesMainID_history" name = "ExpensesMainID" class="form-control">
                                                    <input type = "hidden" id ="CurrentStatusID_history" name="CurrentStatusID" class="form-control">
                                                    <input type = "hidden" id ="UserId" name="UserId" value="{{ Session::get('userId') }}"class="form-control">
                                                    </td>
                                                    <th style="background:#fbfcfd" scope="row"><strong>Date:</strong></th>
                                                    <td type = "text" id ="date_history" name = "date_history"></td>
                                                    <th style="background:#fbfcfd"  scope="row"><strong>Amount:</strong></th>
                                                    <td type = "text" id ="ApprovalAmount_history" name = "ApprovalAmount_history"></td>

                                                </tr>
                                            </tbody>
                                        </table>     
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                        <div class="tab-pane" id ="append_rows_details">
                                            <div class="row">
                                            <div class="col-lg-12 histhead" >  
                                                <div class="col-md-3"> <b>Name</b></div>
                                                <div class="col-md-2"> <b>Date</b></div>
                                                <div class="col-md-2"> <b>Status</b></div>
                                                <div class="col-md-3"> <b>Role</b></div>
                                                <div class="col-md-2"><b>Comments</b></div></div>   
                                            </div>  
                                            <div id="expenseshistoryContainer" style="height: 250px;overflow-x:hidden;overflow-y: scroll; margin-right: -15px;">
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
         </div>
        <div class="modal fade" id="download-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel1">DOWNLOAD EXPENSES</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet box">
                                        <div class="portlet-body">
                                        {{ Form::open(array('url' => '/expensestracker/downloadexpensesdata','id' => 'download_expenses'))}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                 <div class="form-group">
                                                    <label class="control-label inputfont">Start Date</label>
                                                    <input type="text" class="form-control" id="start_date" name="start_date">
                                                    </div>
                                                 </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label inputfont">End Date</label>
                                                        <input type="text" class="form-control" id="end_date" name="end_date">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn green-meadow" id="download_excel">Download </button>
                                                    </div>
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
    


@stop

@section('style')

<style type="text/css">
.modal-dialog{width:81% !important;}
.row voucher_table{width:103 !important;}
timeline-badge {
    height: 80px;
    padding-right: 30px;
    position: relative;
    width: 80px;
    z-index: 1111 !important;
}
.amount-right{ text-align: right; padding-right: 4px;}
.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:300px !important; white-space: pre-wrap !important;}

.ui-iggrid-footer{ height: 30px !important; padding-top: 30px !important; padding-left: 10px !important;}
.timeline-body {
    font-weight: 600;
    margin-bottom: -9px !important;
    margin-left: 75px !important;
    margin-top: -45px !important;
}.amount {
    height : 40px;
}
.timline_style .timeline-badge-userpic {
        border-radius: 30px !important;}

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

#modal_padding{
    padding-top: 5px !important;
    font-size: 12px !important;

}
.ui-iggrid-summaries-footer-text-container{
   
    font-weight: bold;
    padding-left: 30px;
}



</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>
@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>

<script>

$(function () {
    $("#expensesgrid").igGrid({
        dataSource: '/expensestracker/expensestrackdata',
        responseDataKey: "results",
        columns: [
            { headerText: "Code", key: "exp_code", dataType: "string", width: "10%" },
            { headerText: "Expense Type", key: "ExpensesType", dataType: "string", width: "15%" },
            { headerText: "Subject", key: "exp_subject", dataType: "string", width: "20%"},
            { headerText: "Business Unit", key: "business_name", dataType: "string", width: "10%"},
            { headerText: "Submitted By", key: "SubmittedBy", dataType: "string", width: "15%"},
            { headerText: "Submitted Date", key: "ExpSumitDate", dataType: "date",format:"dd-MM-yyyy", width: "10%"},
            { headerText: "Asked For Amount", key: "exp_actual_amount", dataType: "number", width: "11%",columnCssClass: "amount-right"},
            { headerText: "Approval Amount", key: "exp_approved_amount", dataType: "number", width: "11%",columnCssClass: "amount-right"},
            { headerText: "Approval Status", key: "ApprovalStatus", dataType: "string", width: "10%"},
            { headerText: "Action", key: "actions", dataType: "string", width: "8%" }
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'master_lookup_name', allowSorting: true },
                {columnKey: 'exp_code', allowSorting: true },
                {columnKey: 'exp_date', allowSorting: true },   
                {columnKey: 'exp_ref_type', allowSorting: true },
                {columnKey: 'exp_subject', allowSorting: false },                
                {columnKey: 'exp_actual_amount', allowSorting: true },
                {columnKey: 'exp_approved_amount', allowSorting: true },
                {columnKey: 'actions', allowSorting: false },
                {columnKey: 'business_name', allowSorting: true },
            ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'master_lookup_name', allowFiltering: true },
                    {columnKey: 'exp_code', allowFiltering: true },
                    {columnKey: 'exp_date', allowFiltering: true },
                    {columnKey: 'exp_ref_type', allowSorting: true },
                    {columnKey: 'exp_actual_amount', allowFiltering: true },
                    {columnKey: 'exp_approved_amount', allowFiltering: true },
                    {columnKey: 'exp_subject', allowFiltering: false },
                    {columnKey: 'actions', allowFiltering: false },
                    {columnKey: 'business_name', allowFiltering: true },

                ]
            },
            { 
                recordCountKey: 'TotalRecordsCount', 
                pageIndexUrlKey: 'page', 
                pageSizeUrlKey: 'pageSize', 
                pageSize: 10,
                name: 'Paging', 
                loadTrigger: 'auto', 
                type: 'remote' 
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
              summariesCalculated: function(evt, ui){

                var sum=$('div#expensesgrid_summaries_footer_row_text_container_sum_exp_actual_amount').text();
                sum=sum.substr(2);
                $('div#expensesgrid_summaries_footer_row_text_container_sum_exp_actual_amount').text(sum);
                var total=$('div#expensesgrid_summaries_footer_row_text_container_sum_exp_approved_amount').text();
                total=total.substr(2);
                $('div#expensesgrid_summaries_footer_row_text_container_sum_exp_approved_amount').text(total);


              },             
              columnSettings: [          
                {columnKey: "exp_code", allowSummaries: false},            
                {columnKey: "ExpensesType", allowSummaries: false},            
                {columnKey: "exp_subject", allowSummaries: false},            
                {columnKey: "business_name", allowSummaries: false},
                {columnKey: "SubmittedBy", allowSummaries: false},  
                {columnKey: "ExpSumitDate", allowSummaries: false},        
                {columnKey: "exp_actual_amount", allowSummaries: true,summaryOperands:
                [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},      
                {columnKey: "exp_approved_amount", allowSummaries: true,summaryOperands:
                [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},    
                {columnKey: "ApprovalStatus", allowSummaries: false},    
                {columnKey: "actions", allowSummaries: false}
                ]
            },                
        ],
        primaryKey: 'exp_id',
        width: '100%',
        height: '500px',
        defaultColumnWidth: '100px'
    }); 

    var URL = window.location.href;
    var prd_id = URL.split("/openpopup/");
    if(typeof prd_id[1] != 'undefined'){
        viewexpensesdata(prd_id[1]);
    }

});



function viewexpensesdata(exp_id){
    $('#update-document').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/expensestracker/getexpensesdetails/' + exp_id,
            success: function (data)
            {
                
                var expenData = data['expensesData'];
                var apprData = data['apprData'];
                var refType =data['refType'];
                var budata = data['businessData'];
                var status=data['apprrovalPermission'];
                
                // Append business unit data
                $('#update_business_unit').html(budata);
        
                // TO FILLUP THE EXPENSES DATA
                if(expenData[0]){
                    $("#expenses_details td").remove(); 
                    $("#subject").html(expenData[0].exp_subject);
                    $("#FlowType").html(expenData[0].RequestFor);
                    $("#RequestFlowType").val(expenData[0].RequestFor);
                    $("#exp_code").html(expenData[0].exp_code);
                    $("#ExpensesMainID").val(expenData[0].exp_id);
                    $("#ApprovalAmount").html(expenData[0].exp_approved_amount);
                    $("#Approval_amount").val(expenData[0].exp_approved_amount);
                    $("#TallyLedgerName").select2("val", expenData[0].tally_ledger_name);
                    if(expenData[0].tally_ledger_name=== null){
                        $("#TallyLedgerName").select2("val", "101000 : Cash");
                    }
                    $("#date").html(expenData[0].ExpSubmittedDate);
                    $("#Submitteb_expenses").html(expenData[0].SubmittedByName);

                    
                    if(expenData[0].RequestFor == 'Reimbursement'){
                    $('#expenses_details').append(refType);
                    }
                }
                
                // TO FILLUP THE APPROVAL DATA
                var apprStaus = $('#NextStatusID');
                
                apprStaus.find('option').remove().end();

                var apprFlag = 0;
                for(var i=0; i<apprData.length; i++){
                    apprFlag =1;
                    apprStaus.append(
                        $('<option></option>').val( apprData[i].nextStatusId + "," + apprData[i].isFinalStep ).html(apprData[i].condition)
                    );

                    $("#CurrentStatusID").val(apprData[0].currentStatusID);
                }

                if(apprFlag==0){
                    $('#apprFlagSection').hide();
                }else{
                    $('#apprFlagSection').show();
                }

                $('#expense_view_data').formValidation('resetField', 'TallyLedgerName');

                if(status==200){
                    $('#apprFlagSection').hide();
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Your not permitted to approve since your not his reporting manager</div></div>');
                }
            }
    });
}

function historyexpensesdata(exp_id){
    $('#view-upload-document').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/expensestracker/gethistoryexpensesdetails/' + exp_id,
            success: function (data)
            {

                var expenData = data['expensesData'];
                var historyHTML =data['historyHTML'];

                $("#subject_history").html(expenData[0].exp_subject);
                $("#FlowType_history").html(expenData[0].RequestFor);
                $("#exp_history_code").html(expenData[0].exp_code);

                $("#ExpensesMainID_history").val(expenData[0].exp_id);
                $("#ApprovalAmount_history").html(expenData[0].exp_actual_amount);
                $("#date_history").html(expenData[0].ExpSubmittedDate);
                $("#Submitteb_history_expenses").html(expenData[0].SubmittedByName);
                $('#expenseshistoryContainer').html(data.historyHTML);

            }
    });
}

function updateName(exp_id){
    var token  = $("#csrf-token").val();
    var master_lookup = $("#ref_type_id_expense option:selected").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            data: { master_lookup: master_lookup},
            url: '/expensestracker/updatereftypeonly/' + exp_id,
            success: function (data)
            {

            }
    });
}
function updateBusiness(){
    var token  = $("#csrf-token").val();
    var expenseId = $("#ExpensesMainID").val();
    var BusinessId = $('#business_unit_dp').val();  
    var data = 'ExpenseID='+expenseId+'&BusinessId='+BusinessId   
    $.ajax({
            headers:{'X-CSRF-TOKEN':token},
            type:"POST",
            data:data,
            url:'/expensestracker/updatebusinessUnit',
            success:function(data){
                $('#expensesgrid').igGrid("dataBind"); 
            }
    });
}

function updateAmount(detailId, expenseId){
    var token  = $("#csrf-token").val();
    
    //var formData = new FormData();
    var apprAmount = $("#update_approved_amount_"+detailId+"").val();
    var amount = parseInt($("#update_det_actual_amount_"+ detailId+"").html());

    if(apprAmount > amount)
    {
        alert('Enter the Amount less than Actual Amount');
    }else{

        
        $.ajax({
            headers:{'X-CSRF-TOKEN':token},
            type: "POST",
            data: {
                'ApprovedAmount'    : apprAmount,
                'detailId'          : detailId,
                'expensesMainId'    : expenseId 
            },
            //processData: false,
            //contentType: false,
            url:'/expensestracker/updateaproovedamountonly',
            success:function(data){
                var dataArr = data.split('==!!');
                alert(dataArr[0]);
                $('#ApprovalAmount').empty();
                $('#ApprovalAmount').append(dataArr[1]);
                $('#Approval_amount').val(dataArr[1]);
                $('#expensesgrid').igGrid("dataBind"); 
            }
        });

    }

    

}

// for expenses with tally view

/*function expensestally(exp_id){
    $('#expenses_tally_cr_dr').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/expensestracker/getexpensesdetails/' + exp_id,
            success: function (data)
            {
                var expenData = data['expensesData'];
                var apprData = data['apprData'];
            
                // TO FILLUP THE EXPENSES DATA
                if(expenData[0]){
                    $("#expenses_details_cr_dr td").remove(); 
                    $("#subject_cr_dr").val(expenData[0].exp_subject);
                    $("#FlowType_cr_dr").val(expenData[0].RequestFor);
                    $("#ExpensesMainID_cr_dr").val(expenData[0].exp_id);
                    $("#ApprovalAmount_cr_dr").val(expenData[0].exp_actual_amount);
                    $("#date_cr_dr").val(expenData[0].submit_date);

                    var tableRow='';
                    if(expenData[0].exp_det_id != null){
                        for(var i=0;i<expenData.length;i++){

                            var images = expenData[i].exp_det_proof.split(',');

                            var ImageData = "";
                            for(var j=0;j<images.length;j++){

                                ImageData= ImageData + '<a href="'+images[j]+'" id="proof_file" target="_blank"><img  id="expense_file" name="expense_file" src="'+images[j]+'" style = "width:20px;"/></a>&nbsp;&nbsp;'
                            }
                            
                            if(expenData[i]){
                                tableRow = tableRow + '<tr class="gradeX odd">\
                                    <td data-val="list_details" class="prom-font-size"><input type="text" value="'+expenData[i].master_lookup_name+'" id="update_ref_type_cr_dr" name="update_ref_type_cr_dr[]" class="form-control" readonly></td>\
                                    <td data-val="list_details" class="prom-font-size"><input type="text" value="'+expenData[i].exp_det_actual_amount+'" id="update_det_actual_amount_cr_dr" name="update_det_actual_amount_cr_dr[]" class="form-control" readonly></td>\
                                    <td data-val="list_details" class="prom-font-size"><input type="text"  value = "'+expenData[i].exp_det_actual_amount+'" id="update_approved_amount_cr_dr" name="update_approved_amount_cr_dr[]" class="form-control" readonly><input type="hidden"  value = "'+expenData[i].exp_det_id+'" id="hidden_approve_id" name="hidden_approve_id[]" class="form-control"></td>\
                                    <td data-val="list_details" class="prom-font-size">'+ImageData+'</td></tr>';
                            }
                        }
                    }else{
                        tableRow = tableRow + '<tr class="gradeX odd">\
                                    <td colspan="4" data-val="list_details" class="prom-font-size">Details data not found!</td></tr>';
                    }

                    $('#expenses_details_cr_dr').append(tableRow);
                }
                
            }
    });
}*/



// empty values hide the button
$('#update-document').on('hide.bs.modal', function () {
    $("#Comment").val('');
});

function allowNumber(event){
    if (event.shiftKey == true) {
        event.preventDefault();
    }
    if ((event.keyCode >= 48 && event.keyCode <= 57) || 
        (event.keyCode >= 96 && event.keyCode <= 105) || 
        event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
        event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {
    } else {
        event.preventDefault();
    }
    if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
        event.preventDefault(); 
}

$("#expenses_details").keydown(function (event) {
        allowNumber(event);
});

/*$("#download_excel").click(function(e){
 window.open('/expensestracker/downloadexpensesdata','_blank');
 $("#download-document").modal('toggle');
});*/

/*
$("#download_excel").click(function(e){
$("#download-document").modal('toggle');
    var token  = $("#csrf-token").val();
    var strtDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    formData = new Array();
    formData.push({"strtDate": strtDate, "endDate": endDate});
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            //contentType: "application/json",
            url: '/expensestracker/downloadexpensesdata',
            //data:"filterDetails=" + JSON.stringify(formData),
            context: document.body,
            async:false, 
            success: function (data)
            {   
                //success = true
            }
        });  

});*/ 


/*$("#download_expenses").click(function(e){
    $('#download_expenses').formValidation('resetField', 'start_date');
    $('#download_expenses').formValidation('resetField', 'end_date');    
});*/


$('#expense_view_data').formValidation({
        
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        TallyLedgerName: {
            validators: {
                notEmpty: {
                        message: 'Please Select LedgerName'
                }
            }
        }
    }
}).on('success.form.fv', function(e){
    var form=$("#expense_view_data");
    e.preventDefault();
    var token  = $("#csrf-token").val();
        $.ajax({

            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/expensestracker/updateexpensedata',
            data: form.serialize(),

            success: function (respData)
            {
                $("#update_data_table td").remove();   
                $('#update_ledger').select2("val", "");
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Expenses updated succesfully</div></div>' );
                $(".alert-success").fadeOut(20000)
                $('#update-document').modal('toggle');
                $('#expensesgrid').igGrid("dataBind"); 
            }
        });  
});



$('#expense_tally_cr_dr').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            expenses_dr: {
                validators: {
                    notEmpty: {
                        message: 'Please Select Dr'
                    }
                }
            },
            expenses_cr: {
                validators: {
                    notEmpty: {
                        message: 'Please Select Cr'
                    }
                }
            },
        }
})
.on('success.form.fv', function(e){
   var form=$("#expense_tally_cr_dr");
    e.preventDefault();
    var token  = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/expensestracker/saveintovouchers',
            data: form.serialize(),
            success: function (respData)
            {
                $("#update_data_table td").remove();   
                $('#update_ledger').select2("val", "");
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Expenses updated succesfully</div></div>' );
                $(".alert-success").fadeOut(20000)
                $('#update-document').modal('toggle');
                $('#expensesgrid').igGrid("dataBind"); 
            }
        });  
});

$(".modal").on('hidden.bs.modal', function () {

        $('#expense_view_data').formValidation('resetForm', true);

        //Removing the error elements from the from-group
        $('.form-group').removeClass('has-error has-feedback');
        $('.form-group').find('small.help-block').hide();
        $('.form-group').find('i.form-control-feedback').hide();
});


$( document ).ready(function() {
    var date = new Date();
    var end = new Date();

    $('#start_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        onSelect: function(datesel) {
            var stDate = new Date($(this).val());
            $('#end_date').datepicker('option','minDate', stDate);
            $('#end_date').datepicker('setDate', stDate);
            
            $('#download_expenses').formValidation('revalidateField', 'start_date');
        }
    }); 

    var date = new Date();
    $('#end_date').datepicker({
        format: 'yyyy-mm-dd',
        onSelect: function(datesel) {
            $('#download_expenses').formValidation('revalidateField', 'end_date');
        }
    }); 


    $('#download_excel').on( 'click', function(e) {
        $('#download_expenses').formValidation('revalidateField', 'start_date'); 
        $('#download_expenses').formValidation('revalidateField', 'end_date'); 


            if(start == '' || end == ''){
                return false;
            }else{
                return true;
            }

        e.defaultSubmit();

       
     });

    $('#download_exp').on( 'click', function() {
       
        $("#download-document").modal("toggle");

    });

    $("#download-document").on('shown.bs.modal', function(){
       //$('.form-group').removeClass('has-error has-feedback');
       
        $('#download_expenses').data("formValidation").resetForm(true);
       
       
    })

});

$('#download_expenses').formValidation({
        
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            start_date: {
                validators: {
                    notEmpty: {
                        message: 'Enter start date'
                    }
                }
            },end_date: {
                validators: {
                    notEmpty: {
                        message: 'Enter end date'
                    }
                }
            }
        }
}).on('success.form.fv', function(e){

    /*alert("da");

    e.preventDefault();

    var $form = $(e.target);

     var fv = $form.data('formValidation');

    fv.defaultSubmit();
    fv.disableSubmitButtons(false);*/

});


</script>    
@stop   