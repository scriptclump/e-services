@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alerts alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flash_message"></span>
</div>
<?php
$bp = url('uploads/brand_logos');
$productMedia = url('uploads/products');
$base_path = $bp . "/";
//$brand_imag= $productData->get_brand_model->logo_url;
?>
<div class="col-md-12" id="quickproduct1" >
<div class="row" >
  <div class="col-md-12" >
    <div class="portlet-body box_holder">
          <div class="titlebag">
          <div class="col-md-6 ">
            <p><strong>GENERAL</strong></p>
          </div>
            <div class="col-md-6 text-right"><button class="btn btn-link" id="edit_general_info" onclick="editGeneralInfo()" id=""><i class="fa fa-pencil" aria-hidden="true"></i></a></button>
            <button class="btn btn-link text-right"  id="update_general_info">Update</button>  
            </div>
          
        </div> 
        <div class="formmargin">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" id="product_id" value="{{ $productData[0]['product_id'] }}" />
          <div class="col-md-6">
            <div class="form-group row ">
              <label class="col-md-4"><strong>Product Title <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div class="col-md-7">
              <span id="view_product_title" >@if(isset($productData[0]['product_title'])){{$productData[0]['product_title']}}@endif</span> </div></div>
            <div class="form-group row ">
              <label class="col-md-4"><strong>MRP <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="mrp">{{$productData[0]['mrp']}}</span> </div>
              </div>
            <div class="form-group row">
              <label class="col-md-4"><strong>{{ trans('headings.SU') }} <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div class="col-md-7">
              <span id="esu">@if(isset($productData[0]['esu'])){{$productData[0]['esu']}}@endif</span></div>
              </div>
            <div class="form-group row ">
              <label class="col-md-4"><strong>Shelf Life <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
                <span id="shelf_life">{{$productData[0]['shelf_life']}}</span>
               </div>
              </div>
              <div class="form-group row ">
              <label class="col-md-4"><strong>Shelf Lift UOM <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="shelf_life_uom" >@if(isset($shelf_uom_data)){{$shelf_uom_data}}@endif</span> </div>
              </div>
            <div class="form-group row">
              <label class="col-md-4"><strong>Pack Size <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="pack_size" > {{$pack_size}}</span> </div>
              </div>
          </div>
          <div class="col-md-6">
            <div class="form-group row">
              <label class="col-md-3"><strong>Category </strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="view_product_title">@if(isset($category_link)){{$category_link}}@endif</span> </div>
              </div>
            <div class="form-group row">
              <label class="col-md-3"><strong>Manufacturer </strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="view_product_title">@if(isset($manf_name)){{$manf_name}}@endif</span> </div>
              </div>
            <div class="form-group row">
              <label class="col-md-3"><strong>Brand </strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="view_product_title" > @if(isset($brand_name)){{$brand_name}}@endif</span></div> </div>
            <div class="form-group row">
              <label class="col-md-3"><strong>Suppliers </strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-7">
              <span id="view_product_title" > @if(isset($suppliers)){{$suppliers}}@endif</span></div> </div>
<!--            <div class="form-group row">
              <label class="col-md-3"><strong>Is Sellable </strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div class="switch2 col-md-8">
               <label class="switch "><input class="switch-input vr_status4"  type="checkbox" {{$is_sellable}} id="product_is_sellable1" name="product_is_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
            </div>
            </div>
            <div class="form-group row">
              <label class="col-md-3"><strong>CP Enable </strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div class="switch2 col-md-8">
               <label class="switch "><input class="switch-input vr_status4"  type="checkbox" {{$cp_enabled}} id="product_cp_sellable" name="product_cp_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
                </div>
            </div>-->
            <div class="form-group row">
              <label class="col-md-3"><strong>Offer Pack <span id="required_color">*</span></strong></label>
              <div class="col-md-1"><strong>:</strong></div>
              <div  class="col-md-6">
              <span id="offer_pack">@if(isset($offer_pack)){{$offer_pack}}@endif</span> <p id="freebie_note" id="freebie_note" style="padding-top: 2%;font-size: 90%;display: block;"><strong style="color:red;">Note:</strong> If you want add freebie products. Please enable Is sellable.</p></div>
              <div  class="col-md-1">
               <button  type="button" class="btn green-meadow" id="addFreebie_model" data-toggle="modal" href="#addFreeBie"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> </div>

              </div>
          </div>
        </div>
        <div class="formmargin">
          <div class="col-md-12">
            <table id="freeBieConfigGrid"></table>
          </div>
        </div>
      </div>
  </div>
  </div>
  </div>
