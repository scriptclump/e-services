@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/po/index">Purchase Order</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Create PO</li>
        </ul>
    </div>
</div>

<form id="po_form" method="POST" action="/po/savepo">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="" id="sno_increment" value="1">
<input type="hidden" name="_method" value="POST">
               
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">
                    <div class="caption" id="po_heading">Direct PO</div>
                    <div class="tools">&nbsp;</div>
                </div>
                <div class="portlet-body">
                    <div id="ajaxResponse" style="display:none;" class="alert alert-danger"></div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Date</label>
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" class="form-control" name="po_date" id="po_date" value="{{date('m/d/Y')}}" placeholder="PO Date">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="row">                        
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Validity&nbsp;(Days)</label>
                                        <input class="form-control" min="0" type="number" name="validity" id="validity" value="7">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Delivery Date</label>
                                        <div class="input-icon right">
                                            <input type="text" readonly="" class="form-control" name="delivery_before" id="delivery_before" value="{{date('m/d/Y',strtotime(date('d-m-Y').'+ 7 days'))}}" placeholder="Delivery Date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Copy From Indent</label>
                                <select class="form-control select2me" id="indent_id" name="indent_id">
                                    @if($indentId == "")
                                    <option value="Manual Indent">Standard PO</option>
                                    @foreach($indentsList as $indent_code=>$indent_id)  
                                    <option value="{{ $indent_id }}">{{ $indent_code }}</option>
                                    @endforeach
                                    @else
                                    @foreach($indentsList as $indent_code=>$indent_id)  
                                    @if($indent_id == $indentId)
                                    <option value="{{ $indent_id }}">{{ $indent_code }}</option>
                                    @endif
                                    @endforeach

                                    @endif

                                </select>
                            </div>
                        </div>
                       <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Supplier</label>
                                <select class="form-control select2me" data-live-search="true" id="supplier_list" name="supplier_list" onchange="getInvoiceDate()">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Delivery Location</label>
                                <select class="form-control select2me" id="warehouse_list" name="warehouse_list">
                                    <option value="">Select Delivery Location</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">PO Type</label>
                                <select class="form-control select2me" id="po_type" name="po_type">
                                    <option value="2">Value Based</option>
                                    <option value="1">Qty Based</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Payment Mode</label>
                                <select class="form-control select2me" data-live-search="true" id="payment_mode" name="payment_mode">
                                    <option value="1">Post Paid</option>
                                    <option value="2">Pre Paid</option>
                                </select>
                            </div>
                        </div>
                         <?php /*
                        @if(isset($ledgerAccounts) && count($ledgerAccounts)>0)
                        @if(isset($featureAccess['updatePaymentFeature']) && $featureAccess['updatePaymentFeature'])
                        <div id="paymentdiv" style="display:none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Paid Through</label>
                                <select class="form-control select2me" data-live-search="true" id="paid_through" name="paid_through">
                                    <option value="">Select Account</option>
                                    @foreach($ledgerAccounts as $account)                                
                                    <option value="{{ $account->tlm_name.'==='.$account->tlm_group }}">{{ $account->tlm_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Payment&nbsp;Type</label>
                                <select class="form-control select2me" id="payment_type" name="payment_type">
                                    <option value="">Select Type</option>
                                    @foreach($paymentType as $key=>$payment)                                
                                    <option value="{{ $key }}">{{ $payment }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Reference No</label>
                                <input type="text" class="form-control" id="payment_ref" name="payment_ref"/>
                            </div>
                        </div>
                        </div>
                        @endif
                        @endif
                         */ ?>
                         
                        <div id="paymentduediv" style="display:block;">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Payment Due Date</label>
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" class="form-control" name="payment_due_date" id="payment_due_date" value="{{date('m/d/Y')}}" placeholder="Payment Due Date">
                                    </div>
                                </div>
                        </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Supplying DC</label>
                                <select class="form-control select2me" id="dc_warehouse_id" name="dc_warehouse_id">
                                    <option value="">Select Supply Location</option>
                                </select>
                                <b>Margin:</b><span id="margin" style="font-weight:bold;"></span>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Stock Transfer From</label>
                                <select class="form-control select2me" id="st_warehouse_id" name="st_warehouse_id">
                                    <option value="">Select Stock Transfer Location</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Logistics Cost</label>
                                <input type="number" min="0" value="0" class="form-control" name="logistics_cost" id="logistics_cost"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Proforma Invoice(Quote)</label>
                                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                    <div>
                                        <span class="btn default btn-file btn green-meadow">
                                            <span class="fileinput-new">Choose File</span>
                                            <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                                            <input class="form-control" type="file" id="proforma" name="proforma" placeholder="Proof of Document">
                                        </span>
                                        <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                        <div class="thumbnail">
                                            <div id="doc_text">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <div class="form-group">
                                <button type="button" id="addskupopupbtn" href="#basicvalCodeModal3" disabled="" data-toggle="modal" class="btn green-meadow">Add SKU </button>
                            </div>
                        </div>
                        </div>
                    <br/>
                    <span style="float:right;font-size: 11px; font-weight: bold;">* All Amounts in (₹) </span>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="parent">   
                                <table class="table table-striped table-bordered table-advance table-hover fixTable" id="product_list" style="white-space:nowrap; width:100%;">
                                    <thead>
                                        <tr>
                                            <th>SNo</th>
                                            <th>SKU</th>
                                            <th>Product Name</th>
                                            <th>Qty</th>
                                            <th>Free Qty</th>
                                            <th>SOH</th>
                                            <th>Current SOH</th>
                                            <th>MRP(Rs.)</th>
                                            <th title="Previous {{Lang::get('headings.LP')}} across all suppliers & dc">Prev.{{Lang::get('headings.LP')}}</th>
                                            <th>{{Lang::get('headings.LP')}}(Rs.)</th>
                                            <th class="potypeshow">Base&nbsp;Rate(Rs.)</th>
                                            <th class="potypeshow">Sub&nbsp;Total(Rs.)</th>
                                            <th class="potypeshow">Tax %</th>
                                            <th class="potypeshow">Tax Amt</th>             
                                            <th class="potypeshow">Apply Disc.</th>             
                                            <th class="potypeshow">Discount</th>             
                                            <th class="potypeshow">Total(Rs.)</th>
                                            <th class="">Action</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>                                                                        
                                    </tbody>
                                </table>
                                </div>
                        </div>                        
                    </div>
                    <br/>
                     <div class="row">
                        <div class="col-md-2">                       
                            <div class="form-group">
                                <label class="control-label"><strong>Apply discount on bill</strong></label>
                                <input class="apply_discount" name="apply_bill_discount" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                            </div>
                            </div>
                         <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><strong>Discount</strong></label>
                                <div style="float:left"><input class="form-control" min="0" id="discount" style="width:100px;" name="bill_discount" type="number" value="0"></div>
                                <div style="float:left"><input class="bill_discount_type" name="bill_discount_type" type="checkbox" value="1"  style="margin:7px 6px 0px 10px;"></div>
                                <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>
                            </div>
                        </div> 
                        <div class="col-md-2">                       
                            <div class="form-group">
                                <label class="control-label"><strong>Discount Before Tax</strong></label>
                                <input class="apply_discount" name="discount_before_tax" id="discount_before_tax" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                            </div>
                            
                        </div>
                        <div class="col-md-2">                       
                            <div class="form-group">
                                <label class="control-label"><strong>Stock Transfer</strong></label>
                                <input class="control-label" name="stock_transfer" id="stock_transfer" type="checkbox" value="1" style="margin:7px 10px 0px 10px;">
                            </div>
                        </div>  
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><strong>Total Price :</strong> <span id="total_sub_total"></span></label>
                                
                            </div>    
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><strong>Total Tax :</strong> <span id="tax_total"></span></label>
                                
                            </div>    
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><strong>Grand Total :</strong> <span id="grand_total"></span></label>
                               
                            </div>    
                        </div>                                       
                    </div>
                     <div class="row">
                        <div class="col-md-4">                       
                            <div class="form-group">
                                <label class="control-label"><strong>Remarks</strong></label>
                                @if(isset($reasonsArr) && is_array($reasonsArr) && count($reasonsArr)>0)
                                <select name="po_reason" id="po_reason" class="form-control">
                                <option value="">Select</option>
                                @foreach($reasonsArr as $reason)
                                <option value="{{$reason->reason_id}}">{{$reason->name}}</option>
                                @endforeach
                                </select><br>
                                @endif
                                <textarea class="form-control" name="po_remarks" cols="60" id="po_remarks" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><strong>Approval Comments</strong></label>
                                <textarea class="form-control" name="approval_comments" cols="60" id="approval_comments" rows="2"></textarea>
                            </div>
                        </div>                        
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <hr />
                        <div class="col-md-12 text-center"> 
                            <button type="submit" class="btn green-meadow" name="Save" id="save">Save</button>
                            <a class="btn green-meadow" href="/po/index">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</form>
