@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php
                  
$bp = url('uploads/brand_logos');
$productMedia = url('uploads/products/');
$base_path = $bp."/";
?>


 <input type="hidden" name="product_id" id="product_id" value="{{$product_id}}" />
 <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
 <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
 <span id="work_flow_message"></span>
<div class="col-md-12 col-sm-12 ">
<div class="row">
<div class="portlet light tasks-widget ">
<!-- <div class="portlet-title">
<div class="caption caption-md heading_sty"> @if(isset($productData->product_title)){{$productData->product_title}}@endif </div>
</div> -->


<div class="row rowmargtop">
<div class="col-md-6 caption">
@if(isset($productData->product_title)){{$productData->product_title}}@endif
</div>
<div class="col-md-3">
<strong>Article Number <span>:</span></strong> {{$productData->sku}}
</div>
<div class="col-md-2">
<strong>HSN Code <span>:</span></strong> {{$hss_code}}
</div>

<div class="col-md-2 pull-right text-right">
        <a href="javascript:void(0)" class="click" data-id="pop1">
            <i class="fa fa-comment-o" aria-hidden="true"></i>
        </a>
        (<span id="comments_count"></span>)
                             
        <div class="pop_holder" id="pop1" >
    
            <div class="dropdown-menu-list ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 add_comment_text" > 

                </div>

            </div>
            <div class="row"  id="editFormControl">
                <div class="col-lg-12 form_holder">
                    <div class="form-group">
                        <textarea  placeholder="Please Enter Comments. " class="form-control txtBox" id="comments"> </textarea>
                    </div>
                    <button class="btn blue btn-sm submitBtn" type="button" id="comment_submit">Submit</button>
                    <button class="btn blue btn-sm default cancelBtn" type="button">Cancel</button>
                </div>
            </div>
        </div>
    
</div>
</div>

<hr>

<div class="row">

@include('Product::productPreviewImageGallery')
</div>
<div class="col-md-8 col-xs-12 ">
<div class="portlet">
<div class="row rowmarg">
<div class="col-md-4"><strong>Category </strong></div>
<div class="col-md-8">: @if(isset($category_link)) {{$category_link}}@endif</div>
</div>
<div class="row rowmarg">
<div class="col-md-4"><strong>Manufacture </strong></div>
<div class="col-md-8">: @if(isset($manufacturer_name)){{$manufacturer_name}}@endif</div>
</div>
<div class="row rowmarg">
<div class="col-md-4"><strong>Brand </strong></div>
<div class="col-md-8">
: @if(isset($productData->get_brand_model->logo_url))


@if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $productData->get_brand_model->logo_url))
<a href="#" data-toggle="tooltip" data-placement="top" title="@if(isset($productData->get_brand_model->brand_name)){{$productData->get_brand_model->brand_name}}@endif">
<img src="{{$productData->get_brand_model->logo_url}} "  width="100px" height="33px"></a>
@else
<a href="#" data-toggle="tooltip" data-placement="top" title="@if(isset($productData->get_brand_model->brand_name)){{$productData->get_brand_model->brand_name}}@endif">
<img src="{{$bp.'/'.$productData->get_brand_model->logo_url}} "  width="100px" height="33px"></a>

@endif
@endif
</div>
</div>
<div class="row rowmarg form-group">
    <div class="col-md-4"><strong>Product Group ID </strong></div> 
    <div class="col-md-8">:
        <span>@if(isset($productData->product_group_id)){{ $productData->product_group_id}}@endif</span>
        
    </div>
</div>
<div class="row rowmarg">
<div class="col-md-4"><strong>KVI </strong></div>
<div class="col-md-8">: @foreach($kvi_data as $kviValue)
@if($kviValue->value ==$productData->kvi)
<span>{{$kviValue->name}}  </span>
@endif

@endforeach</div>
</div>
<div class="row rowmarg">
<div class="col-md-4"><strong>Manufacturer SKU Code </strong></div>
<div class="col-md-8">: {{$productData->seller_sku}}</div>
</div>