<div class="row" id="quickproduct1">
  <div class="col-lg-12">
  <div class="col-md-4 "> 
    <div class="portlet-body box_holder ">
        <div class="titlebag">
          <div class="col-md-8">
            <p><strong>Price Configuration</strong></p>
          </div>
        <div class="col-md-4 text-right" style="display:none"><button class="btn btn-link" onclick="editPriceInfo()" id="editPriceInfo_id"><i class="fa fa-pencil" aria-hidden="true"></i></button>
            <button class="btn btn-link text-right"  id="update_price_info">Update</button>  
            </div>
        </div>
        <div class="col-md-12 formmargin">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
           <!--  <div class="form-group row">
              <label class="col-md-4"><strong>MRP</strong></label>
             <div class="col-md-1"><strong>:</strong></div>
             <div  class="col-md-7">
              <span id="price_mrp">{{$productData[0]['mrp']}}</span> </div>
              </div> -->
            <div class="form-group row ">
              <label class="col-md-5"><strong>PTR  <span id="required_color">*</span></strong></label>
                            <div class="col-md-1"><strong>:</strong></div>
                <div  class="col-md-6">
                  <input type="hidden" id="price_product_id" value="{{$product_price_id}}">
              <span id="price_ptr">{{$product_ptr}}</span> </div>
              </div>
            <div class="form-group row">
              <label class="col-md-5"><strong>{{ trans('headings.SP') }}<span id="required_color">*</span></strong></label>
                            <div class="col-md-1"><strong>:</strong></div>
                <div  class="col-md-6">
              <span id="price_esp">{{$product_esp}}</span> </div>
            </div> 
            <div class="form-group row">
              <label class="col-md-5"><strong>Effective Date  <span id="required_color">*</span> </strong></label>
                            <div class="col-md-1"><strong>:</strong></div>
                <div  class="col-md-6">
                 <span id="price_effective_date_span">@if($price_effective_data!='0000-00-00'){{$price_effective_data}}@endif</spa>
                </div>
            </div>           
            <div class="form-group row">
                <label class="col-md-5"><strong>State  <span id="required_color">*</span></strong></label>
                <div class="col-md-1"><strong>:</strong></div>
                <div class="col-md-6" style="padding-right:0px;">
                  @if($getStateDetails)
                    @if($product_state_id!='')
                    @foreach($getStateDetails as $stateData)
                      @if($product_state_id==$stateData->zone_id)
                       <span id="state_span">{{$stateData->name}}</span>
                      @endif
                                        
                    @endforeach
                   @else
                    <span id="state_span"></span>
                    @endif 
                @else
                <span id="state_span"></span>
                @endif
               <select id = "price_state"  name = "price_state" onchange="changeTaxClassByState()" class="form-control" >
                                    <option value="">Please Select</option>
                                    @foreach($getStateDetails as $stateData)
                                        @if($product_state_id==$stateData->zone_id)
                                         <option value = "{{$stateData->zone_id}}" selected>{{$stateData->name}}</option>
                                        @else
                                         <option value = "{{$stateData->zone_id}}">{{$stateData->name}}</option>                                        @endif
                                        
                                    @endforeach
                </select>    
            </div>
            </div>
             <div class="form-group row">
                <label class="col-md-5"><strong>Customer Group  <span id="required_color">*</span></strong></label>
                <div class="col-md-1"><strong>:</strong></div>
                <div class="col-md-6" style="padding-right:0px;">
                  @if($getCustomerGroup)

                      @if($product_customer_type!='')
                                    @foreach($getCustomerGroup as $customerData)
                                        @if($product_customer_type==$customerData->value)
                                         <span id="customer_group_span">{{$customerData->master_lookup_name}}</span>
                                        @endif                                       
                                    @endforeach
                      @else
                      <span id="customer_group_span"></span>
                      @endif
                      <select id = "mdl_custgroup"  name = "mdl_custgroup" class="form-control" >
                                    <option value = "no" >Please Select</option>
                                   
                                    @foreach($getCustomerGroup as $customerData)
                                        @if($product_customer_type==$customerData->value)
                                         <option value = "{{$customerData->value}}" selected>{{$customerData->master_lookup_name}}</option>
                                        @else
                                         <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                        @endif                                       
                                    @endforeach
                                </select> 
                    @endif   
            </div>
            </div>
            <div class="form-group row">
                <label class="col-md-5"><strong>Tax Class <span id="required_color">*</span></strong></label>
                <div class="col-md-1"><strong>:</strong></div>
                <div class="col-md-6">
                  <?php   
                    if(!empty($taxClass))
                    {
                      if($product_tax_class!='')
                      {
                        foreach ($taxClass as $value)
                        {
                            if($product_tax_class==$value['tax_class_id'])
                            {
                               echo '<span id="tax_span">'.$value['tax_class_code'].'</span>';
                            }                                               
                        }
                      }else
                      {
                        echo '<span id="tax_span"></span>';
                      }
                        
                    }else{
                      echo '<span id="tax_span"></span>';
                    }?>
                 
                <select id = "tax_class"  name = "tax_class" class="form-control" >
                  <option value ="">Select Tax Class</option>
                  <?php   
                    if(!empty($taxClass))
                    {
                        foreach ($taxClass as $value)
                        {
                            if($product_tax_class==$value['tax_class_id'])
                            {
                                 echo'<option value ="'.$value['tax_class_id'].'" selected>'.$value['tax_class_code'].'</option>';
                            }else
                            {
                                 echo'<option value ="'.$value['tax_class_id'].'">'.$value['tax_class_code'].'</option>';
                            }                                               
                        }
                    }?>
                    </select>  
              </div>
              </div>
              <div class="row">
              <div class="col-md-4"></div>
              <div class="col-md-1"></div>
              </div>
      </div>
  </div>