<div class="modal modal-scroll fade in" id="basicvalCodeModal3" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Add SKU</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-12">
                        <div style="display:none;" id="error-msg" class="alert alert-danger"></div>                        
                        <div class="form-group">
                            <label class="control-label"><strong>SKU </strong><span class="required">*</span></label>
                            <input type="text" id="search_sku" class="form-control" placeholder="SKU,Product Name,UPC" />
                            <input type="hidden" id="addproduct_id" class="form-control" placeholder="SKU,Product Name,UPC" />
                        </div>
                    </div> 
                </div>
                <div class="row">
                    <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><strong>Article Number </strong><span class="required">*</span></label>
                        <span id="prod_sku"></span>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><strong>Brand </strong><span class="required">*</span></label>
                        <span id="prod_brand"></span>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><strong>MRP </strong><span class="required">*</span></label>
                        <span id="prod_mrp"></span>
                    </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn green-meadow" id="addSkubtn">Add</button>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="row">
    <div class="col-md-12 text-right">
        &nbsp;
    </div>
</div>
@include('PurchaseOrder::Form.poErrorModelPopup')

@stop
@section('style')
<style type="text/css"> 
table > thead > tr > th { background:#efefef;}

.parent {height: auto;}
.fixTable {width: 1800px !important;}   
    .closedownload{ border:1px solid #ddd; width:25px; height:25px; padding:5px; margin:5px 0px;}   
    #doc_text{ float:left;width:300px; display:flex; }
    .thumbnail {
        border: none !important;
    }
    .downloadclose{
        position: relative;
        left: 34px;
        top: 9px; font-size:12px !important;color:#F3565D;
        text-decoration: none;
        cursor: pointer;
    }
 .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%; }
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
        position:fixed !important;
    }


  .ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
    }

