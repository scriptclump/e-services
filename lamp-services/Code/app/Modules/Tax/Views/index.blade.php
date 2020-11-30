@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<input id="taxClassCreateAccess" type="hidden" name="taxClassCreateAccess" value="{{$taxClassCreateAccess}}">
<input id="taxClassImportAccess" type="hidden" name="taxClassImportAccess" value="{{$taxClassImportAccess}}">
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    {{ trans('manageTaxLabels.heading_1') }}
                </div>
                <div class="actions">
                    @if($taxClassCreateAccess == '1')
                    <a href="#" class="btn green-meadow" data-id="#" data-toggle="modal" data-target="#createrule-modal">{{ trans('manageTaxLabels.createBtn') }}</a>
                    @endif

                    @if($taxClassImportAccess == '1')
                    <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document"class="btn green-meadow">{{ trans('manageTaxLabels.uploadBtn') }}</a>
                    @endif
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <!--Modal POPUP box for creating the tax rule starts here -->
                    <div class="modal fade" id="createrule-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Create Tax Rule</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet box">
                                                <div class="portlet-body">
                                                    {{ Form::open(['id' => 'taxclass']) }}
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_country') }}</label>
                                                                <select name="country" id="country" class="form-control">
                                                                    <option value="99">India</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_state') }}</label>
                                                                <select id="state" name="state" class="form-control">
                                                                    <!-- <option value="*">* (All)</option> -->
                                                                    @foreach ($states as $state)
                                                                    <option value="{{ $state['zone_id'].'_'.$state['code'] }}">{{ $state['name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_zip_pin_code') }}</label>
                                                                <select name="zip_code" id="zip_code" class="form-control">
                                                                    <option value="*">* (All)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_tax_percent') }}</label>
                                                                <input type="number" name="tax_percentage" id="tax_percentage" min=0 value="" class="form-control" step="any"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_tax_type') }}</label>
                                                                <input type="hidden" name="tax_class_id" id="tax_class_id" value=""/>
                                                                <select id="tax_class_type" name="tax_class_type" class="form-control">
                                                                    <option value="">{{ trans('manageTaxLabels.create_tax_popup_select_tax_type') }}</option>
                                                                    @foreach ($types as $type)
                                                                    <option value="{{ $type['master_lookup_name'] }}">{{ $type['master_lookup_name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_tax_class_code') }}</label>
                                                                <input type="text" name="tax_class_code" id="tax_class_code" value="" class="form-control" readonly />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p>Tax Breakup</p>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">SGST</label>
                                                                <input type="number" min = 0 max = 100 name="sgst" id="sgst" value="0" class="form-control"  />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">CGST</label>
                                                                <input type="number" min = 0 max = 100 name="cgst" id="cgst" value="0" class="form-control" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">IGST</label>
                                                                <input type="number" min = 0 max = 100 name="igst" id="igst" value="0" class="form-control" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">UTGST</label>
                                                                <input type="number" min = 0 max = 100 name="utgst" id="utgst" value="0" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>


<!--                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('manageTaxLabels.create_tax_popup_effective_date') }}</label>
                                                                <div class="input-icon input-icon-sm right">
                                                                    <i class="fa fa-calendar"></i>
                                                                    <input type="text" name="start_date" id="start_date" class="form-control" value="" autocomplete="off">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>-->
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn green-meadow" id="taxsave">{{ trans('manageTaxLabels.create_tax_popup_create_btn') }}</button>
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

                    <!-- create modal popup box ends here -->

                    <!--modal popup for upload excel sheet to create the Tax class rule -->

                    <div class="modal fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">{{ trans('manageTaxLabels.upload_tax_popup_title') }}</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet box">
                                                <div class="portlet-body">
                                                    {{ Form::open(['id' => 'taxclass']) }}
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <a href="/download/Tax_class_template.xlsx" class="form-control btn green-meadow">{{ trans('manageTaxLabels.upload_tax_popup_download_btn') }}</a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                                    <div>
                                                                        <span class="btn default btn-file btn green-meadow btnwidth">
                                                                            <span class="fileinput-new">{{ trans('manageTaxLabels.upload_tax_popup_choose_file') }}</span>
                                                                            <span class="fileinput-exists" style="margin-top:-9px !important;">{{ trans('manageTaxLabels.upload_tax_popup_choose_file') }}</span>
                                                                            <input type="file" name="upload_taxfile" id="upload_taxfile" value="" class="form-control" />
                                                                        </span>
                                                                        <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="button"  class="btn green-meadow" id="taxfile-upload-button">{{ trans('manageTaxLabels.upload_tax_popup_create_btn') }}</button>
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
                    <!--  end of upload excel   -->
                    <table id="countrygrid"></table>
                    <table id="grid"></table>
                    <table id="filtered-data"></table>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>

