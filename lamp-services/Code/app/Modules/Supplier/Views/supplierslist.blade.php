@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">{{$page_title}}</div>                
                 @if($add_suppliers == 1 )
<div class="actions"> <a class="btn green-meadow" href="{{$add_url}}">{{$button_name}}</a> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </div>
                @endif
                @if($vendorexport == 1)
                <div class="actions" style="margin-right:10px; "> <a type="button" id="" href="#vendorexport" data-toggle="modal" class="btn green-meadow">Export Vendor Payment(s)</a> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"></span> </div>
                @endif
            </div>
<?php 
$serviceProviderCount = $vehiclesCount = $vehicleProviderCount = $manpowerProviderCount = $suppliersCount = $space = $spaceProviderCount = 0;
$le_counts = json_decode(json_encode($le_counts),1);

$serviceProviderCount = $vehiclesCount = $vehicleProviderCount = $manpowerProviderCount = $suppliersCount = $space = $spaceProviderCount = 0;

$gridCounts = json_decode(json_encode($counts),1);

foreach($le_counts as $counts)
{
    if($counts['legal_entity_type_id'] == 1002)
    {
        $suppliersCount = $counts['COUNT'];
    }
    if($counts['legal_entity_type_id'] == 1007)
    {
        $serviceProviderCount = $counts['COUNT'];
    }
    if($counts['legal_entity_type_id'] == 1008)
    {
        $vehiclesCount = $counts['COUNT'];
    }
    if($counts['legal_entity_type_id'] == 1009)
    {
        $vehicleProviderCount = $counts['COUNT'];
    }
    if($counts['legal_entity_type_id'] == 1010)
    {
        $manpowerProviderCount = $counts['COUNT'];
    }
    if($counts['legal_entity_type_id'] == 1011)
    {
        $space = $counts['COUNT'];
    }
    if($counts['legal_entity_type_id'] == 1012)
    {
        $spaceProviderCount = $counts['COUNT'];
    }    
}

$suppliersCount = $gridCounts[0]['supplier']['supplier_count'];

$vehicleProviderCount = $gridCounts[2]['providerQuery']['vehicleprovider'];

$vehiclesCount = $gridCounts[1]['vehicle'];


//if ($status === 'suppliers'){
//     $picklistBtn = 'false';
//  $shipmentBtn = 'false';
//  $challanBtn = 'false';
//}
//elseif($status === 'cancelled'){
//  $picklistBtn = 'false';
//  $shipmentBtn = 'false';
//  $challanBtn = 'false';
//  $deliveredBtn = 'false';
//  $invoiceBtn = 'false';
//  $deliveryExeBtn = 'false';
//  $dsrBtn = 'false';
//}
//$totalCompletedOrders = 10;

?>            
    <div class="portlet-body">
        @if(isset($suppliers_grid_filters) && $suppliers_grid_filters == 1 )
            <div class="row">
    			<div class="col-md-12">
    				<div class="caption">
    					<span class="caption-subject bold font-blue"> Browse By :</span>
    					<span class="caption-helper sorting1">
                <a href="{{$app['url']->to('/')}}/suppliers" class="{{($status == 'suppliers') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Suppliers">Suppliers </a>&nbsp;
                <a href="{{$app['url']->to('/')}}/serviceproviders" class="{{($status == 'serviceprovider') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Service Providers">Service Providers </a>&nbsp;

                <a href="{{$app['url']->to('/')}}/vehicleproviders" class="{{($status == 'vehiclelist') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Vehicle Providers">Vehicle Providers </a>&nbsp;
    			<a href="{{$app['url']->to('/')}}/vehicle" class="{{($status == 'vehicles') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Vehicles">Vehicles </a>&nbsp;
                <a href="{{$app['url']->to('/')}}/humanresource" class="{{($status == 'manpower') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Human Resource Providers">Human Resource Providers </a>&nbsp;
    			<a href="{{$app['url']->to('/')}}/spaceprovider" class="{{($status == 'spaceprovider') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Space Providers">Space Providers </a>&nbsp;
                <a href="{{$app['url']->to('/')}}/space" class="{{($status == 'space') ? 'active' : 'inactive'}} link" data-toggle="tooltip" title="Space">Space </a>&nbsp;

        					
    					</span>

    				</div>
    			</div>
    		</div>
        @endif
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style="height: 650px;" data-always-visible="1" data-rail-visible1="0" >
                            <div class="table-responsive">
                                <table id="{{$grid_table_id}}"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">