<div class="row rowmarg">
<div class="col-md-4"><strong>MRP </strong></div>
<div class="col-md-8">: {{ number_format($productData->mrp,2)}}</div>
</div>
@if($esuPermission==1)
<div class="row rowmarg">
<div class="col-md-4"><strong>{{ trans('headings.SU') }} </strong></div>
<div class="col-md-8">: {{$productData->esu}}</div>
</div>
@endif

<div class="row rowmarg">
<div class="col-md-4"><strong>Star </strong></div>
<div class="col-md-8">: {{$product_star}}</div>
</div>

<div class="row rowmarg">
                        <div class="col-md-4"><strong>Is Sellable</strong></div>
                        <div class="col-md-8">
                            @if(isset($productData->is_sellable) && $productData->is_sellable==1)
                             <label class="switch ">: Yes</label>
                            @else
                             <label class="switch ">: No</label>
                            @endif
                           
                        </div>
                    </div> 
                    <div class="row rowmarg">
<div class="col-md-4"><strong>Product Description</strong></div>
<div class="col-md-8">: @if(isset($productData->product_content_model->description)) {{$productData->product_content_model->description}} @endif</div>
</div>
        


</div>
</div>
</div>
      
    <div class="row">     
<div class="portlet-body"> 
<div class="portlet light">
<div class="tabbable-line">
@include('Product::productTabs')
<div class="tab-content">
<div class="col-md-12 text-right" style="font-size:11px"><b>* All Amounts in </b><i class="fa fa-inr" aria-hidden="true"></i></div>

<div class="tab-pane active" id="tab_15_1">

@include('Product::productAttributes')  
@include('Product::approval')
</div>
</form>
<div class="tab-pane" id="tab_15_2">
      <div class="portlet-body ">
                  @include('Product::relatedProducts')
              </div>   
      <div class="row">
          <div class="col-md-12">
              <table id="relatedProductsGrid"></table>

          </div>
      </div>   
  </div> 
    <div class="tab-pane" id="grouped_products">
    <div class="portlet">                              
        <div class="portlet-body ">
            @include('Product::groupedProducts')
        </div>


    </div>           
</div> 
  <div class="tab-pane" id="freebie">
      <div class="portlet">

          <div class="portlet-body form">

              <div class="row">
                  <div class="col-md-12">
                      <table id="freeBieConfigGrid"></table>
                  </div>
              </div>


          </div>



      </div>           
  </div> 
<div class="tab-pane" id="tab_15_3">
<div class="row">
    <div class="col-md-12">
        <table id="packingConfigGrid"></table>
    </div>
  </div>     
</div>          
<div class="tab-pane" id="tab_15_4">
<div id="tableContainer" class="row tableContainer">
   <div class="col-md-4">
        <table id="taxTypes"></table>
   </div>
    <div class="col-md-8"> 
        <table id="productmappingdetailss" class='productmappingdetailss'></table>
    </div>
</div>
<!-- <table id="productmappingdetailss" class='productmappingdetailss'></table>  -->        
</div>

<div class="tab-pane" id="tab_15_5">
<div class="portlet">
<div class="portlet-body">
<div class="table-scrollable">
<table id="productSuppliersGrid"></table>
</div></div></div>                         
</div>
<div class="tab-pane" id="promotion_tab">
<table id="slabprices"></table>                    
</div>
<div class="tab-pane" id="warehouse_config">                                                   
    @include('Product::warehouse_config')
    <table id="product_wh_config"></table>                       
</div>
<div class="tab-pane" id="tab_15_7">
<div class="row"><div class="col-lg-12 histhead" >  
<div class="col-md-3"> <b>User</b></div>
<div class="col-md-2">  </div>  <div class="col-md-2"> <b> Date</b></div>
<div class="col-md-3"> <b>Status</b></div>
<div class="col-md-2" style="margin-left: -93px;"><b>Comments</b></div></div> </div>                         
<div class="timeline" >
@if(isset($history))
@foreach($history as $historyVal )
<?php
$url=  public_path();
if(file_exists($url.$historyVal['profile_picture']) && $historyVal['profile_picture']!='')
{
$img = $historyVal['profile_picture'];      
}
else
{
 $bp = url('uploads/LegalEntities/profile_pics');
 $base_path = $bp."/";   
 $img = $base_path."avatar5.png";         
}
?>
<div class="timeline-item timline_style">
<div class="timeline-badge">
<img class="timeline-badge-userpic" src="{{$img}}">
</div>
    
