@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>

<input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title" style="margin-top: 10px">
                <div class="caption">Inventory Batch</div>
                <!-- <div class="actions"> -->
                    <form id="batch_report" method="post" action="/inventory/batchreport">
                        <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="col-md-2">
                            <input type="text" id="search_sku" class="form-control" placeholder="SKU,Product Name,UPC" />
                            <input type="hidden" id="addproduct_id" name="addproduct_id" class="form-control" placeholder="SKU,Product Name,UPC" />
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <select class="form-control select2me" id="main_batch_idlist" name="main_batch_idlist" autocomplete="Off">
                                    <option value=" ">Select Batch ID</option>                               
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="uploadfile" class="btn green-meadow">Submit</button>
                        </div>

                        <div class="col-md-1">
                            <button type="submit" id="batch_export" class="btn green-meadow">Export</button>
                        </div>
                    </form>
                <!-- </div> -->
                
            </div> 
            <div class="portlet-body">
                
                </div>
                <div class="table-scrollable">
                    <table class="inv-title" id="inventorygrid"></table>
                </div>

            </div>
        </div>
    </div>
</div>


@stop

@section('userscript')
<style type="text/css">
.butmargtop{ margin-top:15px;}
.notific{font-size: 11px; color:f00;}

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
    .SumoSelect > .optWrapper > .options li label {
    white-space: pre-wrap !important;
    word-wrap: break-word !important;
    width: 250px !important;
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
    label {
        padding-bottom: 0px !important;
    }

    .textLeftAlign {
        text-align:left;
    }

    .textRightAlign {
        text-align:right !important;
    }

    .textCenterAlign{
     text-align:center !important;   
    }

    .mapalign {
        text-align:right !important;
    }

    /*edit range slider styles*/	
    .range-min-css{
        -moz-appearance: none;
        border-style: solid;
        border-width: 1px;
        box-sizing: content-box;
        display: block;
        float: left;
        font-size: 14px;
        font-weight: 700;
        height: 20px;
        line-height: 20px;
        margin: 0;
        outline: 0 none;
        padding: 4px;
        text-align: center;
        vertical-align: text-bottom;
        width: 55px;
    }
    .range-max-css{
        -moz-appearance: none;
        border-style: solid;
        border-width: 1px;
        box-sizing: content-box;
        float: right;
        font-size: 14px;
        font-weight: 700;
        height: 20px;
        line-height: 20px;
        margin: 0;
        outline: 0 none;
        padding: 4px;
        position: relative;
        text-align: center;
        top: -30px;
        vertical-align: text-bottom;
        width: 55px;
    }
    .slider{
        padding-top:6px;
        height: 30px;
        margin: 0 80px;
        overflow: visible;
        position: relative;
    }

    .idallign{
        margin-left:34px;
    }
    #ui-id-1{
        width: 280px !important;
    }

.loader {
 position:relative;
 top:40%;
 left: 40%;
 border: 5px solid #f3f3f3;
 border-radius: 50%;
 border-top: 5px solid #d3d3d3;
 width: 50px;
 height: 50px;
 -webkit-animation: spin 2s linear infinite;
 animation: spin 2s linear infinite;
 z-index : 9999999;
}

.ui-autocomplete{
    z-index: 10100 !important; top:10px;  height:100px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;
    position:fixed !important;
}


.ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
}
	
