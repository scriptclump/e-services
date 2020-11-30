@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="POST">
<input type="hidden" name="po_selected" id="po_selected" value="{{$po_selected}}">

<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/grn/index">GRN</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Create GRN</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> GOODS RECEIVED NOTE </div>
                <div class="tools">&nbsp;</div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs active">
                        <li class="ch_tabs disabled active select_grn">
                            <a href="#select_grn" data-toggle="tab"> Select GRN Type </a>
                        </li>
                        <li class="ch_tabs disabled po_history">
                            <a href="#po_history" data-toggle="tab"> PO History </a>
                        </li>
                       <?php /* <li class="ch_tabs disabled upload_doc">
                            <a href="#upload_doc" data-toggle="tab"> Upload Document </a>
                        </li>
                        */ ?>
                        <li class="ch_tabs disabled create_grn">
                            <a href="#create_grn" data-toggle="tab"> Create GRN </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="select_grn">
                            <form id="frmSelectGrn" action="" method="post">
                                @include('Grn::Form.createGrnForm')
                               <!-- <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn green-meadow" id="continue_button">Continue</button>
                                    </div>
                                </div>-->
                            </form>


                            <div>
                            <form id="frmUpload" action="/grn/uploadDoc" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="inward_id" value="">
                                <input type="hidden" name="is_document_required" value="" />
                                <div id="ajaxResponseDoc" class="alert"></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label">Document Type <span class="required" aria-required="true" >*</span></label>
                                        <select class="form-control" name="documentType" id="documentType" onChange="loadrefno(this);">
                                            <option value="">Document Type</option>
                                            @foreach($docTypes as $key=>$docType)
                                            <option value="{{$key}}">{{$docType}}</option>
                                            @endforeach                    
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Reference No</label><input class="form-control" type="text" id="ref_no" name="ref_no" placeholder="Ref No" />
                                    </div>
                                    <div class="col-md-1">
                                        <label class="control-label">Allow Duplicate</label>
                                        <input type="checkbox" name="allow_duplicate" id="allow_duplicate" value="1" style="margin-top:0px !important;"> 
                                        
                                    </div>    
                                    <div class="col-md-2">
                                        <label class="control-label">Document Proof <span class="required" aria-required="true">*</span></label>               

                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;"><div>
                                                <span class="btn default btn-file btn green-meadow">
                                                    <span class="fileinput-new">Choose File</span>
                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                                                    <input class="form-control" type="file" id="upload_file" name="upload_file" placeholder="Proof of Document">
                                                </span>
                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="col-md-2 margtopbtn text-left" style="margin-top:25px;">
                                        <input class="btn btn-success" type="submit" name="btnUpload" value="Add">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <table class="table table-striped table-bordered table-advance table-hover" id="inwardDocList">
                                            <thead>
                                                <tr>
                                                    <th>Document Type</th>
                                                    <th>Ref No</th>
                                                    <th>Created By</th>
                                                    <th style="text-align:center;">Attachment</th>
                                                    <th style="text-align:center;">Action</th>
                                                </tr>
                                            </thead>                    
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                       <!-- <a type="button" class="btn green-meadow" onclick="back('po_history')">Back</a>-->
                                        <a type="button" class="btn green-meadow disabled" id="save_continue" disabled="disabled">Continue</a>
                                    </div>
                                </div>
                            </form>
</div>
                        </div>
                        <div class="tab-pane" id="po_history">
                            <div id="po_history_data"></div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <a type="button" class="btn green-meadow" onclick="back('select_grn')">Back</a>
                                    <a type="button" class="btn green-meadow" onclick="back('create_grn')">Continue</a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="create_grn">
                            <form id="grn_from" action="/grn/createGrn" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" id="sno_increment" name="sno_increment" value="1">
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" id="po_approval_status" value="" />
                                <input type="hidden" id="po_discount_amount" value="0" />

                                <div id="ajaxResponse" style="display:none;" class="alert alert-danger"></div>
                                <?php /*
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="pull-left margin-top-20">
                                            <span href="#" class="btn blue add_sku_btn" data-state='hide'>Add SKU <i id='add_sku_btnsymb' class="fa fa-plus"></i></span>
                                        </div>
                                    </div>
                                </div>
                                @include('Grn::Form.addSkuform')
                               */ ?>
                                <div class="row">
                                    <div class="col-md-12 text-right">&nbsp;</div>
                                </div>
                                <div class="text-right" style="float: right; font-size:11px;">

                                    <span href="#" class="btn blue " id='addallbutton' data-state="hide" OnClick="autoAddAllProducts()">Add All</span>
                                    <span href="#" class="btn red" style="display: none;" id='removeallbutton' data-state="hide" OnClick="removeAddAllProducts()">Remove All</span>
                                    <b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                        
                                            <div class="parent">
                                                

                                                    <table class="table table-striped table-bordered table-advance table-hover fixTable" id="product_list" style="white-space:nowrap;">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>S No</th>
                                                                <th>SKU</th>
                                                                <th>Product Title</th>
                                                                <th>MRP</th>
                                                                <th>PO Qty</th>
                                                                <th>Rec Qty (in Each) </th>
                                                                <th>Total Received</th>                                                                
                                                                <th>Mfg.Date</th>
                                                                <th>Free (in Each)</th>
                                                                <th>Damaged (in Each)</th>
<!--                                                                <th width="20%">Excess (in Each)</th>
                                                                <th width="20%">Missed (in Each)</th>                                                       
                                                                <th width="20%">Quarantine</th>-->
                                                                <th>Remarks</th>
                                                                <th>Base Price</th>
                                                                <th>Sub Total</th>
                                                                <th>Tax %</th>
                                                                <th>Tax Value</th>
                                                                <th>Discount</th>
                                                                <th>Discount Amount</th>
                                                                <th>Total Value</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                         </div>
                                        
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="scroller" style="height: 150px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">   
                                            <table class="table table-striped table-bordered table-advance table-hover table-scrolling" id="">
                                                <thead>
                                                    <tr>
                                                        <th>Total Qty</th>
                                                        <th>Total Base Value</th>
                                                        <th>Discount on Items</th>
                                                        <th>Discount on Bill</th>
                                                        <!--<th>Total Tax%</th>-->
                                                        <th>Total Tax</th>
                                                        <th>Shipping</th>
                                                        <th>Grand Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="odd gradeX">
                                                        <td><span id="totqtylabel">0</span><input type="hidden" name="total_grn_qty" value="0" id="totqtyval"/></td>
                                                        <td>Rs.<span id="basetotlabel">0</span><input type="hidden" name="total_grn_basetotal" value="0" id="basetotval"/></td>
                                                        <td>Rs.<span id="discount_on_items"></span></td>
                                                        <td style="width: 400px;">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <input type="checkbox" name="on_bill_discount_type" id="on_bill_discount_type" style="float:left;width:20px;">
                                                                            <span>%</span>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input class="form-control" type="number" name="discount_on_bill" min="0" value="0" id="discount_on_bill"/>
                                                                            <input class="form-control hide" type="number" name="discount_on_bill_value" value="0" id="discount_on_bill_value" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="discount_on_bill_options" id="discount_on_bill_options" style="float:left;width:20px;" /> 
                                                                    <span>Apply for Line Items</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="hide"><span id="tottaxperlabel">0</span></td>
                                                        <td>Rs.<span id="tottaxvallabel">0</span><input type="hidden" name="total_grn_tax_total" value="0" id="tottaxval"/></td>
                                                        <td><input type="number" name="shippingcost" min="0" value="0" id="shippingcost"/></td>
                                                        <td>Rs.<span id="grandtotlabel">0</span><input type="hidden" name="total_grn_grand_total" value="0" id="grandtotalval"/></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"> 
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <textarea rows="3" name="grn_remarks1" id="grn_remarks1" class="form-control" rows="1"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:0px;">
                                    <hr />
                                    <div class="col-md-12 text-center">
                                        <div style="text-align:center;color: red">
                                            <span id="timer" ></a>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
                                        </div>
                                        <a type="button" class="btn green-meadow" onclick="back('po_history')">Back</a>
                                        <input type="submit" id="final_submit" class="btn green-meadow" value="Save"/>
                                        <a class="btn green-meadow" href="/grn">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ADD SKU Model Start-->