<div class="timeline-body">

<div class="row">
<div class="col-md-4"> <p>{{$historyVal['firstname'].$historyVal['lastname']}}
<span>{{$historyVal['name']}}</span></p>  </div> 
<div class="col-md-2 push_right"><?php echo date('d/m/Y h:i A',strtotime($historyVal['created_at'])); ?></div> 
<div class="col-md-2 push_right">{{$historyVal['master_lookup_name']}}</div>                
<div class="col-md-2 push_right" style="width: 250px;word-wrap: break-word;">{!! $historyVal['awf_comment'] !!}</div></div>                
</div>
</div>
@endforeach
@endif
</div>
</div>
<div class="tab-pane" id="tab_15_8">
  <div class="row">
      <!-- inventory --> 
      <div class="col-md-12">
        <table id="inventorygrid"></table>
      </div>   
  </div>    
</div>
<div class="tab-pane" id="product_history">
   @include('Product::edit_product_history')            
</div>
<div class="tab-pane" id="customer_type_esu">
  <div class="portlet">
      <div class="portlet-body">
          @include('Product::customerTypeEsu')
      </div>
  </div>           
</div>

</div>
</div>
</div>
</div>
<input type="hidden" name="vat_state_wise_tax_classes" id="vat_state_wise_tax_classes" data-vat_state_wise_tax_classes="{{ $state_wise_tax_classes }}"/>
</div>  

@include('Tax::taxmapHorizontalGrid')
@include('Inventory::inventoryupdate-popup')

@stop

@section('style')
<style type="text/css">
.rowmarg{ margin-bottom:5px;}
.rowmargtop{ padding-top:10px;}
li.active a, li.active i {
   /* color: #fff !important;*/
}
.pop_holder {
    height: 300px;
    left: -140px !important;
    min-width: 300px;
    overflow: scroll;
}
.titlerow{ padding: 10px 0px; border-bottom: 1px solid #efefef; margin-bottom: 15px;}   
  
.elastislide-list{max-height:100% !important;}

.md-skip {
    padding: 0px 4px !important;
}

.plusborder{border:1px dashed #337bb6; height:107px; width:89px;}
/*.icon-plus{font-size:54px !important; color:#337bb6; margin-top:45px;}
*/

#photo-viewer {
  position: relative;
  height: 300px;
  overflow: hidden;
}

#photo-viewer.is-loading:after {
  content: url('../img/load.gif');
  position: absolute;
  top: 0;
  left: 0;
}

#photo-viewer img {
  position: absolute;
  max-width: 100%;
  max-height: 100%;
  top: 50%;
  left: 50%;
}

/********** THUMBNAILS **********/

#thumbnails {
  margin: 10px 5px 0 0;
  height: 60px;
}

a.active { opacity: 0.3; }

/********** PARTS OF PAGES **********/

.gallery {
  width: 400px;
  padding: 20px;
  float: left;
}

.description {
  width: 180px;
  float: right;
  padding: 20px 20px 0 0;
  font-size: 85%;
  line-height: 1em;
}

.standfirst { margin: 0; }

/********** BUY BUTTON **********/

a#buy {
  background-color: #ed8e6c;
  color: #ffffff;
  border: none;
  border-radius: 4px;
  padding: 7px 10px 9px 10px;
  margin: 5px 0 20px 0;
  float: right;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

