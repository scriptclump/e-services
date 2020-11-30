@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> FF Report </div>
                <div class="actions">  </div>
            </div>
            <form method="post" id="report_form">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <div class="row margtop">
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label class="control-label">Business Units</label>
                            <input type="hidden" id="hidden_buid" name="hidden_buid" value='<?php if (isset($bu_id) && $bu_id!=''){ echo $bu_id;}else{ echo '';}?>'>
                            <select id="business_unit_id" name="business_unit_id" class="form-control business_unit_id select2me"></select>
                        </div>
                    </div>     
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label>FF Name</label>
                            <div class="input-icon right" style="width: 100%">
                                <input type="text" class="form-control" name="ff_name" id="ff_name" >
                                <input type="hidden" class="form-control" name="ff_id" id="ff_id" value="">
                            </div>
                        </div>
                    </div>         
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label>Start Date</label>
                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_start_date" id="reports_start_date" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label>End Date</label>

                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_end_date" id="reports_end_date" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group err">
                            <label>Day/Month</label>

                            <div class="input-icon right" style="width: 100%">
                                
                                <select class="form-control" name="by_day_month" id="by_day_month" style="width: 97px;padding:7px">
                                    <option value='1'>Day</option>
                                    <option value='2'>Month</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group err">
                            <div class="input-icon right" style="width: 100%"> <br/>                               
                                <input type="submit"  class="btn green-meadow btnn range_info" value='Submit'id="report_range" >                           
                                <input type="button"  class="btn green-meadow btnn range_info" value='Reset'id="filterReset" > 			
								<a href="ffreports/excelSalesReports" id="export_reports" class="btn green-meadow btnn range_info" ><i class="fa fa-download" aria-hidden="true"></i></a>                                                          
                                
                            </div>                                                    
                        </div>
                    </div>
                </div>
            </form>
                                                                    
            <div class="portlet-body">
                <table id="reports_grid"></table>
            </div>
            <div class="col-md-12" style="display: none" id="reports_grid_msg">No Records Found</div>
        </div>
    </div>
</div>
{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}
    .margtop{margin-top:15px;}
    .ui-iggrid-featurechooserbutton{display:none !important}
	.ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
	.rightAlign {
    text-align:right;
	}
    .bu1{
    margin-left: 10px;
    font-size: 18px;
    color:#000000;
}
.bu2{
    margin-left: 20px;
    font-size: 16px;
    color:#1d1d1d;
}.bu3{
    margin-left: 30px;
    font-size: 15px;
    color:#3a3a3a;
}.bu4{
    margin-left: 40px;
    font-size: 14px;
    color:#535353;
}.bu5{
    margin-left: 50px;
    font-size: 13px;
    color: #6d6c6c;
}.bu6{
    margin-left: 60px;
    font-size: 11px;
    color:#868383;
}
.alignCenter{
    text-align: center;
}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
@include('includes.validators')
@include('includes.ignite')
<!--<script src="http://cdn-na.infragistics.com/igniteui/2016.1/latest/js/infragistics.loader.js"></script>
<script src="http://www.igniteui.com/js/external/FileSaver.js"></script>
<script src="http://www.igniteui.com/js/external/Blob.js"></script>-->

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
        
<script>
    $("#ff_name").autocomplete({
    source: function(request, response) {
        $.ajax({
            url: 'ffreports/getffnames',
            dataType: "json",
            data: {
                term : request.term,
                buid : $("#business_unit_id").val()
            },
            success: function(data) {
                response(data);
            }
        });
    },
    min_length: 2,
    delay: 300,
    select: function( event, ui ) {
        console.log( "Selected: " + ui.item.value + " aka " + ui.item.id );
        var ff_name = ui.item.label;
        var ff_id = ui.item.ff_id;
        $('#ff_id').val(ff_id);
      }
    });