<div id="pack_config" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Pack Configuration</h4>
            </div>
            <div class="modal-body">                    
                <form id="packConfigForm">
                    <div style="display:none;" id="error-msg1" class="alert alert-danger"></div>
                    <div class="row margtop">
                        <div class="col-md-3">
                            <label><strong>Product Title:</strong></label>
                            <span id="producttitle"></span>
                            <input name="packproduct_id" id="packproduct_id" readonly="" class="form-control" type="hidden">
                            <input name="packpo_qty" id="packpo_qty" readonly="" class="form-control" type="hidden">
                            <input name="packpo_numof_eaches" id="packpo_numof_eaches" readonly="" class="form-control" type="hidden">
                        </div>
                        <div class="col-md-2">
                            <label><strong>Ebutor Article Number:</strong></label>
                            <span id="articlename"></span>
                        </div>

                        <div class="col-md-1">
                            <label><strong>PO Qty:</strong></label>
                            <span id="poqtylabel"></span>
                        </div>                            
                        <div class="col-md-1">
                            <label><strong>UOM:</strong></label>
                            <span id="pouomlabel"></span>
                        </div>
                        <div class="col-md-1">
                            <label><strong>No. Eaches:</strong></label>
                            <span id="poeacheslabel"></span>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label"><strong>Tax Type</strong></label>
                            <input type="text" readonly="" disabled="" name="tax_type" id="tax_type" class="form-control input-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="control-label"><strong>Tax %</strong></label>
                            <input name="tax_val" id="tax_val" readonly="" disabled="" class="form-control input-sm" type="number" min="0" value="0">
                        </div>
                    </div>
                    <div class="row margtop">
                        <div class="col-md-12">&nbsp;</div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label"><strong>Pack UOM</strong></label>
                            <select name="pack_size" class="form-control">
                                <option>Select Pack UOM</option>
                            </select>                              
                        </div>
                        <div class="col-md-1">
                            <label class="control-label"><strong>Eaches QTY</strong></label>
                            <span id="uomqty1">0</span>
                            <input type="hidden" readonly="" disabled="" id="uomqty" class="form-control" value="0"/>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><strong>Rec Qty</strong></label>
                                <input type="number" min="0" id="rqty" class="form-control" value="0"/>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label"><strong>Total</strong></label>
                                <span id="qtytotal1">0</span>
                                <input type="hidden" readonly="" disabled="" id="qtytotal" class="form-control" value="0"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><strong>Mfg Date</strong></label>
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" id="mfg_date" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <a class="btn green-meadow" id="addpackbtn">ADD</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">                            
                            <div class="table-scrollable">
                                <table class="table table-striped table-bordered table-advance table-hover" id="packconfiglist">
                                    <thead>
                                        <tr>
                                            <th>Pack Size</th>
                                            <th>QTY</th>
                                            <th>Total</th>
                                            <th>Mfg Date</th>
                                            <th>Freshness</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="packcofigtable">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">                            
                            <div class="table-scrollable">
                                <table class="table table-striped table-bordered table-advance table-hover" id="">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>Received</th>
                                            <th>Free</th>
                                            <th>Excess</th>
                                            <th>Damaged</th>
                                            <th>Missed</th>
                                            <th>Quarantine</th>
                                            <th>Discount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Sub Total</td>
                                            <td><input type="number" class="form-control" name="total_received" id="total_received" readonly="" value="0">
                                                <input type="hidden" class="form-control" name="actual_received" id="actual_received" readonly="" value="0"></td>
                                            <th><input type="number" min="0" value="0" class="form-control" name="total_free" id="total_free"></th>
                                            <td><input type="number" min="0" value="0" class="form-control" name="total_excess" id="total_excess"></td>
                                            <td><input type="number" min="0" value="0" class="form-control" name="total_damage" id="total_damage"></td>
                                            <td><input type="number" min="0" value="0" class="form-control" name="total_missed" id="total_missed"></td>
                                            <td><input type="number" min="0" value="0" class="form-control" name="total_quarantine" id="total_quarantine" readonly="" ></td>
                                            <td>
                                                <input type="radio" id="flattype" checked="checked" name="discount_type" value="1"/> Flat
                                                <input type="radio" id="percenttype" name="discount_type" value="2"/> %
                                                <input type="number" min="0" value="0" class="form-control" name="total_discount" id="total_discount">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"> 
                            <div class="form-group">
                                <label class="control-label"><strong>Remarks</strong></label>
                                <textarea name="pr_remarks" id="pr_remarks" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer margtop">
                        <div class="row">
                            <div class="col-md-12 text-center" >
                                <button class="green-meadow btn" type="submit" id="addSku">Save</button>
                                <button class="green-meadow btn" data-dismiss="modal" type="button" id="addSku">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-scroll fade in" id="prcingMismatchModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode">GRN Error</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="pickListErrors table table-bordered">
                            <tbody>
                                <tr bgcolor="#efefef" class="mainhead">
                                    <th width="50%" align="left" valign="middle">Reason : <span id="reason_po_so"></span>.</th>
                                    <th width="50%" align="left" valign="middle">Status : Failed</th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-advance table-hover table-scrolling">
                                            <tbody id="priceMismatchData">
                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <h6 style="color:  red;" id="po_so_adjust_message"></h6> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <div class="row">
                  <div class="col-md-12 text-center">
                      <button class="btn" id="okayPricingMismatch" data-dismiss="modal">Okay</button>
                  </div>
              </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- ADD SKU Model End-->
@stop

@section('style')

<style type="text/css">
table > thead > tr > th { background:#efefef;}

.parent {height: auto;}
.fixTable {width: 1800px !important;white-space:nowrap;}   
    .margtopbtn{margin-top:27px !important;}

    .portlet > .portlet-title { margin-bottom:1px !important;}
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
    .ui-autocomplete{
        z-index: 10100 !important; top:10px;  height:100px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;
    }
    .ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
    }
    .modal-dialog{
        width: 900px !important;
    }
.inpusmwidth{ width:70px; float:left;}

</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/clockface/css/clockface.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/admin/pages/scripts/tableHeadFixer.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-numberformat/jquery.number.min.js') }}" type="text/javascript"></script>
@include('includes.validators')
<script type="text/javascript">
    var docarray=[];

$(document).ready(function () {
    $(".fixTable").tableHeadFixer({"head" : false, "right" : 1});
    $("#frmSelectGrn")
        .find('[name="warehouse"]')
            .change(function(e) {
                /* Revalidate the color when it is changed */
                $('#frmSelectGrn').formValidation('revalidateField', 'warehouse');
            })
            .end()
            .formValidation({
//        live: 'enabled',
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            po_id: {
                validators: {
                    notEmpty: {
                        message: "Please Select PO"
                    }, 
                    callback: {
                        message: "Please Select PO",
                        callback: function(value, validator) {
                            return value != '';
                        }
                    }
                }
            },
            warehouse: {
                validators: {
                    notEmpty: {
                        message: "Please Select Warehouse"
                    }, 
                    callback: {
                        message: "Please Select Warehouse",
                        callback: function(value, validator) {
                            return parseInt(value) > 0;
                        }
                    }
                }
            },
            grn_supplier: {                
                validators: {
                    notEmpty: {
                        message: 'Please Select Supplier'
                    }, 
                    callback: {
                        message: 'Please Select Supplier',
                        callback: function(value, validator) {
                            return parseInt(value) > 0;
                        }
                    }
                }
            }            
    }}).on('success.form.fv', function (event) {
        event.preventDefault();
        back('po_history');
        var po_id = $('#po_id').val();
        if(po_id == 'Manual')
        {
            $('[name="is_document_required"]').val(1);
        }
//        $('#frmSelectGrn').data('formValidation').resetForm();
    });
    $("#frmUpload").formValidation({
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            documentType: {
                validators: {
                    notEmpty: {
                        message: "Please select document type"
                    }
                }
            },
            upload_file: {
                validators: {
                    notEmpty: {
                        message: "Please select file to upload"
                    },
                    file: {
                        extension: 'doc,pdf,docx,jpg,jpeg,png,gif',
                        type: 'application/pdf,application/msword,image/jpeg,image/png,image/gif,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        maxSize: 2097152,   // 2048 * 1024
                        message: 'The selected file is not valid'                        
                    }
                }
            },
            ref_no: {
                validators: {
                    notEmpty: {
                        message: "Please Enter ref no"
                    }
                }
            }
    }}).on('success.form.fv', function (event) {
        event.preventDefault();
        var form = document.getElementById("frmUpload");
        var formdata = new FormData(form);
        var po_id = $('#po_id').val();
        formdata.append("po_id",po_id);
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/grn/uploadDoc",
            type: "POST",
            data: formdata,
            mimeType: "multipart/form-data",

            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            beforeSend: function (xhr) {
                $('input[name="btnUpload"]').attr('disabled', true);
                $('.loderholder').show();
                
            },
            success: function (response) {
                console.log(response);
                console.log(formdata);

                $('#ajaxResponseDoc').removeClass('alert-danger').removeClass('error').addClass('alert-success').html(response.message).show();
                if(response.status == 400){
                    $('#ajaxResponseDoc').addClass('alert-danger').addClass('error')
                }
                $('#inwardDocList').append(response.docText);
                $('#frmUpload')[0].reset();
                $('input[name="btnUpload"]').attr('disabled', false);
                $('.loderholder').hide();
                docarray.push(response.doc_ref_type);
            },
            error: function (response) {
                $('#ajaxResponseDoc').removeClass('alert-success').addClass('alert-danger').html("Unable to save file").show();
                $('.loderholder').hide();
            }
        });
    });
    
    $('#discount_on_bill_options,#on_bill_discount_type').change(function(){
        updateDiscount();
    });
});

function updateDiscount()
{
    var temp = $('#discount_on_bill_options').prop('checked');
    var discountValue = 0;
    var discountPercentType = $('#on_bill_discount_type').prop('checked');
//        if(temp == 'line')
    if(temp == true)
    {
        var discount_on_bill = parseFloat($('#discount_on_bill').val());
        if(discount_on_bill > 0)
        {
            if(discountPercentType)
            {
                discountValue = discount_on_bill; 
            }else{
                var percentLength = $('[name="grn_discount_percent[]"]').length;
                var count = 0;
                $('[id^="product_kvi_"]').each(function(){
                   if($(this).val() == 69010)
                   {
                        count = count+1;
                   } 
                });
                percentLength = percentLength - count;
                discountValue = (discount_on_bill / percentLength);
            }
        }
    }else{
        discountValue = 0;
        discountPercentType = false;
    }
    if(discountValue == 0)
    {
        discountPercentType = false;
    }
    $('[name="grn_discount_percent[]"]').each(function(){
        var productId = parseInt($(this).attr('data-product-id'));
        var kvi = $('#product_kvi_'+productId).val();
        if(kvi != 69010)
        {
            $(this).val(discountValue).trigger('change');
        }
//        $(this).val(discountValue).trigger('change');
    });
    $('[name="grn_discount_type[]"]').each(function(){
        $(this).prop('checked', discountPercentType).trigger('change');
    });
//        $('[name="grn_base_price[]"]').each(function(){
//            $(this).trigger('change');
//        });
}