a#buy:hover { background-color: #ed612f; }
.preview-image { display: none; height: auto; width: 200px;border:2px solid #c3c3c3 !important; }
.icon-jfi-cloud-up-o{font-size:64px !important; color:#337bb6;}
.zoomimgheight{ height:250px; width:100%; margin-bottom:10px;}
    
  
 .del-set-actions{ margin-left:0px; margin-bottom:5px;  margin-top:0px; background:#efefef; width:100px; padding:5px 0px;}   
  .arrowdisable{
    opacity:0.3;}

 .caption{font-weight: bold;word-wrap: break-word; width:500px;}

 .ui-autocomplete{
z-index: 999999999 !important; top:10px;  height:100px; overflow-y: scroll; overflow-x:hidden; border-top:none!important;
position:fixed !important;
}
.ui-autocomplete-input{
z-index: 99999 !important;
}


  .ui-autocomplete li{ border-bottom:1px solid #efefef; padding-top:10px!important; padding-bottom:10px!important; 
    }   
    
    
</style>
{{HTML::style('css/switch-custom.css')}}

<!-- BEGIN PAGE LEVEL STYLES -->


<!--simple zoom css end-->
<!-- END PAGE LEVEL STYLES -->

<!--igniute UI-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/jcarousel.skeleton.css') }}" rel="stylesheet" type="text/css" />



<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components-rounded.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/productview.css') }}" rel="stylesheet" type="text/css" />



<link href="{{ URL::asset('assets/global/plugins/slider/demo.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/slider/es-cus.css') }}" rel="stylesheet" type="text/css" />


<!-- simple zoom css-->
<link href="{{ URL::asset('assets/global/css/jquery.simpleLens.css') }}" rel="stylesheet" type="text/css" /> 
<link href="{{ URL::asset('assets/global/css/jquery.simpleGallery.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/dropzone/css/dropzone.css') }}" rel="stylesheet" type="text/css" /> 

<!--simple zoom css end-->
       
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />


@stop


@section('userscript')
        @include('includes.validators')
@include('includes.group_repo')  
 @include('includes.ignite')  
<!--simple zoom script-->



<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../assets/global/plugins/dropzone/dropzone.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->

<script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/demo.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/form-dropzone.js') }}" type="text/javascript"></script>
 <script src="{{ URL::asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script> 
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-manufactures.js') }}" type="text/javascript"></script> -->
 <script src="{{ URL::asset('assets/global/scripts/jcarousel.skeleton.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/jquery.jcarousel.min.js') }}" type="text/javascript"></script> 
 <script src="{{ URL::asset('assets/admin/pages/scripts/product/form-wizard-package_configuration.js') }}" type="text/javascript"></script>
         <script src="{{ URL::asset('assets/admin/pages/scripts/product/product_grid_tabs.js') }}" type="text/javascript"></script>


<!--Ignietui scripts-->
 <script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js')}}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js')}}" type="text/javascript"></script>
<!-- <script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/product_tax_map_grid.js') }}" type="text/javascript"></script> -->




        <script src="{{ URL::asset('assets/global/plugins/slider/jquery.imagezoom.min.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/slider/modernizr.custom.17475.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/plugins/slider/jquery.elastislide.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/admin/pages/scripts/product/grouped_product_grid.js') }}" type="text/javascript"></script>
        
        <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/global/scripts/approval.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/cellFormatter.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/product_taxMap_Horizontal.js') }}" type="text/javascript"></script> 
        <script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/products_inventory_grid.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/admin/pages/scripts/product/bin_wh_confi.js') }}" type="text/javascript"></script>                                  
        <script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/validation.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('assets/admin/pages/scripts/product/history_grid.js') }}" type="text/javascript"></script>
