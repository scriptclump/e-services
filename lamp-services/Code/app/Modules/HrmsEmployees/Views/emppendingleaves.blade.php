@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message_ajax"></span>
<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/legalentity">Details</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Pending Leaves</li>
        </ul>
    </div>
</div>
    <div class="portlet-body">
     @include('HrmsEmployees::navigationapprovelist')
 </div>
@stop
@section('style')

@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>

    <script>
        $( document ).ready(function() {
          $("#pendingleavelist").igGrid({
            dataSource: '/employee/managerapprovelist/' + <?php echo Session::get('userId');?>,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            width: "100%",
            height: "100%",
            columns: [
            {headerText: "From Date", key:'from_date', dataType:'date',format:"date", width: '15%'},
            {headerText: "To Date", key:'to_date', dataType:'date',format:"date", width: '15%'},
            {headerText: "No Of Days", key:'no_of_days', dataType:'string', width: '10%'},
            {headerText: "Day", key:'day', dataType:'string', width: '15%'},
            {headerText: "Emp Code", key: 'emp_code', dataType: 'string', width: '10%'},
            {headerText: "Employee Name", key: 'employee_name', dataType: 'string', width: '20%'},           
            {headerText: "Leave Type", key: 'leave_type', dataType: 'string', width: '20%'}, 
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "10%"},
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "local",
                    columnSettings: [
                    {columnKey: 'chk', allowSorting: false },
                    {columnKey: 'from_date', allowSorting: true },
                    {columnKey: 'to_date', allowSorting: true },
                    {columnKey: 'emp_code', allowSorting: true },
                    {columnKey: 'employee_name', allowSorting: true },
                    {columnKey: 'leave_type', allowSorting: true },
                   
                    ]
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    //filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'chk', allowFiltering: false },
                        {columnKey: 'from_date', allowFiltering: true },
                        {columnKey: 'to_date', allowFiltering: true },
                        {columnKey: 'emp_code', allowFiltering: true },
                        {columnKey: 'employee_name', allowFiltering: true },
                        {columnKey: 'leave_type', allowFiltering: true },
                     
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
                
            ],
            type: 'local',
            primaryKey: 'leave_history_id',
        });

      });

    function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }


