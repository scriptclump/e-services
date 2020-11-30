@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
<input id="taxMapAccess" type="hidden" name="taxMapAccess" value="{{$taxMapAccess}}">
<input id="taxMapImportAccess" type="hidden" name="taxMapImportAccess" value="{{$taxMapImportAccess}}">
<span id="success_message"></span>
<span id="text-file-mapping-issues"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    {{ trans('taxMapLabels.heading_1') }}
                </div>
                <div class="tools">
                    &nbsp;
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label greenlabel">{{ trans('taxMapLabels.filter_by') }} :</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="caption-helper sorting">
                                            <select id="tax_type_home" name="tax_type_home" class="form-control" placeholder='Tax Type' >
                                                <option value="ALL">ALL</option>
                                                <?php
                                                for ($i = 0; $i < sizeof($alltax); $i++) {
                                                    if ($alltax[$i]['master_lookup_name'] != 'Action') {
                                                        ?>
                                                        <option value="<?php echo $alltax[$i]['master_lookup_name'] ?>"><?php echo $alltax[$i]['master_lookup_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 pull-right text-right">
                        @if($taxMapImportAccess=='1')
                        <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow">{{ trans('taxMapLabels.upload_product_tax_btn') }}</a>
                        @endif
                    </div>
                </div>
                <div class="table-scrollable">
                    <table id="grid"></table>
                    <table id="filtered_grid"></table>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <input type="hidden" name="all_states" id="all_states" data-all_states="{{ $all_state_ids }}"/>
                        <input type="hidden" name="vat_state_wise_tax_classes" id="vat_state_wise_tax_classes" data-vat_state_wise_tax_classes="{{ $state_wise_tax_classes }}"/>
                    </div>
                </div>
                <div class="modal modal-scroll fade" id="permissions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000px">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                            </div>
                            <div class="modal-body">
                                <span id="work_flow_message"></span>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_product_title') }}</b></label>
                                            <label class="control-label" id='productNamedata'></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_sku') }}</b></label>
                                            <label class="control-label" id="skuiddata"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_category') }}</b></label>
                                            <label class="control-label" id='categorydata'></label>
                                        </div>
                                    </div>  
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_brand') }}</b></label>
                                            <label class="control-label" id="branddata"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_hsncode') }}</b></label>
                                            <label class="control-label" id='hsncode'></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_hsndesc') }}</b></label>
                                            <label class="control-label" id="hsndesc"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_hsnper') }}</b></label>
                                            <label class="control-label" id="hsnper"></label>
                                        </div>
                                    </div>
                                </div>
                                <div id="tableContainer" class="row tableContainer">
                                    <div class="col-md-4">
                                        <table id="taxTypes"></table>
                                    </div>
                                    <div class="col-md-8"> 
                                        <table id="productmappingdetailss" class='productmappingdetailss'></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal modal-scroll fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{{ trans('taxMapLabels.uploadtax_popup_title') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/tax/downloadExcelForMapping', 'id' => 'downloadexcel-mapping'))}}
                                                <!-- <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn green-meadow btnwidth" id="download-excel">Download Tax Class Template</button>
                                                        </div>
                                                    </div> -->
                                                <!-- <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" name="withdata" id='withdata'> WIth Data
                                                    </div>
                                                </div> -->

                                            </div>
                                            <div id='filters'>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="hidden" name="hiddencats" id="hiddencats">
                                                            <select id="category_filter" name="category_filter[]" class="form-control multi-select-search-box common" multiple="multiple" placeholder="{{ trans('taxMapLabels.uploadtax_popup_category_select') }}">
                                                                {!!html_entity_decode($allCats)!!}
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="hidden" name="hiddenbrands" id="hiddenbrands">
                                                            <select id="brand_filter"  name="brand_filter[]"  class="form-control multi-select-search-box common" multiple="multiple" placeholder="{{ trans('taxMapLabels.uploadtax_popup_brand_select') }}">
                                                                {!!html_entity_decode($brands)!!}
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <select id="tax_type" name="tax_type" class="form-control" required placeholder='Tax Type' >
                                                                <option value=""><strong>{{ trans('taxMapLabels.uploadtax_popup_tax_select') }}</strong></option>
                                                                <?php
                                                                for ($i = 0; $i < sizeof($alltax); $i++) {
                                                                    if ($alltax[$i]['master_lookup_name'] != 'Action') {
                                                                        ?>
                                                                        <option value="<?php echo $alltax[$i]['master_lookup_name'] ?>"><?php echo $alltax[$i]['master_lookup_name']; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-8"><button type="submit" class="btn green-meadow" id="download-excel">{{ trans('taxMapLabels.uploadtax_popup_download_btn') }}</button></div>
                                                                <div class="col-md-4"><button type="button" id="reset-button" class="btn green-meadow" title="Reset Form"><i class="fa fa-undo "></i></button></div>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="checkbox" name="withdata" id='withdata'> WIth Data
                                                            </div>
                                                        </div> -->
                                                    </div>

                                                </div>
                                            </div>
                                            {{ Form::close() }}
                                            {{ Form::open(array('url' => '/tax/downloadhsndetails', 'id' => 'download_hsn_details'))}}
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="submit" class="col-md-6 btn green-meadow" id="download_hsn_excel">{{ trans('taxMapLabels.uploadtax_popup_download_hsncodes_btn') }}</button>
                                                </div>
                                            </div>
                                            {{ Form::close() }}
                                            <br />
                                            {{ Form::open(['id' => 'taxclass']) }}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:5px !important;">
                                                            <div>
                                                                <span class="btn default btn-file btn green-meadow btnwidth">
                                                                    <span class="fileinput-new">{{ trans('taxMapLabels.uploadtax_popup_choose_file') }}</span>
                                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Tax Class File</span>
                                                                    <input type="file" name="upload_taxfile" id="upload_taxfile" value="" class="form-control"/>
                                                                </span>
                                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label"> </label>
                                                        <button type="button" class="col-md-12 btn green-meadow" id="taxfile-upload-button">{{ trans('taxMapLabels.uploadtax_popup_upload_btn') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <span id="loader" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
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
                <div class="modal fade" id="create-mapping" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Assign Tax Rule</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(['id' => 'taxclass']) }}
                                                <div class="row">
                                                    <div class="text-center"><span class="alert alert-warning" id="nodata" style="display: none">All available taxes already applied.</span></div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label"><b>Avaliable Tax Rules</b></label> &nbsp;&nbsp;
                                                            <select name='assign-maping' id='assign-maping' class="form-control"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center"><span class="alert alert-warning" id="error" style="display: none">Please select one tax class to assign.</span></div>
                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <button type="button"  class="btn green-meadow" id="savetax-mapping" disabled="disabled">Assign Tax Class</button>
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
            @include('Tax::taxmapHorizontalGrid')
        </div>
    </div>
</div>
@stop

@section('userscript')
<style type="text/css">
    .CaptionCont {
        font-weight: bold !important;
        color: #928e8e !important;
    }
    .btnwidth{width:285px;}
    .modal.modal-wide .modal-dialog {
        width: 90%;
    }
    .modal-wide .modal-body {
        height: 550px;
    }

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
    .SumoSelect > .CaptionCont {
        font-weight: bold !important;
        color: #928e8e !important;
    }

    .sel-Approved{font-weight: bold; color: #1BBC9B;}


    .greenlabel{
        text-decoration: none !important;
        color: #32c5d2 !important;
        font-weight: bold!important; font-size:14px;
        line-height: 2; margin-bottom: 0; padding-bottom: 0;  

    }

    .ui-autocomplete{
        z-index: 10100 !important; top:10px;  height:100px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;
        position:fixed !important;
    }


  .ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
    }



</style>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<!--Sumo select CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!-- End of sumo Css -->
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.datasource.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.ui.editors.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.ui.grid.framework.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.ui.grid.updating.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.ui.shared.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.ui.validator.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.util.js') }}" type="text/javascript"></script>
<!--Product Tax Mapping igGrid JavaScript File-->
<script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/cellFormatter.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/taxMapDashboard_TaxTypeWise.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/taxMapDashboard.js')}}" type="text/javascript"></script>
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/product_tax_map_grid.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/product_taxMap_Horizontal.js') }}" type="text/javascript"></script>
<!-- Sumo select JS file -->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<!-- Sumo select js was end -->
@extends('layouts.footer')
<script>

function restrictDBLclick()
{
    $("#grid").dblclick(function (evt) {

        $("html, body").animate({ scrollTop: 0 }, 600); //scroll to top automatically
       $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');
        $(".alert-danger").fadeOut(10000); 

    });
}

// $("#grid").on("iggridupdatingeditcellstarting", function (evt, ui) {
//     var token = $("#token_value").val();
//                 $.ajax({
//                     type:'POST',
//                     url:'/tax/getuserapprovalstatus?_token=' + token,
//                     success:function(data)
//                     {
//                         if(data.status == '0' || data.status == 0 )
//                         {
//                             $('#grid').igGridUpdating("option", "editMode", "none");
//                             $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');
//                             $(".alert-danger").fadeOut(5000);


//                         }
//                         console.log("This is respose from cntroller"+data);
//                         return false;
//                     }
//                 });
// });
$(document).ready(function () {
    window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
    window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..', okCancelInMulti: false});
    window.asd = $('.SlectBox').SumoSelect({csvDispCount: 3, captionFormatAllSelected: "Yeah, OK, so everything."});
});

autosuggest();

$("#taxfile-upload-button").click(function () {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_taxfile")[0].files[0];
    if (typeof stn_Doc == 'undefined')
    {
        alert("{{ trans('tax.select_file') }}");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_mapping_rules', stn_Doc);
    formData.append('test', "sample");
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xlsx','xls']) == -1) {
            alert("{{ trans('tax.invalid_file') }}");
            return false;
        }
    $.ajax({
        type: "POST",
        url: "/tax/producttaxclasscodemapping?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            $('#loader').show();
        },
        complete: function () {
            $('#loader').hide();
        },
        success: function (data)
        {
            if(data.workflowstatus == "rollback"){
                $('#upload-document').modal('toggle');
                $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Sorry Failed to Upload Sheet,Reverting all Records.</div></div>');
                $(".alert-danger").fadeOut(5000);
            }else if(data.workflowstatus == "No permission"){
                //user doesn't have permission for approval workflow cycle
                $('#upload-document').modal('toggle');
                $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');
                $(".alert-danger").fadeOut(5000)
            }else{
                //user have permission for tax approval workflow cycle
                var datalink = data.uniqueRef;
	            var LINK = "<a target='_blank' href=/tax/mappinglogs/"+datalink+">View Details</a>";
	            var consolidatedmsg = "{{ trans('tax.tax_mapping') }}";
	            consolidatedmsg = consolidatedmsg.replace('INSERT', data.insertcount);
	            consolidatedmsg = consolidatedmsg.replace('DUPLICATE', data.duplicatecount);
	            consolidatedmsg = consolidatedmsg.replace('ERROR', data.linescount);
	            consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
	            $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');
	            $("#upload_taxfile").val("");
	            $('#upload-document').modal('toggle');
	            // $("#grid").igGrid("dataBind");
	            return false;
            }
        }
    });
});

var ID = '';
$('#permissions').on('shown.bs.modal', function (e) {
    var myID = $(e.relatedTarget).data('id');


    var token = $("#token_value").val();
    $.ajax({
        type: "GET",
        url: "/tax/getdetailsofproducts/" + myID + "?_token=" + token,
        dataType: "json",
        success: function (data)
        {
            $("#categorydata").text(data['productinfo'][0].cat_name);
            $("#productNamedata").text(data['productinfo'][0].product_title);
            $("#skuiddata").text(data['productinfo'][0].sku);
            $("#branddata").text(data['productinfo'][0].brand_name);

            productTax(myID);
            //Rebinding data if same product clicked again....
            if (ID == myID) {
                $(".productmappingdetailss").igGrid("destroy");
                productTax(myID);
            }
            ID = myID;
        }
    });
    
    $.ajax({
        type: 'GET',
        url: '/tax/hsncodes?type=All&productid=' + myID + '&_token=' + token,
        dataType: "json",
        success: function (data)
        {
            if(data.exist == 'yes'){
                $('#hsncode').html('');
                $('#hsncode').html(data.hsn_codes[0].ITC_HSCodes);
                
                $('#hsndesc').html('');
                $('#hsndesc').html(data.hsn_codes[0].HSC_Desc);
                
                $('#hsnper').html('');
                $('#hsnper').html(data.hsn_codes[0].tax_percent);
            } else if(data.exist == 'no'){
                $('#hsncode').html('');
                $('#hsncode').html('N/A');
                
                $('#hsndesc').html('');
                $('#hsndesc').html('N/A');
                
                $('#hsnper').html('');
                $('#hsnper').html('N/A');
            }
        }
    });
});




$('#permissions').on('hidden.bs.modal', function (e) {
    $("#grid").igGrid("dataBind");
});

$('#withdata').change(function () {
    if ($(this).is(':checked')) {
        $("#filters").show();
    } else
    {
        $("#filters").css("display", "none");

    }
});



$(document).ready(function () {
    window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
    window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..', okCancelInMulti: false});
    window.asd = $('.SlectBox').SumoSelect({csvDispCount: 3, captionFormatAllSelected: "Yeah, OK, so everything."});

    var taxMapEditAccess = $("#taxMapAccess").val();
    if (taxMapEditAccess === '1') {
        $('#grid').igGridUpdating("option", "editMode", "cell");
        console.log("taxMapEditAccess " + taxMapEditAccess);
    }


    var token = $("#token_value").val();
                $.ajax({
                    type:'POST',
                    url:'/tax/getuserapprovalstatus?_token=' + token,
                    success:function(data)
                    {
                        if(data == '0' || data == 0 )
                        {

                            $('#grid').igGridUpdating("option", "editMode", "none");
                            restrictDBLclick();
                        }
                        return false;
                    }
                });

});



var catflag = 0;
var brandflag = 0;


$("#category_filter").on('change', function () {
    var cats = $("#category_filter").val();
    $("#hiddencats").val(cats);
});


$("#brand_filter").on('change', function () {
    var brandss = $("#brand_filter").val();
    $("#hiddenbrands").val(brandss);
});


$('#downloadexcel-mapping').submit(function (e) {
    e.preventDefault(); // don't submit multiple times
    this.submit(); // use the native submit method of the form element
    $('#upload-document').modal('toggle');
});

$("#reset-button").click(function () {
    var brandd = [];
    var cat = [];
    // $("#reset-button")
    $('#tax_type').val($('#tax_type').prop('defaultSelected'));

    $('#brand_filter option:selected').each(function () {
        brandd.push($(this).index());
    });
    for (var i = 0; i < brandd.length; i++) {
        $('.multi-select-search-box')[1].sumo.unSelectItem(brandd[i]);
    }

    $('#category_filter option:selected').each(function () {
        cat.push($(this).index());
    });
    for (var i = 0; i < cat.length; i++) {
        $('.multi-select-search-box')[0].sumo.unSelectItem(cat[i]);
    }

});

function filterdata(tax_type) {
    tax_type1 = tax_type.split(" ");
    tax_type1 = tax_type1[0];
    var arrayfilter = ["ALL", "VAT", "CST", "Sales", "Service", "GST"];
    //removing the tax type from the array 
    var arrayfilter2 = jQuery.grep(arrayfilter, function (value) {
        return value != tax_type1;
    });
    $("#" + tax_type1).removeClass("inactive");
    $("#" + tax_type1).addClass("active");
    for (var i = 0; i < arrayfilter2.length; i++)
    {
        $("#" + arrayfilter2[i]).removeClass("active");
        $("#" + arrayfilter2[i]).addClass("inactive");
    }

    if (tax_type !== 'ALL') {
        $("#grid_container").hide();
        $("#filtered_grid_container").show();
        var filterURL = "/tax/taxtypegrid?taxtype=" + tax_type;
        $("#filtered_grid").igGrid({
            dataSource: filterURL
        });
    } else {
        $("#grid_container").show();
        $("#grid").igGrid("dataBind");
        $("#filtered_grid_container").hide();
    }
}

$("#tax_type_home").on("change", function () {
    var tax_type = $("#tax_type_home").val();
    if (tax_type !== 'ALL') {
        $("#grid_container").hide();
        $("#filtered_grid_container").show();
        var filterURL = "/tax/taxtypegrid?taxtype=" + tax_type;
        $("#filtered_grid").igGrid({
            dataSource: filterURL
        });
    } else {
        $("#grid_container").show();
        $("#grid").igGrid("dataBind");
        $("#filtered_grid_container").hide();
    }

});

$('#upload-document').on('hidden.bs.modal', function (e) {
    console.log("Modal hidden");
    var brandd = [];
    var cat = [];
    $("#upload_taxfile").val("");
    $("#hiddencats").val("");
    $("#hiddenbrands").val("");
    $(".fileinput-filename").html("");
    $('#tax_type').val($('#tax_type').prop('defaultSelected'));

    var tax_type = $("#tax_type_home").val();
    
    if (tax_type !== 'ALL') {
        $("#grid_container").hide();
        $("#filtered_grid_container").show();
        var filterURL = "/tax/taxtypegrid?taxtype=" + tax_type;
        $("#filtered_grid").igGrid({
            dataSource: filterURL
        });
    } else {
        $("#grid_container").show();
        $("#grid").igGrid("dataBind");
        $("#filtered_grid_container").hide();
    }

    $('#brand_filter option:selected').each(function () {
        brandd.push($(this).index());
    });
    for (var i = 0; i < brandd.length; i++) {
        $('.multi-select-search-box')[1].sumo.unSelectItem(brandd[i]);
    }

    $('#category_filter option:selected').each(function () {
        cat.push($(this).index());
    });
    for (var i = 0; i < cat.length; i++) {
        $('.multi-select-search-box')[0].sumo.unSelectItem(cat[i]);
    }
});



</script>





@stop
@extends('layouts.footer')