<!--Ignietui scripts end-->
<!--Ignietui scripts end-->
<script>
            autosuggest();
            approval();
            slabPrices($("#product_id").val());
            jQuery(document).ready(function () {
                // initiate layout and plugins
                Metronic.init(); // init metronic core components 
        productHistoryGrid();
                Layout.init(); // init current layout
                Demo.init(); // init demo features
                FormDropzone.init();
            });
        </script>
        <script type="text/javascript">


    $(document).ready(function(){

       var parent_id = $('#approval_for_id').val();
        childproductlist(parent_id);
        
        $("#rlated_product").on('click', function () {            
            //$(".enableDisableProduct").attr('disabled', true);
            $(".cp_enabled").attr('disabled','true')
        });
        
        
       $('[data-toggle="tooltip"]').tooltip();
    $('li.showicons').mouseover(function(){
      $('div.defaulticons', this).show();
    });
    $('li.showicons').mouseout(function(){
      $('div.defaulticons', this).hide();
    });
    $(document).on('click', '.dz-preview.dz-processing.dz-success.dz-image-preview',function(){
        
         $('#my-dropzone').find('.dz-preview.dz-processing.dz-success.dz-image-preview').not(this).find('.dz-success-mark').addClass('hide');
        $(this).find('.dz-success-mark').toggleClass('hide');
    });
    
                  $('#approval_form').bootstrapValidator({
            message: 'This value is not valid',
            fields: {               
                approval_comments: {
                    validators: {
                        notEmpty: {
                            message: "Please provide comments"
                        }                       
                    }
                },
                approval_select: {
                    validators: {
                        notEmpty: {
                            message: "Please select status"
                        }
                    }
                },
            }
        }).on('success.form.bv', function (event) {
            event.preventDefault();
            var url = '/products/all';
            approvalSave(url);
        });
	
                var parent_id = $('#approval_for_id').val();
        
        $("#rlated_product").on('click', function () {
            $(".enableDisableProduct").attr('disabled', true);
        });
        
        
       $('[data-toggle="tooltip"]').tooltip();
    $('li.showicons').mouseover(function(){
      $('div.defaulticons', this).show();
    });
    $('li.showicons').mouseout(function(){
      $('div.defaulticons', this).hide();
    });
    $(document).on('click', '.dz-preview.dz-processing.dz-success.dz-image-preview',function(){
        
         $('#my-dropzone').find('.dz-preview.dz-processing.dz-success.dz-image-preview').not(this).find('.dz-success-mark').addClass('hide');
        $(this).find('.dz-success-mark').toggleClass('hide');
    });
});





  $(function(){
    
    //$( "li" ).hover(function(e) {
      //$(this).find("div").css("display","block");
    //});
    
    var carsousel = $('#demo4carousel').elastislide({start:0,minItems:4,
      onClick:function( el, pos, evt ) {
        el.siblings().removeClass("active");
        el.addClass("active");
        carsousel.setCurrent( pos );
        evt.preventDefault();
        // for imagezoom to change image 
        var demo4obj = $('#demo4').data('imagezoom');
        demo4obj.changeImage(el.find('img').attr('src'),el.find('img').data('largeimg'));
      },
      onReady:function(){
        //init imagezoom with many options
        $('#demo4').ImageZoom({type:'standard',zoomSize:[480,300],bigImageSrc:'',offset:[10,-4],zoomViewerClass:'standardViewer',onShow:function(obj){obj.$viewer.hide().fadeIn(500);},onHide:function(obj){obj.$viewer.show().fadeOut(500);}});
        
        $('#demo4carousel li:eq(0)').addClass('active');
        
        // change zoomview size when window resize
        $(window).resize(function(){
          var demo4obj = $('#demo4').data('imagezoom');
          winWidth = $(window).width();
          if(winWidth>900)
          {
            demo4obj.changeZoomSize(480,300);
          }
          else
          {
            demo4obj.changeZoomSize( winWidth*0.4,winWidth*0.4*0.625);
          }
        });
        
      }
    });

  
    
  });

</script>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>        

<script type="text/javascript">
    $('.urlimage').on('click', function() {
        var image_preview=$(".urlimage_preview").val();
        $('.preview-image').attr('src', image_preview).fadeIn();
    });

</script>