function ApproveorrejectLeaveByonclick(value){
    var token  = $("#csrf-token").val();
    var selected = [];
    $("input[name='chk[]']").each( function () {
        console.log($(this).prop('checked'));
        if($(this).prop('checked')){
            selected.push($(this).val());
        }
       console.log(selected);
    });
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        data: {leave_id:selected},
        type: "POST",
        url: '/employee/managerapproveorreject/' + value,
        success: function (data)
        {
            if(data == 'Failed'){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">No Pending Leaves Left<button type="button" class="close" data-dismiss="alert"></button></div></div>');
            }
            else if(data == 'Approved'){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Leave(s) Approved Successfully<button type="button" class="close" data-dismiss="alert"></button></div></div>');
            }
            else if(data == 'Rejected'){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Leave(s) Rejected Successfully<button type="button" class="close" data-dismiss="alert"></button></div></div>');
            }
        window.setTimeout(function(){window.location.reload()}, 5000);
        }
    });
}

 function ApprovedHistory(managerid) {
    $("#leavehistory_manager").igGrid({
            columns: [
            {headerText: "From Date", key:'from_date', dataType:'date',format:"date", width:'12%'},
            {headerText: "To Date", key:'to_date', dataType:'date', width: '12%',format:"date"},
            {headerText: "No Of Days", key: 'no_of_days', dataType: 'string', width: '10%'},
            {headerText: "Emp Code", key: 'emp_code', dataType: 'string', width: '12%'},
            {headerText: "Employee Name", key: 'employee_name', dataType: 'string', width: '15%'},         
            {headerText: "Leave type", key: 'leave_type', dataType: 'string', width: '15%'}, 
            {headerText: "Reason", key:'reason', dataType: 'string', width: '20%'},
            {headerText: "Status", key:'status', dataType: 'string', width: '15%'},                    
                ],
            features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'created_at', allowSorting: false },
                ]
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                columnSettings: [
                    {columnKey: 'created_at', allowFiltering: false},
                ]


            },
            {
                recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 15,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                     type: 'local' 
            }
                        ],
                        //primaryKey: "pay_id",
        type: 'local',
        dataSource: "/employee/gethistoryofallleaves/" + managerid,
        autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            //enableUTCDates: true, 
            width: "100%",
            height: "100%",
            rendered: function (evt, ui) {
                igGridHideOption();
            },
        }); 
}




 function customigGridColumns(headers) {

        var columnHeaders = [];
        var columnSummaries = [];

        for (var i = 0; i < headers.length; i++) {
            var headerDataType = "string";
            var cssClass = null;
            var customWidth = "130px";
            var customHeadText = headers[i];
            
            if (headers[i].substring(0, 2) === "1_") {
                headerDataType = "number";
                cssClass = "alignRight";
                customWidth = "60px";

                var summaryType = (headers[i].substring(headers[i].length - 4) == "_Per")?"AVG":"SUM";
                // Summaries Cols
                columnSummaries.push({
                    columnKey: headers[i],
                    allowSummaries: true,
                    summaryOperands: [{
                        "rowDisplayLabel": "",
                        "type": summaryType,
                        "active": true
                    }]
                });
            } else {
                columnSummaries.push({
                    columnKey: headers[i],
                    allowSummaries: false
                });
            }

            if (headers[i].substring(0, 4) === "1_N_") {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    },
                    width: "auto",
                });
            } else if(headers[i].substring(0, 2) === "D_") {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: "date",
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "date", "dd/MM/yyyy");
                    },
                    width: "auto",
                });
            } else {
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    width: "auto",
                });
            }
        }

        return {
            "columnHeaders": columnHeaders,
            "columnSummaries": columnSummaries
        };
    }
    

        function formatigGridContent(columns, selectedId) {
        for (var idx = 0; idx < columns.length; idx++) {
            var newText = columns[idx].headerText;
            
            // Summaries UI changes
            var id_text = "_summaries_footer_row_text_container_sum_";
            $("#"+ selectedId +"_summaries_footer_row_icon_container_sum_" + newText).remove();
            $("#"+ selectedId + id_text + newText).attr("class", "summariesStyle").text($("#"+ selectedId + id_text + newText).text().replace(/\s=\s/g, ''));

            // S.No and Column Title Adjustments below
            if (columns[idx].dataType == "number" || columns[idx].dataType == "double") {
                var isDecimal = columns[idx].headerText.substring(0, 2);
                if (isDecimal === "1_") {
                    var columnText =
                        (columns[idx].headerText.substring(columns[idx].headerText.length - 4) === "_Per") ? columns[idx].headerText.replace("_Per", " %").substring(2) : columns[idx].headerText.substring(2);
                    columnText = (columnText.substring(0, 2) === "N_") ? columnText.substring(2) : columnText;
                    $("#"+ selectedId + "_" + newText + " > span.ui-iggrid-headertext")
                        .html("<p style='text-align: right !important; margin: 0px 5px !important;'>" + columnText.replace(/_/g, ' ') + "</p>")
                        .attr('title', columnText.replace(/_/g,' '));
                }
            } else if (columns[idx].dataType == "string") {
                $("#"+ selectedId +"_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.replace(/_/g,' '))
                    .text(newText.replace(/_/g,' '));
            } else if (columns[idx].dataType == "date") {
                $("#"+ selectedId +"_" + newText + " > span.ui-iggrid-headertext")
                    .attr('title', newText.substring(2).replace(/_/g, ' '))
                    .text(newText.substring(2).replace(/_/g, ' '));
            }

        }
    }



  $("#pendingleavelist").on("iggriddatarendered", function (event, args) {
        $("#pendingleavelist_from_date > span.ui-iggrid-headertext").attr('title', "From Date");
        $("#pendingleavelist_to_date > span.ui-iggrid-headertext").attr('title', "To Date");
        $("#pendingleavelist_emp_code > span.ui-iggrid-headertext").attr('title', "Employee Code");
        $("#pendingleavelist_employee_name > span.ui-iggrid-headertext").attr('title', "Employee Name");
        $("#pendingleavelist_leave_type > span.ui-iggrid-headertext").attr('title', "Leave Type");
        $("#pendingleavelist_reason > span.ui-iggrid-headertext").attr('title', "Reason");
        $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
        
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
        $("#pendingleavelist_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();
        
        var columns = $("#pendingleavelist").igGrid("option", "columns");
        formatigGridContent(columns,"pendingleavelist");
            });


  function igGridHideOption() {
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
  $("#leavehistory_manager_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();

    $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();

  
}
    
</script>
@stop
@extends('layouts.footer')