@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Assets'); ?>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">

        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="portlet-title">
                <div class="caption">ASSETS DASHBOARD  <b  style = "color:blue; margin-left:133px;">Total Assets Val :<span > {{$assetTotalCost}}</span></b></div>
 

                    <div class="actions">

                        @if($importAssetsAccess==1)
                        <a href="#" data-id="#" data-toggle="modal" data-target="#import_asset" class="btn green-meadow">Import Asset</a>
                        @endif

                        <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document-asset-download" class="btn green-meadow">Download Asset</a>

                        <a href="#" data-id="#" data-toggle="modal" data-target="#download-depreciation_data" class="btn green-meadow">Download Depreciation Data</a>

                        @if($addAssetsAccess==1)
                        <a href="#" data-id="#" data-toggle="modal" data-target="#add_asset_product" class="btn green-meadow">Add Asset</a>
                        @endif

                        <a href="/assets/astaprdashboard" target = "_blank" data-id="#" class="btn green-meadow">Approval Asset</a>
                    </div>

            </div>
        </div>

        <div class="portlet-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                        <table id="assetsdashboardgrid"></table>
                        
                    </div>
                </div>
        
            </div>


        <!-- this html code is for download excel for asset -->
        <div class="modal fade" id="upload-document-asset-download" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Asset Download</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">

                                        {{ Form::open(array('url' => '/assets/downloadexcelforassets', 'id' => 'downloadexcel_ass'))}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Manufacturer</label>
                                                    <select id="exp_manufac"  name="exp_manufac" class="form-control select2me" onchange="loadBrandInModal();" >
                                                            <option value = "">--Please select--</option>
                                                            @foreach($getManufactureDetails as $details)
                                                                <option value = "{{$details->legal_entity_id}}">{{$details->business_legal_name}}</option>
                                                            @endforeach
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Brand</label>

                                                    <select id ="exp_brand"  name ="exp_brand" class="form-control select2me" onchange="loaddata();">
                                                            <option value = "">--Please select--</option>
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Category</label>
                                                    <select id = "exp_category"  name = "exp_category" class="form-control select2me" onchange="loaddata();">
                                                    <option value = "">--Please select--</option>
                                                        @foreach($getCategoryDetails as $categ)
                                                        <option value = "{{$categ->category_id}}">{{$categ->cat_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Asset Name</label>
                                                    <select id = "exp_asset_name"  name = "exp_asset_name" class="form-control select2me">
                                                    </select>
                                                    
                                                </div>
                                            </div>

                                            
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow" id="download-excel">Download Asset</button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        {{ Form::close() }}                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- This Modal is written for Import the Asset -->
        <div class="modal fade" id="import_asset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Import Asset</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    
                                    <div class="portlet-body">
                                        {{ Form::open(['id' => 'frm_price_slab']) }}
                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                                    <input type="file" name="import_asset_file" id="import_asset_file" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn green-meadow" id="import_asset_button">Upload Asset Template</button>
                                                    </div>
                                                </div>
                                        {{ Form::close() }} 
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- this html code is for download excel for asset -->
        <div class="modal fade" id="download-depreciation_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Deprciation Data</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">

                                        {{ Form::open(array('url' => '/assets/downloaddepreciationdata', 'id' => 'downloadexcel_asset'))}}

                                        <div class="row">
                                        <div class="col-md-6">
                                                <div class="form-group" id="field_category">
                                                    <label class="control-label">Category</label>
                                                    <select id = "dep_category"  name = "dep_category" class="form-control select2me" onchange="depreciationdata();">
                                                    <option value = "">--Please select--</option>
                                                        @foreach($getCategoryDetails as $categ)
                                                        <option value = "{{$categ->category_id}}">{{$categ->cat_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group" id="field_asset">
                                                    <label class="control-label">Asset Name</label>
                                                    <select id = "dep_asset_name"  name = "dep_asset_name" class="form-control select2me">
                                                        
                                                    </select>
                                                    
                                                </div>
                                            </div>

                                            
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow" id="download-excel">Download Depreciation Data</button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        {{ Form::close() }}                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="add_asset_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">ADD ASSET</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">

                                        {{ Form::open(array('url' => '', 'id' => 'save_asset_product','files'=>'true'))}}

                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group" id="feidls_revalid">
                                                    <label class="control-label">Manufacturer</label>

                                                    <select id="mdl_manufac"  name="mdl_manufac" class="form-control select2me" onchange="loadBrand();" >
                                                            <option value = "">--Please select--</option>
                                                        @foreach($getManufactureDetails as $details)
                                                            <option value = "{{$details->legal_entity_id}}">{{$details->business_legal_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <!-- <a href="/suppliers" target="_blank"> <i class="fa fa-plus"></i></a>
                                                    <a href="#"> <i class="fa fa-refresh" onclick="getManufactureDetails()";></i></a> -->
                                                </div>

                                            </div>

                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label class="control-label"></label>
                                                    <input type="hidden" value="{{$env}}" id="env" name="env"/>
                                                    <button type="button" class="btn green-meadow assetbutton" onclick="func_add_refresh()"><i id="add_refresh" class="fa fa-plus"></i></a></button>
                                                </div>

                                            </div>


                                            

                                            <div class="col-md-6">
                                                <div class="form-group" id="feidls_revalid1">
                                                    <label class="control-label">Brand</label>
                                                    <select id ="mdl_brand"  name ="mdl_brand" class="form-control select2me">
                                                            <option value = "">--Please select--</option>
                                                            <option value = ""></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group" id="feidls_revalid2">
                                                    <label class="control-label">Product Category</label>
                                                    <select id="mdl_category"  name="mdl_category" class="form-control select2me">
                                                            <option value = "">--Please select--</option>
                                                        @foreach($getCategoryDetails as $categ)
                                                            <option value = "{{$categ->category_id}}">{{$categ->cat_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label class="control-label"></label>
                                                    <button type="button" class="btn green-meadow assetbutton" onclick="func_cat_refresh()"><i id="cat_refresh" class="fa fa-plus"></i></a></button>
                                                </div>

                                            </div>

                                            <div class="col-md-5">
                                                <div class="form-group" id="feidls_revalid7">
                                                    <label class="control-label">Asset Category</label>
                                                    <select id="ast_category"  name="ast_category" class="form-control select2me">
                                                            <option value = "">--Please select--</option>
                                                        @foreach($assetcategory as $assetcat)
                                                            <option value = "{{$assetcat->value}}">{{$assetcat->master_lookup_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>




                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group" id="feidls_revalid3">
                                                    <label for="multiple" class="control-label">Business Unit</label>
                                                    <select id ="business_unit_asset" name = "business_unit_asset" class="form-control select2me">
                                                        <option value = "">--Please select--</option>
                                                        @foreach($businessUnit as $bunits)
                                                            <option value="{{$bunits->bu_id}}">{{$bunits->bu_name}}({{$bunits->cost_center}})</option>
                                                        @endforeach
                                                    </select>
                                                </div> 
                                            </div>
                                             <div class="col-md-6">
                                                <div class="form-group" id="fields_revalid8">
                                                    <label class="control-label">Asset Type</label>
                                                    <select id="asset_type"  name="asset_type" class="form-control select2me">
                                                    <option value = "">--Please select--</option>
                                                    <option value = "1">movable</option>
                                                    <option value = "0">non-movable</option>
                                                    </select>
                                                </div>
                                            </div>                                         
                                            
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group" id="feidls_revalid4">
                                                    <label class="control-label">Asset Name</label>
                                                    <input type="text" name="asset_name" id="asset_name" class="form-control" placeholder="Enter ProductName">
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group" id="feidls_revalid5">
                                                    <label class="control-label">MRP</label>
                                                    <input type="text" id="prd_mrp" name="prd_mrp" class="form-control" placeholder="Enter MRP">
                                                </div>                                        
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Asset Image</label>
                                                    <input type="file" name="proof_image" id="proof_image">
                                                </div>
                                            </div>
                                           
                                        </div>


                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <button type="submit" id="addAsset_revalid" class="btn green-meadow" id="download-excel">Add Asset</button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        {{ Form::close() }}
                                                                              
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 


        <div class="modal fade" id="allocate_asset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-id="#">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Allocate Asset</h4>
                    </div>
                    <div id="historyContainer" class="openhist">
                    </div>
                    <div class="modal-body mypagecheck">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">

                                        {{ Form::open(array('url' => '/assets/addAsset', 'id' => 'allocate_data'))}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Company Asset Code</label>
                                                    <input type = "text" id ="company_asset_code" name = "company_asset_code" class="form-control" readonly>
                                                    <input type="hidden" id="hidden_product_id" name="hidden_product_id">
                                                    <input type="hidden" id="hidden_asset_id" name="hidden_asset_id">
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Asset Name</label>
                                                    <input type = "text" id ="add_asset_name" name = "add_asset_name" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Purchase Date</label>
                                                        <input type="text" id="date" name="purchase_date" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Invoice Number</label>
                                                        <input type = "text" id ="invoice_number" name = "invoice_number" class="form-control" readonly>
                                                        
                                                    </div>
                                                </div>
                                        </div>

                                        <div class="row changetype" style="display:none">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Status</label>
                                                    <select id="select_part" name="select_part" class="form-control" onChange="loadDatatype(this)";>
                                                        <option value="0">Allocated</option>
                                                        <option value="1">De-Allocated</option>
                                                        <option value="2">Repaired</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- This Section for Allocation and De-Allocation -->
                                        <div class="row allocation" style="display:none" >
                                            <div class="col-md-6">
                                                <div class="form-group" id="allocate_to_div">
                                                    <label class="control-label" id="allocate_to_lbl">Allocate To</label>
                                                    <input type="text" name="allocate_to" id="allocate_to" class="form-control" placeholder="search name">
                                                    <input type="hidden" name="asset_user_id" id="asset_user_id">                                             
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group" id="allocate_date_div" >
                                                    <label class="control-label" id="allocation_date_lbl">Allocation Date</label>
                                                    <input type="text" name="allocation_date" id="allocation_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Comment</label>
                                                    <textarea name="allocation_comment" id="allocation_comment" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row repair" style="display:none" >
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Repair</label>
                                                    <input type="text" name="repair_to" id="repair_to" class="form-control">
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Repair Date</label>
                                                    <input type="text" name="repair_date" id="repair_date" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Comments</label>
                                                    <textarea name="repair_comment" id="repair_comment" class="form-control"></textarea>
                                                    
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row"> 
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <span name="htmldata" id="htmldata" style="color:red"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                            <button type="submit" class="btn green-meadow" id="asset-save-button">Save</button>
                                            </div>
                                        </div>
                                        {{ Form::close() }}                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <!-- View Modal -->
        <div class="modal fade" id="view-asset-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Asset Information</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12">			
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped table-condensed flip-content">
                                        <tr>
                                            <td width="28%" rowspan="2" align="center" valign="middle"style="background-color:#fff">
                                            <a href="" id="a_prof_image" target="_blank"><img src="" id="prof_image" style="width:30%" alt="No Proof Available"></a>
                                            </td>
                                            <td align="left" valign="middle"><strong>AssetName</strong></td>
                                            <td align="left" valign="middle"><strong>SR NO</strong></td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle"  id ="assetproduct_name"></td>
                                            <td align="left" valign="middle" id="sr_no"></td>
                                        </tr>
                                        <tr>
                                    </tr>
                                    </table>						
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="panel panel-primary" style="border-color: #ccc;">
                                    <div class="panel-heading" style="    color: #000;
                background-color: #ccc;
                border-color: #ccc;
            }">
                                        <h3 class="panel-title">Asset History</h3>
                                        
                                    </div>
                                    <table class="table table-bordered table-striped table-condensed flip-content" id="dev-table">
                                        <thead>
                                            <tr>
                                                <th>Allocated To</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyContainerData">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- UPDATE ASSETS SECTION -->
        <div class="modal fade" id="update-document_asset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-id="#">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Update Asset</h4>
                    </div>
                    <div class="modal-body venky">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">

                                        {{ Form::open(array('url' => '/assets/updateAsset', 'id' => 'update_asset_data'))}}

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Company Asset Code</label>
                                                    <input type = "text" id ="update_company_asset_code" name = "update_company_asset_code" class="form-control" readonly>
                                                    <input type="hidden" id="update_product_id" name="update_product_id">
                                                    <input type="hidden" id="asset_id" name="asset_id">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Asset Name</label>
                                                    <input type = "text" id ="update_asset_name" name = "update_asset_name" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Business Unit</label>
                                                    <select id ="update_business_unit" name = "update_business_unit" class="form-control">
                                                        @foreach($businessUnit as $bu)
                                                            <option value="{{$bu->bu_id}}">{{$bu->bu_name}}({{$bu->cost_center}})</option>
                                                        @endforeach
                                                    </select>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Is Working</label>
                                                    <input type="radio" name="update_is_working" value="Yes" checked> YES<br>
                                                    <input type="radio" name="update_is_working" value="No"> NO<br>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Warranty</label>
                                                    <input type="radio" name="update_warranty" value="Yes" checked> YES<br>
                                                    <input type="radio" name="update_warranty" value="No"> NO<br>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Asset Category</label>
                                                    <input type="hidden" value="" id="product_id_update" name="product_id_update">
                                                     <select id ="asset_category_id" name = "asset_category_id" class="form-control">
                                                        @foreach($assetcategory as $assetdata)
                                                            <option value = "{{$assetdata->value}}">{{$assetdata->master_lookup_name}}</option>
                                                        @endforeach
                                                    </select>
                                                   
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Purchase Date</label>
                                                    <input type="text" id="purchase_date" name="update_purchase_date" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Invoice Number</label>
                                                    <input type = "text" id ="update_invoice_number" name = "update_invoice_number" class="form-control" readonly>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Serial No</label>
                                                    <input type="text" id="update_serial_no" name="update_serial_no" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">MRP</label>
                                                    <input type="text" id="asset_mrp" name="asset_mrp" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                            
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Warranty Year</label>
                                                    <select name="year" id="year" onchange ="calculatedate(this);" class="form-control">
                                                        <option value="" selected="selected">0</option>
                                                        @for( $i=1; $i<=20; $i++ )
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Warranty Month</label>
                                                    <select name="month" id= "month" onchange ="calculatedate(this);" class="form-control">
                                                    <option value="" selected="selected">0</option>
                                                        @for( $i=1; $i<=12; $i++ )
                                                            <option value="{{ $i }}" >{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Warranty Date as per Purchase</label>
                                                    <input type = "text" id ="update_warranty_amc_date" name = "update_warranty_amc_date" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>


                                         <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Depreciation age</label>
                                                    <select name="depresiation_age" id="depresiation_age" onchange ="calculatedepresiation(this);" class="form-control">
                                                        @for( $i=1; $i<=60; $i++ )
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Depreciation Date</label>
                                                    <input type = "text" id ="depresiation_date" name = "depresiation_date" class="form-control" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Residual Value</label>
                                                    <input type="text" id="depression_amount" name="depression_amount" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>

                                                
                                        <div class="row">    
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Notes</label>
                                                    <textarea id="update_notes" name="update_notes" rows="5" cols="5" class="form-control"></textarea>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                            <button type="submit" class="btn green-meadow" id="asset-save-button">UPDATE</button>
                                            </div>
                                        </div>

                                        {{ Form::close() }}                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@stop

@section('style')

<style type="text/css">
.assetbutton{
        margin-top: 20px !important;
    margin-left: -22px !important;
}
.timline_style {
    padding: 0 5px 5px 13px !important;
    margin-bottom:-32px !important;
}
.timeline-body {
    position: relative !important;
    padding: 13px !important;
    margin-top: 19px !important;
    margin-left: 71px !important;
}
.changedByName{margin-left:-71px !important;}
.modal-content {
    padding-bottom: 20px;
}
.push_right {
    margin-left: 30px !important;
}
.timeline {
margin-bottom: 0px !important;
}

.timeline-body {
    font-weight: normal !important;
}

.alignRight{
    text-align: right;
    padding-right: 15px;
}

.summariesStyle{
    font-weight: bold; 
    text-align: right;
    padding-right: 15px;
}
.ui-iggrid-summaries-footer-text-container{
    text-align: right;
    font-weight: bold;
}

.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:410px !important; white-space: pre-wrap !important;}
</style>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>


@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/assets/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/assets/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/assets/assetsModel.js') }}" type="text/javascript"></script>

@stop
@extends('layouts.footer') 