function updateItemTotals()
{
    var total = 0;
    $('[name="grn_discount[]"]').each(function(){
        total = total + parseFloat($(this).val());
    });
    $('#discount_on_items').text(total);
}

function autosuggest() {
    $("#product_sku").autocomplete({
        source: '/grn/getSkus?supplier_id=' + $('#grn_supplier').val() + '&warehouse_id=' + parseInt($('[name="warehouse"]').val()),
        minLength: 2,
        params: {entity_type: $('#supplier_list').val()},
        select: function (event, ui) {
            $('#product_id').val(ui.item.product_id);
            $('#sku_uom').empty();
            $('#sku_uom').append(ui.item.packoum);
        }
    });
}
function deleteDoc(id) {
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: "/grn/delete",
        type: "POST",
        data: {id: id},
        dataType: 'json',
        success: function (response) {
            $('#ajaxResponse').html(response.message);
            var num = response.doc_ref_type;
            var index = docarray.indexOf(num.toString());
            if(index > -1 ){
                docarray.splice(index,1);                
            }
        },
        error: function (response) {
            $('#ajaxResponse').html("{{Lang::get('salesorders.errorInputData')}}");
        }
    });
}
function checkReceivedQty_old() {
    var tot_free = ($('#total_free').val() != '') ? parseInt($('#total_free').val()) : 0;
    var total_excess = ($('#total_excess').val() != '') ? parseInt($('#total_excess').val()) : 0;
    var total_damage = ($('#total_damage').val() != '') ? parseInt($('#total_damage').val()) : 0;
    var total_missed = ($('#total_missed').val() != '') ? parseInt($('#total_missed').val()) : 0;
    var total_quarantine = ($('#total_quarantine').val() != '') ? parseInt($('#total_quarantine').val()) : 0;
    var total_received = parseInt($('#actual_received').val()) + total_excess;
    var tot_removed = tot_free + total_damage + total_missed + total_quarantine;
    if ( total_received >= 0 ) {
        $('#total_received').val(total_received - tot_removed);
    } else {
        $('#error-msg1').html("{{Lang::get('inward.alertRecieveQtyEmpty')}}").show();
        /*window.setTimeout(function () {
         $('#error-msg1').hide()
         }, 2000);*/
        return false;
    }
}
function checkReceivedQty() {
    var total_quarantine = ($('#total_quarantine').val() != '') ? parseInt($('#total_quarantine').val()) : 0;
    var total_received = parseInt($('#actual_received').val());
    var tot_removed = total_quarantine;
    if ( total_received >= 0 ) {
        $('#total_received').val(total_received - tot_removed);
    } else {
        $('#error-msg1').html("{{Lang::get('inward.alertRecieveQtyEmpty')}}").show();
        /*window.setTimeout(function () {
         $('#error-msg1').hide()
         }, 2000);*/
        return false;
    }
}
function packTotal(reference) {
    var rqty = parseInt(reference.closest('tr').find('input[name="rqty"]').val(), 10);
    var uomqty = reference.closest('tr').find('.uomqty').val();
    if ( rqty != '' && uomqty != '' ) {
        var totqty = parseInt(uomqty) * parseInt(rqty);
        reference.closest('tr').find('.qtytotal').val(totqty);
    }
}
function getSuppliers() {
    $('#on_bill_discount_type').prop('checked', false);
    $('#discount_on_bill').val(0);
    $("#po_approval_status").val(0);
    $("#po_discount_amount").val(0);
    $('.loderholder').show();
    $('#product_list').find('tbody').empty();
    $("#grn_supplier").empty();
    $("#warehouse").empty();
    var url = '/grn/getsuppliers';
    var po_id = $('#po_id').val();
    var dataString = {'po_id': po_id};
    $.get(url, dataString, function (response) {
        $("#grn_supplier").append(response.supplierList);
        $("#warehouse").append(response.warehouseList);
        $("#po_approval_status").val(response.po_approval_status);        
        $("#grn_supplier").select2();
        $("#warehouse").select2();
        $('#frmSelectGrn').formValidation('revalidateField', 'grn_supplier');
        $('#frmSelectGrn').formValidation('revalidateField', 'warehouse');
        console.log(response.po_discount_data);
        if(typeof response.po_discount_data.apply_discount_on_bill != "undefined" && response.po_discount_data.apply_discount_on_bill == 1)
        {
            if(typeof response.po_discount_data.discount_type != "undefined" && response.po_discount_data.discount_type == 1)
            {
                $('#on_bill_discount_type').prop('checked', true);
            }
            if(typeof response.po_discount_data.discount != "undefined")
            {
                $('#discount_on_bill').val(response.po_discount_data.discount);
                $("#po_discount_amount").val(response.po_discount_data.discount);
            }
            if(typeof response.po_discount_data.discount_before_tax != "undefined" && response.po_discount_data.discount_before_tax==1)
            {
                $('#discount_on_bill_options').prop('checked', true);
            }
        }
        $("#product_list").append(response.productList);
        $('#sno_increment').val(response.sno);
        //$('#totqtylabel').text(response.calculation.totqty);
        $('#tottaxperlabel').text(response.calculation.tottaxper);
        $('#tottaxvallabel').text(response.calculation.tottaxval);
        $('#basetotlabel').text(response.calculation.basetot);
        $('#grandtotlabel').text(response.calculation.grandtot);
        $('#grandtotalval').val(response.calculation.grandtot);
        $('.mfg_date').datepicker({endDate: "0", autoclose: true});
        autosuggest();
        var isDocumentRequired = response.is_documnet_required;
        $('[name="is_document_required"]').val(isDocumentRequired);
		$(".fixTable").tableHeadFixer({"head" : false, "right" : 1});
		$(".sub_table").removeAttr("style");
        $("#save_continue").removeAttr("disabled").removeClass("disabled");
    });
    getPoHistory(po_id);
    $('.loderholder').hide();
}

function getPoHistory(po_id)
{   
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: '/grn/getpoapprovalhistory/'+po_id,
        type: 'GET',
        success: function (data) {
            $('#po_history_data').append(data);
        }
    });
}

function getCheckedBox() {
    var checked = false;
    $("input[name='grn_sku[]']").each(function () {
        if ( $(this).prop('checked') == true ) {
            checked = true;
            return;
        }
    });
    return checked;
}
function checkProductAdded(product_id) {
    var checked = true;
    $("input[name='grn_product_id[]']").each(function () {
        var productid_exist = $(this).val();
        if ( productid_exist == product_id ) {
            checked = false;
            return;
        }
    });
    return checked;
}
function checkPackAdded(pack_id) {
    var checked = true;
    $("input[name='packsize_id[]']").each(function () {
        var packid_exist = $(this).val();
        if ( packid_exist == pack_id ) {
            checked = false;
            return;
        }
    });
    return checked;
}
function checkDocAdded() {
    var checked = false;

    $("input[name='docs[]']").each(function () {
        var docid = $(this).val();
        if ( docid != '' ) {
            checked = true;
            return;
        }

    });
    
    if(docarray.length>0 && $.inArray("95006",docarray) == -1){
        
        return false;
    }
    return checked;
}

function reCalculateRowJson(productId)
{
    if(productId > 0)
    {
        var discountAmount = parseFloat($('#discount'+productId).text());
        var rowCompleteDetails = $('#row_total_json_'+productId).val();
        if(typeof rowCompleteDetails != "undefined" && rowCompleteDetails != '')
        {
            var rowCompleteArray = $.parseJSON(rowCompleteDetails);
            if(!$.isEmptyObject(rowCompleteArray))
            {
                var subTotal = parseFloat((rowCompleteArray.subTotal).replace(/\,/g,''));
                var taxVal = parseFloat((rowCompleteArray.taxval).replace(/\,/g,''));
//                var discountAmount = calculateDiscountAmount(productId, rowDiscount, subTotal);
                var discount_tax_type = $('#discount_tax_type'+productId).prop('checked');
                if(!discount_tax_type){
                    var finalTotalVal = parseFloat(subTotal + taxVal);
                }else{
                    var finalTotalVal = parseFloat(subTotal + taxVal - discountAmount);
                }            
                rowCompleteArray.total_discount = $.number(discountAmount, 10);
                rowCompleteArray.totalval = $.number(finalTotalVal, 10);
                $('#grntotalval'+productId).val(finalTotalVal);
                $('#row_total_json_'+productId).val(JSON.stringify(rowCompleteArray));
            }
        }
    }
}

function calculateDiscountAmount(productId, discountAmount, subTotal)
{
    if(discountAmount == '')
    {
        discountAmount = 0;
    }
    var discountType = 0;
    var finalDiscountAmount = 0;
    if(productId > 0 && subTotal > 0)
    {
        discountType = $('#discounttype'+productId).prop("checked");
        if(discountType == true){
            finalDiscountAmount = parseFloat(subTotal * (discountAmount/100)).toFixed(5);
        }else{
            finalDiscountAmount = discountAmount;
        }
        if (!$.isNumeric(discountAmount) && discountAmount.indexOf(",") >= 0){
            finalDiscountAmount = discountAmount.replace(/\,/g,'');
        }
    }
    return finalDiscountAmount;
}