<div id="addbrand" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="add_brand_form" method="post" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">ADD BRAND</h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Brand Name <span class="required" aria-required="true">*</span></label>
                                <input name="brand_name" id="brand_name" type="text" class="form-control">
                            </div>
                        </div>    
                        <input name="brand_id" id="edit_brand_id" type="hidden" class="form-control"  />
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Logo <span class="required" aria-required="true">*</span></label>
                                <div class="row">
                                    <div class="col-md-12">  

                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">

                                            <div>
                                                <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                                                    <span class="fileinput-new">Choose File </span>
                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                                    <input id="brandLogo" type="file" name="brand_logo" class="upload" />
                                                </span>

                                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"></div>

                                                <span class="fileinput-filename" style=" float:left; width:233px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

                                            </div>
                                        </div>



                                    </div>
                                </div>
                            </div></div>    
                    </div>      

                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Description <span class="required" aria-required="true">*</span></label>
                                <textarea rows="1" name="brand_desc" id="brand_desc" class="form-control"></textarea>
                            </div></div>    
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Trade Mark Registration Proof</label>
                                <div class="row">
                                    <div class="col-md-12">  

                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">

                                            <div>
                                                <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                                                    <span class="fileinput-new">Choose File </span>
                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                                    <input id="tradeMarkProof" type="file" name="brand_trademark_proof" class="upload" />
                                                </span>

                                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"></div>

                                                <span class="fileinput-filename" style=" float:left; width:233px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

                                            </div>
                                        </div>



                                    </div>
                                </div>
                            </div></div>    
                    </div>      

                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Trade Mark Registration Number
                                    <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                                <input name="brand_trademark_number" id="brand_trademark_number" type="text" class="form-control">
                            </div></div>    
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Brand Authorization Proof</label>
                                <div class="row">
                                    <div class="col-md-12">  

                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">

                                            <div>
                                                <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                                                    <span class="fileinput-new">Choose File </span>
                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                                    <input id="authorizationProof" type="file" name="brand_authorization" class="upload" />
                                                </span>

                                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"></div>

                                                <span class="fileinput-filename" style=" float:left; width:233px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

                                            </div>
                                        </div>



                                    </div>
                                </div>
                            </div></div>    
                    </div>      

                </div>
                <div class="modal-footer ">
                    <center>
                        <button class="btn green-meadow" type="submit" value="Save">Save</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>