label {
    padding-bottom: 5px;
}
.has-feedback .form-control {
   padding-right: 10px;
}
.error{color: red;}
.newproduct{color: blue;font-weight: bold;}


</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/clockface/css/clockface.css" rel="stylesheet') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('script')
<script src="{{ URL::asset('assets/admin/pages/scripts/tableHeadFixer.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-numberformat/jquery.number.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function getInvoiceDate(){
        var supplierId = document.getElementById("supplier_list").value;

        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/grn/getInvoiceDate',
            type: 'POST',
            data: {'supplierId': supplierId},
            beforeSend: function() {
                document.getElementById('payment_due_date').value = 'Loading...';
            },
            success: function (data) {
                document.getElementById('payment_due_date').value = data;
            },
            error: function (response) {
                alert('Credit Period is not yet setup for this supplier !!');
            }
        });
    }
    
    $('#po_date').datepicker({
        maxDate: 0,
        numberOfMonths: 1,
        onSelect: function(dateText) {
            var validity = parseInt($('#validity').val());
            addDays(dateText,validity);
       }
    });
    $('#payment_due_date').datepicker({
        minDate: 0,
        numberOfMonths: 1,
        onSelect: function(dateText) {
       }
    });

    $('#po_reason').change(function(){
        var reasonId = parseInt($(this).val());

         $.ajax({
             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/po/getreason',
             type: 'POST',
             data: {reasonId:reasonId},
             dataType:'JSON',
             success: function (data) {                
                $('#po_remarks').val(data.remarks);
             },
             error: function (response) {

             }
         });
    });
    $('#payment_mode').change(function(){
        var payment_mode = parseInt($(this).val());
        $('#paid_through').removeAttr('required');
        $('#payment_type').removeAttr('required');
        //$('#paymentdiv').hide();
        $('#paymentduediv').show();
        if(payment_mode==2){
            //$('#paid_through').attr('required','required');
            //$('#payment_type').attr('required','required');
            //$('#paymentdiv').show();
            $('#paymentduediv').hide();
        }
    });
    $('#supplier_list').change(function(){
        var supplier_id = parseInt($(this).val());
        $("#warehouse_list").empty();
        $("#dc_warehouse_id").empty();
        $("#st_warehouse_id").empty();
         $.ajax({
             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/po/getWarehouseBySupplierId',
             type: 'POST',
             data: {supplier_id:supplier_id},
             dataType:'JSON',
             success: function (data) {                
                $("#warehouse_list").append(data.warehouses);
                $("#warehouse_list").select2();
                $("#dc_warehouse_id").append(data.warehouses);
                $("#dc_warehouse_id").select2();
                $("#st_warehouse_id").append(data.warehouses);
                $("#st_warehouse_id").select2();
             },
             error: function (response) {

             }
         });
    });
    $('#dc_warehouse_id').change(function(){
        var margin = $('#dc_warehouse_id option:selected').attr('data-margin');
        $("#margin").text(margin);
    });

    $('#validity').change(function(){
        var validity = parseInt($(this).val());
        var dateText = $('#po_date').val();
        addDays(dateText,validity);
    });
    /* change delivery date according to validity days */
    function addDays(dateText,numdays){
        var select_date = new Date(dateText);
        select_date.setDate(select_date.getDate() + numdays);
        var setdate = new Date(select_date);

        var monthText = zeroPad((setdate.getMonth()+1),2);
        var dateText = zeroPad(setdate.getDate(),2);
        var yearText = setdate.getFullYear();
        $('#delivery_before').val(monthText+'/'+dateText+'/'+yearText);
    }
    $('#addproduct_id').val('');
    $('#search_sku').val('');
    function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}