<!--ignuite UI script related products-->   
<script type="text/javascript">
//relatedProductsGrid();
</script>
<script type="text/javascript">
$(document).ready(function(){

   
    var resetOthers = function(id){
        $('.pop_holder').not('#'+id).each(function(){
            var id = $(this).attr('id') ;
            $('[data-id="' + id + '"]').data('clicked', false);
        });
    };
    var mouseEnter = function(){
        var id = $(this).data('id');
        if( $(this).data('clicked') ){
            resetOthers(id);
            return;
        }
        $(this).data('hover', true );
        $('.pop_holder').not('#'+id).hide();
        $('.pop_holder').not('#'+id).each(function(){
            var id = $(this).attr('id') ;
            $('[data-id="' + id + '"]').data('hover', false);
        });
        resetOthers(id);
        //console.log( $(this).data() );
        $("#"+id).show('fast');
    };
    $(".cancelBtn").click(function(){
        $("#comments").val("");
        $("#pop1").removeAttr("style");
    });
    var mouseLeave = function(){
        var id = $(this).data('id');
        if( $(this).data('clicked') ){
            resetOthers(id);
            return;
        }
        $(this).data('hover', false );
        var id = $(this).data('id');
        resetOthers(id);
        //console.log( $(this).data() );
        $("#"+id).hide('fast');
    };

    $("a.click").click(function(){
        var id = $(this).data('id');    
        if( $(this).data('clicked') ){
            $(this).data('clicked', false );
        }else{ $(this).data('clicked', true ); }
        if( $(this).data('hover') == true){
           $(this).data('hover', false );
            $("#"+id).find('input').focus(); 
            return;
        }
        
        //console.log( $(this).data() );
        $('.pop_holder').not('#'+id).hide();
        resetOthers( id );
        $('.pop_holder').not('#'+id).data('hover', false);
        $("#"+id).toggle('fast', function(){
            $("#"+id).find('input').focus();    
        }).finish(function(){
            console.log( this );
        });
        
    }).mouseleave(mouseLeave).mousedown(function(){ return false; })
}); 
      
  
 
 $(document).ready(function(){

    getAllComments();
    function getAllComments()
    {
             var comments_count=0;
             $("#comments_count").text(comments_count);
            url="/getProductComments";
            var product_id= $("#product_id").val();
            var token = $("#csrf-token").val();
            commentsData={product_id:product_id};
            $.ajax({
                      headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    async: false,
                    type: 'POST',
                    data:commentsData,
                    success: function (rs)
                    {

                        var reviewsHtml = [];
                       
                        $.map( JSON.parse(rs), function( value, index ) {
                            
                            reviewsHtml.push('<div class="row pop_inner "><div class="row"><div class="col-lg-2 col-md-2 col-xs-3"><img src="'+value.pic+'" class="img-responsive popimg"></div><div class="col-lg-10 col-md-9 col-xs-9"><div class="row"><p class="name">'+value.name+' <span class="comments_time">'+value.created_on+' </span></p></div></div></div><div ><p >'+value.comments+'</p></div></div>');
                            comments_count=  comments_count+value.count;
                        });
                        //reviewsHtml = reviewsHtml.join('');

                        $('.add_comment_text').html(reviewsHtml);
                        $("#comments").val('');
                        $("#comments_count").text(comments_count);
                    },
                    error: function (err) {
                        console.log('Error: ' + err);
                    }
            });
    }

     $("#comment_submit").click(function()
        {

            var comments_data= $("#comments").val();
            var product_id= $("#product_id").val();
            url="/productComments";
            var token = $("#csrf-token").val();
            commentsData={comments:comments_data,product_id:product_id};
            $.ajax({
                      headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    async: false,
                    type: 'POST',
                    data:commentsData,
                    success: function (rs)
                    {
                        
                        var reviewsHtml = [];
                        var comments_counts= $("#comments_count").text();

                        $.map( JSON.parse(rs), function( value, index ) {
                            reviewsHtml.push('<div class="row pop_inner "><div class="row"><div class="col-lg-2 col-md-2 col-xs-3"><img src="'+value.pic+'" class="img-responsive popimg"></div><div class="col-lg-10 col-md-9 col-xs-9"><div class="row"><p class="name">'+value.name+' <span class="comments_time">'+value.created_on+' </span></p></div></div></div><div ><p >'+value.comments+'</p></div></div>');
                            comments_counts =  parseInt(comments_counts)+parseInt(value.count);

                        });                      

                        reviewsHtml = reviewsHtml.join('');
                        $("#comments_count").text(comments_counts);
                        $('.add_comment_text').append(reviewsHtml);
                        $("#comments").val('');                     
                    },
                    error: function (err) {
                        console.log('Error: ' + err);
                    }
            });

            
        });
        $(document).on('keyup', '.txtBox',function(){
            var val = this.value;
            var el = $(this).closest('.form-body'); 
            console.log( val );
            if( val.length ){
                el.find('.submitBtn').prop('disabled', false);
            }else{ el.find('.submitBtn').prop('disabled', true); }
         }).keyup();
        $('.txtBox').keyup();
});     
       
    </script>
<!--    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>-->

@stop   
@extends('layouts.footer')