@if($vendorexport == 1)
<div class="modal modal-scroll fade in" id="vendorexport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="modalClose" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Export Vendor Payment(s)</h4>
            </div>
            <div class="modal-body">
                <form id="vendorexportForm" action="suppliers/export" class="text-center" >
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <div class="row margtop">
                        <!-- <div class="col-md-6">
                            <div class="form-group err">
                                <label class="control-label">Business Units</label>
                                <input type="hidden" id="hidden_buid" name="hidden_buid" value='<?php //if (isset($bu_id) && $bu_id!=''){// echo $bu_id;}else{ echo '';}?>'>
                                <select id="business_unit_id" name="business_unit_id" class="form-control business_unit_id select2me"></select>
                            </div>
                        </div> -->

                        <div class="col-md-12">
                            <div class="form-group err">
                                <label for="supplier">Supplier</label>
                                <select class="form-control select2me" id="supplier_id" name="supplier_id" style="margin-top: 6px" placeholder="supplier name">
                                    <option value ="">--Please Select--</option>
                                    <!-- <option value="0">ALL</option> -->
                                        @foreach($suppliers as $vendors)
                                            <option value = "{{$vendors['legal_entity_id']}}">{{$vendors['user_name']." "."(".$vendors['le_code'].")"}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group err">
                                <label>Start Date</label>
                                <div class="input-icon right" style="width: 100%">
                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                    <input type="text" class="form-control" name="start_date" id="start_date" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group err">
                                <label>End Date</label>

                                <div class="input-icon right" style="width: 100%">
                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                    <input type="text" class="form-control" name="end_date" id="end_date" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="download" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endif

<div class="modal fade modal-scroll in" id="addp" tabindex="-1" role="addlp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title" id="add_totprd">ADD PRODUCTS</h4>
      </div>
       <form action="" method="post" id="supplier_add_products">
        <input type="hidden" class="form-control" name="edit_form_product_id" id="edit_form_product_id">
        <div class="modal-body">
          <div class="row">
             <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Warehouse <span class="required" aria-required="true">*</span></label>
               <select class="form-control spdt_whid" name="spd_whid" id="spd_whid">
                                <option value="">Select Warehouse</option>
                                                                @if(isset($legalentity_warehouses))                    
								@foreach($legalentity_warehouses as $Val )
									<option value="{{$Val['le_wh_id']}}">{{$Val['lp_wh_name']}}</option>
								@endforeach
                                                                @endif
								</select>
                
              </div>
            </div> 
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Brand <span class="required" aria-required="true">*</span></label>
                <!--<input type="text" class="form-control" name="brand" id="tot_brand_name">-->
                <select class="form-control" name="brand" id="brand">
                  <option value="">Select Brand</option>
                  
        @foreach($brands as $brand )
          
                  <option value="{{$brand['brand_id']}}">{{$brand['brand_name']}}</option>
                  
        @endforeach 
              
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Category<span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="category" id="tot_cat_name">
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Product Name <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="product_name" id="tot_product_name">
                  <option value="">Select Product</option>
                </select>
                <input type="hidden" id="prod_name" name="prod_name" value="">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Product Title <span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" name="product_title" id="tot_product_title">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Supplier Product Code<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" name="supplier_sku_code" id="supplier_sku_code">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">{{ trans('headings.LP') }}</label>
                <input type="text" class="form-control" name="dlp" id="dlp">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Margin(%)<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" name="distributor_margin" id="distributor_margin">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">PTR<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" name="rlp" id="rlp">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Supplier DC Relationship <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="supplier_dc_relationship" id="supplier_dc_relationship">
                  <option value="">Select DC Relationship</option>
                  @foreach($suppliers_dcrealtionship as $suppliers_dcrealtionship)
                              <option value="{{$suppliers_dcrealtionship->value}}">{{$suppliers_dcrealtionship->account_type}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">GRN Freshness Percentage <span class="required" aria-required="true">*</span></label>
                 <input type="text" class="form-control" name="grn_freshness_percentage" id="grn_freshness_percentage">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">TAX TYPES<span class="required" aria-required="true">*</span></label>
                   <select class="form-control" id="tax_type" name="tax_type">
                        <option value="">Select Tax Type</option>    
                            @foreach($tax_types as $tax_types )
                            <option value="{{$tax_types->value}}" >{{$tax_types->account_type}}</option>
                            @endforeach
                    </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">TAX%<span class="required" aria-required="true">*</span></label>
                   <input type="text" class="form-control" name="tax" id="tax">
              </div>
            </div>
           <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">MOQ<span class="required" aria-required="true">*</span></label>
                   <input type="text" class="form-control" name="moq" id="moq">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">MOQ UoM<span class="required" aria-required="true">*</span></label>
                   <select class="form-control" id="moq_uom" name="moq_uom">
                        <option value="">Select MoQ UoM</option>    
                            @foreach($moq_data as $moq_data )
                            <option value="{{$moq_data->value}}" >{{$moq_data->account_type}}</option>
                            @endforeach
                    </select>
              </div>
            </div>
           <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Delivery TAT<span class="required" aria-required="true">*</span></label>
                                               <input type="text" class="form-control" name="delivery_terms" id="delivery_terms">

              </div>
            </div>
           <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Delivery TAT UoM<span class="required" aria-required="true">*</span></label>
               <select class="form-control" id="delivery_tat_uom" name="delivery_tat_uom">
                        <option value="">Select Delivery TAT UoM</option>    
                            @foreach($uom_data as $uomVal )
                            <option value="{{$uomVal->value}}" >{{$uomVal->account_type}}</option>
                            @endforeach
                    </select>
              </div>
            </div>
           <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">GRN DAYS<span class="required" aria-required="true">*</span></label>
               <select class="form-control multi-select-search-box common" multiple="multiple" placeholder='Select GRN Days' id="grn_days" name="grn_days">
                   <option value="SUNDAY">SUNDAY</option>    
                   <option value="MONDAY">MONDAY</option>
                   <option value="TUESDAY">TUESDAY</option>
                   <option value="WEDNESDAY">WEDNESDAY</option>
                   <option value="THURSDAY">THURSDAY</option>
                   <option value="FRIDAY">FRIDAY</option>
                   <option value="SATURDAY">SATURDAY</option>
                    </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">RTV Allowed<span class="required" aria-required="true">*</span></label>
                <div id="return_accepted">
                    <input type="radio" value="1" name="rtv_allowed" id="rtv_allowed">
                  Yes
                  <input type="radio" value="0" name="rtv_allowed" id="rtv_allowed" checked>
                  No </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Inventory Mode <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="inventory_mode" id="inventory_mode">
                  <option value="">Select Inventory Mode</option>
                  
                                        @if(isset($inventory_data))
                                        @foreach($inventory_data as $inv)
                                        
                  <option value="{{$inv->value}}">{{$inv->account_type}}</option>
                  
                                        @endforeach
                                        @endif
         
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">ATP<span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" name="atp" id="atp">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">ATP Period<span class="required" aria-required="true">*</span></label>
                   <select class="form-control" name="atp_period" id="atp_period">
                  <option value="">Select Inventory Mode</option>
                  
                                        @if(isset($atp_peyiod))
                                        @foreach($atp_peyiod as $atp_peyiod)
                  <option value="{{$atp_peyiod->value}}">{{$atp_peyiod->account_type}}</option>
                                        @endforeach
                                        @endif
         
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">KVI<span class="required" aria-required="true">*</span></label>
               <select class="form-control kvit" name="kvi" id="kvi">
                  <option value="">Select KVI</option>
                  @foreach($kvi as $kvi)
                              <option value="{{$kvi->value}}">{{$kvi->account_type}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Is Prefered Supplier<span class="required" aria-required="true">*</span></label>
               <input type="radio" name="is_preferred_supplier" id="is_preferred_supplier" value="1" checked> Yes
               <input type="radio" name="is_preferred_supplier" id="is_preferred_supplier" value="0"> No
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Effective Date<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" name="efet_dat" id="efet_dat">
              </div>
            </div>
            <!--<div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Base Price<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" name="base_price" id="base_price">
              </div>
            </div>-->    
          </div>
          <div class="form-actions">
            <div class="row">
              <div class="col-md-12 text-center">
                <button type="submit" class="btn green-meadow">Save</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>


<div class="modal fade modal-scroll in" id="addbrand" tabindex="-1" role="addlp" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">ADD WAREHOUSE</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Warehouse Name <span class="required" aria-required="true">*</span></label>
                            <select class="form-control">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                                <option>Option 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Contact Name <span class="required" aria-required="true">*</span></label>
                            <select class="form-control">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                                <option>Option 5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Email <span class="required" aria-required="true">*</span></label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Phone <span class="required" aria-required="true">*</span></label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Address 2 <span class="required" aria-required="true">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                                    <select class="form-control">
                                        <option>Option 1</option>
                                        <option>Option 2</option>
                                        <option>Option 3</option>
                                        <option>Option 4</option>
                                        <option>Option 5</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                                    <select class="form-control">
                                        <option>Option 1</option>
                                        <option>Option 2</option>
                                        <option>Option 3</option>
                                        <option>Option 4</option>
                                        <option>Option 5</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3806.194237107969!2d78.37890601487723!3d17.450414988040805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb93ded9f6f0d7%3A0xa3d91e5d00d50b63!2sCyber+Towers!5e0!3m2!1sen!2sin!4v1465378467671" width="265" height="290" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">ERP Code <span class="required" aria-required="true">*</span></label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Latitude</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Logitude</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn green-meadow">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Product::totprice')
@include('PurchaseOrder::Form.legalentityPaymentPopup')
<a class="btn green-meadow" data-toggle="modal" style="display:none;" href="#addbrand">Add Brand</a>
<a class="btn green-meadow" data-toggle="modal" style="display:none;" href="#addp">Add Product</a>

@stop
@section('style')

<style type="text/css">
.rightAlign { text-align:right;}   
.fa-check{color:#32c5d2 !important;}
.fa-times{color:#e7505a !important;}
.fa-thumbs-o-up{color:#3598dc !important;}
.fa-pencil{color:#3598dc !important;}
.fa-trash-o{color:#3598dc !important;}
.sorting1 a{ list-style-type:none !important;   font-size:12px;}
.sorting1 a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting1 a:active{text-decoration:underline !important;border-bottom:1px black;}
.active{text-decoration: underline !important; border-bottom:1px black}
.inactive{text-decoration:none !important; color:#8f8c8c !important;}
</style>

<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
<script type="text/javascript">
    jQuery(document).ready(function () {

        $("#supplier_list_grid_Brands").attr("title", "#Brands");
         $("#supplier_list_grid_Products").attr("title", "#Products");
         $("#supplier_list_grid_Warehouses").attr("title", "#Warehouses");
         $("#supplier_list_grid_Documents_count").attr("title", "#Documents"); 
         $("#supplier_list_grid_Status_checked").attr("title", "is Approved");  
         $("#ser_pro_list_grid_Documents_count").attr("title", "#Documents"); 
		 $("#veh_pro_list_grid_Documents_count").attr("title", "#Documents"); 
		 $("#veh_list_grid_Documents_count").attr("title", "#Documents"); 
		 $("#hr_list_grid_Documents_count").attr("title", "#Documents"); 
		 $("#space_pro_list_grid_Documents_count").attr("title", "#Documents"); 
		 $("#space_list_grid_Documents_count").attr("title", "#Documents"); 
        FormWizard.init();
    });

</script>
@stop

@section('userscript')
@include('includes.ignite')
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/table-datatables-editable.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-supplier.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/admin/pages/scripts/supplier/supplier_grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/payments_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>

<script>

// $(function(){
//     var token=$('#csrf-token').val();
//     var hidden_buid=$('#hidden_buid').val();
//     $.ajax({
//     type:'get',
//     headers: {'X-CSRF-TOKEN': token},
//     url:'/getbu',
//     success: function(res){
//         res.forEach(data=>{
//             $('#business_unit_id').append(data);
//         });
//     if(res ==''){
//         $('#business_unit_id').select2('val',-1);
//     }
//     else{
//         $('#business_unit_id').select2('val',hidden_buid);
//     }
//     }
//       });
//   });

$(document).ready(function () {

        $('#vendorexport').on('hide.bs.modal', function () {
            $("#vendorexportForm").bootstrapValidator('resetForm', true);
            $("#supplier_id").select2("val", "");
            $("#start_date").val("");
            $("#end_date").val("");
            $('.modal-backdrop').remove();
        });
        $( "#start_date" ).datepicker({
            maxDate: new Date(),
            onSelect: function(date) {
                $('#vendorexportForm').bootstrapValidator('revalidateField', 'start_date');
                $("#end_date").datepicker('option', 'minDate', date);
            }
        });
        $("#end_date").datepicker({  maxDate: new Date() });
        $('#vendorexportForm').bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                supplier_id: {
                    validators: {
                        notEmpty: {
                            message: "Please select a supplier"
                        }
                    }
                },
                start_date: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: 'Invalid format'
                        }
                    }
                },
                end_date: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            minDate: 'start_date',
                            message: 'Invalid format'
                        }
                    }
                },
            }
        })
        .on('success.form.bv', function(event) {
                event.preventDefault();
                var form = $('#vendorexportForm');
                window.location = form.attr('action') + '?' + form.serialize();
                $('.close').click();
            });
        $("#end_date").change(function(){
            $('#vendorexportForm').bootstrapValidator('revalidateField', 'start_date');
            $('#vendorexportForm').bootstrapValidator('revalidateField', 'end_date');

        });
    });

    $(".ui-icon-triangle-1-se").click(function () {
        $(".ui-iggrid-filterrow").toggle();
    });

    $(".fileinput-preview").removeClass('fileinput-preview fileinput-exists thumbnail').addClass('fileinput-preview thumbnail');

    $(document).on('click', '.enableDisableProducttot', function() {
    var supplier_id = $(this).attr('id');    
    
    var flag = '';
    if ($(this).is(":checked") === true) {
    flag = 1;
    } else {
    flag = 0;
    }  

    var token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: "/suppliers/checkprovider",
            type: "POST",
            dataType: 'json',
            data: {supplier_id:supplier_id,flag:flag},
            success: function (rs) {
                if(rs.status == 1)
                {
                        if (confirm(rs.vendor) == true)
                        {
                        if (supplier_id != '') {
                        $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                                url: "/suppliers/activate",
                                type: "POST",
                                dataType: 'json',
                                data: {supplier_id:supplier_id,flag:flag},
                                success: function (rs) {
                                    if(flag == 1)
                                    alert("Activated "+rs.vendor+" successfully!")
                                    else
                                    alert("De-Activated "+rs.vendor+" successfully!")    
                                }
                        })
                        }
                        }
						else
						{
                            if(flag == 1){
                            $("#"+supplier_id).prop('checked', false);}
                            else{
                            $("#"+supplier_id).prop('checked', true);}   
						}
                }
            }
    })    

    });

    $(document).on('click', '.deleteSupplier', function (event) {
        //alert(event.target);
        event.preventDefault();
        //var BrandsCount = $.trim($(this).parents('tr').find('[aria-describedby="supplier_list_grid_Brands"]').html());

//        if (BrandsCount == 0)
//        {

            if (confirm("Are you sure you want to delete?") == true)
            {
                var csrf_token = $('#csrf-token').val();

                var legalentity_id = $(this).attr('href');
                var vehicle_id = $(this).attr('vehid');

                $.ajax({
                    headers: {'X-CSRF-TOKEN': csrf_token},
                    url: '/suppliers/delete',
                    type: 'POST',
                    data: {'legalentity_id': legalentity_id,'vehicle_id':vehicle_id},
                    dataType: 'json',
                    async: false,
                    success: function (data) {
                        //console.log(data);
                        //if(data != 2 & data != 0 )
                        //{
                        alert(data.message);
                        if(data.status ==1)
                        {
                        switch (data.grid_id) {
                            case '#ser_pro_list_grid':
                                $(data.grid_id).igGrid({dataSource: 'suppliers/getserviceprovider'});
                                document.querySelector('.ser_pro_list_grid').innerHTML = data.count;                                
                                break; 
                            case '#hr_list_grid':
                                $(data.grid_id).igGrid({dataSource: 'suppliers/gethrproviders'});
                                document.querySelector('.hr_list_grid').innerHTML = data.count;
                                break; 
                            case '#veh_pro_list_grid':
                                $(data.grid_id).igGrid({dataSource: 'suppliers/getvehproviders'});
                                document.querySelector('.veh_pro_list_grid').innerHTML = data.count;
                                break; 
                            case '#veh_list_grid':
                                $(data.grid_id).igGrid({dataSource: 'suppliers/getvehicleslist'});
                                document.querySelector('.veh_list_grid').innerHTML = data.count;
                                break; 
                            case '#space_list_grid':
                                $(data.grid_id).igGrid({dataSource: 'suppliers/getspace'});
                                document.querySelector('.space_list_grid').innerHTML = data.count;
                                break; 
                            case '#space_pro_list_grid':
                                $(data.grid_id).igGrid({dataSource: 'suppliers/getspaceprovider'});
                                document.querySelector('.space_pro_list_grid').innerHTML = data.count;
                                break; 
                            default: 
                                $(data.grid_id).igHierarchicalGrid({dataSource: '/suppliers/getSuppliers'});
                                document.querySelector('.supp_cnt').innerHTML = data.count;
                                
                        }
                    }
                   
                    },
                    cache: false
                });

            }

//        } else {

//            alert('Please delete / unmap brands associated with this Supplier')
//        }


    })

    $(document).on('click', '.editBrand', function (e) {
        e.preventDefault();
        token = $("#csrf-token").val();
        brand_id = $(this).attr('href');
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editBrand/' + brand_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {
                $("#edit_brand_id").val(rs[0].brand_id);
                $("#brand_name").val(rs[0].brand_name);
                $("#brand_desc").val(rs[0].description);
                $("#brandLogo").parents('.fileinput').find('.thumbnail').html('<img src="/uploads/brand_logos/' + rs[0].logo_url + '"/>')
                $("#tradeMarkProof").parents('.fileinput').find('.thumbnail').html('<img src="/uploads/brand_trademark_proofs/' + rs[0].trademark_url + '"/>')
                $("#authorizationProof").parents('.fileinput').find('.thumbnail').html('<img src="/uploads/brand_authorization_proofs/' + rs[0].brand_auth_url + '"/>')
                $('a[href="#addbrand"]').trigger('click');
            }
        });


    });
    
    $(document).on('click', '.deleteBrand', function(e) {
        event.preventDefault();
        brand_id = $(this).attr('href');
        var productsCount = $.trim($(this).parents('tr').find('[aria-describedby$="Brands_child_Products"]').html());
        if (productsCount == 0)
        {
            if (confirm('Are you sure you want to delete?'))
            {
                token = $("#csrf-token").val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: '/suppliers/deletebrand/' + brand_id,
                    processData: false,
                    contentType: false,
                    success: function (rs)
                    {
                        $('#supplier_list_grid').igHierarchicalGrid({dataSource: '/suppliers/getSuppliers'});
                    }
                });
            }
        } else {
            alert('Please delete / unmap products associated with this Brand')
        }
    });

    
    $(document).on('click', '.editProduct', function(e) {
        e.preventDefault();
        token = $("#csrf-token").val();
        var product_id = $(this).attr('href');
        var supplier_id = $(this).attr('data-id');
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editProduct/' + product_id +'/'+supplier_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {
       var inv_type = rs[0].inventory_mode;
      $("#edit_form_product_id").val(rs[0].product_id);
      $(".spdt_whid").val(rs[0].le_wh_id);
      $("#tot_brand_name").val(rs[0].brand_name);
      $("#brand").val(rs[0].brand_id);
      $("#tot_cat_name").val(rs[0].category_id);
      $("#tot_product_name").val(rs[0].product_name);
      $("#tot_product_title").val(rs[0].product_title);
      $("#supplier_sku_code").val(rs[0].supplier_sku_code);
      $("#dlp").val(rs[0].dlp);
      $("#distributor_margin").val(rs[0].distributor_margin);
      $("#rlp").val(rs[0].rlp);
      $("#supplier_dc_relationship").val(rs[0].supplier_dc_relationship);
      $("#grn_freshness_percentage").val(rs[0].grn_freshness_percentage);
      $("#tax_type").val(rs[0].tax_type);
      $("#tax").val(rs[0].tax);
      $("#moq").val(rs[0].moq);
      $("#moq_uom").val(rs[0].moq_uom);
      $("#delivery_terms").val(rs[0].delivery_terms);
      $("#delivery_tat_uom").val(rs[0].delivery_tat_uom);
      $("#grn_days").val(rs[0].grn_days);
      $("#rtv_allowed").val(rs[0].rtv_allowed);
      $("#inventory_mode").val(rs[0].inventory_mode);
      $("#atp").val(rs[0].atp);
      $("#atp_period").val(rs[0].atp_period);
      $(".kvit").val(rs[0].kvi);
      $("#is_preferred_supplier").val(rs[0].is_preferred_supplier);
      $("#efet_dat").val(rs[0].effective_date);
      
      $('input[name="rtv_allowed"]').removeClass('checked');

      $('input[name="rtv_allowed"][value="'+rs[0].rtv_allowed+'"]').attr('checked', true);
      
      $('input[name="rtv_allowed"][value="'+rs[0].rtv_allowed+'"]').parent().addClass('checked');
      
      $('input[name="is_preferred_supplier"]').removeClass('checked');

      $('input[name="is_preferred_supplier"][value="'+rs[0].is_preferred_supplier+'"]').attr('checked', true);
      
      $('input[name="is_preferred_supplier"][value="'+rs[0].is_preferred_supplier+'"]').parent().addClass('checked');
      
      
      $("#tot_margin_type").val(rs[0].is_markup);


      $('a[href="#addp"]').trigger('click');
            editbrands(rs[0].brand_id,rs[0].product_id,rs[0].category_id,rs[0].cat_name);
            
            $("#tot_inventory_mode").val(rs[0].inventory_mode);
            }
        });
    });
    