var skus='';
    $(document).ready(function(){
        $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
        getSuppliers();
        //$("#po_remarks").Editor();
        var indentId = "<?php echo $indentId ?>";
        if(indentId!=''){
            $("#indent_id").select2().select2("val", indentId);
        }
        $("#indent_id").change(function() {
           if($( this ).val() == 'Manual Indent') {
            $('#po_heading').html('Direct PO');
            $('#addskupopupbtn').attr('disabled',true);
           }
           else {
            $('#po_heading').html('PO Against Indent');
            $('#addskupopupbtn').attr('disabled',false);
        }
        });
        $("#proforma").change(function() {
            var file_data = $('#proforma').prop('files')[0];
            var form_data = new FormData();
            form_data.append('upload_file', file_data);
            $('.loderholder').show();
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/po/uploadpodocs',
                dataType: 'html',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data, 
                mimeType: "multipart/form-data",
                type: 'POST',
                success: function(data){
                    data = jQuery.parseJSON(data);
                    if(data.status==200){
                        $("#doc_text").append(data.docText);
                        $('.fileinput-filename').text("");
                    }else{
                        alert(data.message);
                    }
                    $('.loderholder').hide();
                },
                error: function (response) {
                    alert("Unable to save file");
                    $('.loderholder').hide();
                }
            });
        });
        $(document).on('click','.downloadclose',function(){
            var doc_id = $(this).attr('data-doc_id');
            var reference = $(this);
            if(confirm('Do you want to remove?')){
                $.ajax({
                    headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: '/po/deleteDoc/'+doc_id,
                    type: 'POST',
                    data: {},
                    dataType:'JSON',
                    success: function (data) {
                        if (data.status == 200) {
                            reference.closest("div").remove();
                        }
                    },
                    error: function (response) {
                    }
                });
            }else{
                return false;
            }
        });
        $.validator.addMethod("DateFormat", function(value,element) {
                return value.match(/^(0[1-9]|1[012])[- //.](0[1-9]|[12][0-9]|3[01])[- //.](19|20)\d\d$/);
            },
            "Please enter a date in the format mm/dd/yyyy"
        );
        $.validator.addMethod("maxDate", function(value,element) {
                var now = new Date();
                var myDate = new Date(value);
                return this.optional(element) || myDate <= now;
            },
            "Please enter less than current date"
        );
        $('#po_form').validate({
            rules: {
                po_date: {
                    required: true,
                    DateFormat:true,
                    maxDate:true
                },
                validity: {
                    required: true
                },
                supplier_list: {
                    required: true
                },
                warehouse_list: {
                    required: true
                },                
            },
            submitHandler: function (form) {
                var form = $('#po_form');
                var supp_type = $("#supplier_list").select2().find(":selected").attr("le_type_id");
                console.log(supp_type);
                var war_type = $("#warehouse_list").select2().find(":selected").attr("le_type");
                console.log(war_type);
                var dc_war_type_id = $("#dc_warehouse_id").select2().find(":selected").attr("le_type");
                console.log(dc_war_type_id);
                if( (supp_type == 1002 && war_type == 1014) || (war_type == 1001 && dc_war_type_id == 1014)) {
                    if(!confirm('Do you want to create a PO directly to FC? If yes, please check payment received or not?')){
                        return false;
                    }
                }
                if(checkProductQty()){
                    if(checkProductUOM()){
                        if(checkProductMRP()){
                            if(confirm('Do you want to save PO?')){
                                $('.loderholder').show();
                                var stock_transfer_dc = $("#st_warehouse_id").val();
                                if($("#stock_transfer").is(':checked') && stock_transfer_dc == ""){
                                    alert("Please select Stock Transfer Location!");
                                    $('.loderholder').hide();
                                    return;
                                }
                                $.post(form.attr('action'), form.serialize(), function (data) {                       
                                    data = jQuery.parseJSON(data);
                                     if (data.status == 200) {                                         
                                         $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                         $('html, body').animate({scrollTop: '0px'}, 500);
                                         $('.loderholder').hide();
                                         window.setTimeout(function(){window.location.href = '/po/details/' + data.po_id},1000);
                                     } else if (data.message == 'inv_error_found'){
                                        $("#prcingMismatchModal").modal('toggle');
                                        $('.loderholder').hide();
                                        $('#priceMismatchData').html(data.data);
                                        $('#reason_po_so').html(data.reason);
                                        $('#po_so_adjust_message').html(data.adjust_message);
                                    }  else {
                                         $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                                         $('.loderholder').hide();
                                         $('html, body').animate({scrollTop: '0px'}, 500);
                                     }
                                 });
                            }
                        }else{
                                $('#ajaxResponse').html("{{Lang::get('po.alertPOMRP')}}"+" for "+skus).show();
                                $('.loderholder').hide();
                                $('html, body').animate({scrollTop: '0px'}, 500);
                            }
                    }else{
                        $('#ajaxResponse').html("{{Lang::get('po.alertUOM')}}").show();
                        window.setTimeout(function(){$('#error-msg').hide()},2000);
                        $('.loderholder').hide();
                        $('html, body').animate({scrollTop: '0px'}, 500);
                    }
                }else{
                    $('#ajaxResponse').html("{{Lang::get('po.alertPOQty')}}").show();
                    window.setTimeout(function(){$('#error-msg').hide()},2000);
                    $('.loderholder').hide();
                    $('html, body').animate({scrollTop: '0px'}, 500);
                }
            }
        });
        autosuggest();
        function autosuggest(){
            $( "#search_sku" ).autocomplete({
                 source: '/po/getSkus?supplier_id='+$('#supplier_list').val()+'&warehouse_id='+$('#warehouse_list').val(),
                 minLength: 2,
                 params: { entity_type:$('#supplier_list').val() },
                 select: function( event, ui ) {
                      if(ui.item.label=='No Result Found'){
                         event.preventDefault();
                      }
                      $('#addproduct_id').val(ui.item.product_id);
                      $('#prod_brand').text(ui.item.brand);
                      $('#prod_sku').text(ui.item.sku);
                      $('#prod_mrp').text(ui.item.mrp);
                 }
             });
        }
        $("#supplier_list,#warehouse_list").change(function() {
            autosuggest();
            console.log($('#supplier_list').val()+'-=-===-===-='+$('#warehouse_list').val());
            //$('#product_list').find('tbody').empty();
            if($('#supplier_list').val() && $('#warehouse_list').val()){
                $('#addskupopupbtn').attr('disabled',false);
            }else{
                $('#addskupopupbtn').attr('disabled',true);
            }
        });
        $('#addSkubtn').click(function(){
           var product_id = $('#addproduct_id').val();
           if(product_id!=''){

               if(checkProductAdded(product_id)){
                
                    var sno_increment = $('#sno_increment').val();
                    var supplier_id = $('#supplier_list').val();
                    var warehouse_id = $('#warehouse_list').val();
                    $('#addSkubtn').attr('disabled',true);
                    var products = productsAdded();
                    $.ajax({
                             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                             url: '/po/getProductInfo',
                             type: 'POST',
                             data: {products:products,product_id:product_id,sno_increment:sno_increment,supplier_id:supplier_id,warehouse_id:warehouse_id},
                             dataType:'JSON',
                             success: function (data) {
                                $('#addSkubtn').attr('disabled',false);
                                if(data.status==200){
                                    $('#product_list').append(data.productList);
                                    checkPOType();
                                    $('#sno_increment').val(data.sno);
                                    $('.close').click();
                                    $('#search_sku').val('');
                                    $('#addproduct_id').val('');
                                    $('#prod_brand').text('');
                                    $('#prod_sku').text('');
                                    $('#prod_mrp').text('');
									$(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
                                    calTotalvalues();
                                    snoCount();
                                }else{
                                    $('#error-msg').html(data.message).show();
                                    window.setTimeout(function(){$('#error-msg').hide()},3000);
                                }
                             },
                             error: function (response) {

                             }
                         });
                 }else{
                     $('#error-msg').html('Product is already added.').show();
                     window.setTimeout(function(){$('#error-msg').hide()},2000);
                 }
           }else{
               $('#error-msg').html('Please Add Product').show();
               window.setTimeout(function(){$('#error-msg').hide()},2000);
           }
        });
    });

    $('[name="indent_id"]').change(function () {
        getSuppliers();
    });
    $(document).on('click','.delete_product',function(){
        if(confirm('Do you want to remove item?')){
            var product_id = $(this).attr('data-id');
            $(this).closest('tr').remove();
            deleteChildProduct(product_id);
            snoCount();
            return false;
        }
    });
    function deleteChildProduct(product_id) {
        $("input[name='po_product_id[]']").each(function () {
            var parent_id = $(this).closest('tr').find('input[name="parent_id[]"]').val();
            if(product_id==parent_id) {
                $(this).closest('tr').remove();
            }
        });        
    }
    $(document).on('change','#po_type',function(){      
       checkPOType();
    });
    function checkPOType(){
        var po_type = $('#po_type').val();
        if(po_type ==1){
            $('.potypeshow').hide();
            $(".zui-sticky-col").each(function () {
                $(this).removeClass('zui-sticky-col');
                $(this).addClass('zui-sticky-col1');
            });
            $(".zui-scroller").each(function () {
                $(this).removeClass('zui-scroller');
                $(this).addClass('zui-scroller1');
            });
        }else{
            $('.potypeshow').show();
            $(".zui-sticky-col1").each(function () {
                $(this).removeClass('zui-sticky-col1');
                $(this).addClass('zui-sticky-col');
            });
            $(".zui-scroller1").each(function () {
                $(this).removeClass('zui-scroller1');
                $(this).addClass('zui-scroller');
            });
        }
    }
    $(document).on('change','[name="qty[]"]',function(){
        var qty = parseInt($(this).val());
        if(qty>0){
            var pobaseprice = $(this).closest('tr').find('.pobaseprice').val();
            var totprice = parseFloat(qty*pobaseprice).toFixed(2);
            $(this).closest('tr').find('.totpriceval').text(totprice);
            return false;
        }
    });
    var basePrice = 0;
    var totalPrice = 0;
    var qty = 0;
    var noofeach = 0;
    var freeqty =0;
    var freenoofeach = 0;
    var taxper = 0;
    var taxAmt = 0;
    var totalAmt = 0;
    var pre_post_type = 0;    
    var totfreeqty = 0;
    var totpoqty = 0;
    var qtycalculate = 0;
    var eachprice =0; 
    var current_elp=0;
    var price =0;
    var apply_discount =0;
    var item_discount_type =0;
    var item_discount =0;
    var apply_bill_discount =0;
    var bill_discount_type =0;
    var discount_before_tax =0;
    var bill_discount =0;
    var discAmt = 0;
    $(document).on('change','.pretax,.pobaseprice,input[name="qty[]"],input[name="freeqty[]"],[name="freepacksize[]"],.item_discount,.item_discount_type,.apply_discount_item',function(){
        var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
        console.log(product_id);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="packsize[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
        var noofeach = parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        var unit_price = parseFloat($('#unit_price'+product_id).val());
        console.log('unit='+unit_price+'==eachesss='+noofeach);
        $('#baseprice'+product_id).val(unit_price*noofeach);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="po_totprice[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="po_product_id[]"]').val();
        $('.pretax'+product_id).prop('checked',true);
        readValues(product_id);       
        calcPrice(product_id);
    });
    $(document).on('change','input[name="apply_bill_discount"],input[name="bill_discount"],[name="bill_discount_type"],[name="discount_before_tax"]',function(){
        $("input[name='po_totprice[]']").each(function() {
            $(this).trigger('change');
        });
    });
    function readValues(product_id){
        basePrice = parseFloat($('#baseprice'+product_id).val());
        totalPrice = $('#totprice'+product_id).val();
        qty = $('#qty'+product_id).val();
        noofeach = parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        if(!noofeach && isNaN(noofeach)){
            noofeach = 0;
        }
        freeqty = $('#freeqty'+product_id).val();
        freenoofeach = parseInt($('.freepacksize'+product_id).find(':selected').attr('data-noofeach'));
        if(!freenoofeach && isNaN(freenoofeach)){
            freenoofeach = 0;
        }        
        taxper = parseFloat($('#taxper'+product_id).val());
        console.log('taxper==='+taxper);
        pre_post_type = $('input[name="pretax['+product_id+']"]:checked').val();
        totfreeqty = parseInt(freeqty*freenoofeach);
        totpoqty = parseInt(qty * noofeach);
        qtycalculate = totpoqty - totfreeqty;
        eachprice =parseFloat(basePrice /noofeach); 
        price =parseFloat(eachprice * qtycalculate);
        apply_discount = $('input[name="apply_discount['+product_id+']"]:checked').val();
        item_discount_type = $('input[name="item_discount_type['+product_id+']"]:checked').val();
        item_discount = $('input[name="item_discount['+product_id+']"]').val();
        apply_bill_discount = $('input[name="apply_bill_discount"]:checked').val();
        bill_discount_type = $('input[name="bill_discount_type"]:checked').val();
        discount_before_tax = $('input[name="discount_before_tax"]:checked').val();
        bill_discount = $('input[name="bill_discount"]').val();
        discAmt=0;
        
    }
    function getGrandTotal(){
        var grandTotal = 0;
        $("input[name='po_product_id[]']").each(function() {
            var product_id = $(this).val();
            grandTotal += (parseFloat($('#totprice'+product_id).val()) - parseFloat($('#item_discount_amt'+product_id).val()));
        });
        return grandTotal;
    }
    function calcTotal(product_id){
        console.log('totqty===='+totpoqty+'freeqty==='+freeqty+'freeeaches=='+freenoofeach);
        if(totpoqty>=totfreeqty){
            totalAmt =(eachprice*qtycalculate);
            console.log(product_id+'==price'+price+'==taxper'+taxper+'===Each=='+eachprice);
            pre_post_type = (pre_post_type) ? pre_post_type : 0;
            if(pre_post_type==0){
                taxAmt = parseFloat((totalAmt*taxper)/100);
                totalAmt = parseFloat(totalAmt+taxAmt);
                current_elp = eachprice+((eachprice*taxper)/100);
            }else{
                var price_excltax = parseFloat(totalAmt/(1+((taxper*1)/100)));
                taxAmt = parseFloat(totalAmt-price_excltax);
                totalAmt = parseFloat(totalAmt);
                current_elp = eachprice;
            }
            
            $('#unit_price'+product_id).val(eachprice);
            $('#totalPriceText'+product_id).text(price);
            $('#taxtext'+product_id).text($.number(taxAmt,5)); 
            console.log(taxAmt);           
            $('#taxval'+product_id).val(taxAmt);
            $('#totprice'+product_id).val(totalAmt);
            $('#totalval'+product_id).text($.number(totalAmt,5));
            // if(discount_before_tax!=1){
                if(apply_discount==1){
                    if(item_discount_type==1){
                        discAmt = (totalAmt*item_discount)/100;
                    }else{
                        discAmt = item_discount;
                    }
                }
                $('#item_discount_amt'+product_id).val(discAmt);
                var totalAfteritemDisc = parseFloat(totalAmt-discAmt);
                if(apply_bill_discount==1){
                    if(bill_discount_type==1){
                        discAmt = parseFloat(discAmt)+parseFloat((totalAfteritemDisc*bill_discount)/100);
                    }else{
                        var grandTotal= getGrandTotal();
                        var contribution = (totalAfteritemDisc/grandTotal);
                        discAmt = parseFloat(discAmt)+parseFloat(bill_discount*contribution);
                    }
                }
            // }
            var unit_disc = parseFloat(discAmt/totpoqty);
            current_elp = (current_elp-unit_disc);
            $('#curelptext'+product_id).text($.number(current_elp,5));
            $('#curelpval'+product_id).val(current_elp);
            var current_elp_dyn = parseFloat($('#curelpval'+product_id).val());
            console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
            calTotalvalues();
            if(current_elp_dyn.toFixed(5) > parseFloat($('#prev_elp_value'+product_id).val())){
                document.getElementById('curelptext'+product_id).style.color='red';
            }else{
                document.getElementById('curelptext'+product_id).style.color='';
            }
            return false;
        }else{
            alert('Free Qty should not be morethan po Qty');
            $('#freeqty'+product_id).val(0);
            readValues(product_id);
            calcTotal(product_id);
            return false;
        }
    }
    function calcPrice(product_id){
        var totalPrice = $('#totprice'+product_id).val();
        console.log(totalPrice);
        var price_excltax = parseFloat(totalPrice/(1+((taxper*1)/100)));
        taxAmt = parseFloat(totalPrice - price_excltax);
        qtycalculate = totpoqty - totfreeqty;
        eachprice =parseFloat((totalPrice / qtycalculate));
        var price =parseFloat(eachprice*noofeach);
               
        $('#unit_price'+product_id).val(eachprice);        
        $('#baseprice'+product_id).val(price);
        $('#totalPriceText'+product_id).text(price_excltax);
        $('#taxtext'+product_id).text($.number(taxAmt,5));
        $('#taxval'+product_id).val(taxAmt);
        // if(discount_before_tax!=1){
            if(apply_discount==1){
                if(item_discount_type==1){
                    discAmt = (totalPrice*item_discount)/100;
                }else{
                    discAmt = item_discount;
                }
            }
            $('#item_discount_amt'+product_id).val(discAmt);
            var totalAfteritemDisc = parseFloat(totalPrice-discAmt);
            if(apply_bill_discount==1){
                if(bill_discount_type==1){
                    discAmt = parseFloat(discAmt)+parseFloat((totalAfteritemDisc*bill_discount)/100);
                }else{
                    var grandTotal= getGrandTotal();
                    var contribution = (totalAfteritemDisc/grandTotal);
                    discAmt = parseFloat(discAmt)+parseFloat(bill_discount*contribution);
                }
            }
        // }
        var unit_disc = parseFloat(discAmt/totpoqty);
        current_elp = (eachprice-unit_disc);
        $('#curelptext'+product_id).text($.number(current_elp,5));
        $('#curelpval'+product_id).val(current_elp);
        var current_elp_dyn = parseFloat($('#curelpval'+product_id).val());
        console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
        if(current_elp_dyn.toFixed(5) > parseFloat($('#prev_elp_value'+product_id).val())){
                document.getElementById('curelptext'+product_id).style.color='red';
        }else{
                document.getElementById('curelptext'+product_id).style.color='';
            }
        calTotalvalues();
        return false;        
    }
     function getSuppliers() {
        $('#product_list').find('tbody').empty();
        $("#supplier_list").empty();
        $("#warehouse_list").empty();
        var url = '/po/getsuppliers';
        var indentId = $('#indent_id').val();
        var dataString = {'indent_id': indentId};
        $.get(url, dataString, function (response) {
            var data = $.parseJSON(response);
            $("#supplier_list").append(data.supplierList);
            $("#warehouse_list").append(data.warehouseList);
            $("#supplier_list").select2();
            $("#warehouse_list").select2();
            $("#product_list").append(data.productList);
            $('#sno_increment').val(data.sno);
            checkPOType();
            snoCount();
            $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
        });
    }


    function checkProductAdded(product_id) {
        var checked = true;
        $("input[name='po_product_id[]']").each(function () {
            var productid_exist=$(this).val();
            if (productid_exist == product_id) {
                checked = false;
                return;
            }
        });
        return checked;
    }
    function productsAdded() {
        var products = new Array();
        $("input[name='po_product_id[]']").each(function () {
            var product_id=$(this).val();
            products.push(product_id);
        });
        return products;
    }
    function checkProductQty() {
        var checked = true;
        $("input[name='qty[]']").each(function () {
            var productQty=$(this).val();
            if (productQty=='' || productQty<=0) {
                checked = false;
                return;
            }
        });
        return checked;
    }
    function checkProductUOM() {
        var checked = true;
        $("[name='packsize[]']").each(function () {            
            var packsize=$(this).val();
            if (packsize=='') {
                checked = false;
                return;
            }
        });
        return checked;
    }
    function checkProductMRP() {
        skus='';
        var checked = true;
        $(".unitPrice").each(function () {
            var product_id=$(this).attr('data-product_id');
            var unitprice=parseFloat($(this).val());
            var tax_per=parseFloat($('#taxper'+product_id).val());
            var pre_post_type = $('input[name="pretax['+product_id+']"]:checked').val();
            pre_post_type = (pre_post_type) ? pre_post_type : 0;            
            var elp = unitprice;
            if(pre_post_type==0){
                var elp = unitprice+(unitprice*tax_per/100);
            }
            var mrp=parseFloat($('#mrp'+product_id).val());
            var sku=$('#product_sku'+product_id).val();
            if(elp>mrp){
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
        return checked;
    }
    function calTotalvalues(){
        var taxtot = 0;
        $("[id^=taxval]").each(function () {
            taxtot = taxtot + Number($(this).val());            
        });
        $("#tax_total").html($.number(taxtot,2));    

        var tot = 0;

        $("[name='po_totprice[]']").each(function () {
            tot = tot + Number($(this).val());
        });
        $("#grand_total").html($.number(tot,2));

        var subtot = 0;
       
        $('[id^=totalPriceText]').each(function () {
            subtot = subtot + Number($(this).html());
        });
        $("#total_sub_total").html($.number(subtot,2));
    }
    function snoCount(){
        var sno=1;
       $('.snos').each(function () {
            $(this).html(sno);
            sno++;
        });
    }
</script>
@stop
@section('userscript')
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/clockface/js/clockface.js')}}"></script>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