$(document).ready(function () {
    $( "#reports_start_date" ).datepicker({  maxDate: new Date() });
    $( "#reports_end_date" ).datepicker({  maxDate: new Date() });
    var dateFormat = "mm/dd/yy",
      from = $( "#reports_start_date" ).datepicker({
          //defaultDate: "+1w",
            changeMonth: true,          
          }).on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
      to = $( "#reports_end_date" ).datepicker({
            //defaultDate: "+1w",
            changeMonth: true,        
          }).on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          });
    function getDate( element ) {
        var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        } 
        return date;
    }   
        $("#filterReset").click(function () {
            var bu_id = +($("#business_unit_id").val());
            var bydaymonth = $("#by_day_month").val();
            var filterURL = "/ffreports/getreports?business_unit_id="+bu_id+"&bydaymonth="+bydaymonth;                
            $("#report_form").validate().resetForm();
            $('#report_form')[0].reset();
            $('#ff_id').attr('value','');
            $('#business_unit_id').val(bu_id).trigger('change');
            $("#reports_grid").igGrid({dataSource: filterURL});
            $( "#reports_start_date" ).datepicker({  maxDate: new Date() });
            $( "#reports_end_date" ).datepicker({  maxDate: new Date() });
        });
        $("#reports_start_date").keydown(function(e) {
            e.preventDefault();  
        });
        $("#reports_end_date").keydown(function(e) {
            e.preventDefault();  
        });
    });
    
jQuery.validator.addMethod("tax_class", function (value, element) {
    return this.optional(element) || /^[0-9]\d{0,9}(\.\d{1,2})?%?$/.test(value);
}, "Only 2 decimals are allowed");

$('#report_form').validate({
    rules: {
        reports_start_date: {
            required: true
        },
        reports_end_date: {
            required: true
        },                       
    },
    highlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');
    },
    unhighlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');
    },
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function (error, element) {
        if (element.length) {
            error.insertAfter(element);
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form) {
            event.preventDefault();
            var start_date = $("#reports_start_date").val();
            var end_date   = $("#reports_end_date").val();
            var business_unit_id = +($("#business_unit_id").val());
            var ff_id = $("#ff_id").val(); 
            var ffName = $("#ff_name").val();
            var bydaymonth = $("#by_day_month").val();
            var filterURL = "/ffreports/getreports?start_date=" + start_date+'&end_date='+end_date+"&business_unit_id="+business_unit_id+"&ff_name="+ffName+"&ff_id="+ff_id+"&bydaymonth="+bydaymonth;
                                                    
            //$("#reports_grid").igGrid({dataSource: filterURL});
            makeAjaxCallForigGrid(filterURL,"reports_grid");

            $("#reports_grid").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#reports_grid").igGrid("option", "columns");
                formatigGridContent(columns,"reports_grid");
            });
    }
});

$(function(){
    var token=$('#csrf-token').val();
    var hidden_buid=$('#hidden_buid').val();
    $.ajax({
    type:'get',
    headers: {'X-CSRF-TOKEN': token},
    url:'/getbu',
    success: function(res){        
        res.forEach(data=>{
            $('#business_unit_id').append(data);
        });
    if(res ==''){    
        $('#business_unit_id').select2('val',-1);
        getGrid();
    }
    else{
        $('#business_unit_id').select2('val',hidden_buid);
        getGrid();
    }
    }

      });
   
  });

$("#export_reports").click(function() {
    var sta = $("#reports_start_date").val();
    var end = $("#reports_end_date").val();
    var business_unit_id = $("#business_unit_id").val();
    var ffName = $("#ff_name").val();
    var ff_id = $("#ff_id").val();
    var bydaymonth = $("#by_day_month").val();
     $("#export_reports").attr("href", "ffreports/excelSalesReports?start_date=" + sta+"&end_date="+end+"&ff_name="+ffName+"&business_unit_id="+business_unit_id+"&ff_id="+ff_id+"&by_day_month="+bydaymonth);
    if(sta == '' && end =='') {
        return true;
    } else if(sta != '' && end !='') {
        return true;
   } else {
        alert('please select start date and end date');
  return false;
  }
 
});