@stop

@section('userscript')
<style type="text/css">
    .btnwidth{width:250px;}
    .fa-pencil{ color:#3598DC !important;}
    .actionss{padding-left: 22px !important;}
    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}

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

    .textRightAlign {
    text-align:right;
    }
    .mapcntalign{
    text-align:right;
    }


</style>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<!--Bootstrap JavaScript & CSS Files-->
@extends('layouts.footer')

<script>
$(function () {
    $('#start_date').datepicker({
        autoclose: true,
        dateFormat: 'yy-mm-dd'
    });

    $('#countrygrid').igHierarchicalGrid({
        dataSource: '/tax/countryname?country_id=99',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'results',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "{{ trans('manageTaxLabels.gridLevel_1_column_1') }}", key: "name", dataType: "string", width: "15%"}

        ],
        columnLayouts: [
            {
                dataSource: '/tax/onlystatenames?country_id=99',
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'states',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {headerText: "{{ trans('manageTaxLabels.gridLevel_2_column_1') }}", key: "name", dataType: "string", width: "50%"},
                    {headerText: "{{ trans('manageTaxLabels.gridLevel_2_column_2') }}", key: "actions", dataType: "string", width: "50%"}
                ],
                columnLayouts: [
                    {
                        dataSource: '/tax/dashboard',
                        autoGenerateColumns: false,
                        mergeUnboundColumns: false,
                        responseDataKey: "results",
                        generateCompactJSONResponse: false,
                        enableUTCDates: true,
                        width: "100%",
                        columns: [
                            {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_1') }}", key: "tax_class_type", dataType: "string", width: "25%"},
                            {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_2') }}", key: "tax_class_code", dataType: "string", width: "40%"},
                            // {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_3') }}", key: "coutryname", dataType: "string", width: "25%", hidden: true},
                            // {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_4') }}", key: "name", dataType: "string", width: "20%", hidden: true},
                            {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_3') }}", key: "tax_percentage", dataType: "string", width: "20%", template: '<div class="textRightAlign"> ${tax_percentage} </div>'},
                            {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_4') }}", key: "mappingcount", dataType: "string", width: "20%", columnCssClass: "mapcntalign"},
//                            {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_5') }}", key: "date_start", dataType: "date", width: "15%"},
                            {headerText: "{{ trans('manageTaxLabels.gridLevel_3_column_6') }}", key: "action", dataType: "string", width: "20%"},
                        ],
                        features: [
                            {
                                name: "Sorting",
                                type: "remote",
                                columnSettings: [
                                    {columnKey: 'action', allowSorting: false},
                                    {columnKey: 'coutryname', allowSorting: false},
                                    {columnKey: 'mappingcount', allowSorting: false},
                                ]

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
                        primaryKey: 'tax_class_id',
                        width: '100%',
                                height: '100%',
                        initialDataBindDepth: 0,
                        localSchemaTransform: false
                    }],
                features: [
                    {
                        name: "Filtering",
                        type: "local",
                        mode: "simple",
                        filterDialogContainment: "window",
                        columnSettings: [{columnKey: 'actions', allowFiltering: false}]
                    }],
                primaryKey: 'zone_id',
                width: '100%',
                height: '500px',
                localSchemaTransform: false,
                rendered: function (evt, ui) {
                    $("#countrygrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
                    $("#countrygrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                    $("#countrygrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                    $("#countrygrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
                    $("#countrygrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
                    $("#countrygrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                }
            }],
        primaryKey: 'country_id',
        width: '100%',
        height: '610px',
        initialDataBindDepth: 0,
        initialExpandDepth: 0, // Auto expand child grids if value is 0
        localSchemaTransform: false
    });


});

function deleteVal(deleteid,taxcode)
{
    var token = $("#take").val();
    var deletemsg = "{{ trans('tax.delete') }}";
    deletemsg = deletemsg.replace('CODE', taxcode);
    var confirmation_alert = confirm("Do you want to Delete this Tax Rule?");

    if (confirmation_alert == true)
    {
        $.ajax({
            type: "GET",
            url: "/tax/deleterule/" + deleteid + "?_token=" + token,
            success: function (data)
            {
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+deletemsg+'</div></div>');
                $(".alert-success").fadeOut(20000)
                var firstChildGrid = $('#countrygrid').igHierarchicalGrid('allChildrenWidgets')[1];
                firstChildGrid.dataBind();
            },
            error: function (data)
            {
                alert("error");
            }
        });
    } else
    {
        return false;
    }
}

jQuery.validator.addMethod("tax_class", function (value, element) {
    return this.optional(element) || /^[0-9]\d{0,9}(\.\d{1,2})?%?$/.test(value);
}, "Only 2 decimals are allowed");

jQuery.validator.addMethod("all_taxes_percentages", function (value, element) {
    console.log("testingggg");
    

    return isNullOrWhitespace();
}, "All SGST, CGST, IGST and UTGST shouldn't be zero");


jQuery.validator.addMethod("all_sum_not_hundred", function (value, element) {
    return sumofGSTs();
}, "Invalid data");

function isNullOrWhitespace() {
    var cgst = $("#cgst").val();
    var sgst = $("#sgst").val();
    var igst = $("#igst").val();
    var utgst = $("#utgst").val();

    if(cgst == 0 && sgst == 0 && igst == 0 && utgst == 0)
    {
        return false;
    }else{
        return true;
    }
}

function sumofGSTs() {
    var cgst = $("#cgst").val();
    var sgst = $("#sgst").val();
    var igst = $("#igst").val();
    var utgst = $("#utgst").val();
    var gst_sum = parseInt(cgst) + parseInt(sgst);
    var sum = gst_sum + parseInt(igst) + parseInt(utgst);
    var utgst_sum =  parseInt(cgst) + parseInt(utgst);

    if(sum > 100)
    {
        return false;
    }else{
        if(gst_sum != 100 && gst_sum > 0)
        {
            return false;
        }else if(igst != 100 && igst > 0)
        {
            return false;
        }else if(utgst_sum != 100 && utgst_sum > 0){
            return false;
        }else{
            return true;
        }
    }
}

/*$("#sgst").blur(function(){
    var sgst = $("#sgst").val();
    var cgst = 100 - sgst;
    $("#cgst").val(cgst);
});

$("#cgst").blur(function(){
    var sgst = $("#cgst").val();
    var cgst = 100 - sgst;
    $("#sgst").val(cgst);
});*/

$('#taxclass').validate({
    rules: {
        tax_class_type: {
            required: true
        },
        tax_class_code: {
            required: true
        },
        country: {
            required: true
        },
        state: {
            required: true
        },
        zip_code: {
            required: true
        },
        tax_percentage: {
            required: true,
            tax_class: true

        },
        sgst: {
            required: false,
            all_taxes_percentages: true,
            //all_sum_not_hundred : true
        },
        // sgst: {
        //     required: false,
        //     all_taxes_percentages: true
        // },
        // igst: {
        //     required: false,
        //     all_taxes_percentages: true
        // },
        // utgst: {
        //     required: false,
        //     all_taxes_percentages: true
        // },

        'category[]': {
            required: true,
            minlength: 1
        }
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
        var state_percent = new Array();
        var cat = [];
        var final_url = "";
        var taxclassname = $("#tax_class_type").val();
        var taxclass_code = $("#tax_class_code").val();
        var country = $("#country").val();
        var state = $("#state").val();
        var zip_code = $("#zip_code").val();
        var tax = $("#tax_percentage").val();
        var start_dt = $("#start_date").val();
        var end_dt = $("#end_date").val();
        var sgst = $("#sgst").val();
        var cgst = $("#cgst").val();
        var igst = $("#igst").val();
        var utgst = $("#utgst").val();
        var token = $("input[name=_token]").val();
        var taxClassId = $("#tax_class_id").val();
        if (taxClassId == "" || taxClassId == null) {
            $('#taxsave').html("{{ trans('manageTaxLabels.create_tax_popup_create_btn') }}");
            final_url = "/tax/create?_token=" + token;
            state_percent.push({"taxclass": taxclassname, "taxclasscode": taxclass_code, "country": country, "state": state, "zip_code": zip_code, "tax_percentage": tax, "start_date": start_dt, "end_date": end_dt, "cgst" : cgst, "sgst" : sgst, "igst" : igst, "utgst" : utgst});
        } else {
            final_url = "/tax/update?_token=" + token;
            state_percent.push({"tax_class_id": taxClassId, "taxclass": taxclassname, "taxclasscode": taxclass_code, "country": country, "state": state, "zip_code": zip_code, "tax_percentage": tax, "start_date": start_dt, "end_date": end_dt, "cgst" : cgst, "sgst" : sgst, "igst" : igst, "utgst" : utgst});
        }
        // state_percent.push({"taxclass": taxclassname, "taxclasscode": taxclass_code, "country": country, "state": state, "zip_code": zip_code, "tax_percentage": tax, "start_date": start_dt, "end_date": end_dt, "cgst" : cgst, "sgst" : sgst, "igst" : igst, "utgst" : utgst});
        $.ajax({
            url: final_url,
            type: "POST",
            data: "details=" + JSON.stringify(state_percent),
            success: function (data)
            {
                var TaxClasscode = $("#tax_class_code").val();
                var updatemsg = "{{ trans('tax.update') }}";
                updatemsg = updatemsg.replace('CODE', TaxClasscode);
                var createmsg = "{{ trans('tax.create') }}";
                createmsg = createmsg.replace('CODE', TaxClasscode);
                var duplicate = "{{ trans('tax.duplicate') }}";
                duplicate = duplicate.replace('CODE', TaxClasscode);
                var effective_date = "{{ trans('tax.effectivedate_exists') }}";
                if (taxClassId == "" || taxClassId == null) {
                    $("#tax_class_type").val('');
                    $("#tax_class_code").val('');
                    $("#state").val('4035_*');
                    $("#tax_percentage").val('');
                    $("#start_date").val('');
                    $("#end_date").val('');
                    $("#tax_class_id").val('');
                    $('#taxsave').html("{{ trans('manageTaxLabels.create_tax_popup_create_btn') }}");
                }
                if (data == "error")
                {
                    $('#createrule-modal').modal('toggle');
                    $("#countrygrid").igHierarchicalGrid("dataBind");
                    $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>' + duplicate + '</div></div>');
                    $(".alert-warning").fadeOut(20000);
                    return false;
                }

                if (data == "effective-exists")
                {
                    $('#createrule-modal').modal('toggle');
                    $("#countrygrid").igHierarchicalGrid("dataBind");
                    $("#error_message").html('<div class="flash-message"><div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert"></button>' + effective_date + '</div></div>');
                    $(".alert-warning").fadeOut(20000);
                    return false;
                }

                if ($.isNumeric(data))
                {//success message while craeting the new Tax Class rule
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+createmsg+'</div></div>');
                    $(".alert-success").fadeOut(20000)
                } else
                {//success message while editing
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+updatemsg+'</div></div>');
                    $(".alert-success").fadeOut(20000)
                }
                $('#createrule-modal').modal('toggle');
                // var firstChildGrid = $('#countrygrid').igHierarchicalGrid('allChildrenWidgets')[0];
                // firstChildGrid.dataBind();
                $("#countrygrid").igHierarchicalGrid("dataBind");
            }
        });
    }
});

$("#taxfile-upload-button").click(function () {
    var stn_Doc = $("#upload_taxfile")[0].files[0];
    var formData = new FormData();
    var token = $("#take").val();
    if (typeof stn_Doc == 'undefined')
    {
        alert("{{ trans('tax.select_file') }}");
        return false;
    }
    formData.append('upload_doc', stn_Doc);
    formData.append('test', "sample");
    $.ajax({
        type: "POST",
        url: "/tax/uploadexcelsheet?_token=" + token,
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#loadingmessage').show();
        },
        complete: function () {
            $('#loadingmessage').hide();
        },
        success: function (data)
        {
            // $("#success_message").html('<div class="flash-message">
            //     <div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'
            //      + "Upload Result:<br> Total Insert: " + data.success + " || " + "Failed Records: " + data.failedcount + " || " + "updated count: " + data.update + " || " + "<a target='_blank' href=/" + datalink + ">View Details</a>" + '</div></div>');
            var LINK = "<a target='_blank' href=/tax/accesslogs/" + data.reference + ">View Details</a>";
            var consolidatedmsg = "{{ trans('tax.tax_creation') }}";
                consolidatedmsg = consolidatedmsg.replace('INSERT', data.successcount);
                consolidatedmsg = consolidatedmsg.replace('FAILED', data.failedcount);
                consolidatedmsg = consolidatedmsg.replace('UPDATE', data.updatecount);
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
            if(data.headcount == 0)
            {
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers mis-matched </div></div>');
                return false;
            }
            var datalink = data.datalink;
            $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>' + consolidatedmsg + '</div></div>');
            $("#upload_taxfile").val("");
            $('#upload-document').modal('toggle');
            $("#countrygrid").igHierarchicalGrid("dataBind");
        }
    });
});

$('#createrule-modal').on('shown.bs.modal', function (e) {
    var myID = $(e.relatedTarget).data('id');
    $('#country').attr("disabled", false);
    $('#state').attr("disabled", false);
    if (myID == "#")
    {
        $("#tax_class_id").val("");
        $("#tax_class_type").val("");
        $("#tax_class_code").val("");
        $("#tax_percentage").val("");
        $("#start_date").val("");
        $('#taxsave').html("{{ trans('manageTaxLabels.create_tax_popup_create_btn') }}");
        $('#myModalLabel').html("{{ trans('manageTaxLabels.create_tax_popup_create_title') }}");
        return false;
    }
    var datatypefortext = $(e.relatedTarget).data('type');
    if (datatypefortext == 'add')
    {
        $("#tax_percentage").val("");
        $("#tax_class_name").val("");
        $("#start_date").val("");
        $("#tax_class_id").val("");
        $('#tax_class_code').val('');
        $('#tax_class_type').val('');

        $('#taxsave').html("{{ trans('manageTaxLabels.create_tax_popup_create_btn') }}");
        $('#myModalLabel').html("{{ trans('manageTaxLabels.create_tax_popup_create_title') }}");
    }

    if (datatypefortext == 'edit')
    {
        $('#taxsave').html("{{ trans('manageTaxLabels.create_tax_popup_update_btn') }}");
        $('#myModalLabel').html("{{ trans('manageTaxLabels.create_tax_popup_update_title') }}");
    }
    var token = $("#take").val();
    $.ajax({
        type: "GET",
        url: "/tax/edit/" + myID + "?_token=" + token,
        dataType: "json",
        beforeSend: function () {
            $('#loadingmessage').show();
        },
        complete: function () {
            $('#loadingmessage').hide();
        },
        success: function (data)
        {
            console.log(data);
            if (data['taxclassdata'] != null)
            {
                if (data['taxclassdata']['date_start'] == "1970-01-01" || data['taxclassdata']['date_start'] == "0000-00-00")
                {
                    $("#start_date").val(startDate);
                } else
                {
                    var st_dt = new Date(data['taxclassdata']['date_start']);
                    var day = st_dt.getDate();
                    var month = st_dt.getMonth() + 1;
                    var year = st_dt.getFullYear();
                    var startDate = year + '-' + month + '-' + day;
                    $("#start_date").val(startDate);
                }

                // var ed_dt = new Date(data['taxclassdata']['date_end']);
                // var end_day = ed_dt.getDate();
                // var end_month = ed_dt.getMonth() + 1;
                // var end_year = ed_dt.getFullYear();
                // var end_Date = end_year + '-' + end_month + '-' + end_day;

                $("#tax_class_type").val(data['taxclassdata']['tax_class_type']);
                $("#tax_class_code").val(data['taxclassdata']['tax_class_code']);
                $("#tax_percentage").val(data['taxclassdata']['tax_percentage']);
                $("#cgst").val(data['taxclassdata']['CGST']);
                $("#sgst").val(data['taxclassdata']['SGST']);
                $("#igst").val(data['taxclassdata']['IGST']);
                $("#utgst").val(data['taxclassdata']['UTGST']);
                // $("#end_date").val(end_Date);
                $("#tax_class_id").val(data['taxclassdata']['tax_class_id']);
            }

            $.each(data['states'], function (index, value) {

                if (data['taxclassdata'] != null)
                {
                    if (data['taxclassdata']['state_id'] == value['zone_id'])
                    {
                        stateid_val = value['zone_id'] + "_" + value['code'];
                    }
                } else
                {
                    if (myID == value['zone_id'])
                    {
                        stateid_val = value['zone_id'] + "_" + value['code'];
                    }
                }
            });
            $("#state").val(stateid_val);
            $('#state').attr("disabled", true);
            $('#country').attr("disabled", true);
        }
    });
})

$('#tax_percentage').blur(function () {
    var taxclassname = $("#tax_class_type").val();
    var state = $("#state").val();
    var arr = state.split('_');
    var percentage = $("#tax_percentage").val();
    var taxcode = taxclassname + "_IN" + "_" + arr[1] + "_" + "*" + "_RATE" + "-" + percentage;
    $("#tax_class_code").val(taxcode);

});

$('#tax_class_type,#state').change(function () {
    var taxclassname = $("#tax_class_type").val();
    var state = $("#state").val();
    var arr = state.split('_');
    var percentage = $("#tax_percentage").val();
    var taxcode = taxclassname + "_IN" + "_" + arr[1] + "_" + "*" + "_RATE" + "-" + percentage;
    $("#tax_class_code").val(taxcode);
});

/*$("#tax_percentage").blur(function(){
 var tax_percentage = $("#tax_percentage").val();
 var arr = tax_percentage.split('.');
 var decimals = arr[1];
 // alert("Length="+decimals.length);
 if(decimals.length > 2)
 {
 $("#tax_percentage-error").text("Pleaseee").css('display','block');
 $("#tax_percentage").focus();
 return false;
 }      
 });*/

$(".modal").on('hide.bs.modal', function () {
    var form_id = $(this).find('form').attr('id');
    var validator1 = $("#" + form_id).validate();
    validator1.resetForm();
    $("#" + form_id + " div.form-group").removeClass('has-error');
});

$("#createrule-modal").on('hide.bs.modal', function () {
    $("#sgst").val(0);
    $("#cgst").val(0);
    $("#igst").val(0);
    $("#utgst").val(0);

   $("#state").val("4035_*");
});



$('#upload-document').on('hidden.bs.modal', function (e) {
    console.log("Modal hidden");
    $("#upload_taxfile").val("");
    $(".fileinput-filename").html("");
});
</script>

@stop
@extends('layouts.footer')