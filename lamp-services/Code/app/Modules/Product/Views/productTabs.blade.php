<ul class="nav nav-tabs product_tab_size" style="font-size:12px !important;">
<li class="active"><a href="#tab_15_1" data-toggle="tab">Product Information </a></li>
@if($Related_Products == 1)
<li><a href="#tab_15_2" data-toggle="tab" id="rlated_product">Related Products</a></li>
@endif
@if($grouped_Products==1)
<li ><a href="#grouped_products" data-toggle="tab">Grouped Products </a></li>
@endif
@if($Freebie_Configuration == 1)
 <li><a href="#freebie" data-toggle="tab" id="rlated_product">Freebie Configuration</i></a></li>
 @endif
 @if($Packing_Configuration == 1)
<li><a href="#tab_15_3" data-toggle="tab">Packing Configuration</a></li>
@endif
@if($warehouse_info == 1)
<li><a href="#warehouse_config" data-toggle="tab">Warehouse Configuration</a></li>
@endif
@if($Tax_Information == 1)
<li><a href="#tab_15_4" data-toggle="tab">Tax Information</a></li>
@endif
@if($Suppliers == 1)
<li><a href="#tab_15_5" data-toggle="tab">Suppliers</a></li>
@endif

<!-- <li><a href="#tab_15_6" data-toggle="tab">Change History <i class="fa fa-lock" aria-hidden="true"></i></a></li> -->
@if($Pricing_tab == 1)
<li><a href="#promotion_tab" data-toggle="tab">Pricing</a></li>
@endif

@if($Inventory == 1)
<li><a href="#tab_15_8" data-toggle="tab">Inventory</a></li>
@endif
@if($Approval_History == 1)
<li><a href="#tab_15_7" data-toggle="tab">Approval History</a></li>
@endif 
@if($pro_history_tab == 1)
<li><a href="#product_history" id="tab_history" data-toggle="tab">Product History</a></li>
@endif
@if($pro_packs_new_tab == 1)
<li><a href="#productPacksTab" id="productPacksId" data-toggle="tab">Product Packs</a></li>
@endif 
<!--<li ><a href="#product_history" data-toggle="tab">History </a></li>-->
@if($cp_tab == 1)
<li><a href="#cpenable" id="cpenablefordcfc" data-toggle="tab">Product CP Enable DC/FC</a></li>
@endif
@if($prd_elp_hst == 1)
<li><a href="#product_elp_history" id="productelphistory" data-toggle="tab">Product ELP History</a></li>
@endif
@if($prd_cust_esu == 1)
<li><a href="#customer_type_esu" id="customertypeesu" data-toggle="tab">Customer Type ESU</a></li>
@endif

</ul>