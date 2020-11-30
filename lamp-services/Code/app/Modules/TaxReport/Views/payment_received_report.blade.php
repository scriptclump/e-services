@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget" style="height: auto;">
                <div class="portlet-title">
                    <div class="caption">{{trans('taxReportLabels.ffpayment_heading')}}</div>
                    <div class="tools ">
                        <div class="actions">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <a href="#tag_1" data-toggle="modal" id = "exportreport" class="btn btn-success">Export Report</a>
                        </div>      
                        <div class="modal modal-scroll fade in" id="tag_1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
                        `   <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <button type="button" id="modalclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="basicvalCode">Export Report</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="ExportForm" action="/taxreport/downloadPaymentReceivedReport" class="text-center" method="POST" onsubmit="return validateform();">
                                        <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">
                                        <div class="row">
                                        <!-- <div class="col-md-12" align="center"> -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-icon right">
                                                    <i class="fa fa-calendar"></i>
                                                    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                                                    <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-icon right">
                                                    <i class="fa fa-calendar"></i>
                                                    <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                    <label class="control-label">DC</label>

                                                    <select class="form-control select2me" id="warehouse" name="warehouse[]" autocomplete="Off">
                                                        <option value>Please Select DC</option>
                                                        <option value="0">All</option>
                                                        @foreach($dcs as $dc)
                                                        @if($dc->lp_wh_name!='')
                                                        <option value="{{ $dc->le_wh_id}}"> {{ $dc->lp_wh_name }}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                    </div>   
                                                </div> 
                                            </div> 
                                       <hr/>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                <div class="input-icon right">
                    <label class="control-label">From Date</label>
                    <i class="fa fa-calendar"></i>
                    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                    <input type="text" id="from_date" name="from_date" class="form-control" placeholder="From Date" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <div class="input-icon right">
                     <label class="control-label">To Date</label>
                    <i class="fa fa-calendar"></i>
                        <input type="text" id="to_date" name="to_date" class="form-control" placeholder="To Date" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">DC</label>
                    <select class="form-control select2me" id="warehouse_filter" name="warehouse[]" autocomplete="Off">
                            <option value="0">All</option>
                            @foreach($dcs as $dc)
                            @if($dc->lp_wh_name!='')
                            <option value="{{ $dc->le_wh_id}}"> {{ $dc->lp_wh_name }}</option>
                            @endif
                            @endforeach
                    </select>
            </div>   
        </div>
        <div class="col-md-3">      
            <div class="form-group searchbutt">
                <label class="control-label"> &nbsp;</label>
                <button class="btn btn-success" id="search_payment_details">Search</button>
            </div>
        </div>    
    </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="paymentsReceivedGrid"></table>
            </div>
        </div>
    </div>

    @stop

    @section('style')
<!-- <style type="text/css">

.ui-iggrid .ui-iggrid-tablebody td:nth-child(3) {

           text-align: left !important;
 }

</style> -->
@stop
        @section('userscript')
            <!--Ignite UI Required Combined CSS Files-->
            <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
            <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
            <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
            <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
            <!--Sumoselect CSS Files-->
            <link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
            <!--Ignite UI Required Combined JavaScript Files--> 
            <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
            <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
            <!-- jquery validation file -->
            <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
            <!-- jquery validatin file included -->
            <!--Sumoselect JavaScriptFiles-->
            <script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
            <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
        @extends('layouts.footer')
<script type="text/javascript">
    $(document).ready(function () {
                                                               
    var dateFormat = "dd/mm/yy";
    from = $( "#fromdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          })
    from_date = $( "#from_date" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          })
    /*.on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          })*/,
      to = $( "#todate" ).datepicker({
            //defaultDate: "+1w",
             /* dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,  */
            defaultDate: "+1w",
              changeYear: true,
            yearRange: "-10:+0", // last ten years
              dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,       
          })
      to_date = $( "#to_date" ).datepicker({
            //defaultDate: "+1w",
             /* dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,  */
            defaultDate: "+1w",
              changeYear: true,
            yearRange: "-10:+0", // last ten years
              dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,       
          })
      /*.on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          })*/;

          from = $( "#fsdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          })
    /*.on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          })*/,
      to = $( "#tsdate" ).datepicker({
            //defaultDate: "+1w",
             /* dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true, */
             defaultDate: "+1w",
            changeYear: true,
          yearRange: "-10:+0", // last ten years
            dateFormat : dateFormat,
            changeMonth: true,         
          })
      /*.on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          })*/;
          
function getDate( element ) {
    var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        } 
      return date;
}  

    $("#fromdate").keydown(function(e) {
            e.preventDefault();  
    });
        $("#todate").keydown(function(e) {
            e.preventDefault();  
        });
    });



 $('#uploadfile').on('click', function() {
    
    var dc=$("#warehouse").val();
    var startDate = document.getElementById("fromdate").value;
    var endDate = document.getElementById("todate").value;
    if ((Date.parse(endDate) < Date.parse(startDate))) {
        alert("To date should be greater than from date");
        return false;
    }

    if(startDate==""){
        alert("Please Select From Date");
        return false;
    }

    if(endDate==""){
        alert("Please Select To Date");
        return false;
    }
    }); 

    
$(document).ready(function ()
    {
        $(function () {
              var from_date = $('#from_date').val();
              var to_date = $('#to_date').val();
              var le_wh_id = $('#le_wh_id').val();
                $('.paymentsReceivedGrid').igGrid({
                      
                        dataSource: '/taxreport/getpaymentdetails?%24fillter=from_date+eq'+from_date+' and to_date+eq'+to_date+' and le_wh_id+eq'+le_wh_id,
                        initialDataBindDepth: 0,
                        autoGenerateColumns: false,
                        mergeUnboundColumns: false,
                        generateCompactJSONResponse: false,
                        responseDataKey: "results", 
                        enableUTCDates: true, 
                        width: "100%",
                         height: "500px",
                         columns: [
                            {headerText: "Payment Code", key: "pay_code", dataType: "string",width:"180px"},
                            {headerText: "Ledger Account", key: "ledger_account", dataType: "string",width:"200px"},
                            {headerText: "Reference Number", key: "txn_reff_code", dataType: "string", width: "150px"},
                            // {headerText: "Mode of Deposit", key: "Mode_Type", dataType: "string", width: "20%"},
                            {headerText: "DC Name", key: "warehouse_name", dataType: "string", width: "150px"},
                            {headerText: "Transaction Date", key: "transaction_date", dataType: "string",width: "150px"},
                            {headerText: "Payment Type", key: "payment_type", dataType: "string", width: "100px"},
                            {headerText: "Amount", key: "pay_amount", dataType: "number", width: "150px"},
                            {headerText: "Created By", key: "Created_By", dataType: "string", width: "150px"},
                            {headerText: "Created At", key: "Created_At", dataType: "string", width: "100px"},
                            
                        ],

                        features: [
                        {
                            name:'Filtering',
                            type: "remote",
                            mode: "simple",
                            allowFiltering: true,
                            filterDialogContainment: "window",
                            columnSettings: [
                             {columnKey: 'pay_code', allowFiltering: true },
                             {columnKey: 'ledger_account', allowFiltering: true },
                             {columnKey: 'txn_reff_code', allowFiltering: true },
                             {columnKey: 'Created_By', allowFiltering: true },
                             {columnKey: 'Created_At', allowFiltering: true },
                             //{columnKey: 'Mode_Type', allowFiltering: true },
                             {columnKey: 'warehouse_name', allowFiltering: true },
                             {columnKey: 'transaction_date', allowFiltering: true },
                             {columnKey: 'payment_type', allowFiltering: true },
                             {columnKey: 'pay_amount', allowFiltering: true },
                             
                    ]
                    },
                    { 
                    name: "Summaries",              
                    type:"local",              
                    showDropDownButton: false,
                    summariesCalculated: function(evt, ui){                         
                    var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                    listPricesummaryCells.each(function() {         
                        if ($(this).text() != "") {    
                            $(this).text($(this).text().substr(2)); 
                            $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                        }
                    });
                    },
                    columnSettings: [          
                        {columnKey: "pay_code", allowSummaries: false},            
                        {columnKey: "ledger_account", allowSummaries: false},            
                        {columnKey: "txn_reff_code", allowSummaries: false},            
                        {columnKey: "warehouse_name", allowSummaries: false},
                        {columnKey: "transaction_date", allowSummaries: false},
                        {columnKey: "payment_type", allowSummaries: false},        
                        {columnKey: "pay_amount", allowSummaries: true, summaryOperands:                                       
                        [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "Created_By", allowSummaries: false},
                        {columnKey: "Created_At", allowSummaries: false}]
                    },
                    {
                    name: "ColumnFixing",
                    fixingDirection: "right",
                    columnSettings: [
                    {
                        columnKey: "pay_code",
                        
                        allowFixing: false,
                    },
                    {
                        columnKey: "ledger_account",
                       
                        allowFixing: false,
                    },
                    {
                        columnKey: "txn_reff_code",
                        
                        allowFixing: false,
                    },
                  
                    {
                        columnKey: "warehouse_name",
                        allowFixing: false
                    },
                    {
                        columnKey: "transaction_date",
                        allowFixing: false
                    },
                    {
                        columnKey: "payment_type",
                        allowFixing: false
                    },
                    {
                        columnKey: "pay_amount",
                        allowFixing: false
                    },
                   
                    {
                        columnKey: "Created_By",
                        allowFixing: false
                    },
                    {
                        columnKey: "Created_At",
                        allowFixing: false
                    },
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
                            ]
                    });

   

});


    $("#search_payment_details").click(function(){

        var token = $('#csrf-token').val();
        var from_date = $('#from_date').val();
        from_date=from_date.split("/").reverse().join("-");
        var to_date = $('#to_date').val();
        to_date=to_date.split("/").reverse().join("-");
        if ((Date.parse(to_date) < Date.parse(from_date))) {
        alert("To date should be greater than from date");
        return false;
        }

        if(from_date==""){
            alert("Please Select From Date");
            return false;
        }

        if(to_date==""){
            alert("Please Select To Date");
            return false;
        }
        var le_wh_id = $('#warehouse_filter').val();
        $(".paymentsReceivedGrid").igGrid({dataSource: '/taxreport/getpaymentdetails?%24fillter=Created_At  between "'+ from_date +'" and "'+ to_date +'" andwarehouse dc_id+eq '+le_wh_id});
        });
    });

    $('#ExportForm').submit(function(){
    $('#modalclose').click();
    });

    $('#exportreport').click(function(){
    $('#fromdate').val('');
    $('#todate').val('');
    $('#warehouse').select2('val','');
    })
                                                           

</script>
 @stop
@extends('layouts.footer')