function calculateDiscount(productId, discountAmount)
{
    if(discountAmount == '' || discountAmount < 0)
    {
        discountAmount = 0;
    }
    if(isNaN(discountAmount))
    {
        discountAmount = 0;
    }
    var discountType = 0;
    if(productId > 0)
    {
        discountType = $('#discounttype'+productId).prop("checked");
        var count = $('#packconfiglist-'+productId+' tbody').children().length;
        var finalRowTotal = 0.00;
        
        if(count == 0)
        {
            discountAmount = 0.00;
        }
//        var subTotal = parseFloat($('#subTotal'+productId).text().replace(/\,/g,''));
//        var rowTotal = parseFloat($('#totalval'+productId).text().replace(/\,/g,''));
        var rowTotal = calculateRowTotal(productId);
        if(discountType == true){
//            discountAmount = $.number(rowTotal * (discountAmount/100), 5);
            var temp = rowTotal * discountAmount;
            discountAmount = $.number((temp/100), 5);
        }else{
            var perUnitDiscount = $('#unit_discount_'+productId).val();
            console.log('perUnitDiscount');
            console.log(perUnitDiscount);
            if(perUnitDiscount > 0)
            {
                var grn_received_qty = $('#grn_received_'+productId).val();
				var grn_damaged_qty = $('#total_grn_damaged_'+productId).val();
				console.log('grn_damaged_qty');
                console.log(grn_damaged_qty);
				grn_received_qty = (grn_received_qty - grn_damaged_qty);
                console.log('grn_received_qty');
                console.log(grn_received_qty);
                if(grn_received_qty > 0)
                {
                    discountAmount = (perUnitDiscount * grn_received_qty).toFixed(5);
                }
				var remaining_packpo_qty = $('#remaining_packpo_qty_'+productId).val();
				var po_discount_amount = $('#po_discount_amount_'+productId).val();
				if(remaining_packpo_qty == grn_received_qty)
				{
					discountAmount = po_discount_amount;
				}
                console.log('discountAmount');
                console.log(discountAmount);
            }
        }
        if (!$.isNumeric(discountAmount) && discountAmount.indexOf(",") >= 0){
            discountAmount = discountAmount.replace(/\,/g,'');
        }
//        var rowTaxValue = parseFloat($('#taxval'+productId).val());
//        if(discountAmount > (subTotal + rowTaxValue))
//        if(discountAmount > (rowTotal - discountAmount))
        if((rowTotal.toFixed(5) - discountAmount) < 0)
        {
            $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('inward.alertDiscount')}}").show();
            $('html, body').animate({scrollTop: '0px'}, 500);
            $('#discountpercent'+productId).val(0);
            discountAmount = 0;
        }else{
            var discount_on_bill = $('#discount_on_bill').val();
            var discount_on_bill_options = $('#discount_on_bill_options').prop('checked');
            if(discount_on_bill > 0 && discount_on_bill_options == false)
            {
//                discountAmount = 0;
            }
        }
//        finalRowTotal = $.number(((subTotal + rowTaxValue) - discountAmount), 5);
        var discount_tax_type = $('#discount_tax_type'+productId).prop('checked');
        if(!discount_tax_type)
        {
            var received = parseInt($('#grn_received_'+productId).val());
            var basePrice = parseFloat($('#baseprice'+productId).val());
            var free_qty = parseInt($('#grn_received_'+productId).closest('tr').find('.grn_free').val());
            var damage_qty = parseInt($('#grn_received_'+productId).closest('tr').find('.grn_damaged').val());
            var total_grn_free = parseInt($('#total_grn_free_' + productId).val());
            var total_grn_damaged = parseInt($('#total_grn_damaged_' + productId).val());
            var tax_per = parseFloat($('#grn_received_'+productId).closest('tr').find('input[name="grn_taxper[]"]').val());
            var totQty = parseInt(received - (free_qty + total_grn_free + total_grn_damaged + damage_qty));
            var taxVal = parseFloat((((basePrice * totQty)-discountAmount) * tax_per) / 100);
            rowTotal = rowTotal + taxVal;
        }
        finalRowTotal = $.number((rowTotal - discountAmount), 5);        
        $('#discountamount'+productId).val(discountAmount);
        $('#totalval'+productId).text(finalRowTotal);
        $('#grntotalval'+productId).val(finalRowTotal);
        $('#discount'+productId).text(discountAmount);
        if(!$('#discounttype'+productId).prop("checked") && discountAmount > 0)
        {
            $('#discountpercent'+productId).val(discountAmount);
        }
        $('#discountval'+productId).val(discountAmount);
    }
    calcGrandTot();
    reCalculateRowJson(productId);
    updateItemTotals();
}

function calculateRowTotal(productId)
{
    var total = 0;
    if(productId > 0)
    {
        var basePrice = parseFloat($('#baseprice'+productId).val());
        var product_id = productId;
        var received = parseInt($('#grn_received_'+productId).val());
//        var rqty = parseInt($(this).closest('tr').find('input[name="rqty"]').val());
        if(received > 0)
        {
            var free_qty = parseInt($('#grn_received_'+productId).closest('tr').find('.grn_free').val());
            var damage_qty = parseInt($('#grn_received_'+productId).closest('tr').find('.grn_damaged').val());
            var total_grn_free = parseInt($('#total_grn_free_' + productId).val());
            var total_grn_damaged = parseInt($('#total_grn_damaged_' + productId).val());
            var tax_per = parseFloat($('#grn_received_'+productId).closest('tr').find('input[name="grn_taxper[]"]').val());
//            var discount = parseFloat($('#grn_received_'+productId).closest('tr').find('input[name="grn_discount_amount[]"]').val());
//            if(discount < 0)
//            {
//                discount = 0;
//                calculateDiscount(product_id, discount);
//            }
            var totQty = parseInt(received - (free_qty + total_grn_free + total_grn_damaged + damage_qty));
            var subTotal = basePrice * totQty;
//            var taxVal = parseFloat(((basePrice * tax_per) / 100) * totQty);
            var taxVal = parseFloat(((basePrice * totQty) * tax_per) / 100);
            console.log('taxVal');
            console.log(taxVal);
            var discount_tax_type = $('#discount_tax_type'+productId).prop('checked');
//            var total = subTotal + taxVal - discount;
            if(discount_tax_type)
            {
                var total = subTotal + taxVal;
            }else{
                var total = subTotal;
            }
//            $('#taxtext' + product_id).text($.number(taxVal, 5));
//            $('#taxval' + product_id).val(taxVal);
//            $('#subTotal' + product_id).text($.number(subTotal, 5));
//            $('#totalval' + product_id).text($.number(total, 5));
//            $('#grntotalval' + product_id).val(total);
//            calcGrandTot();
        }
    }
    return total;
}

function recalculateRowValues()
{
    $('input[name="grn_base_price[]"]').trigger('change');
    return;
}