</style>
<!-- datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<!--Sumoselect JavaScriptFiles-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<!--Ignite UI Required Combined JavaScript Files--> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
@extends('layouts.footer')
<script type="text/javascript">
        autosuggest();
        function autosuggest(){
            $( "#search_sku" ).autocomplete({
                 source: '/inventory/getbatchSkus?supplier_id='+$('#supplier_list').val()+'&warehouse_id='+$('#warehouse_list').val(),
                 minLength: 2,
                 params: { entity_type:$('#supplier_list').val() },
                 select: function( event, ui ) {
                      if(ui.item.label=='No Result Found'){
                         event.preventDefault();
                      }
                      $('#addproduct_id').val(ui.item.product_id);
                      //$('#prod_sku').text(ui.item.sku);
                      //$('#prod_mrp').text(ui.item.mrp);
                      $.ajax({
                             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                             url: '/inventory/getbatchIdsbySKU',
                             type: 'POST',
                             data: {product_id:ui.item.product_id},
                             success: function (data) {
                                $("#main_batch_idlist").empty();                
                                $("#main_batch_idlist").append(data);
                                $('#main_batch_idlist').select2('val', '');
                             },
                             error: function (response) {

                             }
                         });
                 }
             });
        }
    var dateFormat = "dd-mm-yyyy";
    from = $( "#fromdate" ).datepicker({
            format : dateFormat,
            changeMonth: true,          
          }),
      to = $( "#todate" ).datepicker({
              format : dateFormat,
              maxDate:0,
            changeMonth: true,        
          });
       $('#uploadfile').on('click', function() {
    
            var startDate = document.getElementById("fromdate").value;
            var endDate = document.getElementById("todate").value;
            var main_batch_idlist = document.getElementById("main_batch_idlist").value;
            var product_id = $('#addproduct_id').val();

            if ((Date.parse(endDate) < Date.parse(startDate))) {
              alert("To date should be greater than End date");
             return false;
            }

            if(startDate=="" && endDate!=''){
                alert("Please Select From Date");
                return false;
            }

            if(endDate=="" && startDate!=''){
                alert("Please Select To Date");
                return false;
            }
            $("#inventorygrid").igGrid({dataSource: '/inventory/inventorybatchhistory?%24fillter=fromdate+eq'+startDate+' and todate+eq'+endDate+' and mainbatchidsfilter+eq'+main_batch_idlist+' and product_id+eq'+product_id}).igGrid("dataBind");
        });
    $("#fromdate").datepicker().datepicker("setDate", new Date());
    $("#todate").datepicker().datepicker("setDate", new Date());
    var startDate1 = document.getElementById("fromdate").value;
    var endDate1 = document.getElementById("todate").value;   
    $('#inventorygrid').igGrid({
        dataSource: '/inventory/inventorybatchhistory?%24fillter=fromdate+eq'+startDate1+' and todate+eq'+endDate1,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'Batch ID', key: 'main_batch_id', dataType: 'string'},
            {headerText: 'Product Name', key: 'SKU', dataType: 'string'},
            {headerText: 'Supplier', key: 'display_name', dataType: 'string'},
            {headerText: 'Business Name', key: 'business_legal_name', dataType: 'string'},
            {headerText: 'Customer Type', key: 'legal_entity_type', dataType: 'string',columnCssClass: 'textCenterAlign'},
            {headerText: 'Order Qty', key: 'ord_qty', dataType: 'number',columnCssClass: 'textCenterAlign'},
            {headerText: 'Invoice Qty', key: 'inv_qty', dataType: 'numbers',columnCssClass: 'textCenterAlign'},             
            {headerText: 'ESP', key: 'esp', dataType: 'number',columnCssClass: 'textCenterAlign'},       
            {headerText: 'ELP', key: 'elp', dataType: 'number',columnCssClass: 'textCenterAlign'},

            {headerText: 'MFG Date', key: 'mfg_date', dataType: 'date',columnCssClass: 'textCenterAlign'},

            {headerText: 'EXP Date', key: 'exp_date', dataType: 'date',columnCssClass: 'textCenterAlign'},            
            /*{headerText: 'created at', key: 'created_at', dataType: 'string'},*/
                ],
        features: [
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 20,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",

        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,

        }
        ],
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false});

  $('#batch_export').on('click', function() {
    
            var startDate = document.getElementById("fromdate").value;
            var endDate = document.getElementById("todate").value;
            var main_batch_idlist = document.getElementById("main_batch_idlist").value;
            var product_id = $('#addproduct_id').val();

            if ((Date.parse(endDate) < Date.parse(startDate))) {
              alert("To date should be greater than End date");
             return false;
            }

            if(startDate=="" && endDate!=''){
                alert("Please Select From Date");
                return false;
            }

            if(endDate=="" && startDate!=''){
                alert("Please Select To Date");
                return false;
            }
            $('#batch_report').submit();
        });
</script>
@stop
@extends('layouts.footer')