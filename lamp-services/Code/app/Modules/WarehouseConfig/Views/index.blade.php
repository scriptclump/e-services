    @extends('layouts.default')
    @extends('layouts.header')
    @extends('layouts.sideview')
    @section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption"> Location Configuration </div>
                    <div class="actions">   
                    <button type="button" class="btn green-meadow" data-toggle="modal" data-target="#binDiaConf">Bin Dimension Conf</button>
                    <button  type="hidden" style="display:none;"  class="btn green-meadow" id="rackLevelMultiBinConfig_model" data-toggle="modal" href="#rackLevelMultiBinConfig"> Multiple Bin Configurations  </button>
                    <button  type="hidden"  class="btn green-meadow" id="addWarehouse_model" data-toggle="modal" href="#addFreeBie"> Add Location </button>
                    </div>
                </div>    
                <div class="portlet-body">
                    <div id="warehouse_config_grid"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- multiple rack level configuration process -->
    <form action="" id="multi_bin_config" method="POST">
    <div id="rackLevelMultiBinConfig" class="modal fade" role="dialog">
    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Multiple Bin Configurations Rack Level</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group err">
                  <label class="control-label">Warehouse Name</label>
                  <select name="Rack_level_warehouse_name" id="Rack_level_warehouse_name" onchange="getRackLevelByWhId();" class="form-control" > 
                        @if(isset($warehouseList))
                         <option value="0">Please Select...</option>
                            @foreach($warehouseList as $wareshouseName)
                                <option value="{{$wareshouseName['le_wh_id']}}">{{$wareshouseName['wh_location']}}</option>
                            @endforeach
                        @endif
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group err">
                  <label class="control-label">Rack </label>
                  <select name="rack_level_name" id="rack_level_name" onchange="getBinDim()"  class="form-control select2me ">
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
               <div class="col-md-6">
                <div class="form-group err">
                  <label class="control-label">Bin Type</label>
                  <select name="rack_bin_type"  id="rack_bin_type" onchange="getRackCapacity()" class="form-control ">
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group ">
                  <label class="control-label">Number of Bins  </label>
                   <input type="number" disabled="disbaled" id="rack_no_bins" name="rack_no_bins" class="form-control" value="" >
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit"  id="save_binDimConf" class="btn btn-success" align="center">Save</button>
          </div>
        </div>
      </div>
    </div>
    </form>
    <form action="" class="" id="binDimConf_model" method="POST">
    <div id="binDiaConf" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Bin Dimensions Configurations</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group err">
                  <label class="control-label">Bin Types</label>
                  <select name="bin_dim_name" id="bin_dim_name"  class="form-control ">
                   @if(isset($binType))
                   <option value="0">Please select...</option>
                    @foreach($binType as $binValue)
                    <option value="{{$binValue['location_value']}}">{{$binValue['location_name']}}</option>
                    @endforeach
                   @endif
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group ">
                  <label class="control-label">Length </label>
                   <input type="number" min="0"  id="bin_dim_lenght" name="bin_dim_lenght" placeholder="Lenght" class="form-control" value="">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group ">
                  <label class="control-label">Breadth</label>
                   <input type="number" min="0" id="bin_dim_width" name="bin_dim_width" placeholder="width" class="form-control" value="" >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group ">
                  <label class="control-label">Height </label>
                   <input type="number" min="0"  id="bin_dim_height" name="bin_dim_height" placeholder="height" class="form-control" value="" >
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group ">
                  <label class="control-label">LBH UOM </label>
                   <select name="bin_lenghtUOm" id="bin_lenghtUOm"  class="form-control ">
                      @if(isset($lenghtUom))
                          @foreach($lenghtUom as $lenghtUomVal)                          
                            @if($lenghtUomVal['location_value']==12001)
                              <option value="{{$lenghtUomVal['location_value']}}" selected >{{$lenghtUomVal['location_name']}}</option>
                            @endif
                          @endforeach
                      @endif
                    </select>
                </div>
              </div>
            </div>
            <div class="row">            
              <div class="col-md-6">
                <div class="form-group ">
                  <label class="control-label">Weight </label>
                   <input type="text"  id="bin_weight" name="bin_weight" placeholder="Weight" class="form-control" value="" >
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group ">
                  <label class="control-label">Weight UOM </label>
                   <select name="bin_weightUOm" id="bin_weightUOm"  class="form-control ">
                      @if(isset($weightUom))
                          @foreach($weightUom as $weightUomVal)                           
                              <option value="{{$weightUomVal['location_value']}}"  >{{$weightUomVal['location_name']}}</option>
                          @endforeach
                      @endif
                    </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit"  id="save_binDimConf" class="btn btn-success" align="center">Save</button>
          </div>
        </div>
      </div>
    </div>
    </form>
    <button type="button" style="display:none;"  class="btn btn-info btn-lg" data-toggle="modal" id="edit_wh" data-target="#myModal">Open Modal</button>
    <!-- edit warehouse only -->
    <form action="" class="" id="editWarehouse_model" method="POST">
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Location Configuration</h4>
                </div>
                <div class="modal-body">
                  <div class="modal-body model_style">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group err">
                            <label class="control-label">Warehouse Name</label>
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                             <input type="hidden" name="edit_wh_loc_id" id="edit_wh_loc_id" value="" />
                            <input type="text"  id="edit_warehouse_name" name="edit_warehouse_name" placeholder="name" class="form-control" value="" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group err">
                            <label class="control-label">Warehouse Location Type</label>
                            <span class="custom-dropdown custom-dropdown--white">    
                             <select name="edit_level_type" id="edit_level_type"  class="form-control ">
                                <option value="0">Please select</option>
                                @if(isset($locationType))
                                    @foreach($locationType as $locationValue)                           
                                        <option value="{{$locationValue['location_value']}}"  >{{$locationValue['location_name']}}</option>
                                    @endforeach
                                @endif
                              </select>
                            </span>
                          </div>
                        </div>       
                      </div>
                      
                      <div class="row">                     
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Sort Order </label>
                                <input type="number" min="0" class="form-control " name="edit_sort_order" id="edit_sort_order"  >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">X Axis </label>
                                <input type="number" min="0" name="edit_x_axis" id="edit_x_axis" placeholder="X Axis " class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Y Axis </label>
                                <input type="number" min="0" name="edit_y_axis" id="edit_y_axis" placeholder="Y  Axis" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Z Axis </label>
                                <input type="number" min="0" name="edit_z_axis" id="edit_z_axis" placeholder="Z Axis" class="form-control" />
                            </div>
                        </div>
                      </div>
                    <div class="modal-footer">
                      <div class="row">
                        <div class="col-md-12 text-center">
                          <button type="submit" class="btn green-meadow save_warehouse" >Save</button>
                        </div>
                      </div>
                    </div>
                  </div>           
                </div>
              </div>
              
            </div>
        </div>
    </form>
    <!-- this is for multiple bin configuration -->

    <!-- end edit warehouse model -->
    <form action="" class="" id="warehouse_configuration" method="POST">
      <input type="hidden" name="_token" id="csrf-token1" value="{{ Session::token() }}" />
      <div class="modal fade modal-scroll" id="addFreeBie" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
              <h4 class="modal-title">Location Configuration</h4>
            </div>
            <div class="modal-body model_style">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group err">
                    <label class="control-label">Warehouse Name</label>
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <select name="warehouse_name" id="warehouse_name"  class="form-control" onchange="addProducts()"> 
                        @if(isset($warehouseList))
                         <option value="0">Please Select...</option>
                            @foreach($warehouseList as $wareshouseName)
                                <option value="{{$wareshouseName['le_wh_id']}}">{{$wareshouseName['wh_location']}}</option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden"  id="warehouse_config_id" name="warehouse_config_id" placeholder="name" class="form-control" value="">
                     <input type="hidden"  id="parent_loc_id" name="parent_loc_id" placeholder="name" class="form-control" value="">
                    <input type="hidden"  id="edit_location_name" name="edit_location_name" placeholder="name" class="form-control" value="">
                     <input type="hidden"  id="wh_loc_id" name="wh_loc_id" placeholder="name" class="form-control" value="">
                    <input type="hidden"  id="le_wh_id" name="le_wh_id" placeholder="name" class="form-control" value="">    
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group err">
                    <label class="control-label">Warehouse Location Type</label>
                    <span class="custom-dropdown custom-dropdown--white">    
                     <input type="hidden"  id="level_type_text" name="level_type_text" placeholder="name" class="form-control" value="">
                     <input type="hidden"  id="level_type_value" name="level_type_value" placeholder="name" class="form-control" value="">
                     <select name="level_type" id="level_type"  class="form-control">
                        <option value="0">Please select</option>
                            @if(isset($locationType))
                                @foreach($locationType as $locationValue) 
                                    @if($locationValue['location_value']!=120001)           
                                    <option value="{{$locationValue['location_value']}}">{{$locationValue['location_name']}}</option>
                                    @endif
                                @endforeach
                            @endif
                      </select>
                    </span>
                  </div>
                </div>       
              </div>
              <div class="row"> 
                <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Location Name List</label>    
                        <select name="location_name_type" id="location_name_type"  class="form-control select2me">
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Location Name</label>    
                        <input name="location_name" id="location_name" placeholder="Location Name " class="form-control" />
                    </div>
                </div>
              </div>
              <div class="row reserved_product_id_class" >
                <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Product Group Name</label>
                          <select class="form-control select2me" id="product_group2" onchange="getBinLevelByGroPro()" name="product_group2">
                            <option value="0">Please select...</option>
                            @if(isset($grp_products))
                              @foreach($grp_products as $grpValue)
                              <option value="{{$grpValue['product_group_id']}}">{{$grpValue['product_title']}}</option>
                              @endforeach
                            @endif
                          </select> 
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Preferred Product Name</label>
                          <select class="form-control select2me" id="pref_pro_id" name="pref_pro_id">
                          </select> 
                    </div>
                </div>
            </div>
            <div class="row rack_lbh_dimensions">    
              <div class="col-sm-6">
                <div class="col-md-4">
                  <div class="form-group ">
                    <label class="control-label">Length </label>
                    <input type="text"  id="rack_length" name="rack_length" placeholder="Lenght" class="form-control" value="">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group ">
                    <label class="control-label">Breadth</label>
                     <input type="text"  id="rack_breadth" name="rack_breadth" placeholder="width" class="form-control" value="" >
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group ">
                    <label class="control-label">Height </label>
                     <input type="text"  id="rack_height" name="rack_height" placeholder="height" class="form-control" value="" >
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="control-label">LBH UOM</label>    
                    <select name="rack_lenghtUom" id="rack_lenghtUom"  class="form-control ">
                    @if(isset($lenghtUom))
                        @foreach($lenghtUom as $lenghtUomVal)  
                            @if($lenghtUomVal['location_value']==12001)
                              <option value="{{$lenghtUomVal['location_value']}}" selected >{{$lenghtUomVal['location_name']}}</option>
                            @endif
                            
                        @endforeach
                    @endif
                  </select>
                </div>
              </div>             
            </div>
            <div class="row reserved_product_id_class"> 
              <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Bin Dimension Type</label>    
                      <select name="bin_dim_list" id="bin_dim_list" class="form-control select2me">
                        <input type="hidden" id="selected_bin_dim" name="selected_bin_dim" value="">
                      </select>
                  </div>
                </div> 
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Bin Category Type</label>    
                      <select name="bin_category_type" id="bin_category_type" class="form-control select2me">
                        <input type="hidden" id="selected_bin_category" name="selected_bin_category" value="">
                      </select>
                  </div>
                </div>         
              </div>
              <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Sort Order </label> 
                        <input type="number" class="form-control " min="0" name="sort_order" id="sort_order" >
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label">X Axis </label>
                        <input type="number" min="0" name="x_axis" id="x_axis" placeholder="X  Axis" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label">Y Axis </label>
                        <input type="number" min="0" name="y_axis" id="y_axis" placeholder="Y  Axis" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label">Z Axis </label>
                        <input type="number" min="0" name="z_axis" id="z_axis" placeholder="Z Axis" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <div class="row">
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn green-meadow save_warehouse" >Save</button>
                </div>
              </div>
            </div>
          </div>
        <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
      </div>
    </form>
    <!-- bin configuration  -->


    <!-- end -->
    <iframe class="lightbox"  style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        
    </iframe>
    {{HTML::style('css/switch-custom.css')}}
    <link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
    {{HTML::style('css/dragdrop/jquery-ui.css')}}
    <!--Sumoselect CSS Files-->

    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />



    @stop

    @section('style')
    <style type="text/css">
	.rightAlign { text-align:right;}
    .level_class1{
      font-size:14px !important;  font-weight:600 !important;  background:#dbdbdb !important;
    }
    .level_class2{
      font-size:12px !important; padding-left:10% !important;  font-weight: bold !important; 
    }
    .level_class3{
      font-size:12px !important; padding-left:12% !important; background:#fff !important; 
    }
    .level_class4{
     font-size:12px !important; padding-left:15% !important; background:#fff !important; 
    }
    .level_class5{
     font-size:12px !important; padding-left:17% !important; background:#fff !important; 
    }
    .ui-front{
        border-bottom: 1px solid #000 !important; 
    }
    .select2-results .select2-highlighted {
        background: rgba(0,0,0,0.3);
        color:#000;
        text-shadow:0 0px 0 #000;
        font-weight:bold;
    }
    .ui-autocomplete{
            z-index: 10100 !important; top:5px;  height:100px; width: 270px!important; overflow-y: scroll; overflow-x:hidden;
        }
      .ui-autocomplete li{ border-bottom:1px solid #efefef;width: 270px!important; padding-top:10px!important; padding-bottom:5px!important; border-width:0px 1px 0px 1px !important; border-color:#000 !important;border-style: solid !important;
        }
       .ui-icon-check{color:#32c5d2 !important;}
        .ui-igcheckbox-small-off{color:#e7505a !important;}
        .fa-thumbs-o-up{color:#3598dc !important;}
    	.fa-rupee{color:#3598dc !important;}
        .fa-pencil{color:#3598dc !important;}
        .fa-trash-o{color:#3598dc !important;}
    	.ui-iggrid-featurechooserbutton{display:none !important}
    	.ui-icon.ui-corner-all.ui-icon-pin-w{display:none !important}
    	.fa-fast-forward{color:#3598dc !important;}
    	.ui-icon-pin-s{display:none !important}

    #warehouse_config_grid_Actions {text-align:center !important;}
        

    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
    </style>
    <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
    @stop
    @section('userscript')
    @include('includes.validators')
    @include('includes.ignite')
     <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
    {{HTML::script('assets/admin/pages/scripts/warehouseconfig/warehouseconfig_grid.js')}}
    <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
    <!-- grouped product list -->
    {{HTML::script('assets/admin/pages/scripts/warehouseconfig/warehouse_form_wizard.js')}}
    @extends('layouts.footer')
    <script>
        jQuery(document).ready(function() {
          $('.reserved_product_id_class').hide();
          $('.rack_lbh_dimensions').hide();
          
        FormWizard.init();
        });


       
           var token  = $("#csrf-token").val();
           function getBinLevelByGroPro(){
             // getBinDimensions($("#product_group2").val());
              $("#pref_pro_id").select2().select2('val',0);

              getProductsByGrpId($("#product_group2").val(),$("#pref_pro_id").val());
           }
           function getProductsByGrpId(pro_group_id,pref_pr_id='') {
            
              //getBinDimensions(pro_group_id);
              if(pro_group_id!='0')
              {
                 var lol_url='/getProductsByProdutGrp/'+pro_group_id+"/"+$("#warehouse_name").val();
                  $.ajax({
                      headers: {'X-CSRF-TOKEN': token},
                      url: lol_url,
                      type: 'POST',                                         
                      success: function (rrs) 
                      { 
                        $("#pref_pro_id").html(rrs);
                        if(pref_pr_id!='no')
                        $("#pref_pro_id").select2().select2('val',pref_pr_id);
                       
                      }
                  }); 
              }
           }
            function addLevelConfiguration(wid,level_text)
            {
                $('#location_name_type').find('option').remove().end();
                $("#warehouse_name").prop('disabled', false);
                 $('#location_name_type').select2('enable');
                $("#location_name").prop('disabled', false);
                $('#warehouse_configuration')[0].reset();
                var url='/getWarehouseDetails/'+wid;
                var token  = $("#csrf-token").val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'POST',                                          
                    success: function (rs) 
                    {       
                        var level_id=rs[0].wh_location_types+1;  
                        $("#le_wh_id").val(rs[0].le_wh_id);                    
                        $("#warehouse_config_id").val(wid); 

                        $("#addWarehouse_model").click();
                    }
                });
            }
            function getBinDim(){
              getBinDimensions();
              getBinCategoryType();
            }
            function getRackLevelByWhId()
            {

              var lol_url='/getLevelWiseDetails/120006/'+$("#Rack_level_warehouse_name").val();
              $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                  url: lol_url,
                  type: 'POST',
                  success: function (rrs) 
                  { 
                    $("#rack_level_name").html(rrs);
                    
                  }
              }); 
            }
            function getRackCapacity(){
              var bin_dim_id=$("#rack_bin_type").val();
                var url='/checkRackCapacity';     
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'POST',
                    data:{'bin_dim_id':bin_dim_id,'wh_id':$("#Rack_level_warehouse_name").val(),'rack_id':$("#rack_level_name").val()},                                         
                    success: function (rrs) 
                    { 
                      if(rrs==='false')
                      {
                        alert("Please enter rack dimension."); 
                      }else if(rrs==0)
                      {
                        alert("Bins are not available for respective bin type.");
                      }else
                      {
                        $("#rack_no_bins").val(rrs);
                      }
                    }
                }); 
            }
            function editLevelConfiguration(wid,level_text)
            {
                var url='/getWarehouseDetails/'+wid;
                var token  = $("#csrf-token").val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'POST',                                          
                    success: function (rs) 
                    {       
                        var lol_url='/getLevelWiseDetails/'+rs[0].wh_location_types+'/'+rs[0].le_wh_id;
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            url: lol_url,
                            type: 'POST',
                            data:{'edit_location_type':rs[0].parent_loc_id},                                         
                            success: function (rrs) 
                            { 
                                $("#location_name_type").html(rrs);
                                $("#location_name_type").select2().select2('val',rs[0].parent_loc_id);
                            }
                        }); 

                        var level_id=rs[0].wh_location_types;
                        $("#edit_location_name").val(rs[0].wh_location);  
                        $("#warehouse_name").val(rs[0].le_wh_id);
                        $("#warehouse_name").attr('disabled','disabled'); 
                        $("#le_wh_id").val(rs[0].le_wh_id);
                        $("#parent_loc_id").val(rs[0].parent_loc_id);
                        $("#location_name").val(rs[0].wh_location);
                        $("#level_type_value").val(rs[0].wh_location_types);
                        if(rs[0].wh_location_types!='120006')
                        {
                          $('.reserved_product_id_class').hide();                          
                        }else
                        {
                          $('.reserved_product_id_class').show();
                           $("#pref_pro_id").select2().select2('val',0);
                           $("#pref_pro_id").select2().select2('val',0);
                        }
                        /*if(rs[0].wh_location_types!='120005')
                        {
                          $('.rack_lbh_dimensions').hide();
                        }else
                        {
                          $('.rack_lbh_dimensions').show();
                        }*/
                        $("#level_type").val(rs[0].wh_location_types);
                        $("#wh_loc_id").val(rs[0].wh_loc_id);   
                        $("#level_type").attr('disabled','disabled');
                        $('#location_name_type').select2('disable');
                        $("#location_name").prop('disabled', true); 
                        $("#rack_length").val(rs[0].length);
                        $("#rack_breadth").val(rs[0].breadth);                    
                        $("#rack_height").val(rs[0].height);
                        $("#rack_lenghtUom").select2().select2('val',rs[0].lenght_UOM);  
                        var sort_order=(rs[0].sort_order==null)?0:rs[0].sort_order;
                        $("#sort_order").val(sort_order);
                        $("#x_axis").val(rs[0].x);
                        $("#y_axis").val(rs[0].y);
                        $("#z_axis").val(rs[0].z);
                         $("#selected_bin_dim").val(rs[0].bin_type_dim_id);
                        $("#product_group2").select2().select2('val',rs[0].res_prod_grp_id); 
                         getBinDimensions(rs[0].res_prod_grp_id,rs[0].bin_type_dim_id);
                         getBinCategoryType(rs[0].bin_category);
                         getProductsByGrpId(rs[0].res_prod_grp_id,rs[0].pref_prod_id);
                        $("#addWarehouse_model").click();

                    }
                });
            }
            function editWarehouseConfiguration(wid,level_text)
            {
                $('#editWarehouse_model')[0].reset();
                var url='/getWarehouseDetails/'+wid;
                var token  = $("#csrf-token").val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'POST',                                          
                    success: function (rs) 
                    {       
                        var level_id=rs[0].wh_location_types;
                        $("#edit_wh_loc_id").val(rs[0].wh_loc_id);
                        $("#edit_location_name").val(rs[0].wh_location);  
                        $("#edit_warehouse_name").val(rs[0].location_name);
                        $("#edit_level_type").val(level_id);
                       
                        $("#edit_level_type").attr('disabled','disabled'); 
                        var sort_order=(rs[0].sort_order==null)?0:rs[0].sort_order;
                        $("#edit_sort_order").val(sort_order);
                        $("#edit_wh_length").val(rs[0].length);
                        $("#edit_wh_breadth").val(rs[0].breadth);
                        $("#edit_wh_height").val(rs[0].height);
                        var lbh_uom=(rs[0].lbh_uom==null)?0:rs[0].lbh_uom;
                        $("#edit_lbh_uom_id").val(lbh_uom);
                        $("#edit_weight_id").val(rs[0].weight);
                       var weight_uom=(rs[0].weight_uom==null)?0:rs[0].weight_uom;
                        $("#edit_weight_uom").val(weight_uom);
                        $("#edit_x_axis").val(rs[0].x);
                        $("#edit_y_axis").val(rs[0].y);
                        $("#edit_z_axis").val(rs[0].z);
                        $("#edit_wh").click();
                    }
                });
            }
            function getBinDimensions(grp_id='',bin_id='')
            {
              if(grp_id=='')
              {
                grp_id='testing';
              }
              var url="/getBinDimensionsData/"+grp_id+'/'+$("#warehouse_name").val();
              var token  = $("#csrf-token").val();
              $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'GET',                                           
                    success: function (rs) 
                    {
                      $("#bin_dim_list").html(rs);
                      $("#rack_bin_type").html(rs);
                      if(bin_id!='')
                      {
                        $("#bin_dim_list").select2().select2('val',bin_id);
                      }
                    }
                  });
            }
             function getBinCategoryType(cat_id='')
            {
             
              var url="/getBinCategory";
              var token  = $("#csrf-token").val();
              $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'GET',                                           
                    success: function (rs) 
                    {
                      $("#bin_category_type").html(rs);
                      if(cat_id!='')
                      {
                        $("#bin_category_type").select2().select2('val',cat_id);
                      }
                    }
                  });
            }
            function deleteLevelConfiguration(wid)
            {
            var r = confirm("Are you sure do you want to delete...?");
            if (r == true)
            {           
                var url='/deleteWarehouseLevel/'+wid;
                var token  = $("#csrf-token").val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'POST',                                          
                    success: function (rs) 
                    { 
                      if(rs=='false')
                      {
                          alert("Please delete child levels.");
                      }
                      else if(rs == 'bin_pro')
                      {
                        alert("This bin have products.");
                      }else
                      {
  						            $('#warehouse_config_grid').igTreeGrid('dataBind'); 
                          $('#warehouse_config_grid').igTreeGrid({dataSource: 'getwarehouseconfig'});
                          alert("Successfully deleted.");
                      }
                    }
                });
              }
            }
            function addProducts(){
              console.log('in product list',$('#product_group2').val());
              var url="/getGroupedProductsList"+"/"+$("#warehouse_name").val();
              var token  = $("#csrf-token").val();
              $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'GET',                                           
                    success: function (rs) 
                    {
                      $('#product_group2').empty();
                      //$('#warehouse_name').
                      console.log(rs);
                      $.each(rs,function(index,item){
                            $('#product_group2').append(
                                $("<option>",{
                                    value: item.product_group_id,
                                    text: item.product_title
                                },"</option>")
                              )
                      })
                    }
                  });
            }
            
            
            
        $(document).ready(function(){
            $('#addWarehouse_model').on('click',function(e) 
            { 
              if (e.originalEvent !== undefined)
              { 
                $('.reserved_product_id_class').hide();
                 $("#pref_pro_id").select2().select2('val',0);
                 $("#pref_pro_id").select2().select2('val',0);
                $("#parent_loc_id").val('');
                $("#edit_location_name").val('');
                $("#wh_loc_id").val('');
                $("#le_wh_id").val('');
                $('#selected_bin_dim').val('');
                $("#bin_dim_list").select2().select2('val',0);
                $('#bin_category_type').select2().select2('val',0);
                $("#location_name_type").select2().select2('val',0);
                $('#pref_pro_id').select2().select2('val',0);                
                $('.reserved_product_id_class').hide();
                $('.rack_lbh_dimensions').hide();
                $('#pref_pro_id').find('option').remove().end();
                $('#warehouse_configuration')[0].reset();
                $('#location_name_type').find('option').remove();
                $("#warehouse_name").prop('disabled', false);
                $("#level_type").prop('disabled', false);
                $('#location_name_type').select2('enable');
                $("#location_name").prop('disabled', false);
                $("#product_group2").select2().select2('val',0);
              }
             });
            $('#rackLevelMultiBinConfig_model').on('click',function(e) {
              if (e.originalEvent !== undefined)
              { 
                $('#multi_bin_config')[0].reset();
                $("#rack_level_name").select2().select2('val',0);
                $('#rack_level_name').find('option').remove().end();
                $("#rack_bin_type").select2().select2('val',0);
                $('#rack_bin_type').find('option').remove().end();
              }
             });
            

             $("#reserved_group_id").autocomplete({
                   source:'/getGroupedProducts',
                    minLength: 2,
                    select: function(event, ui) {
                        event.preventDefault();
                        $("#reserved_group_id").val(ui.item.label);
                        $("#reserved_group_id_val").val(ui.item.value);
                    }
                });
            $("#level_type").on('change',function(){
                var url='/getLevelWiseDetails/'+$(this).val()+'/'+$("#warehouse_name").val();
                var token  = $("#csrf-token").val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    type: 'POST',
                    data:{'edit_location_type':''},                                            
                    success: function (rs) 
                    { 
                        $("#location_name_type").html(rs);
                        if($("#level_type").val()=='120006' && $("#warehouse_name").val()!=0)
                        {
                           $('.reserved_product_id_class').show();                            
                           getBinDimensions();
                           getBinCategoryType();
                        }else
                        {                            
                          $('.reserved_product_id_class').hide();
                          $('#product_group2').select2().select2('val',0);
                          $('#bin_dim_list').select2().select2('val',0);
                          $('#bin_category_type').select2().select2('val',0);
                          $('#pref_pro_id').select2().select2('val',0);
                          $('#pref_pro_id').find('option').remove().end();                           
                        }
                    }
                });
            });
            $("#warehouse_name").change(function(){
              $("#level_type").val(0);
              $('#location_name_type').find('option').remove().end();
            });
            $("#level_type").change(function(){
               $("#location_name_type").select2().select2('val',0);
            });
        });
    </script>

    @stop