function calcGrandTot() {
	$('#grn_from').find('input[type="submit"]').prop('disabled', false);
    var baseTotal = 0;
    var recQty = 0;
    var totTax = 0;
    var rowDiscounts = 0;
    $('#discount_on_bill_value').val(0);
    $('input[name="grn_received[]"').each(function () {
        recQty += parseFloat($(this).val());
    });
//    $('input[name="grn_total[]"').each(function () {
//        baseTotal += parseFloat($(this).val());
//    });
    $("span[id^='subTotal']").each(function(){
        var temp = $(this).text();
        baseTotal += parseFloat(temp.replace(/\,/g,''));
    });
    
    $('input[name="grn_taxvalue[]"').each(function () {
        totTax += parseFloat($(this).val());
    });
    
    $('input[name="grn_discount_amount[]"').each(function () {
        rowDiscounts += parseFloat($(this).val());
    });
    $('#totqtylabel').text(recQty);
    $('#totqtyval').val(recQty);
    $('#basetotlabel').text($.number(baseTotal, 5));
    $('#basetotval').val(baseTotal);
    if(totTax >= 0)
    {
        $('#tottaxvallabel').text($.number(totTax, 5));
        $('#tottaxval').val(totTax);
    }    
    var shippingCost = parseFloat($('#shippingcost').val());
    if(isNaN(shippingCost))
    {
        shippingCost = 0;
    }
    var discount_on_bill = parseFloat($('#discount_on_bill').val());
    var po_discount_amount = parseFloat($("#po_discount_amount").val());
    if(po_discount_amount > 0)
    {
        discount_on_bill = po_discount_amount;
    }
    if(isNaN(discount_on_bill))
    {
        discount_on_bill = 0;
    }
    if(discount_on_bill > 0)
    {
        var discountAmount = 0;
//        var grandtotalval = $('#grandtotalval').val();
        var grandtotalval = parseFloat((baseTotal + shippingCost + totTax) - rowDiscounts);
        var discount_on_bill_options = $('#discount_on_bill_options').prop('checked');
        if(discount_on_bill_options != true)
        {
            var on_bill_discount_type = $('#on_bill_discount_type').prop('checked');
            console.log('on_bill_discount_type');
            console.log(on_bill_discount_type);
            if(on_bill_discount_type == true)
            {
                var temp2 = (grandtotalval * discount_on_bill);
                discountAmount = (temp2 / 100);
            }else{
                discountAmount = discount_on_bill;
            }
            if(discountAmount > 0)
            {
                $('#discount_on_bill_value').val(discountAmount);
            }
        }
        var po_approval_status = $("#po_approval_status").val();
        if(grandtotalval > 0 && (grandtotalval - discountAmount) < 0)
        {
            console.log('we are in if');
            if(po_approval_status == 57119)
            {
                console.log('we are in if if');
                discount_on_bill = grandtotalval;
                //$('#discount_on_bill').val(discount_on_bill);
            }else{
                console.log('we are in else else');
            $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('inward.alertDiscount')}}").show();
            $('html, body').animate({scrollTop: '0px'}, 500);
//            $('#discountpercent' + productId).val(0);
            discount_on_bill = 0;
            }
        }else{
            console.log('we are in else');
            if(po_approval_status == 57119)
            {
                var po_discount_amount = $("#po_discount_amount").val();
                if(discountAmount > po_discount_amount)
                {
                    //discountAmount = po_discount_amount;
                }
                //$('#discount_on_bill').val(discountAmount);
            }
            discount_on_bill = discountAmount;
        }
    }
    if(discount_on_bill_options){
        var grandTot = (baseTotal + shippingCost + totTax);
    }else{
        var grandTot = (baseTotal + shippingCost + totTax) - (discount_on_bill + rowDiscounts);
    }
    if(grandTot < 0)
    {
        grandTot = 0;
    }
    $('#grandtotlabel').text($.number(grandTot, 5));
    $('#grandtotalval').val(grandTot.toFixed(5));
}
$(document).ready(function () {
    $('#grn_date').datepicker();
    $('#invoice_date').datepicker();
    $('#mfg_date').datepicker({endDate: "0", autoclose: true});
    autosuggest();
    $('.add_sku_btn').click(function () {
        var curState = $(this).attr('data-state');
        if ( curState == 'show' ) {
            $('#addskurow').hide('slow');
            $(this).attr('data-state', 'hide');
            $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
        } else {
            $('#addskurow').show('slow');
            $(this).attr('data-state', 'show');
            $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
        }
    });
    $(document).on('click', '.grnItem', function () {
        var itemId = $(this).attr("id");

        $("#packinfo-" + itemId).toggle("slow", function () {
        });

        if ( $(this).find('i').hasClass('fa-caret-right') ) {
            $(this).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        }
        else if ( $(this).find('i').hasClass('fa-caret-down') ) {
            $(this).find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
        }

    });
    
    $(document).on('change', 'input[name="rqty"]', function () {
        $(this).val(parseInt($(this).val(), 10));
    });
    
    $(document).on('change', 'input[name="grn_base_price[]"]', function () {
        var basePrice = parseFloat($(this).val());
        var product_id = $(this).closest('tr').find('input[name="grn_product_id[]"]').val();
        var received = parseInt($(this).closest('tr').find('input[name="grn_received[]"]').val());
        var rqty = parseInt($(this).closest('tr').find('input[name="rqty"]').val());
        if(received > 0)
        {
            var free_qty = parseInt($(this).closest('tr').find('.grn_free').val());
            var damage_qty = parseInt($(this).closest('tr').find('.grn_damaged').val());
            var total_grn_free = parseInt($('#total_grn_free_' + product_id).val());
            var total_grn_damaged = parseInt($('#total_grn_damaged_' + product_id).val());
            var tax_per = parseFloat($(this).closest('tr').find('input[name="grn_taxper[]"]').val());
            var discount = parseFloat($(this).closest('tr').find('input[name="grn_discount_amount[]"]').val());
            if(discount < 0)
            {
                discount = 0;
                calculateDiscount(product_id, discount);
            }
            var totQty = parseInt(received - (free_qty + total_grn_free + total_grn_damaged + damage_qty));
            var subTotal = basePrice * totQty;
            var taxVal = parseFloat(((basePrice * tax_per) / 100) * totQty);
            var total = subTotal + taxVal - discount;
            $('#taxtext' + product_id).text($.number(taxVal, 5));
            $('#taxval' + product_id).val(taxVal);
            $('#subTotal' + product_id).text($.number(subTotal, 5));
            $('#totalval' + product_id).text($.number(total, 5));
            $('#grntotalval' + product_id).val(total);
            calcGrandTot();
        }else{
            calcGrandTot();
        }
    });
    $(document).on('change', '#warehouse', function () {
        autosuggest();
    });
    $(document).on('change', '#grn_supplier', function () {
        autosuggest();
        var supplier_id = $(this).val();
        if ( supplier_id != '' ) {
            $.ajax({
                headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/grn/getWarehouses',
                type: 'POST',
                data: {supplier_id: supplier_id},
                dataType: 'JSON',
                success: function (data) {
                    $('#warehouse').html(data.warehouse_list);
                    $("#warehouse").select2();
                },
                error: function (response) {
                    
                }
            });
        }
    });
    $(document).on('change', '#po_id', function () {
        console.log('change triggered');
        $('#po_history_data').html('');
        getSuppliers();
    });
    $(document).on('change', 'warehouse', function () {
        $('#frmSelectGrn').formValidation('revalidateField', 'warehouse');
        autosuggest();
    });

    var po_selected = $("#po_selected").val();
    if(po_selected!=0){
        $("#po_id").val(po_selected).trigger("change");
    }

    $(document).on('blur','#invoice_id',function(){
        var invoiceval = $(this).val();
        $("#reference_id").val(invoiceval);
        $("#ref_no").val(invoiceval);
    });

    $(document).on('click', '.edit_product', function () {
        var product_id = $(this).attr('data-id');
        var po_qty = $(this).attr('data-poqty');
        var po_qty_uom = $(this).attr('data-poqtyuom');
        var po_num_eaches = $(this).attr('data-ponumeaches');
        $('[name="pack_size"]').empty();
        $('#packcofigtable').empty();
        $('#packConfigForm')[0].reset();
        if ( product_id != '' ) {
            var edit_data = $('span.productPackdata' + product_id + ' :input').serialize();
            var grn_remarks = $('#grn_remarks' + product_id).val();
            var discountval = $('#discountval' + product_id).val();
            var discounttype = $('#discounttype' + product_id).val();
            var discountpercent = $('#discountpercent' + product_id).val();
            $.ajax({
                headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/grn/getProductPackInfo?' + edit_data + '&grn_remarks=' + grn_remarks + '&discountval=' + discountval + '&discounttype=' + discounttype,
                type: 'POST',
                data: {product_id: product_id},
                dataType: 'JSON',
                success: function (data) {
                    $('#rqty').val(0);
                    $('#uomqty1,#qtytotal1').text(0);
                    $('span#producttitle').text(data[0].product_title);
                    $('span#articlename').text(data[0].seller_sku);
                    $('[name="pack_size"]').append(data['packuom']);
                    $('#tax_type').val(data['taxes']['taxtype']);
                    $('#tax_val').val(data['taxes']['taxpercent']);
                    $('#packproduct_id').val(product_id);
                    $('#packcofigtable').append(data['packcofigtable']);
                    $('#total_received').val(data['grn_received']);
                    $('#actual_received').val(data['actual_received']);
                    $('#total_free').val(data['grn_free']);
                    $('#total_excess').val(data['grn_excess']);
                    $('#total_damage').val(data['grn_damaged']);
                    $('#total_missed').val(data['grn_missed']);
                    $('#total_quarantine').val(data['grn_quarantine']);
                    $('#total_discount').val(discountpercent);

                    if ( discounttype == 2 ) {
                        $("#percenttype").prop("checked", true);
                    } else {
                        $("#flattype").prop("checked", true);
                    }
                    $('#pr_remarks').val(data['grn_remarks']);

                    $('#poqtylabel').text(po_qty);
                    $('#pouomlabel').text(po_qty_uom);
                    $('#poeacheslabel').text(po_num_eaches);
                    $('#packpo_qty').val(po_qty);
                    $('#packpo_numof_eaches').val(po_num_eaches);

                },
                error: function (response) {

                }
            });
        }
    });
    $(document).on('click', '.delete_product_pack', function () {
        var receivedtotal = parseInt($(this).closest('tr').find('.receivedtotal').val());
        var quarantined = $(this).closest('tr').find('[name="pack_status[]"]').hasClass("quarantined");
        var productId = $(this).closest('tr').find('input[id="product_id"]').val();
        var total_received = parseInt($('#total_received').val());
        
        var row_free = parseInt($(this).closest('tr').find('[id="row_free_qty_'+productId+'"]').val());
        var total_grn_free = parseInt($('#total_grn_free_' + productId).val());
        total_grn_free = parseInt(total_grn_free - row_free);
        if(total_grn_free < 0)
        {
            total_grn_free = 0;
        }
        $('#total_grn_free_' + productId).val(total_grn_free);
        
        var row_damaged = parseInt($(this).closest('tr').find('[id="row_damaged_qty_'+productId+'"]').val());
        var total_grn_damaged = parseInt($('#total_grn_damaged_' + productId).val());
        total_grn_damaged = parseInt(total_grn_damaged - row_damaged);
        if(total_grn_damaged < 0)
        {
            total_grn_damaged = 0;
        }
        $('#total_grn_damaged_' + productId).val(total_grn_damaged);
        var total_quarantine = parseInt($('#total_quarantine').val());
        var quarnqty = parseInt($(this).closest('tr').find('.receivedtotal').val());
        if ( total_quarantine > 0 && quarantined ) {
            var total_quarantine = parseInt(total_quarantine) - parseInt(quarnqty);
            $('#total_quarantine').val(parseInt(total_quarantine));
            receivedtotal = 0;
        }
        var rowRecievedQty = parseInt($(this).closest('tr').find('td.total_recieved_qty').text());
        var closest = $(this).closest('tr').closest('table').closest('tr').prev();
        var total_received = parseInt(closest.find('[name="grn_totreceived[]"]').val());
        var totalValue = Math.abs(total_received - rowRecievedQty);
        if(totalValue == 0)
        {
            $('#taxtext' + productId).text($.number(0.00, 5));
            $('#taxval' + productId).val(0.00);
            $('#subTotal' + productId).text($.number(0.00, 5));
            $('#totalval' + productId).text($.number(0.00, 5));
            $('#grntotalval' + productId).val(0.00);
        }
        closest.find('[name="grn_received[]"]').val(totalValue);
        closest.find('[name="grn_totreceived[]"]').val(totalValue);
        closest.find('[name="grn_totreceived[]"]').closest('td').find('span').text(totalValue);
//        var totqtyval = parseInt($('#totqtyval').val());
//        if(rowRecievedQty > totqtyval)
//        {
//            $('#totqtylabel').text(rowRecievedQty - totqtyval);
//            $('#totqtyval').val(rowRecievedQty - totqtyval);
//        }else{
//            $('#totqtylabel').text(totqtyval - rowRecievedQty);
//            $('#totqtyval').val(totqtyval - rowRecievedQty);
//        }
        $('#total_received,#actual_received').val(total_received - receivedtotal);
        $(this).closest('tr').remove();
        recalculateRowValues(productId, rowRecievedQty);
        calcGrandTot();
        return false;
    });
    
    $(document).on('change', '[name="grn_discount_percent[]"], [name="grn_discount_inc_tax[]"]', function(){
        var productId = parseInt($(this).attr('data-product-id'));
        if($(this).attr('name') == 'grn_discount_inc_tax[]')
        {
            var discountAmount = parseFloat($('#discountpercent'+productId).val());
        }else{
            var discountAmount = parseFloat($(this).val());
        }
        if(productId > 0)
        {
            calculateDiscount(productId, discountAmount);
        }
    });
    
    $(document).on('change', '[name="grn_discount_type[]"]', function(){
        var productId = parseInt($(this).attr('data-product-id'));
        var discountAmount = parseFloat($('#discountpercent'+productId).val());
        if(productId > 0)
        {
            calculateDiscount(productId, discountAmount);
        }
    });
    
    $(document).on('click', '.delete_product', function () {
        var product_id = $(this).attr('data-id');
        $(this).closest('tr').remove();
        $('#packinfo-' + product_id).remove();
        calcGrandTot();
        return false;
    });
    $(document).on('click', '.addpackbtn', function () {
        $('.loderholder').show();
        var closest = $(this).closest('tr');
        var rqty = parseInt(closest.find('[name="rqty"]').val(), 10);
        var rqty_eaches = closest.find('[name="pack_size"] option:selected').attr('data-noofeach');
        var recievedQuantity = (rqty * rqty_eaches);
        if(!(rqty > 0))
        {
            alert('Received Qty should be greater than 0');
            $('#error-msg1').html('Received Qty should be greater than 0').show();
            window.setTimeout(function () {
                $('#error-msg1').hide();
            }, 5000);
            $('.loderholder').hide();
            return false;
        }
        var product_id = closest.find('input[name="grn_product_id[]"]').val();
        var pack_size = closest.find('[name="pack_size"]').val();
//        var uomqty = closest.find('.uomqty').val();                
//        var qtytotal = parseInt(closest.find('.qtytotal').val());
        var qtytotal = recievedQuantity;
        var free = parseInt(closest.find('.grn_free').val());
        if(free > recievedQuantity)
        {
            alert('Received Qty should be greater than free qty');
            $('#error-msg1').html('Received Qty should be greater than free qty').show();
            window.setTimeout(function () {
                $('#error-msg1').hide();
            }, 5000);
            $('.loderholder').hide();
            return false;
        }
        var damaged = parseInt(closest.find('.grn_damaged').val());
        var grn_excess = parseInt(closest.find('.grn_excess').val());
        var grn_missed = parseInt(closest.find('.grn_missed').val());
        var grn_quarantine = parseInt(closest.find('.grn_quarantine').val());
        var mfg_date = closest.find('.mfg_date').val();
        var totalQtyReceived = parseInt(rqty * rqty_eaches);
        var totalExtraQtyReceived = parseInt(free + damaged + grn_excess + grn_missed + grn_quarantine);
        if(totalExtraQtyReceived > totalQtyReceived)
        {
            alert('Received Qty should be greater than other qty');
            $('#error-msg1').html('Received Qty should be greater than other qty').show();
            window.setTimeout(function () {
                $('#error-msg1').hide();
            }, 5000);
            $('.loderholder').hide();
            return false;
        }
//        var total_received = parseInt(closest.find('[name="grn_totreceived[]"]').val()) + qtytotal;
        var total_received = parseInt(closest.find('[name="grn_totreceived[]"]').val());
        var packpo_num_eaches = parseInt(closest.find('[name="packpo_numof_eaches"]').val());
//        var packpo_qty = (closest.find('[name="packpo_qty"]').val() * packpo_num_eaches);
        var packpo_qty = (closest.find('[name="packpo_qty"]').val());
        var remaining_packpo_qty = $('#remaining_packpo_qty_'+product_id).val();
        var uomqty = closest.find('#pack_uom_qty_'+product_id+'  option:selected').attr('data-noofeach');
        var purchaseOrder = $('[name="po_id"]').val();
        if ( pack_size != '' && ( purchaseOrder == 'Manual' || qtytotal > 0) ) {
            //if (checkPackAdded(pack_size)) {
            if ( (total_received + (rqty * rqty_eaches)) <= remaining_packpo_qty ) {
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: '/grn/getPackText',
                    type: 'POST',
                    data: {packproduct_id: product_id, pack_size: pack_size, uomqty: uomqty, rqty: rqty, qtytotal: qtytotal, mfg_date: mfg_date, free: free, damaged: damaged},
                    dataType: 'JSON',
                    success: function (data) {
                        var total_received2 = parseInt(closest.find('[name="grn_totreceived[]"]').val());
                        if ( (total_received2 + (rqty * rqty_eaches)) > remaining_packpo_qty ) {
                            alert('Received Qty should not be more than PO Qty');
                            $('#error-msg1').html('Received Qty should not be more than PO Qty').show();
                            window.setTimeout(function () {
                                $('#error-msg1').hide();
                            }, 5000);
                            return;
                        }
                        var freshness = parseInt(data.freshness_per);
                        if ( freshness >= 75 ) {
                            $('#packconfiglist-' + product_id).append(data.product_data);
                            var received_qty = parseInt(data.received_qty);
                            var total_received = parseInt(closest.find('[name="grn_totreceived[]"]').val());
                            closest.find('[name="grn_received[]"]').val(received_qty + total_received);
                            var total_grn_free = parseInt($('#total_grn_free_' + product_id).val());
                            total_grn_free = parseInt(total_grn_free + free);
                            $('#total_grn_free_' + product_id).val(total_grn_free);
                            var total_grn_damaged = parseInt($('#total_grn_damaged_' + product_id).val());
                            total_grn_damaged = parseInt(total_grn_damaged + damaged);
                            $('#total_grn_damaged_' + product_id).val(total_grn_damaged);
                            closest.find('[name="grn_totreceived[]"]').val(received_qty + total_received);
                            $('#received' + product_id).text(received_qty + total_received);
                            closest.find('[name="pack_size"] option:first').attr("selected", true);
                            var eaches = closest.find('[name="pack_size"] option:first').attr('data-noofeach');
                            closest.find('.mfg_date').val('');
                            closest.find('.uomqty').val(eaches);
                            closest.find('[name="rqty"],.qtytotal,.grn_free,.grn_damaged').val(0);
                            
                            var received = parseInt((received_qty - (total_grn_free + total_grn_damaged)) + total_received);
                            var total_discount = 0;
                            var discount_type = 1;

                            var discountPercentVal = total_discount;
                            var tax_per = $('#taxper' + product_id).val();
                            var basePrice = $('#baseprice' + product_id).val();
                            var subTotal = received * basePrice;
                            
                            var rowDiscount = $('#discountpercent'+product_id).val();
                            total_discount = parseFloat(calculateDiscountAmount(product_id, rowDiscount, subTotal));
                            var discount_tax_type = $('#discount_tax_type'+product_id).prop('checked');
                            console.log('disctax tyope ===',discount_tax_type);
                            console.log('suuuuubbb111---'+subTotal+'===='+total_discount);
                            if(!discount_tax_type){
                                var taxVal = ((subTotal-total_discount) * tax_per) / 100;
                                subTotal = subTotal-total_discount;
                                var total = subTotal + taxVal;
                            }else{
                                var taxVal = ((basePrice * tax_per) / 100) * received;
                                var total = subTotal + taxVal - total_discount;
                            }
                            console.log('suuuuubbb---'+subTotal+'===='+total+'====='+taxVal+'===='+total_discount+'===='+tax_per);
                            $('#taxtext' + product_id).text($.number(taxVal, 5));
                            $('#taxval' + product_id).val(taxVal);
                            $('#subTotal' + product_id).text($.number(subTotal, 5));
                            $('#totalval' + product_id).text($.number(total, 5));
                            $('#grntotalval' + product_id).val(total);
                            
                            var tempTotals = new Array();
                            tempTotals = {
                                "taxval" : $.number(taxVal, 10),
                                "subTotal" : $.number(subTotal, 10),
                                "totalval" : $.number(total, 10),
                                "total_discount" : $.number(total_discount, 10)
                            };
                            $('<input>').attr({
                                type: 'hidden',
                                name: product_id,
                                id: 'row_total_json_'+product_id,
                                value: JSON.stringify(tempTotals)
                            }).appendTo($('#grntotalval' + product_id));
                            var discountAmount = parseFloat($('#discountpercent'+product_id).val());
                            if(product_id > 0)
                            {
                                calculateDiscount(product_id, discountAmount);
                            }
                            calcGrandTot();

                        } else {
                            alert('Freshness percentage is ' + freshness + ', it should be more than 75%');
                            $('#error-msg1').html('Freshness percentage is ' + freshness + ', it should be more than 75%').show();
                            window.setTimeout(function () {
                                $('#error-msg1').hide();
                            }, 5000);
                        }
                    },
                    error: function (response) {

                    }
                });
            } else {
                alert('Received Qty should not be more than PO Qty');
                $('#error-msg1').html('Received Qty should not be more than PO Qty').show();
                window.setTimeout(function () {
                    $('#error-msg1').hide();
                }, 5000);
            }
            /*}else{
             $('#error-msg1').html('Pack Size Already Added').show();
             window.setTimeout(function(){$('#error-msg1').hide()},2000);
             }*/
        } else {
            alert("{{Lang::get('inward.alertUomEmpty')}}");
            $('#error-msg1').html("{{Lang::get('inward.alertUomEmpty')}}").show();
            /* window.setTimeout(function () {
             $('#error-msg1').hide()
             }, 2000);*/
        }
        $('.loderholder').hide();
    });
    $(document).on('change', '#total_free,#total_excess,#total_damage,#total_missed,#total_quarantine', function () {
        checkReceivedQty();
    });
    $(document).on('change', '[name="pack_status[]"]', function () {
        var status = $(this).val();
        var total_quarantine = parseInt($('#total_quarantine').val()); //20 6
        var quarnqty = parseInt($(this).closest('tr').find('.receivedtotal').val());
        if ( status == '91003' ) {
            var total_quarantine = parseInt(total_quarantine) + parseInt(quarnqty);
            $(this).addClass('quarantined');
        } else {
            if ( total_quarantine > 0 ) {
                if ( $(this).hasClass("quarantined") ) {
                    var total_quarantine = parseInt(total_quarantine) - parseInt(quarnqty);
                    $(this).removeClass('quarantined');
                }
            }
        }
        $('#total_quarantine').val(parseInt(total_quarantine));
        checkReceivedQty();
    });

    $(document).on('change', '[name="pack_size"]', function () {
        var reference = $(this);
        var oum = $(this).val();
        $(this).closest('tr').find('.uomqty').val(0);
        $(this).closest('tr').find('.qtytotal').val(0);
        if ( oum != '' ) {
            var noofeach = $(this).find(':selected').attr('data-noofeach');
            var oumtext = $(this).find(':selected').text();
            $(this).closest('tr').find('.uomqty').val(noofeach);
            packTotal(reference);
        }
    });
    $(document).on('change', 'input[name="rqty"]', function () {
        var reference = $(this);
        packTotal(reference);
    });

    $(document).on('change', '#discount_on_bill', function () {
        var shippingcost = parseFloat($('#shippingcost').val());
        var discount_on_bill = parseFloat($('#discount_on_bill').val());
//        if(discount_on_bill > 0)
//        {
//            
//        }
        updateDiscount();
        var grandtotalval = parseFloat($('#grandtotalval').val());        
        console.log('grandtotalval');
        console.log(grandtotalval);
        
        
        if(isNaN(shippingcost))
        {
            shippingcost = 0;
        }
        if(isNaN(discount_on_bill))
        {
            discount_on_bill = 0;
        }
        if ( discount_on_bill >= 0 ) {
            var discountPercentType = $('#on_bill_discount_type').prop('checked');
            if(discountPercentType)
            {
                var totcost = parseFloat(grandtotalval + shippingcost).toFixed(5);
            }else{
                var discount_on_bill_options = $('#discount_on_bill_options').prop('checked');
                if(discount_on_bill_options)
                {
                    var totcost = parseFloat(grandtotalval + shippingcost).toFixed(5);
                }else{
                    var totcost = parseFloat(grandtotalval + shippingcost - discount_on_bill).toFixed(5);
                }
            }
            //console.log('totcost=='+totcost+'grand=='+grandtotalval+'ship==='+shippingcost+'disc==='+discount_on_bill);
            if ( totcost > -1 ) {
                //var grandtot = parseFloat(totcost - discount_on_bill).toFixed(2);
                //$('#grandtotlabel').text(grandtot);
                calcGrandTot();
//                $('#ajaxResponse').hide();
            } else {
                $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('inward.alertDiscount')}}").show();
                $('html, body').animate({scrollTop: '0px'}, 500);
                $(this).val(0);
                calcGrandTot();
                return false;
            }
        }
    });

    $(document).on('change', '#shippingcost', function () {
        var shippingcost = parseFloat($('#shippingcost').val());
//        var grandtotalval = parseFloat($('#grandtotalval').val());                
        if(isNaN(shippingcost))
        {
            shippingcost = 0;
        }
        console.log('shippingcost');
        console.log(shippingcost);
        var basePrice = parseFloat($('[name="total_grn_basetotal"]').val());
        var taxPrice = parseFloat($('[name="total_grn_tax_total"]').val());
        var discountTotal = 0;
        $('[name="grn_discount[]"]').each(function(){
            discountTotal = discountTotal + parseFloat($(this).val());
        });
        var grandtotalval = $.number(parseFloat((basePrice + taxPrice) - discountTotal), 5);
        console.log('grandtotalval');
        console.log(grandtotalval);
        if (shippingcost > 0 ) {
            console.log('we are in if');
            var grandtotalval = (grandtotalval - shippingcost);
        }
        $('#grandtotlabel').text($.number(grandtotalval, 5));
        $('#grandtotalval').val(grandtotalval);
    });
    
    $('#grn_add_sku').click(function () {
        var addSkuArr = {};
        var product_sku = $('#product_sku').val();
        var order_qty = $('#order_qty').val();
        var sku_uom = $('#sku_uom').val();
        if(product_sku == '')
        {
            $('#error-msg').html("{{Lang::get('inward.product_sku_validation')}}").show();
            window.setTimeout(function () {
                $('#error-msg').hide();
            }, 5000);
            return;
        }else if(order_qty <= 0)
        {
            $('#error-msg').html("{{Lang::get('inward.qty_validation_error')}}").show();
            window.setTimeout(function () {
                $('#error-msg').hide();
            }, 5000);
            return;
        }else if(sku_uom <= 0 || sku_uom == ''){
            $('#error-msg').html("{{Lang::get('inward.qty_uom_validation_error')}}").show();
            window.setTimeout(function () {
                $('#error-msg').hide();
            }, 5000);
            return;
        }
        $('#addskurow').find('input, select, textarea').each(function () {
            var id = $(this).attr('id');
            var value = $(this).val();
            addSkuArr[id] = value;
            var input_type = $(this).attr('type');
            if ( input_type == 'number' ) {
                $(this).val(0);
                $('#order_qty').val(1);
            } else {
                $(this).val('');
            }

        });
        addSkuArr['sno_increment'] = $('#sno_increment').val();
        addSkuArr['supplier_id'] = $('#grn_supplier').val();
        addSkuArr['le_wh_id'] = $('#warehouse').val();
        var product_id = addSkuArr['product_id'];
        var totqty = $('#totqtyval').val();
        var basetotval = $('#basetotval').val();
        var tottaxval = $('#tottaxval').val();
        var grandtotalval = $('#grandtotalval').val();
        if ( product_id != '' ) {
            if ( checkProductAdded(product_id) ) {
                $.ajax({
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    type: 'POST',
                    url: '/grn/addGrnSkuText',
                    dataType: 'JSON',
                    cache: false,
                    data: {addSkuArr},
                    success: function (response) {
                        $('#product_list').append(response.product_data);
                        $('#sno_increment').val(response.sno_increment);
                        var prqty = parseInt(totqty) + parseInt(response.calculation.totqty);
                        var prbase = parseFloat(basetotval) + parseFloat(response.calculation.basetot);
                        var prtax = parseFloat(tottaxval) + parseFloat(response.calculation.tottaxval);
                        var prgrand = parseFloat(grandtotalval) + parseFloat(response.calculation.grandtot);
                        $('#totqtylabel').text(prqty);
                        $('#totqtyval').val(prqty);
                        $('#tottaxperlabel').text(response.calculation.tottaxper);
                        $('#tottaxval').val(prtax);
                        $('#basetotlabel').text($.number(prbase, 5));
                        $('#basetotval').val(prbase.toFixed(5));
                        $('#grandtotlabel').text($.number(prgrand, 5));
                        $('#grandtotalval').val(prgrand.toFixed(5));
//                        $('.editprod' + product_id).click();
                        $('.mfg_date').datepicker({endDate: "0",autoclose: true});
                    },
                    error: {
                    }
                });
            } else {
                $('#error-msg').html("{{Lang::get('inward.alertAlreadAdded')}}").show();
                window.setTimeout(function () {
                    $('#error-msg').hide()
                }, 5000);
            }
        } else {
            $('#error-msg').html("{{Lang::get('inward.alertSkuEmpty')}}").show();
            window.setTimeout(function () {
                $('#error-msg').hide()
            }, 5000);
        }
    });
    $('.noEnterSubmit').keypress(function (e) {
        if ( e.which == 13 )
            return false;
        //or...
        if ( e.which == 13 )
            e.preventDefault();
    });
    $("#grn_from").validate({
        rules: {
            po_id: {
                required: true
            },
            grn_supplier: {
                required: true
            },
            warehouse: {
                required: true
            }
        },
        submitHandler: function (form) {
            $('#grn_from').find('input[type="submit"]').prop('disabled', true);
            $('.loderholder').show();
            var form = $('#grn_from');
            // console.log(form.serialize());
            var totrec = 0;
            $('input.packtotrec').each(function () {
                totrec += parseInt($(this).val()) || 0;
            });
            if ( getCheckedBox() ) {
                if ( totrec > 0 ) {
                    $('.loderholder').show();
                    var formData = $('#frmSelectGrn, #grn_from').serialize();
                    if ( confirm("Are you sure, you want to create the GRN?")) {
                    $.ajax({
                        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                        url: form[0].action,
                        type: form[0].method,
                        data: formData,
                        method: "POST",
                        dataType: 'json',
                        success: function (data) {
                            if ( data.status == 200 ) {
                                $('.loderholder').hide();
                                $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                $('html, body').animate({scrollTop: '0px'}, 500);
                                window.setTimeout(function () {
                                    window.location.href = '/grn/details/' + data.inward_id
                                }, 2000);
                            } else {
                                $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                                $('html, body').animate({scrollTop: '0px'}, 500);
                                $('.loderholder').hide();

                                if(data.status == 400 || data.status == 500){
                                    displayTime();
                                }
                                if(data.status == 401){
                                    $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.reason).show();
                                    $("#prcingMismatchModal").modal('toggle');
                                    $('#priceMismatchData').html(data.data);
                                    $('#reason_po_so').html(data.reason);
                                    $('#po_so_adjust_message').html(data.adjust_message);

                                }
                            }
                        },
                        error: function (response) {
                            $('#grn_from').find('input[type="submit"]').prop('disabled', false);
                            $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('salesorders.errorInputData')}}").show();
                            $('html, body').animate({scrollTop: '0px'}, 500);
                            $('.loderholder').hide();
                        }
                    });
                } else {
                $('#grn_from').find('input[type="submit"]').prop('disabled', false);
                $('.loderholder').hide();
                return false;
            }
                } else {
                    $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('inward.alertRecieveQtyEmpty')}}").show();
                    $('#grn_from').find('input[type="submit"]').prop('disabled', false);
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    $('.loderholder').hide();
                    return false;
                }
            } else {
                $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html("{{Lang::get('inward.alertAtleastOneProd')}}").show();
                $('#grn_from').find('input[type="submit"]').prop('disabled', false);
                $('html, body').animate({scrollTop: '0px'}, 500);
                $('.loderholder').hide();
            }
        }
    });

    $("#packConfigForm").validate({
        rules: {
            pack_size: {
                required: false
            },
            rqty: {
                required: false
            },
        },
        submitHandler: function (form) {
            var form = $('#packConfigForm');
            var packpo_num_eaches = parseInt($('#packpo_numof_eaches').val());
            var packpo_qty = ($('#packpo_qty').val() * packpo_num_eaches);
            var rec = (parseInt($('#total_received').val())) ? parseInt($('#total_received').val()) : 0;
            var free = (parseInt($('#total_free').val())) ? parseInt($('#total_free').val()) : 0;
            var excess = (parseInt($('#total_excess').val())) ? parseInt($('#total_excess').val()) : 0;
            var damage = (parseInt($('#total_damage').val())) ? parseInt($('#total_damage').val()) : 0;
            var missed = (parseInt($('#total_missed').val())) ? parseInt($('#total_missed').val()) : 0;
            var quaran = (parseInt($('#total_quarantine').val())) ? parseInt($('#total_quarantine').val()) : 0;
            var total_received = rec + free + excess + damage + missed + quaran;
            if ( $("input[name='packsize_id[]']").length ) {
                if ( total_received > 0 ) {
                    if ( (total_received > packpo_qty) ) {
                        if ( !confirm("{{Lang::get('inward.confirmMsg')}}") ) {
                            return false;
                        }
                    }
                    checkReceivedQty();
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                        url: '/grn/createPackInputText',
                        type: 'POST',
                        data: form.serialize(),
                        dataType: 'JSON',
                        success: function (data) {
                            var product_id = data.received_data.product_id;
                            var received = data.received_data.received;
                            var free_qty = data.received_data.free;
                            var total_discount = data.received_data.total_discount;
                            var discount_type = data.received_data.discount_type;
                            var discountPercentVal = total_discount;
                            var tax_per = $('#taxper' + product_id).val();
                            var basePrice = $('#baseprice' + product_id).val();
                            var totQty = (received - free_qty);
                            var subTotal = totQty * basePrice;
                            var taxVal = ((basePrice * tax_per) / 100) * totQty;
                            var total = subTotal + taxVal - total_discount;

                            $('#taxtext' + product_id).text($.number(taxVal, 5));
                            $('#taxval' + product_id).val(taxVal);
                            $('#subTotal' + product_id).text($.number(subTotal, 5));
                            $('#totalval' + product_id).text($.number(total, 5));
                            $('#grntotalval' + product_id).val(total);

                            $('#received' + product_id).text(data.received_data.received);
                            $('#free' + product_id).text(data.received_data.free);
                            $('#damaged' + product_id).text(data.received_data.damage);
                            $('#missed' + product_id).text(data.received_data.missed);
                            $('#excess' + product_id).text(data.received_data.excess);
                            $('#quarantine' + product_id).text(data.received_data.quarantine);
                            $('#discount' + product_id).text($.number(total_discount, 5));
                            $('#discounttype' + product_id).val(discount_type);
                            $('#discountpercent' + product_id).val(discountPercentVal);
                            $('#discountval' + product_id).val(total_discount);
                            $('#grn_remarks' + product_id).text(data.received_data.pr_remarks);
                            $('.productPackdata' + product_id).empty();
                            $('.productPackdata' + product_id).append(data.inputList);
                            calcGrandTot();
                            $('.close').click();
                        },
                        error: function (response) {

                        }
                    });
                } else {
                    $('#error-msg1').html("{{Lang::get('inward.alertRecieveQty')}}").show();
                    /*window.setTimeout(function () {
                     $('#error-msg1').hide()
                     }, 2000);*/
                    return false;
                }
            } else {
                $('#error-msg1').html("{{Lang::get('inward.alertRecieveQtyEmpty')}}").show();
                /* window.setTimeout(function () {
                 $('#error-msg1').hide()
                 }, 2000);*/
                return false;
            }
        }
    });
    $(document).on('click', '.grn-del-doc', function () {
        var docId = $(this).attr("id");
        if ( confirm("{{Lang::get('inward.alterDelete')}}") ) {
            deleteDoc(docId);
            $(this).closest('tr').remove();
        }
    });

    $('#save_continue').click(function (event) {
        $('#frmSelectGrn').formValidation('revalidateField', 'po_id');
        $('#frmSelectGrn').formValidation('revalidateField', 'grn_supplier');
        $('#frmSelectGrn').formValidation('revalidateField', 'warehouse');

        var isDocRequired = parseInt($('[name="is_document_required"]').val());
        if(isDocRequired)
        {
            if ( checkDocAdded() ) {
//                $('li.createtab').removeClass('disabled');
//                $('[href="#create-grn"]').click();
                $('#ajaxResponseDoc').hide();
                back('create_grn');
            } else {

                $('#ajaxResponseDoc').show();

                $('#ajaxResponseDoc').addClass('error').html("{{Lang::get('inward.alertUploadDoc')}}");
                
                    if(docarray.length>0 && $.inArray("95006",docarray) == -1){
                       
                        $('#ajaxResponseDoc').addClass('error').html("{{Lang::get('inward.alertAckDoc')}}");
                    }
            }
        }else{
            $('#ajaxResponseDoc').hide();
            back('create_grn');
        }
    });