/*function getGrid(){
    let bu_id=$('#business_unit_id').val();

    $('#reports_grid').igGrid({
    dataSource: '/ffreports/getreports?business_unit_id='+bu_id,

    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    rowHeight:12,
    enableUTCDates: true,
    expandColWidth: 0,
    renderCheckboxes: true,
    columns: [
        {headerText: 'DC Name', key: 'display_name', dataType: 'string', width: "150px"},
        {headerText: 'Name', key: 'name', dataType: 'string', width: "150px"},
        {headerText: 'Hub Name', key: 'hub_name', dataType: 'string',width: "150px"},
        {headerText: 'Beat', key: 'beat', dataType: 'string',width: "200px"},
        {headerText: 'Orders', key: 'order_cnt', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${order_cnt} </div>'},
        {headerText: 'Calls', key: 'calls_cnt', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${calls_cnt} </div>'},
        {headerText: 'TBV', key: 'tbv', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${tbv} </div>'},
        {headerText: 'UOB', key: 'uob', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${uob} </div>'},
        {headerText: 'ABV', key: 'abv', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${abv} </div>'},
        {headerText: 'TLC', key: 'tlc', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${tlc} </div>'},
        {headerText: 'ULC', key: 'ulc', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${ulc} </div>'},
        {headerText: 'ALC', key: 'alc', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${alc} </div>'},
        {headerText: 'Contribution', key: 'contrib', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${contrib} </div>'},
        {headerText: 'TGM', key: 'margin', dataType: 'string', width: "100px",template: '<div class="rightAlign" style="padding-right:10px"> ${margin} </div>'},
        {headerText: 'Delivered Margin', key: 'delivered_margin', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${delivered_margin} </div>'},
        {headerText: 'Cancel Order Count', key: 'cancel_ord_cnt', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${cancel_ord_cnt} </div>'},
        {headerText: 'Cancel Order Value', key: 'cancel_ord_val', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${cancel_ord_val} </div>'},
        {headerText: 'Return Order Count', key: 'return_ord_cnt', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${return_ord_cnt} </div>'},
        {headerText: 'Return Order Value', key: 'return_ord_val', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${return_ord_val} </div>'},
        {headerText: 'Cancel Order(%)', key: 'cancel_percent', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${cancel_percent} </div>'},
        {headerText: 'Return Order(%)', key: 'return_percent', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${return_percent} </div>'},
        {headerText: 'Today Business', key: 'today_business', dataType: 'string', width: "150px",template: '<div class="rightAlign" style="padding-right:10px"> ${today_business} </div>'},
        {headerText: 'Call Date', key: 'order_date', dataType: 'string', width: "100px"},

    ],
    features: [
                    {
                        name: "ColumnFixing",
                        fixingDirection: "right",
                        columnSettings: [
                            {
                                columnKey: "order_date",
                                isFixed: true,
                                allowFixing: false
                            }
                        ]
                    },
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'order_date', allowFiltering: false},
            ],
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
            columnSettings: [
                {columnKey: 'order_date', allowSorting: false},
            ],
        },
        {
            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        }
    ],
    primaryKey: 'ff_rp_id',
    width: '100%',
    height:'540px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    rendered: function (evt, ui) {
        $("#reports_grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
        $("#reports_grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        $("#reports_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
    }
});
}*/


function getGrid(){
    let bu_id=$('#business_unit_id').val();
    let bydaymonth=$('#by_day_month').val();
    
    makeAjaxCallForigGrid('/ffreports/getreports?business_unit_id='+bu_id+'&page=0&pageSize=10&bydaymonth='+bydaymonth,"reports_grid");

            $("#reports_grid").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#reports_grid").igGrid("option", "columns");
                formatigGridContent(columns,"reports_grid");
              

            });
}


