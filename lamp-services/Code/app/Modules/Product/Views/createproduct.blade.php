@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget" style="height:auto;">
    <div class="portlet-title">
        <div class="caption"> {{trans('products.lables.create_product')}} </div>
        <div class="tools"> 
            <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span>
        </div>
    </div>
<div class="portlet-body">
<div id="form-wiz">
    <form id="createproduct" action="" method="POST" >  
     <!--    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" /> -->
        <div class="row">
             <div class="col-md-6">
                 <div class="form-group">
                    <input type="hidden" name="getManfId" id="getManfId" value="">
 
                    <label class="control-label">{{trans('products.lables.manufacturer')}}  <span class="required">*</span></label>
                    <select name="manufacturer_name" id="manufacturer_name" class="form-control select2me">
                    </select>
                    
                </div>
            </div>  
            <div class="col-md-6">
                 <div class="form-group">
                     <input type="hidden" name="getBrandId" id="getBrandId" value="">

                    <label class="control-label">{{trans('products.lables.brand')}}  <span class="required">*</span></label>
                    <select class="form-control select2me" id="brand_id" name="brand_id">
                    </select>
                    
                </div>
            </div>  
            
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">{{trans('products.lables.categories')}}  <span class="required">*</span></label>
                        <select class="form-control select2me" name="category" id="category">
                        </select>
                    
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">{{trans('products.lables.prodct_title')}} <span class="required">*</span></label>
                    <input type="text" class="form-control"  name="product_title" id="product_title" value=""/>
                </div>
            </div>
            
        </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">MRP <span class="required">*</span></label>
                        <input type="number" min="0" value="0" class="form-control"  name="product_mrp" id="product_mrp" value=""/>
                    
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">{{trans('headings.SU')}}<span class="required">*</span></label>
                    <input type="text" class="form-control"  name="product_esu" id="product_esu" value=""/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Star <span class="required">*</span></label>
                     <select class="form-control select2me" name="product_star" id="product_star">
                        <option value="0">Please Select...</option>
                        @if(!empty($product_stars))
                            @foreach($product_stars as $starValues)
                                <option value="{{$starValues->value}}">{{$starValues->name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>            
        </div>
         <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">KVI <span class="required">*</span></label>
                        <select class="form-control select2me" name="kvi_value" id="kvi_value">
                            <option value="0">Please Select...</option>
                            @if($kvi)
                                @foreach($kvi as $kvi)
                                    <option value={{$kvi->value}}>{{$kvi->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Offer Pack <span class="required">*</span></label>
                        <select class="form-control select2me" name="product_offer_pack" id="product_offer_pack">
                            <option value="0">Please Select...</option>
                            @if($offer_pack)
                                @foreach($offer_pack as $packValue)
                                    <option value="{{$packValue->name}}">{{$packValue->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    
                </div>
            </div>
<!--            <div class="col-md-1">
                <div class="form-group">
                    <label class="control-label">Is Sellable </label>
                    <label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="product_is_sellable" name="product_is_sellable"  check="false" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
                </div>
            </div>-->
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Pack Size </label>
                    <input type="number" min="0" value="0" class="form-control"  name="product_pack_size" id="product_pack_size" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Pack Size UOM </label>
                    <input type="text" value="" class="form-control"  name="product_pack_size_uom" id="product_pack_size_uom" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Each Quantity <span class="required">*</span></label>
                        <input type="number" min="1" value="1" class="form-control"  name="product_each_qty" id="product_each_qty" value=""/>

                    
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">CFC Quantity </label>
                    <input type="number" min="1"  class="form-control"  name="product_cfc_qty" id="product_cfc_qty" value=""/>
                </div>
            </div>
            
        </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Suppliers <span class="required">*</span></label>
                        <select class="form-control select2me" name="product_suppliers" id="product_suppliers">
                            <option value="0">Please Select...</option>
                            @if(!empty($supplier_list))
                                @foreach($supplier_list as $key=>$value)
                                    <option value='{{$key}}'>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>                    
                </div>
            </div>        
         </div>       
        <div class="row" style="margin-top:50px;">
        <hr />
            <div class="col-md-12 text-center"> 
               <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">{{trans('products.lables.save')}} </button>               
                <a href="/products"> <button type="button"  class="btn green-meadow">{{trans('products.lables.cancel')}}</button></a>
             
            </div>
        </div>
    </form>

</div>
</div>
</div>
</div>
</div>

@stop


@section('style')
<style type="text/css">



     #dragandrophandler
    {
        border: 2px dashed #92AAB0;
        width: 350px;
        height: 50px;
        color: #92AAB0;
        text-align: center;
        vertical-align: middle;
        padding: 10px 0px 10px 10px;
        font-size:200%;
        display: table-cell;
    }
    .progressBar {
        width: 100px;
        height: 22px;
        border: 1px solid #ddd;
        border-radius: 5px; 
        overflow: hidden;
        display:inline-block;
        margin:0px 10px 5px 5px;
        vertical-align:top;
    }

    .progressBar div {
        height: 100%;
        color: #fff;
        text-align: right;
        line-height: 22px; /* same as #progressBar height if we want text middle aligned */
        width: 0;
        background-color: #0ba1b5; border-radius: 3px; 
    }
    .statusbar
    {
        border-top:1px solid #A9CCD1;
        min-height:25px;
        width:450px;
        padding:10px 10px 0px 10px;
        vertical-align:top;
    }
    .statusbar:nth-child(odd){
        background:#EBEFF0;
    }
    .filename
    {
        display:inline-block;
        vertical-align:top;
        width:150px;
    }
    .filesize
    {
        display:inline-block;
        vertical-align:top;
        color:#30693D;
        width:80px;
        margin-left:10px;
        margin-right:5px;
    }
    .abort{
        background-color:#A8352F;
        -moz-border-radius:4px;
        -webkit-border-radius:4px;
        border-radius:4px;display:inline-block;
        color:#fff;
        font-family:arial;font-size:13px;font-weight:normal;
        padding:4px 15px;
        cursor:pointer;
        vertical-align:top
    }
   
.preview-image { display: none; height: auto; width: 200px; }
    
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
    
/* SAM */
    .contenttab-line {
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
    font-weight: bold;
}
    .connectselect {
    padding-bottom: 10px;
}
    .prodatt {
    padding-left: px;
    /* padding-top: 15px; */
}
    .prodatt1 {
    padding-left: 15px;
    padding-top: 15px; 
}
.dz-default.dz-message {
    border: 2px dashed #3598dc;
    color: #3598dc;
    height: 150px;
    padding-top: 90px;
    /* padding-left: 35px; */
    text-align: center;
}
.dz-details img {
    width: 100%;
    padding-top: 15px;
}
    .imgtitle {
    padding-top: 10px;
    font-weight: 600;
}

.imgtitle p {
    font-weight: 400;
    padding-top: 5px;
    font-size: 12px;
    line-height: 4.5px;
}
    
.portlet.light {
     height: auto !important; 
     min-height: auto !important; 
   }
    
.nav-tabs > li > a, .nav-pills > li > a {
    font-weight: bold;
}
    
label {
    font-weight: normal !important;
}    
  
i.fa.fa-cloud-upload {
    color: #428bca;
    font-size: 70px;
    /* margin-top: -110px; */
    /* padding-left: 559px; */
    position: relative;
    float: left;
    left: 47%;
    top: -110px;
}
/* SAM */
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
{{HTML::style('css/switch-custom.css')}}

@stop
@section('script')
<script type="text/javascript"> 
            jQuery(document).ready(function () {
                FormWizard.init();
            });
$('#kvi_value').change(function(){
    if($('#kvi_value').val()==69010){
        if($("#kvi_value option[value='Freebie']").length == 0){
            var option1 = $("<option/>", {value: 'Freebie', text: 'Freebie'});
            $('#product_offer_pack').append(option1);
        }
        $('#product_offer_pack').select2("val", 'Freebie');
        $('#product_offer_pack').attr('disabled', true);    
    }else{
        $('#product_offer_pack').select2("val",'');
        $('#product_offer_pack').attr('disabled', false);
        $("#product_offer_pack option[value='Freebie']").remove();
    }
    
})
</script>
@stop

@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/get_manufacturer_list.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/product/form-wizard-create-product.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>

@stop
@extends('layouts.footer')