function editbrands(brand_id,product_id,category_id,cat_name)
{
                $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url: "/suppliers/productsbybrand",
                type: "POST",
                data:{brand: brand_id},
                success: function (rs) {
                    $('#tot_product_name').empty();
                    $('#tot_product_name').append(rs);
                    $('#tot_product_name').val(product_id);
                    $('#tot_cat_name').empty();
                    $('#tot_cat_name').append($('<option>', {value: category_id,text : cat_name }));
                    console.log(rs);
               }  
               });
    
}    

    
      $(document).on('click', '.deleteProduct', function(event) {
        event.preventDefault();
        product_id = $(this).attr('href');
        if (confirm('Are you sure you want to delete this product?'))
        {
            token = $("#csrf-token").val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url: '/suppliers/deleteProduct/' + product_id,
                processData: false,
                contentType: false,
                success: function (rs)
                {
                    $('#supplier_list_grid').igHierarchicalGrid({dataSource: '/suppliers/getSuppliers'});
                }
            });
        }
    });


    $('a[href="#addbrand"]').on('click', function (e) {
        brand_validation_var.resetForm();
        $('#add_brand_form .has-error').each(function () {
            $(this).removeClass('has-error');
        });
        if (e.originalEvent !== undefined)
        {
            $('input[name="brand_logo"]').rules('add', 'required');

            $('#add_brand_form')[0].reset();
            $('#edit_brand_id').val('');
            $("#brandLogo").parents('.fileinput').find('.thumbnail').html('')
            $("#tradeMarkProof").parents('.fileinput').find('.thumbnail').html('')
            $("#authorizationProof").parents('.fileinput').find('.thumbnail').html('')
        } else {
            $('input[name="brand_logo"]').rules('remove', 'required');
        }
    })

    $('a[href="#addp"]').on('click', function (e) {
      //supplier_product_formwizard.resetForm();
        $('#supplier_add_products .has-error').each(function () {
            $(this).removeClass('has-error');
        });
        if (e.originalEvent !== undefined)
        {
            $('#supplier_add_products')[0].reset();
            $('#edit_form_product_id').val('');
        } else {

        }
    })

        
    $(document).on('click', '.set_price', function(e) {   
        $('#set_price_date').datepicker();
        $('#set_price_date').datepicker("option", "minDate", new Date());
        $("#set_price_date").keydown(function(e){
             e.preventDefault(); 
      });
      
    $('a[href="#setPrice"]').trigger('click');
    $("#set_price_elp").val('');
    $("#set_price_date").val('');    
    //$("#set_price_form").data('validator').resetForm();
    $('#set_price_form .has-error').each(function(){
     $(this).removeClass('has-error');
    });
     var product_price_id = $(this).attr('href');     
     var token = $("#csrf-token").val();     
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editSetPrice/' + product_price_id,
            type: 'POST',
            success:  function(response) {
               var data     = $.parseJSON(response);
               var supId    = data[0].supp_id;               
               var prod_id  = data[0].prd_id;
                $("#set_price_whid").val(data[0].wh_id);                
                $("#set_supplier_id").val(data[0].supp_name);
                $("#set_product_id").val(data[0].prod_title);
                $("#price_mrp").val(data[0].mrp);                 
                $("#set_price_productId").val(prod_id);
                $("#set_price_whId").val(data[0].wh_id);
                $("#set_price_supId").val(supId);
               console.log(response);
            }
            
        });
        $(".set_price_whid").attr('disabled',true);
        $("#set_supplier_id").attr('disabled',true);
        $("#set_product_id").attr('disabled',true);
        $("#price_mrp").attr('disabled',true);
      
   });

</script>
@stop
@extends('layouts.footer')