//    $('#continue_button').click(function (event) {
//        $('.nav-tabs li').removeClass('active');
//        $('.tab-content div.tab-pane').removeClass('active');
//        $('.upload_doc').addClass('active');
//        $('#upload-doc').addClass('active');     
//    });
    $('.ch_tabs').click(function (event) {
        if ( $(this).hasClass('disabled') ) {
            return false;
        }
    });    
});
function back(referenceName)
{
    if('select_grn' == referenceName)
    {
        $('#continue_button').removeClass('disabled');
        $('#continue_button').prop('disabled', false);
    }
    $('.nav-tabs li').removeClass('active');
    $('.'+referenceName).addClass('active');
    $('div.tab-pane').removeClass('active');
    $('#'+referenceName).addClass('active');
}

function autoAddAllProducts(){
    
    $(".addpackbtn").each(function() {
        $(this).click();
    });
    $("#addallbutton").hide();

}

function removeAddAllProducts(){
    
    $(".delete_product_pack").each(function() {
        $(this).click();
    });
    $("#addallbutton").show();
}
$("#okayPricingMismatch").click(function(){
    $('#grn_from').find('input[type="submit"]').prop('disabled', false);
})
function loadrefno(){
    
        var documentType = $("#documentType").val();
        if(documentType == '95006'){
            $('#frmUpload').bootstrapValidator('enableFieldValidators', 'ref_no', false);
        }
        else{
            $('#frmUpload').bootstrapValidator('enableFieldValidators', 'ref_no', true);
        }
}

function displayTime(counter = 60){
    var interval = setInterval(function() {
        counter--;
        // Display 'counter' wherever you want to display it.
        if (counter <= 0) {
            clearInterval(interval);
            $('#timer').hide();
            $('#final_submit').attr("disabled",false);
            return;
        }else{
            $('#timer').show();
            $('#final_submit').attr("disabled",true);
            $('#timer').text("Please wait for "+counter+" seconds and verify grn might created.");
        }
    }, 1000);
}

</script>
<style type="text/css">.loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%;    }
    .error{color: red;}
</style>

<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>

@stop