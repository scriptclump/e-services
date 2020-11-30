@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/pr/index">Purchases</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Create P.R</li>
        </ul>
    </div>
</div>
<form id="pr_form" method="POST" action="/pr/savepr">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="" id="sno_increment" value="1">
    <input type="hidden" name="_method" value="POST">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">
                    <div class="caption">Purchase Return</div>
                    <div class="tools">&nbsp;</div>
                </div>
                <div class="portlet-body">
                    <div id="ajaxResponse" style="display:none;" class="alert alert-danger"></div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Supplier</label>
                                <select class="form-control select2me" data-live-search="true" id="supplier_list" name="supplier_list">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Dispatch Location</label>
                                <select class="form-control select2me" id="warehouse_list" name="warehouse_list">
                                    <option value="">Select Dispatch Location</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Sales Return Inv No.</label>
                                <input type="text" value="" class="form-control valid" name="sale_return_inv_no" id="sale_return_inv_no" aria-invalid="false">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Purchase Return Ack</label>
                                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                    <div>
                                        <span class="btn default btn-file btn green-meadow">
                                            <span class="fileinput-new">Choose File</span>
                                            <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                                            <input class="form-control" type="file" id="returnack" name="returnack" placeholder="Proof of Document">
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
                        <div class="col-md-8 text-right">
                            <div class="form-group">
                                <button type="button" id="addskuprpupbtn" href="#basicvalCodeModal3" disabled="" data-toggle="modal" class="btn green-meadow">Add SKU </button>
                            </div>
                        </div>
                    </div>                    
                    <br />
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="parent">   
                                <table class="table table-striped table-bordered table-advance table-hover fixTable" id="product_list" style="white-space:nowrap; width:100%;">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Product Name</th>
                                            <th title="Return from SOH">SOH Qty</th>
                                            <th title="Return from Damaged Qty">DIT Qty</th>
                                            <th title="Return from Missing Qty">DND Qty</th>
                                            <?php /* <th>Free Qty</th> */ ?>
                                            <th>MRP</th>
                                            <th>{{Lang::get('headings.LP')}}</th>
                                            <th class="">Base&nbsp;Rate</th>
                                            <th class="">Sub&nbsp;Total</th>
                                            <th class="">Tax %</th>
                                            <th class="">Tax Amt</th>                                                         
                                           <?php /* <th class="">Discount</th>
                                            <th class="">Disc.Amount</th> */ ?>
                                            <th class="">Total</th>
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
                    <?php /* <div class="row">
                         <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><strong>Discount On Bill</strong></label>
                                <div style="float:left"><input class="form-control" min="0" id="discount" style="width:100px;" name="bill_discount" type="number" value="0"></div>
                                <div style="float:left"><input class="bill_discount_type" name="bill_discount_type" type="checkbox" value="1"  style="margin:7px 6px 0px 10px;"></div>
                                <div style="float:left"><span style="margin-top:8px; font-size:9px;"><strong>%</strong></span>  </div>
                            </div>
                        </div>                                                
                    </div> */ ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><strong>Remarks</strong></label>
                                <?php /*<select name="pr_reason" id="pr_reason" class="form-control">
                                    <option value="">Select</option>
                                    @if($reasonsArr)
                                    @foreach($reasonsArr as $reason)
                                    <option value="{{$reason->reason_id}}">{{$reason->name}}</option>
                                    @endforeach
                                    @endif
                                </select><br>*/ ?>
                                <textarea class="form-control" name="pr_remarks" cols="60" id="pr_remarks" rows="6"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <hr/>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn green-meadow" name="Save" id="save">Save</button>
                            <a class="btn green-meadow" href="/pr/index">Cancel</a>
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
                            <label class="control-label">SKU <span class="required">*</span></label>
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
<script src="{{ URL::asset('assets/global/plugins/jquery-numberformat/jquery.number.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$('#pr_reason').change(function () {
    var reasonId = parseInt($(this).val());
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: '/po/getreason',
        type: 'POST',
        data: {reasonId: reasonId},
        dataType: 'JSON',
        success: function (data) {
            $('#pr_remarks').val(data.remarks);
        },
        error: function (response) {

        }
    });
});
$('#addproduct_id').val('');
$('#search_sku').val('');
$(document).ready(function () {
    $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
    getSuppliers();
    $('#pr_form').validate({
            rules: {
                supplier_list: {
                    required: true
                },
                warehouse_list: {
                    required: true
                },
            },
            submitHandler: function (form) {
                var form = $('#pr_form');
                if(checkProductQty()){
                    if(checkProductInv()){
                        if(checkProductMRP()){
                            if(confirm('Do you want to save PR?')){
                                $('.loderholder').show(); 
                                $.post(form.attr('action'), form.serialize(), function (data) {
                                     data = jQuery.parseJSON(data);
                                     if (data.status == 200) {
                                         $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                         $('html, body').animate({scrollTop: '0px'}, 500);
                                         $('.loderholder').hide();
                                         window.setTimeout(function(){window.location.href = '/pr/details/' + data.pr_id},1000);
                                     } else {
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
                        $('#ajaxResponse').html("SOH/DIT/DND Qty should not be more than current value for "+skus).show();
                        window.setTimeout(function(){$('#error-msg').hide()},2000);
                        $('.loderholder').hide();
                        $('html, body').animate({scrollTop: '0px'}, 500);
                    } 
                }else{
                    $('#ajaxResponse').html("{{Lang::get('po.alertPOQty')}}"+" for "+skus).show();
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
        $('#product_list').find('tbody').empty();
        if($('#supplier_list').val() && $('#warehouse_list').val()){
            $('#addskuprpupbtn').attr('disabled',false);
        }else{
            $('#addskuprpupbtn').attr('disabled',true);
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
                             url: '/pr/getProductInfo',
                             type: 'POST',
                             data: {products:products,product_id:product_id,sno_increment:sno_increment,supplier_id:supplier_id,warehouse_id:warehouse_id},
                             dataType:'JSON',
                             success: function (data) {
                                $('#addSkubtn').attr('disabled',false);
                                if(data.status==200){
                                    $('#product_list').append(data.productList);
                                    $('#sno_increment').val(data.sno);
                                    $('.close').click();
                                    $('#search_sku').val('');
                                    $('#addproduct_id').val('');
                                    $('#prod_brand').text('');
                                    $('#prod_sku').text('');
                                    $('#prod_mrp').text('');
                                    $(".fixTable").tableHeadFixer({"head" : false, "right" : 2});
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

    $('#supplier_list').change(function(){
        var supplier_id = parseInt($(this).val());
        $("#warehouse_list").empty();
         $.ajax({
             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/pr/getWarehouseBySupplierId',
             type: 'POST',
             data: {supplier_id:supplier_id},
             dataType:'JSON',
             success: function (data) {
                $("#warehouse_list").append(data.warehouses);
                $("#warehouse_list").select2();
             },
             error: function (response) {

             }
         });
    });
function getSuppliers() {
    $('#product_list').find('tbody').empty();
    $("#supplier_list").empty();
    $("#warehouse_list").empty();
    var url = '/pr/getsuppliers'; 
    $.get(url, function (response) {
        var data = JSON.parse(response);
        $("#supplier_list").append(data.supplierList);
        $("#warehouse_list").append(data.warehouseList);
        $("#supplier_list").select2();
        $("#warehouse_list").select2();
        $("#product_list").append(data.productList);
        $('#sno_increment').val(data.sno);
    });
}

    var basePrice = 0;
    var totalPrice = 0;
    var qty = 0;
    var soh_qty = 0;
    var dit_qty = 0;
    var dnd_qty = 0;
    var noofeach = 0;
    var freeqty =0;
    var freenoofeach = 0;
    var taxper = 0;
    var taxAmt = 0;
    var totalAmt = 0;
    var pre_post_type = 0;    
    var totfreeqty = 0;
    var totprqty = 0;
    var qtycalculate = 0;
    var eachprice =0; 
    var current_elp=0;
    var price =0;
    var item_discount =0;
    var item_discount_type =0;
    var item_disc_tax_type =0;
    var item_discAmt = 0;
    var subTotal = 0;
    $(document).on('change','.pretax,.prbaseprice,input[name="soh_qty[]"],input[name="dit_qty[]"],input[name="dnd_qty[]"]',function(){ //,[name="item_discount[]"],[name="item_discount_type[]"],[name="item_disc_tax_type[]"],input[name="freeqty[]"],[name="freepacksize[]
        var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
        console.log(product_id);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="packsize[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
        var noofeach = parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        var unit_price = parseFloat($('#unit_price'+product_id).val());
        console.log('unit='+unit_price+'==eachesss='+noofeach);
        $('#baseprice'+product_id).val(unit_price*noofeach);
        readValues(product_id);
        calcTotal(product_id);
    });
    $(document).on('change','[name="pr_totprice[]"]',function(){
        var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
        $('.pretax'+product_id).prop('checked',true);
        readValues(product_id);       
        calcPrice(product_id);
    });
    function readValues(product_id){
        basePrice = parseFloat($('#baseprice'+product_id).val());
        totalPrice = $('#totprice'+product_id).val();
        //qty = $('#qty'+product_id).val();
        soh_qty = $('#soh_qty'+product_id).val();
        dit_qty = $('#dit_qty'+product_id).val();
        dnd_qty = $('#dnd_qty'+product_id).val();
        soh_qty = (soh_qty!='' && soh_qty>=0)?soh_qty:0;
        dit_qty = (dit_qty!='' && dit_qty>=0)?dit_qty:0;
        dnd_qty = (dnd_qty!='' && dnd_qty>=0)?dnd_qty:0;
        qty = parseInt(soh_qty)+parseInt(dit_qty)+parseInt(dnd_qty);
        noofeach = 1;//parseInt($('.packsize'+product_id).find(':selected').attr('data-noofeach'));
        if(!noofeach && isNaN(noofeach)){
            noofeach = 0;
        }
        freeqty = 0;//$('#freeqty'+product_id).val();
        freenoofeach = 0;//parseInt($('.freepacksize'+product_id).find(':selected').attr('data-noofeach'));
        if(!freenoofeach && isNaN(freenoofeach)){
            freenoofeach = 0;
        }        
        taxper = parseFloat($('#taxper'+product_id).val());
        console.log('taxper==='+taxper);
        pre_post_type = $('input[name="pretax['+product_id+']"]:checked').val();
        totfreeqty = parseInt(freeqty*freenoofeach);
        totprqty = parseInt(qty * noofeach);
        qtycalculate = totprqty - totfreeqty;
        eachprice =parseFloat(basePrice /noofeach); 
        price =parseFloat(eachprice * qtycalculate);
        
       /* item_discount =$('#item_discount'+product_id).val();
        item_discount_type =$('#item_discount_type'+product_id+':checked').val();
        item_discount_type = (item_discount_type) ? item_discount_type : 0;
        item_disc_tax_type =$('#item_disc_tax_type'+product_id+':checked').val();
        item_disc_tax_type = (item_disc_tax_type) ? item_disc_tax_type : 0;
        console.log('disc=='+item_discount+'=disctype==='+item_discount_type+'taxtype=='+item_disc_tax_type); */
    }
    function calcTotal(product_id){
        console.log('totqty===='+totprqty+'freeqty==='+freeqty+'freeeaches=='+freenoofeach);
        if(totprqty>=totfreeqty){
            totalAmt =(eachprice*qtycalculate);
            console.log(product_id+'==price'+price+'==taxper'+taxper+'===Each=='+eachprice);
            pre_post_type = (pre_post_type) ? pre_post_type : 0;            
            if(pre_post_type==0){
                taxAmt = parseFloat((totalAmt*taxper)/100);
                subTotal = totalAmt;
                totalAmt = parseFloat(totalAmt+taxAmt);
                current_elp = eachprice+((eachprice*taxper)/100);
            }else{
                var price_excltax = parseFloat(totalAmt/(1+((taxper*1)/100)));
                subTotal = price_excltax;
                taxAmt = parseFloat(totalAmt-price_excltax);
                totalAmt = parseFloat(totalAmt);
                current_elp = eachprice;
            }
           /* item_discAmt = calcItemDiscount(product_id);
            totalAmt = totalAmt - item_discAmt; */
            $('#unit_price'+product_id).val(eachprice);
            $('#totalPriceText'+product_id).text($.number(price,5));
            $('#taxtext'+product_id).text($.number(taxAmt,5));
            $('#curelptext'+product_id).text($.number(current_elp,5));
            $('#taxval'+product_id).val(taxAmt);
            $('#totprice'+product_id).val(totalAmt);
            $('#totalval'+product_id).text($.number(totalAmt,5));            
            console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
            return false;
        }else{
            alert('Free Qty should not be morethan pr Qty');
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
        qtycalculate = totprqty - totfreeqty;
        eachprice =parseFloat((totalPrice / qtycalculate));
        var price =parseFloat(eachprice*noofeach);
        subTotal = price_excltax;
       // item_discAmt = calcItemDiscount(product_id);
        $('#unit_price'+product_id).val(eachprice);
        $('#curelptext'+product_id).text($.number(eachprice,5));
        $('#totalPriceText'+product_id).text($.number(price_excltax,5));
        $('#taxtext'+product_id).text($.number(taxAmt,5));
        $('#taxval'+product_id).val(taxAmt);
        $('#baseprice'+product_id).val(price).trigger('change');
        console.log(product_id+'==TotT'+taxAmt+'==TotA'+totalAmt+'==='+pre_post_type);
        return false;
    }
  /*  function calcItemDiscount(product_id){
        if(item_discount_type==1){
            if(item_disc_tax_type==1){
                subTotal = subTotal+taxAmt;
            }
            item_discAmt = (subTotal*item_discount)/100;
        }else{
            item_discAmt = item_discount;
        }
        if(item_discAmt>totalAmt){
            alert('Discount amount should not be more than total');
            $('#item_discount'+product_id).val(0);
            readValues(product_id);
            calcTotal(product_id);
            return false;
        }
        $('#item_discount_amt'+product_id).val(item_discAmt);
        $('#item_discount_text'+product_id).text($.number(item_discAmt,5));
        return item_discAmt;
    }
*/
    function checkProductAdded(product_id) {
        var checked = true;
        $("input[name='pr_product_id[]']").each(function () {
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
        $("input[name='pr_product_id[]']").each(function () {
            var product_id=$(this).val();
            products.push(product_id);
        });
        return products;
    }
    
    function checkProductQty() {
        skus='';
        var checked = true;
        $("input[name='soh_qty[]']").each(function () {
            var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
            var soh_qty = $(this).val();
            var dit_qty = $('#dit_qty'+product_id).val();
            var dnd_qty = $('#dnd_qty'+product_id).val();
            var productQty = parseInt(soh_qty)+parseInt(dit_qty)+parseInt(dnd_qty);            
            if (productQty == '' || productQty <= 0) {
                var sku=$('#product_sku'+product_id).val();
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
        return checked;
    }
    function checkProductInv() {
        skus='';
        var checked = true;
        $("input[name='soh_qty[]']").each(function () {
            var product_id = $(this).closest('tr').find('input[name="pr_product_id[]"]').val();
            var soh_qty = $(this).val();
            var dit_qty = $('#dit_qty'+product_id).val();
            var dnd_qty = $('#dnd_qty'+product_id).val();
            
            var currsoh = parseInt($('#currsoh'+product_id).val());
            var currdit = parseInt($('#currdit'+product_id).val());
            var currdnd = parseInt($('#currdnd'+product_id).val());
            var sku=$('#product_sku'+product_id).val();
            //alert('cursoh='+currsoh+'=soh_qty='+soh_qty+'=currdit='+currdit+'=dit_qty='+dit_qty+'=currdnd='+currdnd+'=dnd_qty='+dnd_qty);
            if((soh_qty>currsoh)||(dit_qty>currdit)||(dnd_qty>currdnd)){
                checked = false;
                skus = skus+sku+',';
                return;
            }
        });
        skus='<strong>'+skus.replace(/,\s*$/, "")+'</strong>';
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
    $(document).on('click','.delete_product',function(){
        if(confirm('Do you want to remove item?')){
            var product_id = $(this).attr('data-id');
            $(this).closest('tr').remove();
            deleteChildProduct(product_id);
            return false;
        }
    });
    function deleteChildProduct(product_id) {
        $("input[name='pr_product_id[]']").each(function () {
            var parent_id = $(this).closest('tr').find('input[name="parent_id[]"]').val();
            if(product_id==parent_id) {
                $(this).closest('tr').remove();
            }
        });        
    }

    $("#sale_return_inv_no").change(function(){
        if($('#sale_return_inv_no').val()==""){
            // $('#pr_form').formValidation('enableFieldValidators', 'sale_return_inv_no',false);
            return false;
        }else{
            var sr_inv_no = $("#sale_return_inv_no").val();
            checkSrInvoiceNo(sr_inv_no);
            // $('#pr_form').formValidation('enableFieldValidators', 'sale_return_inv_no',true);
            return false;
        }
    });

    $("#returnack").change(function() {
        var file_data = $('#returnack').prop('files')[0];
        var form_data = new FormData();
        form_data.append('upload_file', file_data);
            $('.loderholder').show();
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/pr/uploadpodocs',
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
                    url: '/pr/deleteDoc/'+doc_id,
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

    function checkSrInvoiceNo(sr_inv_no){
        var supplier_id = $('#supplier_list').val();
        var warehouse_id = $('#warehouse_list').val();
        let pr_totprice = [];
        $("input[name^='pr_totprice']").each(function (){
            pr_totprice.push($(this).val());
        });


         $.ajax({
             headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/pr/checksrinvno',
             type: 'POST',
             dataType:'JSON',
             data:{sr_inv_no:sr_inv_no,supplier_id:supplier_id,pr_totprice:pr_totprice,pr_le_wh_id:warehouse_id},
             success: function (data) {
                $('#addSkubtn').attr('disabled',false);
                if(data.status==200){
                    
                }else{
                    alert(data.message);
                }
             },
             error: function (response) {

             }
         });
    }


</script>
@stop
@section('userscript')
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