</div>
  <div class="col-md-4 pack1" >
    <div class="portlet-body box_holder">
        <div class="row titlebag">
          <div class="col-md-8">
            <p><strong>Pack Configuration</strong></p>
          </div>

                    <div class="col-md-4 text-right"><button class="btn btn-link" onclick="editPackInfo()" id="edit_pack_info"><i class="fa fa-pencil" aria-hidden="true"></i></a></button>
            <button class="btn btn-link text-right"  id="update_pack_info">Update</button>  
            </div>


        </div>
        <div class="col-md-12 formmargin2">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <div class="form-group row">
              <table class="table table-striped table-bordered table-advance table-hover" id="sample_2">
    <thead>
        <tr>
            <th>PACK Qty</th>
            <th>Qty <span id="required_color">*</span></th>
            <th>Effective Date <span id="required_color">*</span></th>
            <th>Is Sellable</th>
        </tr>
    </thead>

    <tbody>
        <tr class="odd gradeX">
            <td >Each Qty</td>
            <td><span id="pack_each_qty">@if(isset($each_qty)){{$each_qty}}@endif</span></td>
            <td><span id="each_eff_date_span">{{$each_date}}</span></td>
            <td align="center">   
                <span id="each_is_sellable_span">@if($each_issellable=='checked="true"')Yes @else No @endif</span>            
                <label class="switch " id="each_qty_sellable_lable"><input class="switch-input vr_status4"  type="checkbox" {{$each_issellable}} id="each_qty_sellable" name="product_is_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
            </td>
        </tr>
                    

        <tr class="odd gradeX">
        <td >Inner Qty</td>
        <td ><span id="pack_inner_qty">@if(isset($inner_qty)){{$inner_qty}}@endif</span>
</td>
<td><span id="inner_eff_date_span">{{$inner_date}}</span></td>
    <td align="center">
        <span id="inner_is_sellable_span">@if($inner_issellable=='checked="true"')Yes @else No @endif</span>            
        <label class="switch " id="inner_qty_sellable_lable"><input class="switch-input vr_status4"  type="checkbox" {{$inner_issellable}} id="inner_qty_sellable" name="product_is_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
    </td>
    </tr>
    <tr class="odd gradeX">
        <td >CFC Qty</td>
        <td ><span id="pack_cfc_qty">{{$cfc_qty}}</span></td>
        <td><span id="cfc_eff_date_span">{{$cfc_date}}</span></td>
        <td align="center">
            <span id="cfc_is_sellable_span">@if($cfc_issellable=='checked="true"')Yes @else No @endif</span>            
            <label class="switch " id="cfc_qty_sellable_lable"><input class="switch-input vr_status4"  type="checkbox" {{$cfc_issellable}} id="cfc_qty_sellable" name="product_is_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
        </td>
    </tr>
    </tbody>
    
    </table>
              </div>  
      </div>
      </div>
  </div>
  <div class="col-md-4" >
    <div class="portlet-body box_holder">
        <div class="row titlebag">
          <div class="col-md-8">
            <p><strong>Inventory Configuration</strong></p>
          </div>
          @if($warehouse_name!='' || $invetory_soh!='')
          <div class="col-md-4 text-right" style="display:none"><button class="btn btn-link" onclick="editInventoryInfo()" id="edit_inventory_info"><i class="fa fa-pencil" aria-hidden="true"></i></a></button>
            <button class="btn btn-link text-right"  id="update_inventory_info">Update</button>  
            </div>
          @endif

        </div>
        <div class="col-md-12 formmargin">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <div class="form-group row">
              <label class="col-md-5"><strong>DC Name</strong></span></label>
              <div class="col-md-1"><strong>:</strong></div>
               <div class="col-md-6"><span >{{$warehouse_name}}</span></div>
          </div>
           <div class="form-group row">
              <label class="col-md-5"><strong>SOH</strong></span></label>
              <div class="col-md-1"><strong>:</strong></div>
               <div class="col-md-6"><span id="soh_inventory">{{$invetory_soh}}</span></div>
              </div>
           <div class="form-group row">
              <label class="col-md-5"><strong>ATP</strong></label>
              <div class="col-md-1"><strong>:</strong></div>
               <div class="col-md-6" ><span id="atp_inventory">{{$invetory_atp}}</span></div>
              </div>
                    <div class="form-group row">
              <label class="col-md-5"><strong>Available Inventory</strong></label>
              <div class="col-md-1"><strong>:</strong></div>
               <div  class="col-md-6">
               <span id="available_inv">{{$available_inventory}}</span></div>
              </div>
              <div class="form-group row">
              <label class="col-md-5"><strong>Order Qty</strong></label>
              <div class="col-md-1"><strong>:</strong></div>
               <div  class="col-md-6">
               <span id="available_inv">{{$order_qty}}</span></div>
              </div> 
              
              </div>          
      </div>

 
  </div>
  <div class="col-md-4" align="center" >
    <button class="btn btn-sucess text-right"  > <a href="/products">Cancel</a></button>
   </div>
 
 </div>
</div>



@include('Product::freeBieProduct')

  @section('style')
  <style type
"text/css">
#required_color{color:red;}
#quickproduct1{padding-top:15px;}
#quickproduct1 .formmargin{ margin-top:15px;}
#quickproduct1 .formmargin2{ margin-top:0px;}
#quickproduct1 .formmargin2 table{font-size:14px}
#quickproduct1 .formmargin2 table th{ font-weight:bold; white-space: nowrap;padding-top:0px;padding-bottom:0px;}
#quickproduct1 .titlebag{    background-color: #efefef;
    margin: 0px 0px 0px 0px;
    height: 40px;}
