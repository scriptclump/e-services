@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="alerts alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flash_message"></span>
</div>
<span id="success_flash_message"></span>
<span id="danger_flash_message"></span>
<span id="work_flow_message"></span>
<?php
$bp = url('uploads/brand_logos');
$productMedia = url('uploads/products');
$base_path = $bp . "/";
//$brand_imag= $productData->get_brand_model->logo_url;
?>

<!-- alert message for price -->
<span id="success_message_ajax"></span>
<div class="col-md-12 col-sm-12 ">

    <div class="row">
        <div class="portlet light tasks-widget ">

            <div class="portlet-title">
                <div class="caption">  
                    <form  method="post" id="edit_product_form_id">
                        <input type="text" class="form-control input-xlarge" name="product_title" id="product_title" value="@if(isset($productData->product_title)){{$productData->product_title}}@endif"/>
                         <input type="hidden" id="getcategory_id" value="{{$category_id}}"> 

                </div>
                <div class="caption col-lg-3 text-center"> <div class=" form-group"> {{trans('products.edit.artical_num')}} <span>:</span> <span class="numnerstyle">{{$productData->sku}}<span> </div></div>
                <div class="caption col-lg-2 text-center"> <div class=" form-group"> HSN Code <span>:</span> <span class="numnerstyle">{{$hss_code}}<span> </div></div>
                @if($duplicate_product_permissions == 1)    
                <a class="btn green-meadow" id="duplicateproduct" data-toggle="modal" href="#duplicate_prd" style="margin-top:10px;">Create Duplicate Product</a>
                @endif
                @if($product_mobile_view)  
                <a class="btn green-meadow" id="productcache" data-toggle="modal" href="#prd_cache" style="margin-top:10px;">Product Mobile View</a>
                @endif
                                <div class="actions col-md-1 text-right">
                                    <a href="javascript:void(0)" class="click" data-id="pop1">
                                        <i class="fa fa-comment-o" aria-hidden="true"></i>
                                    </a>
                                    (<span id="comments_count"></span>)

                                    <div class=" pop_holder" style="left: -228px !important;
                                         position: absolute;" id="pop1" >

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

                                <!-- <div class="col-md-2 text-left" style="margin-top:8px;margin-left:0px;">Comment</div>
                                -->
                                </div>

                                <div class="row">

                                    @include('Product::productImageGallery')

                                    <input type="hidden" name="product_id" id="product_id" value="{{$product_id}}" />
                                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input id="productStatus" type="hidden" name="productStatus" value="">



                                    <div class="col-md-8 col-xs-12 ">
                                        <div class="portlet rowhol">
                                            <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>{{trans('products.edit.category')}} </strong></div>
                                                <div class="col-md-8">@if(isset($category_link)) {{$category_link}}@endif</div>
                                            </div>
                                            <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>{{trans('products.edit.manufacturer')}} </strong></div>
                                                <div class="col-md-8">
                                                    <input type="hidden" name="getManfId" id="getManfId" value="@if(isset($manufacturer_name)){{$manufacturer_name}}@endif">
                                                    <select name="manufacturer_name" id="manufacturer_name" class="form-control select2me">
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>{{trans('products.edit.brand')}} </strong></div>
                                                <div class="col-md-8">
                                                    <input type="hidden" name="getBrandId" id="getBrandId" value="@if(isset($productData->brand_id)){{$productData->brand_id}}@endif">



                                                    <select class="form-control select2me" id="brand_id" name="brand_id">

                                                    </select>
                                                    <span id="showLoader" style="display:none;position: relative;left: -5%;top: -27px;">
                                                        <img src="../img/ajax-loader2.gif">
                                                    </span>                 
                                                </div>
                                            </div> 
                                             <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>Product Group Name </strong></div>
                                            @if($supplier_login_permissions == 1)
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="hidden" name="product_group_id" id="product_group_id"  value="@if(isset($product_group_data['product_grp_id'])){{ $product_group_data['product_grp_id']}}@endif" />
                                                        

                                                        <select disabled class="form-control select2me" id="product_group" name="product_group" disabled>
                                                        </select> 
                                                    </div>
                                                </div>
                                            </div>
                                            @elseif($pro_grp_add_feature == 1 && $pro_grp_edit_feature == 1)
                                             <div class="col-md-8">

                                                <div class="row">
                                                    <div class="col-md-10">
                                                       
                                                        <input type="hidden" name="product_group_id" id="product_group_id"  value="@if(isset($product_group_data['product_grp_id'])){{ $product_group_data['product_grp_id']}}@endif" />
                                                        

                                                        <select class="form-control select2me" id="product_group" name="product_group">
                                                        </select> 
                                                    </div>
                                                   <div class="col-md-1" style="margin-left:-23px !important;">
                                                         <button type="button" class="btn green-meadow" id="addProductGroup_id" data-toggle="modal" href="#addProduct_group_model"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button  type="button" class="btn green-meadow" id="editProductGroup_id" data-toggle="modal" href="#editProduct_group_model"> <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>

                                            </div>
                                            @elseif($pro_grp_edit_feature == 1)
                                            <div class="col-md-8">

                                                <div class="row">
                                                    <div class="col-md-11">
                                                       
                                                        <input type="hidden" name="product_group_id" id="product_group_id"  value="@if(isset($product_group_data['product_grp_id'])){{ $product_group_data['product_grp_id']}}@endif" />
                                                        

                                                        <select class="form-control select2me" id="product_group" name="product_group">
                                                        </select> 
                                                    </div>
                                                    <div class="col-md-1"  style="margin-left:-23px !important;">
                                                        <button  type="button" class="btn green-meadow" id="editProductGroup_id" data-toggle="modal" href="#editProduct_group_model"> <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>

                                            </div>
                                            @elseif($pro_grp_add_feature == 1)
                                             <div class="col-md-8">

                                                <div class="row">
                                                    <div class="col-md-11">
                                                       
                                                        <input type="hidden" name="product_group_id" id="product_group_id"  value="@if(isset($product_group_data['product_grp_id'])){{ $product_group_data['product_grp_id']}}@endif" />
                                                        

                                                        <select class="form-control select2me" id="product_group" name="product_group">
                                                        </select> 
                                                    </div>
                                                    <div class="col-md-1" style="margin-left:-23px !important;">
                                                         <button type="button" class="btn green-meadow" id="addProductGroup_id" data-toggle="modal" href="#addProduct_group_model"> <i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                             <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="hidden" name="product_group_id" id="product_group_id"  value="@if(isset($product_group_data['product_grp_id'])){{ $product_group_data['product_grp_id']}}@endif" />
                                                        

                                                        <select class="form-control select2me" id="product_group" name="product_group" disabled>
                                                        </select> 
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            </div> 
                                            <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>{{trans('products.edit.kvi')}} </strong></div>
                                                <input type="hidden" id="kvi_id" value="{{$productData->kvi}}"> 
                                                <div class="col-md-8"><select class="form-control select2me" name="kvi_name" id="kvi_name">
                                                    @if($supplier_login_permissions == 1)
                                                        <option value="69001">Regular </option>
                                                        <option value="69010">Freebie </option>
                                                    @else
                                                        @foreach($kvi_data as $kviValue)
                                                        <option value="{{$kviValue->value}}">{{$kviValue->name}}  </option>
                                                        @endforeach
                                                    @endif
                                                    </select></div>
                                            </div> 
                                            @if($supplier_login_permissions == 0)
                                            <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>{{trans('products.edit.manufacturer_sku')}} </strong></div>
                                                <div class="col-md-8"><input type="text" class="form-control" name="product_sku" id="product_sku" value="@if(isset($productData->seller_sku)){{ $productData->seller_sku}}@endif"/></div>
                                            </div> 
                                            @endif
                                            <div class="row rowmarg">
                                                <div class="col-md-4"><strong>{{trans('products.edit.mrp')}}</strong></div>
                                                <div class="col-md-8"><input type="text" class="form-control" name="product_mrp" id="product_mrp" value="@if(isset($productData->mrp)){{ $productData->mrp}}@endif"/></div>
                                            </div>
											@if($esuPermission==1)
                                             <div class="row rowmarg">
                                                <div class="col-md-4"><strong>{{trans('headings.SU')}}</strong></div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" min="0" name="product_esu" id="product_esu" value="@if(isset($productData->esu)){{ $productData->esu}}@endif"/>      
                                                </div>
                                                <div class="col-md-3">
                                                
                                                    <button id="update_esu_for_alldcs" name="update_esu_for_alldcs" class="btn blue">Update ESU for all DC's</button>      
                                                </div>
                                            </div>
                                            @else
                                            <input type="hidden" class="form-control" name="product_esu" id="product_esu" value="@if(isset($productData->esu)){{ $productData->esu}}@endif"/> 
                                            @endif 
											
                                            @if($supplier_login_permissions == 0)
                                                <div class="row rowmarg">
                                                    <div class="col-md-4"><strong>Star:</strong></div>
                                                    <div class="col-md-8">
                                                        <select class="form-control select2me" name="product_star" id="product_star">
                                                            @foreach($product_star as $starValue)
                                                                @if($productData->star==$starValue->value)
                                                                    <option value="{{$starValue->value}}" selected="selected">{{$starValue->name}} </option>
                                                                @else
                                                                    <option value="{{$starValue->value}}">{{$starValue->name}} </option>
                                                                @endif
                                                            @endforeach
                                                        </select>                                            
                                                    </div>
                                                </div>
                                            @endif 

											 @if($sellablePermissions==1)
                                             <div class="row rowmarg">
                                                <div class="col-md-4"><strong>Is Sellable:</strong></div>
                                                <div class="col-md-3">
                                                    @if(isset($productData->is_sellable) && $productData->is_sellable==1)
                                                     <label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="product_is_sellable" checked="true" name="product_is_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
                                                    @else
                                                     <label class="switch "><input class="switch-input vr_status4"  type="checkbox" check="false" id="product_is_sellable" name="product_is_sellable" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
                                                    @endif                                                   
                                                </div>
                                                <div class="col-md-3">
                                                <button id="update_sellablests_for_alldcs" name="update_sellablests_for_alldcs" class="btn blue">Update Is Sellable status for all DC's</button>
                                                </div> 
                                            </div>
											@endif
                                            <input type="hidden" id="product_is_sellble" value="{{$productData->is_sellable}}"/>
											@if($cpPermissions==1 && $productData->status == 1)
                                            <div class="row rowmarg">
                                                <div class="col-md-4"><strong>CP Enabled:</strong></div>
                                                <div class="col-md-3">
                                                    @if(isset($productData->cp_enabled) && $productData->cp_enabled==1)
                                                    <label class="switch "><input class="switch-input product_cp_enabled"  data_product_id = '{{$productData->product_id}}'  data_product_tax = '{{$tax}}'  data_product_name = '{{$productData->product_title}}' 
                                                                                 data_product_pricing = '{{$pricing}}' data_is_sellable = '{{$productData->is_sellable}}'  type="checkbox" check="false" id="product_cp_enable{{$productData->product_id}}" checked="true" name="product_cp_enabled" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
                                                    @else
                                                    <label class="switch "><input class="switch-input product_cp_enabled"  data_product_id = '{{$productData->product_id}}'  data_product_tax = '{{$tax}}' data_product_name = '{{$productData->product_title}}' 
                                                                                   data_product_pricing = '{{$pricing}}' data_is_sellable = '{{$productData->is_sellable}}'  type="checkbox" check="false" id="product_cp_enable{{$productData->product_id}}" name="product_cp_enabled" ><span class="switch-label " data-on="Yes"  data-off="No"></span><span class="switch-handle"></span></label>
                                                    @endif                                                   
                                                </div>
                                                <div class="col-md-3">
                                                <button id="update_cpsts_for_alldcs" name="update_cpsts_for_alldcs" class="btn blue">Update CP status for all DC's</button>
                                                </div> 
                                            </div> 
                                            @endif
                                            
                                            
                                            <div class="row rowmarg form-group">
                                                <div class="col-md-4"><strong>{{trans('products.edit.product_description')}}</strong></div>
                                                <div class="col-md-8"><textarea rows="3" name="product_description" id="product_description" >@if(isset($productData->product_content_model->description)){{$productData->product_content_model->description}}@endif</textarea> </div>
                                            </div>  




                                        </div>
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
                                                        @include('Product::editProductAttributes')  
                                                        </form>
                                                    </div>

                                                    <div class="tab-pane" id="tab_15_2">
                                                        <div class="portlet">
                                                            <div class="portlet-body ">
                                                                @include('Product::relatedProducts')
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
                                                                    <div class="col-md-12 text-right">
                                                                        <button  type="button" class="btn green-meadow" id="addFreebie_model" data-toggle="modal" href="#addFreeBie"> ADD</button>
                                                                    </div>
                                                                </div>
                                                                @include('Product::freeBieProduct')

                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <table id="freeBieConfigGrid"></table>
                                                                    </div>
                                                                </div>


                                                            </div>



                                                        </div>           
                                                    </div>          

                                                    <div class="tab-pane" id="tab_15_3">
                                                        <div class="portlet">

                                                            <div class="portlet-body form">


                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button  type="button" class="btn green-meadow" id="packConfig" data-toggle="modal" href="#addpacking"> ADD</button>
                                                                    </div>
                                                                </div>

                                                                @include('Product::packageConfiguration')

                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <table id="packingConfigGrid"></table>
                                                                    </div>
                                                                </div>


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
                                                        <!-- <table id="productmappingdetailss" class='productmappingdetailss'></table> -->         
                                                    </div>

                                                    <div class="tab-pane" id="tab_15_5">
                                                        <div class="portlet">
                                                            <div class="portlet-body">
                                                                <div class="table-scrollable">
                                                                    <table id="productSuppliersGrid"></table>
                                                                </div></div></div>                         
                                                    </div>
                                                    
                                                    <div class="tab-pane" id="promotion_tab">
                                                        <div class="row">
                                                            <div class="col-md-12 text-right">
                                                                <a href="#" data-id="#" data-toggle="modal" data-target="#save_price" class="btn green-meadow">Add Price</a>
                                                            </div>
                                                        </div>
                                                        <table id="slabprices"></table>                       
                                                    </div>
                                                     <div class="tab-pane" id="warehouse_config">
                                                        <div class="row">
                                                            <div class="col-md-12 text-right">
                                                                <a href="#" data-id="#" data-toggle="modal" data-target="#add_bin_config" id="warehouse_config_id" class="btn green-meadow">Warehouse Configuration</a>
                                                            </div>
                                                        </div>
                                                        @include('Product::warehouse_config')
                                                        <table id="product_wh_config"></table>                       
                                                    </div>

                                                    <div class="tab-pane" id="tab_15_7">
                                                        <div class="row"><div class="col-lg-12 histhead" >  
                                                                <div class="col-md-1"> <b>User</b></div>
                                                                <div class="col-md-2">  </div>  
                                                                <div class="col-md-2"> <b> Date</b></div>
                                                                <div class="col-md-3"> <b>Status</b></div>
                                                                <div class="col-md-3"><b>Comments</b></div></div>   </div>  

                                                        <div class="timeline" >
                                                            @if(isset($history))
                                                            @foreach($history as $historyVal )
                                                            <?php
                                                            $url = public_path();
                                                            if (file_exists($url . $historyVal['profile_picture']) && $historyVal['profile_picture'] != '') {
                                                                $img = $historyVal['profile_picture'];
                                                            } else {
                                                                $bp = url('uploads/LegalEntities/profile_pics');
                                                                $base_path = $bp . "/";
                                                                $img = $base_path . "avatar5.png";
                                                            }
                                                            ?>
                                                            <div class="timeline-item timline_style">
                                                                <div class="timeline-badge">
                                                                    <img class="timeline-badge-userpic" src="{{$img}}">
                                                                </div>

                                                                <div class="timeline-body">

                                                                    <div class="row">
                                                                        <div class="col-md-2"> <p>{{ucwords($historyVal['firstname']).' '.ucwords($historyVal['lastname'])}}
                                                                                <span>{{$historyVal['name']}}</span></p>  </div> 
                                                                        <div class="col-md-2"><?php echo date('d/m/Y h:i A', strtotime($historyVal['created_at'])); ?></div> 
                                                                        <div class="col-md-3 push_right">{{$historyVal['master_lookup_name']}}</div>                
                                                                        <div class="col-md-3 push_right" style="width: 350px;word-wrap: break-word;">{!! $historyVal['awf_comment'] !!}</div></div>                
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


                                                    <div class="tab-pane" id="productPacksTab">
                                                        <div class="portlet">                              
                                                            <div class="portlet-body">
                                                                @include('Product::productPacks')
                                                            </div>
                                                        </div>           
                                                    </div>


                                                    <div class="tab-pane" id="cpenable">
                                                        <div class="portlet">                              
                                                            <div class="portlet-body">
                                                                @include('Product::cpenableProducts')
                                                            </div>
                                                        </div>           
                                                    </div>
                                                    <div class="tab-pane" id="product_elp_history">
                                                        <div class="portlet">                              
                                                            <div class="portlet-body">
                                                                @include('Product::productElpHistory')
                                                            </div>
                                                        </div>           
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
                                </div>  

                                <div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h4 class="modal-title">{{trans('products.edit.upload_img')}}</h4>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">
                                                    <div class="col-md-12">

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <form action="/saveproductimages/{{$product_id}}" class="dropzone" id="my-dropzone">
                                                                    <div class="col-md-3 box_hold dz-preview dz-processing dz-success dz-image-preview" >
                                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                                                                        <i class="fa fa-cloud-upload" style="font-size:40px;" aria-hidden="true"></i>
                                                                        <p>{{trans('products.edit.upload_img')}}</p>    
                                                                    </div>

                                                                </form>
                                                            </div>
                                                        </div>

                                                        <div class="row" style="border-top:1px solid #eeeeee; margin-top:20px; padding-top:10px;"><div class="col-md-12"><label>Add  image url</label></div>
                                                            <div class="col-md-12"> 

                                                                <div class="col-md-8">
                                                                    <div class="row">
                                                                        <input id="images" class="form-control urlimage_preview" type="text" name="img">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="row">
                                                                        <button class="btn btn-success urlimage">Add Image</button>
                                                                    </div>
                                                                </div> 
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="preview">
                                                                            <img src="" class="preview-image"   id="preview_image" alt="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                              



                                                    </div>       



                                                </div></div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                            </div>

                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>

                                <div id="duplicate_prd" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h4 class="modal-title">Create Duplicate Product</h4>
                                            </div>
                                            <div class="modal-body">
                                                <br>
                                                <form id="duplicate_product" action="">
                                                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                                                    <input id="pid" type="hidden" name="pid" value="">
                                                    <br>
                                                    <div class="row">
                                                        <label class="col-md-12">Product title</label>
                                                        <div class="col-md-12 " align="center">
                                                            <input type="textarea" class="form-control" name="title" id="title" value=""/>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row text-center">
                                                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                                        <button type="submit" id="create_duplicate" name="create_duplicate" class="btn blue import_save">Create Duplicate Product</button>
                                                    </div>
                                                </form>
                                                <input type="hidden" name="vat_state_wise_tax_classes" id="vat_state_wise_tax_classes" data-vat_state_wise_tax_classes="{{ $state_wise_tax_classes }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div id="prd_cache" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="dismiss_model_popup"></button>
                                                <h4 class="modal-title">Mobile View</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="product_cache_warehouse">
                                                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <select class="select2me form-control" id="mobile_view_dcid" name="mobile_view_dcid">
                                                                <option value=''>Please Select DC</option>
                                                            @foreach($dcs as $dc)
                                                                    <option  value="{{$dc->le_wh_id}}">
                                                                        {{$dc->display_name}}
                                                                    </option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="select2me form-control" id="mobile_customer_group" name="mobile_customer_group">
                                                                <option value=''>Please Select Customer Type</option>
                                                            @foreach($getCustomerGroup as $customerData)
                                                            <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                        <!-- <div class="col-md-6 row text-center">
                                                            <button type="button" id="mobile_view" name="mobile_view" class="btn blue mobile_view">Mobile View</button>
                                                        </div> --><br/>
                                                        <label class="col-md-12"><b>@if(isset($productData->product_title)){{$productData->product_title}}@endif</b></label>
                                                        
                                                    </div>
                                                    
                                                    <div id="replace_div_mobile_view">
                                                        
                                                    </div>
                                                    <br>
                                                    <div class="row text-center">
                                                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                                        <button type="button" id="product_cache_flush" name="product_cache_flush" class="btn blue product_cache_flush">Product Cache Flush</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
 
                @include('Product::totprice')

                                <div class="addEditPriceSection">
                                    @include('Pricing::addEditPriceSection')
                                </div>
                                @include('Tax::taxmapHorizontalGrid')
                                @include('Inventory::inventoryupdate-popup')
                                @include('Product::addGroupedProduct')
                                
                                <br/>
                                @stop
                                {{HTML::style('css/switch-custom.css')}}