function makeAjaxCallForigGrid(customUrl,selectedId) {
 $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: customUrl,
            type: 'POST',
            dataType:"json",                                          
            beforeSend: function () {
               $('#loader').show();
            },
            complete: function () {
                $('#loader').hide();
            },
            success: function (response) 
            {
                $('#reports_grid_msg').css('display','none');
                console.log(response.headers.length);
                console.log(response.Records.length);
                if (response.headers.length > 0 && response.Records.length > 0) {
                    console.log('in response');
                    var result = customigGridColumns(response.headers);
                    var columnSettingss=[];
                    for(var i=0;i<result.columnHeaders.length;i++){
                            columnSettingss.push({columnKey:result.columnHeaders[i].key});
                    }
                    var customFeatures = customigGridFeatures(10,columnSettingss,response.TotalRecordsCount);
                    customFeatures.push({
                        name: "Summaries",
                        columnSettings: result.columnSummaries,
                        showDropDownButton: false,
                        showSummariesButton: false,
                        type: 'local'
                    });
                   console.log(customFeatures);
                    $('#'+selectedId).igGrid({
                        dataSource: customUrl,//response.Records,
                        responseDataKey: "Records",
                        columns: result.columnHeaders,
                        autoGenerateColumns: false,
                        width: "100%",
                        features: customFeatures,
                    });
                }else{
                    if ($("#"+selectedId).data("igGrid") !=null) {
                        $("#"+selectedId).igGrid("destroy");
                    }
                    $('#reports_grid_msg').css('display','block');
                }
            }
           
        });
}

function formatigGridContent(columns, selectedId) {
        for (var idx = 0; idx < columns.length; idx++) {
            var newText = columns[idx].headerText;
            
            // Summaries UI changes
            /*var id_text = "_summaries_footer_row_text_container_sum_";
            $("#"+ selectedId +"_summaries_footer_row_icon_container_sum_" + newText).remove();
            $("#"+ selectedId + id_text + newText).attr("class", "summariesStyle").text($("#"+ selectedId + id_text + newText).text().replace(/\s=\s/g, ''));
*/
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


    function customigGridColumns(headers) {

        var columnHeaders = [];
        var columnSummaries = [];

        for (var i = 0; i < headers.length; i++) {
            var headerDataType = "string";
            var cssClass = "alignCenter";
            var customWidth = "5%";
            var customHeadText = headers[i];
            
            if (headers[i].substring(0, 2) === "1_") {
                headerDataType = "number";
                cssClass = "alignCenter";
                customWidth = "90px";

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
                    width: "90px",
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
                    width: "90px",
                });
            } else {
                if(headers[i].substring(0, 20) != "1_user_id" && headers[i].substring(0, 20) != "1_le_wh_id" && headers[i].substring(0, 20) != "1_ff_rp_id"){
                columnHeaders.push({
                    headerText: customHeadText,
                    key: headers[i],
                    dataType: headerDataType,
                    columnCssClass: cssClass,
                    headerCssClass: cssClass,
                    width: "150px",
                });
            }
            }
        }

        return {
            "columnHeaders": columnHeaders,
            "columnSummaries": columnSummaries
        };
    }
function customigGridFeatures(customPageSize,columnSettings,TotalRecordsCount){
        return [
                    {
                        name: 'Paging',
                        type: 'remote',
                        pageSize: customPageSize,
                        pageIndexUrlKey: "page",
                        pageSizeUrlKey: "pageSize",
                        recordCountKey: 'TotalRecordsCount'
                    },
                    {
                        name: "Filtering",
                        columnSettings :columnSettings,
                        type: "remote",
                        mode: "simple",
                        filterDialogContainment: "window",
                    },
                    {
                        name: 'Sorting',
                        type: 'remote',
                        persist: false,
                    },
                    {
                        name: "Resizing",
                    },
                    {
                        name: "RowSelectors",
                    },
                    {
                        name: "Selection",
                        multipleSelection: true,
                    },
                    {
                        name: "ColumnFixing",
                    },
                    {
                        name: "Tooltips",
                        visibility: "always",
                        showDelay: 500,
                        hideDelay: 500,
                    }
                ];
    }

</script>


@stop

@extends('layouts.footer')