#quickproduct1 .titlebag p{ padding-top: 10px;}

#quickproduct1 .rowmarg{ margin-bottom:5px;}
#quickproduct1 .rowmargtop{ padding-top:10px;}
#quickproduct1  lable .col-md-3 .cln{
    float:right;
    margin-left:20px;
    }
    
    #quickproduct1 .pack1{padding:0px;}
    
    #quickproduct1  .switch2 .switch{
    margin-top: -4px !important;
}
#quickproduct1 .formmargin .col-md-1{padding:0px 0px;}
#quickproduct1  li.active a, li.active i {
   /* color: #fff !important;*/
}
#quickproduct1  .pop_holder {
    height: 300px;
    left: -140px !important;
    min-width: 300px;
    overflow: scroll;
}
#quickproduct1 .titlerow{ padding: 10px 0px; border-bottom: 1px solid #efefef; margin-bottom: 15px;}   
  
#quickproduct1 .elastislide-list{max-height:100% !important;}
#quickproduct1 .link_btn{ text-decoration:underline;}
#quickproduct1 .form-group .col-md-1 {
    width: 1%;
}

#quickproduct1 .box_holder{ border: 1px solid #e7ecf1;
    margin-bottom: 15px;
    padding-bottom: 15px;
    overflow: hidden;}
    }
    </style>
@stop
{{HTML::style('css/switch-custom.css')}}
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<!--Ignietui scripts end-->
@section('userscript')
@include('includes.group_repo')
@include('includes.ignite')
@include('includes.validators')

<script src="{{ URL::asset('assets/admin/pages/scripts/product/form-wizard-free-bie-configuration.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/product/freeBieConfigGrid.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/product/freebie_validator.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/product/duplicate_products_validator.js') }}" type="text/javascript"></script>
    <script>
    jQuery(document).ready(function() {
        // initiate layout and plugins
        Layout.init(); // init current layout
        Demo.init(); // init demo features
        FormWizard.init();
    });
</script>
<script type="text/javascript">
var datePickerOptions = {
          dateFormat: 'yy/m/d',
          firstDay: 1,
          changeMonth: true,
          changeYear: true
      }