@section('style')
                                <style type="text/css">
								.right-align-labels {    position: absolute;    right: 17px;    bottom: 50px;    color: blue;}
                                    /*.taxTypes tbody tr td{height:50px !important;}*/

                                    .tabbable-line > .nav-tabs > li {
                                        margin-right: 15px !important;
                                    }
                                    .rowmarg{ margin-bottom:15px; padding-bottom:15px;}


                                    li.active a, li.active i {
                                        /* color: #fff !important;*/
                                    }

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
                                     .preview_image1 { height: 30px; width: 20px;border:2px solid #c3c3c3 !important; }

                                    .icon-jfi-cloud-up-o{font-size:64px !important; color:#337bb6;}
                                    .zoomimgheight{ height:250px; width:100%; margin-bottom:10px;}


                                    .del-set-actions{ margin-left:0px; margin-bottom:5px;  margin-top:0px; background:#efefef; width:100px; padding:5px 0px;}   
                                    .arrowdisable{
                                        opacity:0.3;}

                                    .titlerow{ padding: 10px 0px; border-bottom: 1px solid #efefef; margin-bottom: 15px;}   

                                    #taxTypes{width: 100% !important;  }
                                    .grp_width{
                                        width: 424px !important;
                                    }

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
                                @stop

                                <link href="{{ URL::asset('assets/global/plugins/slider/es-cus.css') }}" rel="stylesheet" type="text/css" />

                                <link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
                                <!-- BEGIN PAGE LEVEL STYLES -->

                                <link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css') }}" rel="stylesheet" type="text/css" />
                                {{HTML::style('css/dragdrop/jquery-ui.css')}}
                                <link href="{{ URL::asset('http://www.jqueryscript.net/css/jquerysctipttop.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/css/components-rounded.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/css/productview.css') }}" rel="stylesheet" type="text/css" />

                                <!-- simple zoom css-->
                                <link href="{{ URL::asset('assets/global/css/jquery.simpleLens.css') }}" rel="stylesheet" type="text/css" /> 
                                <link href="{{ URL::asset('assets/global/css/jquery.simpleGallery.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/dropzone/css/dropzone.css') }}" rel="stylesheet" type="text/css" /> 
                                <!--simple zoom css end-->
                                <!-- END PAGE LEVEL STYLES -->

                                <!--igniute UI-->
                                <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/css/jcarousel.skeleton.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
                                <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />

                                <link href="{{ URL::asset('assets/global/plugins/slider/demo.css') }}" rel="stylesheet" type="text/css" />

                                <link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
                                <!--igniute UI end-->

                                
                                
                                @section('userscript')
                                @include('includes.group_repo')
                                @include('includes.ignite')
                                @include('includes.validators')

                                <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/global/plugins/get_manufacturer_list.js') }}" type="text/javascript"></script>
                                <!-- grouped product list -->
                              
                                
                                <!-- brands and manf drop down -->
                                <script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script>
                                 <!--simple zoom script-->

                                <script src="{{ URL::asset('assets/global/scripts/jquery.simpleGallery.js') }}" type="text/javascript"></script> 
                                <script src="{{ URL::asset('assets/global/scripts/jquery.simpleLens.js') }}" type="text/javascript"></script> 
                                <!--simple zoom script-->

                                <!-- BEGIN PAGE LEVEL PLUGINS -->
                                <script src="../../assets/global/plugins/dropzone/dropzone.js"></script>
                                <!-- END PAGE LEVEL PLUGINS -->
                                <!-- BEGIN PAGE LEVEL SCRIPTS -->
                                
                                                           
                                <script src="{{ URL::asset('assets/global/plugins/slider/jquery.imagezoom.min.js')}}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/global/plugins/slider/modernizr.custom.17475.js')}}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/global/plugins/slider/jquery.elastislide.js')}}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/layout4/scripts/demo.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/form-dropzone.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>                                 
                                <script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-manufactures.js') }}" type="text/javascript"></script> 
                                <script src="{{ URL::asset('assets/global/scripts/jcarousel.skeleton.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/global/scripts/jquery.jcarousel.min.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/form-wizard-free-bie-configuration.js') }}" type="text/javascript"></script>
                              
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/form-wizard-package_configuration.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/product_image_gallery_configuration.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/product_grid_tabs.js') }}" type="text/javascript"></script>
                                 <script src="{{ URL::asset('assets/admin/pages/scripts/product/form-wizard-wh-config.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/bin_wh_confi.js') }}" type="text/javascript"></script>                                  
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/pricing_configuration.js') }}" type="text/javascript"></script>
                               
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/set_price.js') }}" type="text/javascript"></script>
                               
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/duplicate_products_validator.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/freebie_validator.js') }}" type="text/javascript"></script>
                                 <script src="{{ URL::asset('assets/admin/pages/scripts/product/grouped_product_grid.js') }}" type="text/javascript"></script>
                                 <script src="{{ URL::asset('assets/admin/pages/scripts/product/product_pack_configuration_validator.js')}}" type="text/javascript"></script>
                                  <script src="{{ URL::asset('assets/admin/pages/scripts/product/related_product_configuration.js')}}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/cellFormatter.js')}}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/TaxModule/product_taxMap_Horizontal.js') }}" type="text/javascript"></script>
                               <script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/price/priceModel.js') }}" type="text/javascript"></script>

                                <!--<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-supplier.js') }}" type="text/javascript"></script> -->
                                <script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/products_inventory_grid.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/common_product_js.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/InventoryModule/validation.js') }}" type="text/javascript"></script>
                                <script src="{{ URL::asset('assets/admin/pages/scripts/product/history_grid.js') }}" type="text/javascript"></script>

<script type="text/javascript">
$("#addFreebie_model").show();
$("#productPacksId").click(function(){
   loadProductPackGrid();
});
function loadProductPackGrid(){
    var product_id = $("#product_id").val();
    $('#productPacksTableGrid').igGrid({
        dataSource: '/products/productpackgrid?product_id='+product_id,
        responseDataKey: 'results',
        width:"100%",
        columns: [
            {headerText: 'Customer Type', key: 'customer_type', dataType: 'string',},  
            {headerText: 'DC', key: 'dcname', dataType: 'string'},
            {headerText: 'Pack', key: 'pack', dataType: 'string'},
            {headerText: 'Color', key: 'color', dataType: 'string'},
            {headerText: 'ELP', key: 'elp', dataType: 'number',template: '<div style="text-align:right"> ${elp} </div>'},
            {headerText: 'ESP', key: 'esp', dataType: 'number', template: '<div style="text-align:right"> ${esp} </div>'},
            {headerText: 'Margin', key: 'margin', dataType: 'number',template: '<div style="text-align:right"> ${margin} </div>'},
        ],
        features: [
        {
            name: "Filtering",
            type:"remote",
            allowFiltering: true,
            caseSensitive: false
        }, 
        {
             name: 'Sorting',
             type: "local",
         },
         {
            name : 'Paging',
            type: "local",
            pageSize : 25,
        }
        ],
        primaryKey: 'product_id',
        initialDataBindDepth: 1,
    });
}
</script>
<!--Ignietui scripts end-->
<script>
     $(function(){
        $("#kvi_name").select2("destroy");
        $("#freeBieProduct_id").select2("destroy");  
        $("#freebie_state_id").select2("destroy");  
        $("#freebie_warehouse_id").select2("destroy");  
        $(".select2").select2();
});
    autosuggest();
    jQuery(document).ready(function() {
        $('#set_price_date').datepicker();  
        productHistoryGrid();
        // initiate layout and plugins
        // Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Demo.init(); // init demo features
        FormDropzone.init();
        PackConficFormWizard.init();
        WarehouseFormWizard.init();
        FormWizard.init();        
		$('#effective_date').datepicker();
		$("#effective_date").keydown(function(e){
			e.preventDefault(); 
		  });                  
		  
		$("#demo4carousel").on('click', 'li', function(el, pos, evt) {
            $('#demo4carousel li.active').removeClass("active");
            $(this).addClass("active");
            // for imagezoom to change image
            var demo4obj = $('#demo4').data('imagezoom');
            demo4obj.changeImage($(this).find('img').attr('src'), $(this).find('img').data('largeimg'));
        }); 	  
        $("#packEaches").change(function()
        {
            var eaches = $("#packEaches").val();
            $("#editPackEaches").val(eaches);
        });    
       $(document).on("click","#editProductGroup_id",function(){
            var pid=$("#product_id").val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url: '/getProductGroupName/'+pid,
                type: 'POST',
                success: function (rs)
                {                         
                    $("#edit_product_group_id").val(rs.product_grp_id);
                    $("#edit_product_group_name").val(rs.product_name);
                }
            });
        });

       groupedProductList();
        
    });
    function groupedProductList()
       {
            $.ajax({
            headers: 
            {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            url: '/getProductGroupListByManf/'+$("#product_id").val(),
            type: 'GET',                                             
            success: function (rs) 
            {
                console.log(rs);
                $("#product_group").html(rs);
                $('.prod_class').css('color','#0174DF !important');
                var product_group_id=$("#product_group_id").val();
                $("#product_group").select2().select2('val',product_group_id);
            }
            });
       }

   function cpenableproduct(dcid){
    if($('#product_cp_enabled_'+dcid).is(":checked")){
        var cpenable=1;
    }else{
        var cpenable=0;
    }

    var productid=$("#product_id").val();
    var dcid=dcid;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/cpenabled',
            type: 'POST',
            data:{
                cpenable:cpenable,
                productid:productid,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                    alert(rs);
                    $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
            });
   }

   function issellableproduct(dcid){
    if($('#product_is_sellable_'+dcid).is(":checked")){
        var issellable=1;
    }else{
        var issellable=0;
    }

    var productid=$("#product_id").val();
    var dcid=dcid;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/issellable',
            type: 'POST',
            data:{
                issellable:issellable,
                productid:productid,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                    alert(rs);
                    $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
            });
   }

   function editEsu(dcid){
   $('#esu_'+dcid).attr('readonly', false);
   }

   function esuSave(dcid){
   var esuval=$('#esu_'+dcid).val();
   var productid=$("#product_id").val();
   $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/saveesu',
            type: 'POST',
            data:{
                esuval:esuval,
                productid:productid,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                $('#esu_'+dcid).attr('readonly', true);
                alert(rs);
                $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
            });
   }

   function custEsuSave(cust_id){
   var esuval=$('#esu_'+cust_id).val();
   var productid=$("#product_id").val();
   $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/savecustesu',
            type: 'POST',
            data:{
                esuval:esuval,
                productid:productid,
                cust_id:cust_id,
            },                                             
            success: function (rs) 
            {
                $('#esu_'+cust_id).attr('readonly', true);
                alert(rs);
                $("#customerTypeEsuGrid").igGrid({dataSource: '/products/customertypeesu?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
            });
   }


   $("#update_esu_for_alldcs").click(function(){
    event.preventDefault();
    var esu_val=$('#product_esu').val();
    var productid=$("#product_id").val();
    var dcid=0;
   $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/saveesu',
            type: 'POST',
            data:{
                esu_val:esu_val,
                productid:productid,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                alert(rs);
                $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
            });
    });
    

    $("#update_sellablests_for_alldcs").click(function(){
    event.preventDefault();
    //var issellable_prdt=$('#product_is_sellable').val();
    if($('#product_is_sellable').is(":checked")){
        var issellable_prdt=1;
    }else{
        var issellable_prdt=0;
    }
    var productid=$("#product_id").val();
    var dcid=0;       //indicating all dcs
   $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/issellable',
            type: 'POST',
            data:{
                issellable_prdt:issellable_prdt,
                productid:productid,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                alert(rs);
                $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
        });
    });


    $("#update_cpsts_for_alldcs").click(function(){
    event.preventDefault();
    //var issellable_prdt=$('#product_is_sellable').val();
    var productid=$("#product_id").val();console.log('#product_cp_enable'+productid);
    if($('#product_cp_enable'+productid).is(":checked")){
        var product_cp_enable=1;
    }else{
        var product_cp_enable=0;
    }
    
    var dcid=0;  //indicating all dcs
   $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/products/cpenabled',
            type: 'POST',
            data:{
                product_cp_enable:product_cp_enable,
                productid:productid,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                alert(rs);
                $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
            }
        });
    });      

    $('#download_template_button').on('click',function() {
        
        if($('#warehouse_tot').val()==0) {
            
            alert('Please select Warehouse')
            return false;
            
        }
        
    });

    $('#mobile_view_dcid,#mobile_customer_group').on('change',function(){
        var dcid=$('#mobile_view_dcid').val();
        var customer_type=$("#mobile_customer_group").val();
        var product_id=$("#product_id").val();
        if(dcid==''){
            alert('Please Select DC');
            return false;
        }
        if(customer_type==''){
            alert('Please Select Customer Type');
            return false;
        }
        $.ajax({
           headers: {'X-CSRF-TOKEN': token},
            url: '/products/productmobileview',
            type: 'POST',
            data:{
                customer_type:customer_type,
                product_id:product_id,
                dcid:dcid,
            },
            beforeSend: function () {
               $('.spinnerQueue').css('display','block');
            },
            complete: function () {
                $('.spinnerQueue').css('display','none');
            },                                             
            success: function (rs) 
            {
                $('#replace_div_mobile_view').empty();
                $('#replace_div_mobile_view').replaceWith(rs);
            } 
        })
    });

    $('#product_cache_flush').on('click',function(){
        var dcid=$('#mobile_view_dcid').val();
        var product_id=$("#product_id").val();
        var customer_type=$("#mobile_customer_group").val();
        if(dcid==''){
            alert('Please Select DC');
            return false;
        }
        if(customer_type==''){
            alert('Please Select Customer Type');
            return false;
        }
        $.ajax({
           headers: {'X-CSRF-TOKEN': token},
            url: '/products/productcacheflush',
            type: 'POST',
            data:{
                customer_type:customer_type,
                product_id:product_id,
                dcid:dcid,
            },                                             
            success: function (rs) 
            {
                if(rs){
                    alert('Product Flushed Successfully');
                    
                }else{
                    alert('Something went wrong,Please Flush Product After Sometime');
                }
                $('#dismiss_model_popup').click();
            } 
        })
    }); 

    $('#productcache').on('click',function(){
        $('#replace_div_mobile_view').empty();
        $('#mobile_view_dcid').select2('val','');
        $('#mobile_customer_group').select2('val','');
    });

    
</script>
@stop   
@extends('layouts.footer')