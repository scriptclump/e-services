@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/po/index">Purchases</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Purchase Orders</li>
        </ul>
    </div>
</div>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Purchase Orders </div>

                <div class="actions">
                    @if(isset($featureAccess['exportFeature']) && $featureAccess['exportFeature'])
                    <a type="button" id="" href="#exportpo" data-toggle="modal" class="btn green-meadow">Export PO</a>
                    <a type="button" id="" href="#importpo" data-toggle="modal" class="btn green-meadow">Import PO</a>
                    <a type="button" id="" href="#poreport" data-toggle="modal" class="btn green-meadow">Purchase Report</a>
                    <a type="button" id="" href="#pohsnreport" data-toggle="modal" class="btn green-meadow">Purchase HSN Report</a>
                    @endif
                    @if(isset($poGSTReport) && $poGSTReport==1)
                    <a type="button" id="" href="#poGSTreport" data-toggle="modal" class="btn green-meadow">Purchase GST Report</a>
                    @endif
                    @if(isset($featureAccess['createFeature']) && $featureAccess['createFeature'])
                    <a href="/po/create" class="btn green-meadow">Create PO</a>
                    @endif

                </div>


            </div>
            <div class="portlet-body">
                <div class="row" style="position: relative;">
                    <div class="col-md-2" style ="">
                        <div class="form-group">
                        <input type="text" class="form-control " name="from_date_report" id="from_date_report"  placeholder="From Date" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2" style ="">
                        <div class="form-group">
                           <input type="text" class="form-control " name="to_date_report" id="to_date_report"  placeholder="To Date" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-md-2" style ="">
                    <div class="form-group genra">
                        <button type="button" id="filter_button" class="btn green-meadow">Go</button>
                    </div>
                  </div>
              </div>
                <div class="row sortingborder">
                    <div class="col-md-12">
                        <div class="caption">
                            <span class="caption-subject bold font-blue"> Filter By :</span>
                            <span class="caption-helper sorting">
                                                    
                                <a href="{{$app['url']->to('/')}}/po/index/initiated{{$dates}}"  class="{{($filter_status == 'initiated') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="PO Created">Initiation ({{$poCounts['initiated']}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/created{{$dates}}" class="{{($filter_status == 'created') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="PO Verified">Verification ({{isset($poCounts['created'])?$poCounts['created']:0}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/verified{{$dates}}" class="{{($filter_status == 'verified') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="PO Approved">Approval ({{isset($poCounts['verified'])?$poCounts['verified']:0}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/approved{{$dates}}" class="{{($filter_status == 'approved') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Stock Dispatched by Supplier">Fulfillment ({{isset($poCounts['approved'])?$poCounts['approved']:0}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/posit{{$dates}}" class="{{($filter_status == 'posit') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Stock Received & Inspected at DC">Inspection ({{isset($poCounts['posit'])?$poCounts['posit']:0}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/receivedatdc{{$dates}}" class="{{($filter_status == 'receivedatdc') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Invoice Checked">Acceptance ({{isset($poCounts['receivedatdc'])?$poCounts['receivedatdc']:0}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/checked{{$dates}}" class="{{($filter_status == 'checked') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="GRN Created">GRN ({{$poCounts['checked']}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/grncreated{{$dates}}" class="{{($filter_status == 'grncreated') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Stock Moved to Shelves">Putaway ({{$poCounts['grncreated']}})</a>&nbsp;&nbsp;
                                <br/><span style="padding-left:84px;"></span>
                                <a href="{{$app['url']->to('/')}}/po/index/open{{$dates}}" class="{{($filter_status == 'open') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Open">Open ({{$poCounts['opened']}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/canceled{{$dates}}" class="{{($filter_status == 'canceled') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Cancelled">Cancelled ({{$poCounts['canceled']}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/shelved{{$dates}}" class="{{($filter_status == 'shelved') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Completed">Completed ({{$poCounts['shelved']}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/payments{{$dates}}" class="{{($filter_status == 'payments') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="PO Paid">Payments ({{$poCounts['immediatepay']}}/{{$poCounts['paid']}})</a>&nbsp;&nbsp;
                                <a href="{{$app['url']->to('/')}}/po/index/allpo{{$dates}}"  class="{{($filter_status == 'allpo' || $filter_status == '') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="All POs">Total ({{$poCounts['all']}})</a>&nbsp;&nbsp;
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        &nbsp;
                        <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in (₹)</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                            <table id="poList"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-scroll fade in" id="importpo" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Import PO</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="importpoform" action="/po/importPOExcel" class="text-center" method="post" enctype="multipart/form-data">
                        <div class="row">

                            <div class="col-md-12" align="center">
                                <div style="display:none;" id="error-msg" class="alert alert-danger"></div>                        
                                <div class="form-group">
                                    <div class="fileUpload btn green-meadow"> <span id="up_text">Choose PO Template</span>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="file" name="pofile" id="pofile" class="form-control upload"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" id="uploadfile" class="btn green-meadow">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12 text-center">
                        <a href="/po/downloadpoimport" class="btn green-meadow">Download Po Template</a>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@if(isset($featureAccess['exportFeature']) && $featureAccess['exportFeature'])
<div class="modal modal-scroll fade in" id="exportpo" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Export PO</h4>
            </div>
            <div class="modal-body">
                <form id="exportPOForm" action="/po/downloadPOExcel" class="text-center" method="post">
                    <div class="row">                       
                     <div class="col-md-12" align="center">
                        <div class="col-md-8">
                        <div class="form-group">
                                    <select  name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box" multiple="multiple" placeholder="Please Select DC ">
                                    <!-- <option value="0"></option> -->

                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach
                                        
                                 </select>    
                              </div>
                        </div>
                            <div style="display:none;" id="error-msg" class="alert alert-danger"></div>                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-scroll fade in" id="poreport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Finance Purchase Report</h4>
            </div>
            <div class="modal-body">
                <form id="POReportForm" action="/po/downloadPOReport" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">

                    <div class="col-md-8">
                        <div class="form-group">
                                    <select  name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box" multiple="multiple" placeholder="Please Select DC" >
                                    <!-- <option value="0" ">All DC'S</option> -->

                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach

                                 </select>    
                              </div>
                    </div>
                            <div style="display:none;" id="error-msg1" class="alert alert-danger"></div>                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="pofdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="potdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" id="grn_date" name="grn_date" class="" title="select if date is grn date"> GRN Date
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal modal-scroll fade in" id="pohsnreport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Finance Purchase HSN Report</h4>
            </div>
            <div class="modal-body">
                <form id="POHSNReportForm" action="/po/downloadPOHsnReport" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">

                    <div class="col-md-8">
                        <div class="form-group">
                                    <select  name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box" multiple="multiple" placeholder="Please Select DC" >
                                    <!-- <option value="0" ">All DC'S</option> -->

                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach

                                 </select>    
                              </div>
                    </div>
                            <div style="display:none;" id="error-msg1" class="alert alert-danger"></div>                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="hsnfdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="hsntdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <!--<div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" id="grn_date" name="grn_date" class="" title="select if date is grn date"> GRN Date
                                </div>
                            </div>
                        </div>-->
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>




<div class="modal modal-scroll fade in" id="poGSTreport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Finance Purchase Report</h4>
            </div>
            <div class="modal-body">
                <form id="POGSTReportForm" action="/po/downloadpoGSTReport" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                        <div class="col-md-8">
                         <div class="form-group">
                                    <select  name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box" multiple="multiple" placeholder="Please Select DC" >
                                    <!-- <option value="0" ">All DC'S</option> -->

                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach

                                 </select>    
                              </div>
                          </div>
                            <div style="display:none;" id="error-msg1" class="alert alert-danger"></div>                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="gstfdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="gsttdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" id="grn_date" name="grn_date" class="" title="select if date is grn date"> GRN Date
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



@endif
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<style type="text/css">
    /*.portlet > .portlet-title { margin-bottom:0px !important;}*/

    .imgborder{border:1px solid #ddd !important;}
    .tabs-left.nav-tabs > li.active > a, .tabs-left.nav-tabs > li.active > a:hover > li.active > a:focus {
        border-radius: 0px !important;  
    }
    .nav>li>a:visited{
        color:red !important;
    }
    tabs.nav>li>a {
        padding-left: 10px !important;
    }
    .note.note-success {
        background-color: #c0edf1 !important;
        border-color: #58d0da !important;
        color: #000 !important;
    }
    hr {
        margin-top:0px !important;
        margin-bottom:10px !important;
    }
    .portlet > .portlet-title {
        border-bottom: 0px !important;
    }

    .SumoSelect > .optWrapper > .options {
    text-align: left;
    }
    .favfont i{font-size:18px !important;}
    .actionss{padding-left: 22px !important;}

    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;font-size: 12px;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#676767 !important;}

    .fa-eye{color:#3598dc !important;}
    .fa-print{color:#3598dc !important;}
    .fa-download{color:#3598dc !important;}
    #poList_poId{padding-left:8px !important;}
    #poList_Supplier{padding-left:6px !important;}
    #poList_Status{padding-left:-3px !important;}
    #poList_fixedBodyContainer{overflow-x: auto!important;}
    .nowrap{
        white-space: nowrap !important;
    }
    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{
        border-spacing: 0px !important;
    }
    #poList_fixedContainerScroller{
         height: 0px !important;
    }
    .centerAlignment { text-align: center;}
    .rightAlignment { text-align: right;}
    th.ui-iggrid-header:nth-child(6), th.ui-iggrid-header:nth-child(7), th.ui-iggrid-header:nth-child(8){
        text-align: right !important;
    } 
.SumoSelect > .optWrapper > .options {
    text-align: left;
}
#poList_pager_label{
    margin-left: -20%;
}   
</style>
@stop

@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/poscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
   $(document).ready(function () {

        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
  
    });   
function filterPO(status) {
    var formData = $('#frm_po').serialize();
    $("#poList").igGrid({
        dataSource: "/po/ajax/" + status + "?" + formData,
        autoGenerateColumns: false
    });
}
function getNextDay(select_date) {
    select_date.setDate(select_date.getDate() + 1);
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth() + 1), 2) + '/' + zeroPad(setdate.getDate(), 2) + '/' + setdate.getFullYear();
    return nextdayDate;
}
function getNextDayPO(select_date) {
        select_date.setDate(select_date.getDate());
        var setdate = new Date(select_date);
        var nextdayDate = setdate.getFullYear() + '-' + zeroPad((setdate.getMonth() + 1), 2) + '-' + zeroPad(setdate.getDate(), 2);
        return nextdayDate;
    }
function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}

$(document).ready(function () {
    var from_date = $('#from_date_report').val();
    var to_date = $('#to_date_report').val();
    purchaseOrderGrid('{{$filter_status}}',from_date,to_date);

    $('#fdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#tdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#tdate').datepicker({
        maxDate: 0,
    });
    $('#pofdate,#hsnfdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#potdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#potdate,#hsntdate').datepicker({
        maxDate: 0,
    });
   $('#gstfdate').datepicker({
    maxDate: 0,
    onSelect: function () {
        var select_date = $(this).datepicker('getDate');
        var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
        $('#gsttdate').datepicker('option', 'minDate', seldayDate);
    }
});

$('#gsttdate').datepicker({
    maxDate: 0,
});

    $("#toggleFilter").click(function () {
        $("#filters").toggle("slow", function () {
        });
    });
    $.validator.addMethod("DateFormat", function (value, element) {
        if (value != '') {
            return value.match(/^(0[1-9]|1[012])[- //.](0[1-9]|[12][0-9]|3[01])[- //.](19|20)\d\d$/);
        } else {
            return true;
        }
    },
            "Please enter a date in the format mm/dd/yyyy"
            );
    $.validator.addMethod("maxDate", function (value, element) {
        var now = new Date();
        var tomorrow = new Date(now.getTime() + (24 * 60 * 60 * 1000));
        var myDate = new Date(value);
        return this.optional(element) || myDate <= tomorrow;
    },
            "should not be future date"
            );
    $.validator.addMethod("minDate", function (value, element) {
        var fdate = new Date($('#fdate').val());
        var myDate = new Date(value);
        return this.optional(element) || myDate >= fdate;
    },
            "should not be less than from date"
            );
    $('#exportPOForm').validate({
        rules: {
            fdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
            },
            tdate: {
                required: true,
                DateFormat: true,
                maxDate: true,
                minDate: true,
            },
            "loc_dc_id[]": {
                required: true,
            },
        },
        submitHandler: function (form) {
            var form = $('#exportPOForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
    $('#POReportForm').validate({
        rules: {
            pofdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
            },
            potdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
                minDate: true,
            },
            "loc_dc_id[]":{
                required: true,
            },
        },
        submitHandler: function (form) {
            var form = $('#POReportForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
 $('#POGSTReportForm').validate({
  rules: {
    gstfdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
    },
    gsttdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
        minDate: true,
    },
    "loc_dc_id[]":{
        required: true,
    },
},
submitHandler: function (form) {
    var form = $('#POGSTReportForm');
    window.location = form.attr('action') + '?' + form.serialize();
    $('.close').click();
}
});

    $('#importpoform').submit(function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $(this).attr('action');
        $('.spinnerQueue').show();
        $('.close').trigger('click');
        $("#uploadfile").attr("disabled", "disabled");
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            beforeSend: function (xhr) {
                $('.spinnerQueue').show();
                $('.close').trigger('click');
            },
            success: function (data) {
                $('.spinnerQueue').hide();
                $('.close').trigger('click');
                if(data.status == 200){
                    window.open('/'+data.url);
                }else if(data.status == 400){
                    if(data.url !="")
                        window.open('/'+data.url);
                    else
                        alert(data.message);
                }else{
                    alert('Server Error!');
                }
                $("#uploadfile").removeAttr("disabled");
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                $(".alert-success").fadeOut(30000)
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
    $("#importpo").on('hide.bs.modal', function () {
        $('#importpoform')[0].reset();
        $("#uploadfile").removeAttr("disabled");
    });
});
$('a.link').on('click touchend', function(e) {
  var link = $(this).attr('href');
  window.location = link;
});
$('#exportpo').on('hidden.bs.modal', function (e) {
  // do something when this modal window is closed...
      $('#fdate').val("");
      $('#tdate').val("");
});
 
// $('#from_date_report').datepicker({
//     dateFormat: 'yy-mm-dd',
//     maxDate:0,
// });
    
// $('#to_date_report').datepicker({
//     dateFormat: 'yy-mm-dd',
//     maxDate:0,
//  });

        $("#from_date_report").keypress(function(event) {event.preventDefault();});
        $("#to_date_report").keypress(function(event) {event.preventDefault();});
        $('#from_date_report').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: 0,
            onSelect: function () {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDayPO(select_date);
                $('#to_date_report').datepicker('option', 'minDate', select_date);
            }
        });
        $('#to_date_report').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: '+0D',
        });


    $("#filter_button").click(function() {

    var csrf_token = $('#csrf-token').val();
    //console.log('inf filter');
    //console.log('{{$filter_status}}');
    var from_date = $('#from_date_report').val();
    var to_date = $('#to_date_report').val();
    var userid = $('#userid').val();
    window.location.href = '/po/index/allpo?from_date='+from_date+'&to_date='+to_date;
    
    });
    

    selecteddates();
    function selecteddates(){

        var param1var = getQueryVariable("from_date");
        var param2var = getQueryVariable("to_date");
        var from_date = $('#from_date_report').val();
        var to_date = $('#to_date_report').val();
        if(from_date == "" && to_date == ""){
            $("#from_date_report").val(param1var);
            $("#to_date_report").val(param2var);

        }    

        
    }

    function getQueryVariable(variable) {
      var query = window.location.search.substring(1);
      var vars = query.split("&");
      for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
          return pair[1];
        }
      } 
     // alert('Query Variable ' + variable + ' not found');
    }

    // var dateFormat = "dd-mm-yy";
    // from = $( "#fromdate" ).datepicker({
    //         defaultDate: "+1w",
    //         dateFormat : dateFormat,
    //           maxDate:0,
    //         changeMonth: true, 
    //         changeYear: true,         
    //       }),
    //   to = $( "#todate" ).datepicker({
    //         defaultDate: "+1w",
    //           dateFormat : dateFormat,
    //           minDate:0,
    //         changeMonth: true,  
    //         changeYear: true,      
    //       });
   


</script>
@stop
@extends('layouts.footer')