$(document).ready(function()
    {
        freeBieConfigGrid();
       /* if($("#offer_pack").text()== 'Freebie' || $("#offer_pack").text() =='Consumer Pack Outside')
        {
            freeBieConfigGrid();
        }*/
        var token = $("#csrf-token").val();
        var product_id=$("#product_id").val();
       $("#update_general_info").hide();
       $("#freebie_note").hide();
       $("#price_state").hide();
       $("#mdl_custgroup").hide();
       $("#tax_class").hide();
       $("#tax_span").show();
       $("#update_inventory_info").hide();
       $("#each_qty_sellable_lable").hide();
       $("#cfc_qty_sellable_lable").hide();
       $("#inner_qty_sellable_lable").hide();
       $("#cancel_general_info").hide();
       $("#update_price_info").hide();
       $("#addFreebie_model").hide();
       $("#update_inventory_info").hide();
       $("#update_pack_info").hide();
       var objRegExp  =  "/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/";
       //check when document loaded
        if(($("#product_is_sellable1").prop('checked')==true) && ($("#offer_pack").text()=='Freebie' || $("#offer_pack").text()=='Consumer Pack Outside'))
        {
          $("#addFreebie_model").show();
        }
        if($("#offer_pack").text()!='Freebie' || $("#offer_pack").text()!='Consumer Pack Outside'){
            $("#freebie_note").hide();
        }
        // click on is sellable ajax call
       $("#product_is_sellable1").click(function(){
         var formData = new FormData();
         var is_sellable= ($("#product_is_sellable1").prop('checked')==true)?1:0;
         formData.append('product_id', product_id);   
          formData.append('is_sellable',is_sellable);  
          $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                  method: "POST",
                  url:  '/products/saveProductIsSellable',
                  processData: false,
                  contentType: false,                                             
                  data: formData,
                  success: function (rs)
                  {
                      alert("Successfully updated.");
                  }
              });
          offer_pack_validations();
       });  
       //update inventory 
       $("#update_inventory_info").click  (function(){
            var formData = new FormData();
            var soh=$("#soh_inventory").val();
            var atp=$("#atp_inventory").val();
            formData.append('soh', soh);   
            formData.append('atp',atp);
            
            if(soh!='' && atp!='')
            {
              if((Math.sign(soh) && soh >0) && (Math.sign(atp) || atp >=0))
              {
                $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                  method: "POST",
                  url:  '/products/updateInventory/'+product_id,
                  processData: false,
                  contentType: false,                                             
                  data: formData,
                  success: function (rs)
                  {
                      alert("Successfully updated.");
                      $("#atp_inventory").replaceWith('<span id="atp_inventory">'+rs.atp+'</span>');
                      $("#soh_inventory").replaceWith('<span id="soh_inventory">'+rs.soh+'</span>');
                      $("#available_inv").text(rs.available_inventory);                      
                      $("#edit_inventory_info").show();
                      $("#update_inventory_info").hide();
                  }
              });
              }else
              {
                alert("Please enter numbers/ More than zero.");
              }
            }else
            {
              alert("Please enter required fields");
            }
           
       });
       //click on cp enabled
        $("#product_cp_sellable").click(function(){
         var formData = new FormData();
        var cp_enabled= ($("#product_cp_sellable").prop('checked')==true)?1:0;
         formData.append('ProductId', product_id); 
          formData.append('flag',cp_enabled); 
          $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                  method: "POST",
                  url:  '/products/QuickProductCpStatus',
                  processData: false,
                  contentType: false,                                             
                  data: formData,
                  success: function (rs)
                  {

                    if(rs==1)
                    {
                      alert("Successfully updated.");
                    }else
                    {
                      alert("Please set Tax/ Price/ Is sellable");
                      $("#product_cp_sellable").prop('checked',false);
                    }
                  }
              });
       });  

       //offer pack show add button when we get freebie  
      $(document).on('change',"#offer_pack", function()
        {
          offer_pack_validations();   
        });
       $("#update_general_info").click(function()
        {
            var product_title= $("#view_product_title").val().replace(/\s+/g, '');
            var mrp= $("#mrp").val();
            var esu= $("#esu").val(); 
            var pack_size= $("#pack_size").val();
            var offer_pack= $("#offer_pack").val();
            var shelf_life_uom =$("#shelf_life_uom").val();
            var shelf_life= $("#shelf_life").val();
            var is_sellable='0';
            var cp_enabled=0;
            var decimal=  /^[-+]?[0-9]+\.[0-9]+$/;  
             var numbers=  /[0-9]+$/;  
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
            var formData = new FormData();           
            if($("#product_is_sellable1").prop('checked') == true)
            {
                is_sellable=1;
            }
            if($("#product_cp_sellable").prop('checked') == true)
            {
                cp_enabled=1;
            }
            formData.append('product_title', $("#view_product_title").val()); 
            formData.append('mrp', $("#mrp").val());
            formData.append('esu', $("#esu").val());
            formData.append('pack_size', $("#pack_size").val());
            formData.append('offer_pack', $("#offer_pack").val());
            formData.append('shelf_life_uom', $("#shelf_life_uom").val());
            formData.append('shelf_life', $("#shelf_life").val());
            formData.append('is_sellable', is_sellable);
            formData.append('cp_enabled',cp_enabled);
             
            if(product_title!='' && mrp!='' && esu!='' && pack_size!='' && shelf_life_uom!='' && offer_pack!='' && shelf_life!='')
            {
              if(Math.sign(pack_size) && pack_size > 0 && Math.sign(mrp) && mrp > 0 && Math.sign(esu) && esu>0 && Math.sign(shelf_life) && shelf_life>0)
              {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    method: "POST",
                    url:  '/products/saveProductGeneralInfo/'+product_id,
                    processData: false,
                    contentType: false,                                             
                    data: formData,
                    success: function (rs)
                    {
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                        if(rs=='false')
                        {
                            alert("Please remove freebie products to change offer pack status.");
                        }else
                        {
                            alert("Successfully updated.");
                            $("#update_general_info").hide();
                            $("#edit_general_info").show();
                            $("#view_product_title").replaceWith('<span id="view_product_title">'+$("#view_product_title").val()+'</span>');
                            $("#esu").replaceWith('<span id="esu">'+esu+'</span>');
                            $("#mrp").replaceWith('<span id="mrp">'+mrp+'</span>');
                            $("#pack_size").replaceWith('<span id="pack_size">'+pack_size+'</span>');
                            $("#shelf_life").replaceWith('<span id="shelf_life">'+shelf_life+'</span>');
                            $("#offer_pack").replaceWith('<span id="offer_pack">'+offer_pack+'</span>');
                            $("#shelf_life_uom").replaceWith('<span id="shelf_life_uom">'+$("#shelf_life_uom option:selected").text()+'</span>');
                        }         

                    }
                });
              }else
              {
                alert("Please enter numbers/ More than zero.");
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
              }
              
            }else{
              alert("Please fill required fields.");
              $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
            }
        });
        $("#update_pack_info").click(function()
        {
            var formData = new FormData();
            var pack_each_qty= $("#pack_each_qty").val();
            var pack_inner_qty= $("#pack_inner_qty").val();
            var pack_cfc_qty= $("#pack_cfc_qty").val(); 
            var each_qty_sellable= 0;
            var each_qty_sellable_span='No';
            var inner_qty_sellable= 0;
            var inner_qty_sellable_span='No';
            var cfc_qty_sellable =0;
            var cfc_qty_sellable_span ='No';
            if($("#each_qty_sellable").prop('checked') == true)
            {
                each_qty_sellable=1;
                each_qty_sellable_span='Yes';
            }
             if($("#cfc_qty_sellable").prop('checked') == true)
            {
                cfc_qty_sellable=1;
                cfc_qty_sellable_span='Yes';
            }
             if($("#inner_qty_sellable").prop('checked') == true)
            {
                inner_qty_sellable=1;
                inner_qty_sellable_span='Yes';
            }
            formData.append('pack_each_qty', $("#pack_each_qty").val());
            formData.append('pack_inner_qty', $("#pack_inner_qty").val());
            formData.append('pack_cfc_qty', $("#pack_cfc_qty").val());
            formData.append('each_qty_sellable', each_qty_sellable); 
            formData.append('inner_qty_sellable', inner_qty_sellable);
            formData.append('cfc_qty_sellable', cfc_qty_sellable);
            formData.append('each_date', $("#each_date").val()); 
            formData.append('inner_date', $("#inner_date").val());
            formData.append('cfc_date', $("#cfc_date").val());
            if($("#pack_cfc_qty").val()!='' && $("#pack_inner_qty").val()!='' && $("#pack_each_qty").val() && $("#each_date").val()!='' && $("#inner_date").val()!='' && $("#cfc_date").val()!='')
            {
               if((Math.sign(pack_inner_qty) && pack_inner_qty >0 ) && (Math.sign(pack_each_qty) && pack_each_qty >0) && (Math.sign(pack_cfc_qty) && pack_cfc_qty >0) )
               {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    method: "POST",
                    url:  '/products/saveProductPackInfo/'+product_id,
                    processData: false,
                    contentType: false,                                             
                    data: formData,
                    success: function (rs)
                    {
                        alert("Successfully updated.");
                        $("#edit_pack_info").show();
                        $("#update_pack_info").hide();
                        $("#pack_each_qty").replaceWith('<span id="pack_each_qty">'+pack_each_qty+'</span>');
                        $("#pack_inner_qty").replaceWith('<span id="pack_inner_qty">'+pack_inner_qty+'</span>');
                        $("#pack_cfc_qty").replaceWith('<span id="pack_cfc_qty">'+pack_cfc_qty+'</span>');
                        $("#each_date").replaceWith('<span id="each_eff_date_span">'+$("#each_date").val()+'</span>');
                        $("#cfc_date").replaceWith('<span id="cfc_eff_date_span">'+$("#cfc_date").val()+'</span>');
                        $("#inner_date").replaceWith('<span id="inner_eff_date_span">'+$("#inner_date").val()+'</span>');
                        $("#each_is_sellable_span").text(each_qty_sellable_span);
                        $("#inner_is_sellable_span").text(inner_qty_sellable_span);
                        $("#cfc_is_sellable_span").text(cfc_qty_sellable_span);
                        $("#each_is_sellable_span").show();
                        $("#inner_is_sellable_span").show();
                        $("#cfc_is_sellable_span").show();
                        $("#cfc_qty_sellable_lable").hide();
                        $("#inner_qty_sellable_lable").hide();
                        $("#each_qty_sellable_lable").hide(); 
                    }
                });
              }else{
                alert("Please enter numbers/ more than zero.");
              }
            }else{
              alert("Please enter required fields.");
            }
            
        });
        $("#update_price_info").click(function()
        {
            
            var formData = new FormData();
            var price_esp= $("#price_esp").val();
            var price_mrp= $("#price_mrp").val();
            var price_ptr= $("#price_ptr").val();
            var price_product_id= $("#price_product_id").val();
            var mrp= $("#mrp").text(); 
          ///  var price_esu= $("#price_esu").val();
            var price_state= $("#price_state").val();
            var mdl_custgroup=$("#mdl_custgroup").val();
             var tax_class=$("#tax_class").val();
             var price_effective_data=$("#price_effective_data").val();
            var is_apply=0;
            formData.append('price_esp', $("#price_esp").val());
            formData.append('price_product_id', price_product_id);
            formData.append('price_mrp', $("#price_mrp").val());
            formData.append('price_ptr', $("#price_ptr").val());
            formData.append('price_effective_data', $("#price_effective_data").val());
            formData.append('price_state', $("#price_state").val());
            formData.append('mdl_custgroup', $("#mdl_custgroup").val());
            formData.append('tax_class', $("#tax_class").val());
            if(price_esp!=''  && price_ptr!='' && price_effective_data!='' && price_state!='' && mdl_custgroup!='no' && tax_class!='')
            {
              if((Math.sign(price_esp) && price_esp >0 && parseInt(mrp)>=price_esp  ) && (Math.sign(price_ptr) && price_ptr >0 && parseInt(mrp)>=price_ptr ))
              {
                  if((Math.sign(price_esp) && price_esp >0 && parseInt(price_ptr)>=price_esp  ))
                  {
                      $.ajax({
                      headers: {'X-CSRF-TOKEN': token},
                      method: "POST",
                      url:  '/products/saveProductPriceInfo/'+product_id,
                      processData: false,
                      contentType: false,                                             
                      data: formData,
                      dataType:"json",
                      success: function (rs)
                      {
                         alert("Successfully updated.");
                          $("#state_span").show();
                          $("#tax_span").show();
                          $("#customer_group_span").show();
                         $("#state_span").html($("#price_state option:selected").text());
                          $("#customer_group_span").text(rs.customer_type);
                          $("#tax_span").text(rs.tax_class);
                         $("#price_product_id").val(rs.price_product_id);
                          $("#update_price_info").hide();
                          $("#editPriceInfo_id").show(); 
                          $("#mdl_custgroup").hide();
                          $("#price_state").hide();                         
                          $("#tax_class").hide();
                          $("#price_esp").replaceWith('<span id="price_esp">'+price_esp+'</span>');
                          $("#price_ptr").replaceWith('<span id="price_ptr">'+price_ptr+'</span>');
                          $("#price_effective_data").replaceWith('<span id="price_effective_date_span">'+ $("#price_effective_data").val()+'</span>');
                          
                      }
                  });
                  }
                  else
                  {
                     alert("SP value must be lessthan /equal to PTR.");
                  }
                }else{
                  alert("PTR/ SP must be greaterthan zero/ less than are equal to MRP.");
                } 
            }
            else
            {
              alert("Please fill required fields.");
            }

           
        });
    });
