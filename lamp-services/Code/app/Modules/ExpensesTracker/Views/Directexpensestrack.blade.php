@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Direct Expenses Tracker'); ?>
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="portlet-title">
                <div class="caption">Direct Expenses Dashboard <span style="color: blue;">[ Total Advance : {{$Totals[0]->AdvanceTotalAmount}} ] &nbsp&nbsp&nbsp [ Total Reimbursement : {{$Totals[0]->RemTotalAmount}} ]</span>
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


        <!-- View  History of direct expenses -->
        <div class="modal fade" id="view-upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>

                        </button>
                        <h4 class="modal-title" id="myModalLabel"> Ledger Details </h4>
                    </div>
                    <div class="modal-body">

                        <div class="col-md-10">
                                <table class="table table-bordered  table-condensed flip-content" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody style="font-size: 11px !important;">
                                        <tr>
                                            <td colspan="2" style="background:#fbfcfd;"><strong>Ledger Name</strong>
                                                 
                                            </td>
                                           <td colspan="4" id ="Submitteb_direct_expenses" name = "Submitteb_direct_expenses"></td>

                                        </tr>
                                        <tr>
                                            <td style="background:#fbfcfd"><strong>Total Advance</strong></td>
                                            <td type = "text" id ="Total_Advance" name = "Total_Advance"></td>

                                        
                                            <td style="background:#fbfcfd" scope="row"><strong>Total Reuim.</strong></td>
                                            <td type = "text" id ="Total_Reuim" name = "Total_Reuim"></td>

                                            <td style="background:#fbfcfd" scope="row"><strong>Balance</strong></td>
                                            <td type = "text" id ="Balance_tot" name = "Balance_tot"></td>

                                        </tr>
                                    </tbody>
                                </table>     
                        </div>
                        {{ Form::open(array('url' => '/expensestracker/downloaddirectexpenses', 'id' => 'downloaddirectexpenses'))}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="hidden" name="submited_by_id" id="submited_by_id" value="">
                                <button type="submit" class="btn green-meadow" id="download-excel">Download Expenses</button>
                            </div>
                        </div>
                        {{ Form::close() }}

                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                <div class="portlet-body">
                                    
                                    <div class="tab-pane" id ="append_rows_details">
                                        <div class="row">
                                        <div class="scroller" style="height: 300px; max-height: 600px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#96999c">
                                            <table class="table table-striped">
                                                <thead >
                                                    <th>Date</th>
                                                    <th>Exp Code</th>
                                                    <th>Description</th>
                                                    <th>Trans Type</th>
                                                    <th style="text-align: right">Advance (&#8377;)</th>
                                                    <th style="text-align: right">Reiumbersement (&#8377;)</th>
                                                    <th style="text-align: right">Balance (&#8377;)</th>
                                                </thead>
                                                <tbody id="directexpensesContainer">
                                                </tbody>
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


#expensesgrid_RemTotalAmount{

    text-align: right !important;
}
#expensesgrid_AdvanceTotalAmount {
    text-align: right !important;
}

#expensesgrid_actions{
    text-align: center !important;
}

#expensesgrid_balance{
    text-align: right !important;

}
#modal_padding{
    padding-top: 5px !important;
    font-size: 12px !important;

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
        dataSource: '/expensestracker/directexpensesdata',
        responseDataKey: "results",
        columns: [
            { headerText: "Ledger Name", key: "SubmittedBy", dataType: "string", width: "20%"},
            { headerText: "Business Unit", key: "BuName", dataType: "string", width: "10%"},

            { headerText: "Advance (&#8377;)", key: "AdvanceTotalAmount", dataType: "number", width: "20%",columnCssClass: "amount-right"},
            { headerText: "Reiumbersement (&#8377;)", key: "RemTotalAmount", dataType: "number", width: "20%",columnCssClass: "amount-right"},
            { headerText: "Balance (&#8377;)", key: "balance", dataType: "number", width: "20%",columnCssClass: "amount-right"},
            { headerText: "Action", key: "actions", dataType: "string", width: "10%" }
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'SubmittedBy', allowSorting: true },
                {columnKey: 'BuName', allowSorting: true },
                {columnKey: 'AdvanceTotalAmount', allowSorting: true },
                {columnKey: 'RemTotalAmount', allowSorting: true },
                {columnKey: 'balance', allowSorting: true },
                {columnKey: 'actions', allowSorting: false },
            ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'SubmittedBy', allowFiltering: true },
                    {columnKey: 'BuName', allowFiltering: true },
                    {columnKey: 'AdvanceTotalAmount', allowFiltering: true },
                    {columnKey: 'RemTotalAmount', allowFiltering: true },
                    {columnKey: 'balance', allowFiltering: true },
                    {columnKey: 'actions', allowFiltering: false },

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



function directexpensesdata(submited_by_id){
    $('#view-upload-document').modal('toggle');
    $("#directexpensesContainer").empty();
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/expensestracker/getdirectexpensesdetails/' + submited_by_id,
            success: function (data)
            {

                var expenData = data['expensesData'];
                var historyHTML =data['historyHTML'];
                 $("#subject_history").html(expenData[0].exp_subject);

                $("#ExpensesMainID_history").val(expenData[0].exp_id);
                $("#ApprovalAmount_history").html(expenData[0].exp_actual_amount);
                $("#date_history").html(expenData[0].ExpSubmittedDate);
                $("#Submitteb_direct_expenses").html(expenData[0].SubmittedByName);
                $("#Total_Advance").html(data['AdvanceAmountTot']);
                $("#Total_Reuim").html(data['RemAmountTot']);
                $("#Balance_tot").html(data['balance']);
                $('#directexpensesContainer').html(data['historyHTML']);
                $('#submited_by_id').val(submited_by_id)

            }
    });
}





// empty values hide the button
$('#update-document').on('hide.bs.modal', function () {
    $("#Comment").val('');
});







</script>    
@stop   