</script>
<script>
   function offer_pack_validations()
   {
     if($("#product_is_sellable1").prop('checked')==true)
          {
            $("#freebie_note").hide();
            if($("#offer_pack").text()=='Freebie' || $("#offer_pack").text()=='Consumer Pack Outside')
            {
              $("#addFreebie_model").show();
            }
            else if($("#offer_pack").val()=='Freebie' || $("#offer_pack").val()=='Consumer Pack Outside')
            {
              $("#addFreebie_model").show();
            }else
            {
              $("#addFreebie_model").hide();
            }
            
          }else
          {
             if($("#offer_pack").text()=='Freebie' || $("#offer_pack").text()=='Consumer Pack Outside')
             {
                $("#freebie_note").show();
             }else if($("#offer_pack").val()=='Freebie' || $("#offer_pack").val()=='Consumer Pack Outside')
             {
                $("#freebie_note").show();
             }else
             {
                $("#freebie_note").hide();
             }
            $("#addFreebie_model").hide();
            
          }
   }

    function editGeneralInfo()
    {
        

            $("#edit_general_info").hide();
            $("#cancel_general_info").show();
            $("#update_general_info").show();
            var token = $("#csrf-token").val();
            var product_title= $("#view_product_title").text();
            var mrp= $("#mrp").text();
            var esu= $("#esu").text(); 
            var pack_size= $("#pack_size").text();
            var offer_pack= $("#offer_pack").text();
            var shelf_life_uom =$("#shelf_life_uom").text();
            var shelf_life= $("#shelf_life").text();
            /*$("#update_general_info").show();
            if($("#offer_pack").val()== 'Freebie' || $("#offer_pack").val() =='Consumer Pack Outside')
            {
                $("#addFreebie_model").show();
            }
            else
            {
                $("#addFreebie_model").hide();
            }*/
            $("#view_product_title").replaceWith('<input type="text" id="view_product_title" class="form-control" value="'+product_title+'"/>');
            $("#esu").replaceWith('<input type="text" id="esu" class="form-control" value="'+esu+'"/>');
            $("#mrp").replaceWith('<input type="text" id="mrp" class="form-control" value="'+mrp+'"/>');
            $("#pack_size").replaceWith('<input type="text" id="pack_size" class="form-control" value="'+pack_size+'"/>');
            $("#shelf_life").replaceWith('<input type="text" id="shelf_life" class="form-control" value="'+shelf_life+'"/>');              
            
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
            $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: '/products/getOfferPackData',
                    async: true,
                    type: 'GET',
                    success: function (rs)
                    {
                        var offer_pack_html='<option value="">Please Select...</option>';
                        $.each(rs, function (k, v) 
                        {
                            if(v.name==offer_pack)
                            {
                                
                                offer_pack_html+= '<option value="'+v.name+'" selected>'+v.name+'</option>';
                            }else
                            {
                                offer_pack_html+= '<option value="'+v.name+'">'+v.name+'</option>';
                            }                        
                        });    
                        $("#offer_pack").replaceWith('<select name="offer_pack" id="offer_pack" class="form-control select2me">'+offer_pack_html+'</select>');
                         $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);                    
                    }
            });
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
            $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: '/products/getShelfLifeUOMdata',
                    async: true,
                    type: 'GET',
                    success: function (rs)
                    {
                        var shelf_life_html='<option value="">Please Select...</option>';
                        $.each(rs, function (k, v) 
                        {
                            if(v.name == shelf_life_uom)
                            {
                                shelf_life_html+= '<option value="'+v.value+'" selected>'+v.name+'</option>';
                            }else
                            {
                                shelf_life_html+= '<option value="'+v.value+'">'+v.name+'</option>';
                            }                        
                        });    
                        $("#shelf_life_uom").replaceWith('<select name="shelf_life_uom" id="shelf_life_uom" class="form-control select2me">'+shelf_life_html+'</select>');
                         $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);                    
                    }
            });
            
           
    }

    function editPriceInfo()
    {
      $('body').on('focus',"#price_effective_data", function(){
        $(this).datepicker({minDate: 0,"autoclose": true,dateFormat: 'dd/mm/yy', constrainInput: true});
      });

        $("#update_price_info").show();
        $("#price_state").show();
        $("#state_span").hide();
        $("#mdl_custgroup").show();
        $("#customer_group_span").hide();
        $("#editPriceInfo_id").hide();
        $("#tax_span").hide();
        $("#tax_class").show(); 
        var token = $("#csrf-token").val();
        var price_esp= $("#price_esp").text();
         var price_effective_data1= $("#price_effective_date_span").text();
        var price_effective_data= $("#price_effective_date_span").text();
        split_date=price_effective_data.split('/');
        var TodayDate = new Date();
        var endDate= new Date(Date.parse(split_date[1]+'/'+split_date[0]+'/'+split_date[2]));


         if (endDate< TodayDate ) 
          {
          var today = new Date();
          var dd = today.getDate();
          var mm = today.getMonth()+1; //January is 0!

          var yyyy = today.getFullYear();
          if(dd<10){
              dd='0'+dd
          } 
          if(mm<10){
              mm='0'+mm
          } 
          price_effective_data= dd+'/'+mm+'/'+yyyy;
          }
        if(price_effective_data=='')
        {
         
          if($.trim($("#price_effective_date_span").text()==''))
          {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!

            var yyyy = today.getFullYear();
            if(dd<10){
                dd='0'+dd
            } 
            if(mm<10){
                mm='0'+mm
            } 
            price_effective_data= dd+'/'+mm+'/'+yyyy;
            price_effective_data1=price_effective_data;
          }     

        }
          var price_mrp= $("#price_mrp").text();
        //var price_esu= $("#price_esu").text();
      
        var price_ptr= $("#price_ptr").text(); 
        $("#update_price_info").show();
        $("#price_esp").replaceWith('<input type="text" id="price_esp" class="form-control" value="'+price_esp+'"/>');
        $("#price_mrp").replaceWith('<input type="text" id="price_mrp" class="form-control" value="'+price_mrp+'"/>');
        $("#price_effective_date_span").replaceWith('<input type="text" id="price_effective_data" readonly="true" class="form-control" value="'+price_effective_data1+'"/>');
        
        /*$("#price_esu").replaceWith('<input type="text" id="price_esu" class="form-control" value="'+price_esu+'"/>');*/
        $("#price_ptr").replaceWith('<input type="text" id="price_ptr" class="form-control" value="'+price_ptr+'"/>');
    }
    function editPackInfo()
    {
        $("#edit_pack_info").hide();
        $("#update_pack_info").show();
        $("#each_is_sellable_span").hide();
        $("#inner_is_sellable_span").hide();
        $("#cfc_is_sellable_span").hide();
        $("#cfc_qty_sellable_lable").show();
        $("#inner_qty_sellable_lable").show();
        $("#each_qty_sellable_lable").show();

        var pack_each_qty= $("#pack_each_qty").text();
        var pack_inner_qty= $("#pack_inner_qty").text();
        var pack_cfc_qty= $("#pack_cfc_qty").text();
        $("#pack_each_qty").replaceWith('<input type="text" id="pack_each_qty" class="form-control" value="'+pack_each_qty+'"/>');
        $("#pack_inner_qty").replaceWith('<input type="text" id="pack_inner_qty" class="form-control" value="'+pack_inner_qty+'"/>');
        $("#pack_cfc_qty").replaceWith('<input type="text" id="pack_cfc_qty" class="form-control" value="'+pack_cfc_qty+'"/>');
        $("#each_eff_date_span").replaceWith('<input type="text" id="each_date" readonly="true" class="form-control" value="'+$("#each_eff_date_span").text()+'"/>');
        $("#inner_eff_date_span").replaceWith('<input type="text" id="inner_date" readonly="true" class="form-control" value="'+$("#inner_eff_date_span").text()+'"/>');
        $("#cfc_eff_date_span").replaceWith('<input type="text" id="cfc_date" readonly="true" class="form-control" value="'+$("#cfc_eff_date_span").text()+'"/>');
        $('body').on('focus',"#cfc_date", function(){
        $(this).datepicker({minDate: 0,"autoclose": true,dateFormat: 'dd/mm/yy','setDate': new Date($("#cfc_eff_date_span").text())});
      });
        $('body').on('focus',"#inner_date", function(){
        $(this).datepicker({minDate: 0,"autoclose": true,dateFormat: 'dd/mm/yy'});
      });
        $('body').on('focus',"#each_date", function(){
        $(this).datepicker({minDate: 0,"autoclose": true,dateFormat: 'dd/mm/yy'});
      });

    }
    function changeTaxClassByState()
    {
        var state_id=$("#price_state").val();
        var token = $("#csrf-token").val();
        tax_html="<option value=''>Please select</option>";
        if(state_id!='')
        {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url: '/product/getTaxClassDropDown/'+state_id,
                async: false,
                type: 'GET',
                success: function (rs)
                {
                    if(rs!="")
                    {
                         $.each(rs, function (k, v) 
                        {
                            tax_html+= '<option value="'+v.tax_class_id+'" >'+v.tax_class_code+'</option>';
                                                 
                        });
                         console.log(tax_html);
                         $("#tax_class").html(tax_html);

                    }else
                    {
                        $("#tax_class").html(tax_html);
                    }
                    
                }
            });
        }else
        {
            $("#tax_class").html(tax_html);
        }
    }
    function editInventoryInfo()
    {
        $("#edit_inventory_info").hide();
        $("#update_inventory_info").show();
        var soh_inventory=$("#soh_inventory").text();
        var atp_inventory=$("#atp_inventory").text();
        $("#atp_inventory").replaceWith('<input type="text" id="atp_inventory" class="form-control" value="'+atp_inventory+'"/>');
        $("#soh_inventory").replaceWith('<input type="text" id="soh_inventory" class="form-control" value="'+soh_inventory+'"/>');

    }
   
</script>
@stop

@stop   
@extends('layouts.footer')