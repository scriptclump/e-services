@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('style')
{{HTML::style('jqwidgets/styles/jqx.base.css')}}
@stop
@section('style')
<style type="text/css">
    .box-header i {
        font-size: 12px !important;
        margin-right: 5px !important;
    }
    .jqx-grid-cell1 i {
        font-size: 16px !important;
        background-color: #fff !important;
        border-radius: 50px;
        padding: 5px;
        margin: 0px 5px;
        color: #7a7a7a;
        width: 30px;
        height: 28px !important;
        text-align: center;
        box-shadow: 0 0px 4px 0 rgba(0, 0, 0, 0.1), 0 2px 5px 0 rgba(0, 0, 0, 0.12);
    }
    .form .form-row-seperated .form-group {
        border-bottom: 0px solid #efefef !important;
        padding: 0px 0 !important;
    }
    .text-align-reverse {
        text-align: left !important;
    }
    .cell-color {
        background-color: #edfcff;
        text-align: center;
    }
    .form-horizontal .control-label{
        padding-top:0px !important;
    }
    .nav-tabs {
        border-bottom: 1px solid #ddd;
        margin-bottom: 15px;
    }
    .form-control{
        width:97% !important;
    }
    .wordwrap{
        white-space: pre;
        white-space: pre-wrap;
        white-space: pre-line;
        white-space: -pre-wrap;
        white-space: -o-pre-wrap;
        white-space: -moz-pre-wrap;
        word-wrap: break-word;
    }
    .nav-tabs-custom{box-shadow: 0 0px 0px rgba(0,0,0,0) !important;}
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
       
        overflow-x: hidden;
        
        padding-right: 20px;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 100px;
    }
</style>
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box-header">
            <div class="box-header no-padding">
                <h3 class="box-title">Manage Channels</h3>
                <div class="portlet-title pull-right">
                    <div class="actions"><a class="btn btn-primary btn-sm" href="javascript:;"  id="btn_cont_edit">
                            <i class="fa fa-plus"></i> Save & Continue </a>
                        <a class="btn btn-primary btn-sm" href="javascript:;" id="Cancel_btn"> <i class="fa fa-remove"></i> Cancel </a> </div>
                </div>
            </div>
        </div>
        <div class="box-body">
        <div class="portlet-body form">
            <div class="form-body">
                <!--tabs block start-->
                <div class="portlet">
                    <div class="portlet-body">
                        <div class="tabbable">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs main_list">
                                    <li class="active">
                                        <a data-toggle="tab" href="#tab_general" aria-expanded="true">
                                            Basic </a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="tab" href="#tab_meta" aria-expanded="false">
                                            Channel Level Charges </a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="tab" href="#tab_images" aria-expanded="false">
                                            Map Channel Categories </a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="tab" href="#tab_fee" aria-expanded="false">
                                            Channel Category Fee </a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="tab" href="#tab_reviews" aria-expanded="false">
                                            Shipping Fee
                                        </a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="tab" href="#tab_history" aria-expanded="false">
                                            Integration </a>
                                    </li>
                                </ul>
                                <div class="tab-content no-space">
                                    <div id="tab_general" class="tab-pane active">
                                        <form class="form-horizontal" action="#" enctype="multipart/form-data" id="update_channel_form">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Name</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" id="channnel_name" name="channnel_name" value="<?php echo $channel_data->channnel_name ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Type</label>
                                                            <div class="col-md-9">
                                                                <div class="radio-list">
                                                                    <?php
                                                                    //echo $channel_data->channel_type;
                                                                    $arr = explode(", ", $channel_data->channel_type);

                                                                    //print_r($arr);
                                                                    ?>

                                                                    <label><span class=""><input type="checkbox" name="channel_type" id="channel_type" value="B2B" <?php
                                                                            if (in_array('B2B', $arr)) {
                                                                                echo "checked";
                                                                            }
                                                                            ?> ></span>&nbsp;B2B</label>
                                                                    <label><span class=""><input type="checkbox" name="channel_type" id="channel_type" value="B2C" <?php
                                                                            if (in_array('B2C', $arr)) {
                                                                                echo "checked";
                                                                            }
                                                                            ?> ></span>&nbsp;B2C </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Description</label>
                                                            <div class="col-md-9">
                                                                <textarea id= "channel_description"  name="channel_description" class="form-control" style="height:100px !important"><?php echo $channel_data->channel_description ?> </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Logo</label>
                                                            <div class="col-md-9" >
                                                                <div><img src="<?php echo $channel_data->channel_logo; ?>" height="22" width="25" class="pull-left" ></div>
                                                                <input id="image" type="file" name="channel_logo" class="pull-left" />
                                                            </div>
                                                        </div>
                                                    </div>                                                        
                                                </div>
                                                <!--/row-->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Url</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" value="<?php echo $channel_data->channel_url ?>" id="channel_url" name="channel_url">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Enable Logo*</label>
                                                            <div class="col-md-9" >
                                                                <div><img src="<?php echo $channel_data->channel_logo; ?>" height="22" width="25" class="pull-left" ></div>
                                                                <input id="image_enable" type="file" name="channel_enable_logo" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Price Url</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" value="<?php echo $channel_data->price_url ?>" id="price_url" name="price_url">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Channel Disable Logo*</label>
                                                            <div class="col-md-9" >
                                                                <div><img src="<?php echo $channel_data->channel_logo; ?>" height="22" width="25" class="pull-left" ></div>
                                                                <input id="image_disable" type="file" name="channel_disable_logo">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/row-->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">T & C Url</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" value="<?php echo $channel_data->tnc_url ?>"  id="tnc_url" name="tnc_url">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Shipping Url</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control"  id="shipping_url" name="shipping_url" value="<?php echo $channel_data->shipping_url ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Location*</label>
                                                            <div class="col-md-9">
                                                                <select id="location"  name="location" class="form-control select2">
                                                                    <option value="">Select...</option>
                                                                    @foreach($location as $locations) 
                                                                    <?php
                                                                    $select = '';
                                                                    if ($locations->iso_code_3 == $channel_data->country_code)
                                                                        $select = 'selected="selected"';
                                                                    ?>
                                                                    <option {{$select}} value="{{$locations->iso_code_3}}">{{$locations->name}}</option>
                                                                    @endforeach          
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--/span-->
                                                </div>
                                                <!--/row-->
                                            </div>
                                        </form>
                                    </div>
                                    <div id="tab_meta" class="tab-pane">
                                        <div class="form-body">
                                            <div class="portlet-body">
                                                <a href="#myModal1" role="button" id="add_charges" class="btn btn-info pull-right" data-toggle="modal" style="margin-bottom:10px;">ADD Charges</a>
                                                <div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                <h4 class="modal-title">Add Channel Charges</h4>
                                                            </div>
                                                            <form id="channel_level_charges" action="#">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Service Name*</label>
                                                                                <select id="service_type_id"  name="service_type_id" class="form-control">
                                                                                    <option value="">Select...</option>
                                                                                    @foreach($serviceTypes as $serviceType)
                                                                                    <option value="{{$serviceType->service_type_id}}">{{$serviceType->service_name}}</option>
                                                                                    @endforeach          </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Mode of Payment*</label>
                                                                                <input type="hidden" name="charge_idd" id="charge_idd" value="0">
                                                                                <select id="recurring_interval" name="recurring_interval" class="form-control">
                                                                                    <option value="">Select...</option>
                                                                                    @foreach($paymentsTypes as $paymentsType)
                                                                                    <option value="{{$paymentsType->value}}">{{$paymentsType->master_lookup_name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Ebutor Fee*</label>
                                                                                <input id ="ebutor_fee" name="ebutor_fee" type="text" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Charges*</label>
                                                                                <input id="charges" name="charges" type="text" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Charge Type*</label>
                                                                                <select id="charge_type" name="charge_type" class="form-control">
                                                                                    @foreach($chargeType as $chargeTypes)
                                                                                    <option  value="{{$chargeTypes->value}}">{{$chargeTypes->master_lookup_name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Currency*</label>
                                                                                <select id = "currency_id" name= "currency_id" class="form-control">
                                                                                    <option value="">Select Currency</option>
                                                                                    @foreach($CurrencyTypes as $CurrencyType)
                                                                                    <option  value="{{$CurrencyType->currency_id}}">{{$CurrencyType->code}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Is Recurring?*</label>
                                                                                <select id="is_recurring" name="is_recurring" class="form-control">
                                                                                    <option value="0">Yes</option>
                                                                                    <option value="1">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="submit" class="btn btn-info" aria-hidden="true" value="Submit">
                                                                    <button class="btn yellow" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover" id="sample_1">
                                                    <thead>
                                                        <tr>
                                                        <!-- <th class="table-checkbox"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes"/></th> -->
                                                            <th>Services Type</th>
                                                            <th>Ebutor Fee*</th>
                                                            <th>Charges</th>
                                                            <th>Charges Type</th>
                                                            <th>Currency</th>
                                                            <th>Is Recurring</th>
                                                            <th>Recurring Interval</th>
                                                            <th width="150">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id= "chargesData">

                                                        @foreach($channelCharges as $channelCharge)

                                                        <tr class="odd gradeX">
                                                        <!-- <td><input type="checkbox" class="checkboxes" value="1"/></td> -->
                                                            <td>{{$channelCharge->service_name}}</td>
                                                            <td>{{$channelCharge->ebutor_fee}}</td>
                                                            <td>{{$channelCharge->charges}}</td>
                                                            <?php if ($channelCharge->charge_type == "34001") { ?>
                                                                <td>Percentage</td> <?php } else if ($channelCharge->charge_type == "34002") { ?>
                                                                <td>Value</td><?php } else { ?>
                                                                <td>Not Defined</td> <?php } ?>
                                                            <td>{{$channelCharge->code}}</td>
                                                            <td><?php
                                                                if ($channelCharge->is_recurring == "1") {
                                                                    $rec = "No";
                                                                } else {
                                                                    $rec = "Yes";
                                                                }
                                                                echo $rec;
                                                                ?></td>
                                                            <td>{{$channelCharge->recurring_interval}}</td>
                                                            <td class="jqx-grid-cell1">
                                                                <a href="#" role="button" class="edit-charge-iddd" data-channel-idd="{{$channelCharge->channel_charges_id}}" style="margin-bottom:10px;" title="{{$channelCharge->channel_charges_id}}"><i class="fa fa-pencil"></i></a>
                                                                <a href="javascript:;" id="delID"  onclick= "channelchargeDel({{$channelCharge->channel_charges_id}})" data-toggle="tooltip" title="Remove"><i class="fa fa-trash-o"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab_images" class="tab-pane">
                                        <div class="text-align-reverse margin-bottom-10" id="tab_images_uploader_container" style="position: relative;">
                                            <!-- <a href="#myModal2" role="button"  class="btn blue pull-right" data-toggle="modal" style="margin-bottom:10px;">ADD Category</a>
                                            <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            -->     
                                            <div class="container">

                                                <!-- Nav tabs -->
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="active">
                                                        <a href="#categorylist" role="tab" data-toggle="tab">
                                                            <icon >Categories List</icon> 
                                                        </a>
                                                    </li>
                                                    <li><a href="#map_cat" role="tab" data-toggle="tab">
                                                            <i class="">Ebutor category mapping</i> 
                                                        </a>
                                                    </li>

                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content">
                                                    <div class="tab-pane fade active in" id="categorylist">
                                                        <div class="container" >       
                                                            <div class="table-responsive" id="mapping_category_list">          

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="map_cat">
                                                        <div class="row">
                                                            <form class="eventInsForm" id="channel_category_map" method="post">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">

                                                                        <label>Is Parent chanenl Category *</label>
                                                                        <input type="checkbox" name="is_parent" id="is_parent" value="">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Channel Categories *</label>
                                                                        <input type="hidden" name="hidden-channel_id" id="hidden-channel_id" value="">
                                                                        <input id= "getchannel_categories1" name= "category_name" type="text" class="form-control ">
                                                                        <input id= "channel_category_id" name= "channel_category_name" type="hidden" form="class-control ">
                                                                        <input type="hidden" name="hidden-ebutor_id" id="hidden-ebutor_id" value="">
                                                                        <input type="hidden" name="channel_id" value="{{$channel_edit_id}}">
                                                                    </div>
                                                                </div>
                                                                <!--/span-->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Ebutor Categories *</label>
                                                                        <input type="hidden" name="hidden-catid" id="hidden-catid" value="0">
                                                                        <input id="getebutor_categories" name="ebutor_category_name" type="text" class="form-control ">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 text-right">
                                                                    <input type="submit" id="ebutor_category_name" name="ebutor_categor_map" value="Add" class="btn btn-info">
                                                                </div>
                                                            </form>
                                                            <!--/span-->
                                                        </div>
                                                    </div>                                                 
                                                </div>                                                
                                            </div>
                                        </div>                                            
                                    </div>
                                    <div id="tab_fee" class="tab-pane">
                                        <div class="table-container" style="">
                                            <div class="row">
                                                <form id="update_channel_fee">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Channel Category*</label>
                                                            <input type="text" name="channel_catgory_1" id="channel_catgory_1" class="form-control"/>
                                                            <input type="hidden" name="channel_catgory_id1" id="channel_catgory_id1"/>
                                                            <input type="hidden" name="channel_id1" id="channel_id1" value="{{$channel_edit_id}}"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Channel Fee*</label>
                                                            <input name="channel_cat_fee1" id="channel_cat_fee1" type="text" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Charge Type*</label>
                                                            <select id= "category_chargeType1" name="category_chargeType1" class="form-control">
                                                                <!--  <option value="">Select...</option> -->
                                                                @foreach($chargeType as $chargeTypes)
                                                                <option  value="{{$chargeTypes->value}}">{{$chargeTypes->master_lookup_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Currency*</label>
                                                            <select id= "channel_category_currency1" name="channel_category_currency1" class="form-control">
                                                                <option value="">Select...</option>
                                                                @foreach($CurrencyTypes as $CurrencyType)
                                                                <option  value="{{$CurrencyType->currency_id}}">{{$CurrencyType->code}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <input type="submit" class="btn btn-info pull-right" value="Save" aria-hidden="true">                                                    
                                                    <!--/span-->
                                                </form>
                                            </div>                                                
                                        </div>
                                        <div class="col-md-8">
                                            <div id="treeGrid"></div>                                                    
                                        </div>
                                    </div>
                                    <div id="tab_reviews" class="tab-pane">
                                        <div class="table-container" style="">
                                            <div id="datatable_reviews_wrapper" class="dataTables_wrapper dataTables_extended_wrapper no-footer">
                                                <a href="#shipping_fee_model" role="button" id="add_shipping_charges" class="btn btn-info pull-right" data-toggle="modal" style="margin-bottom:10px;">ADD Shipping Charges</a>
                                                <div id="shipping_fee_model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form action="" id="add_shipping_charges_form" name="add_shipping_charges_form">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                                    <h4 class="modal-title">Add Shipping Charges</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Shipment Type*</label>
                                                                                <input type="hidden" name="channel_id" value="{{ $channel_edit_id }}"/>
                                                                                <input type="hidden" name="shipment_id" id="shipment_id" value="">
                                                                                <select id="shipment_type" required="required" name="shipment_type" class="form-control">
                                                                                    <option value="">--Select--</option>
                                                                                    <option value="0">Heavy</option>
                                                                                    <option value="1">Non-Heavy</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Is Additional Weight?*</label>
                                                                                <select id="additional_weight" name="additional_weight" class="form-control">
                                                                                    <option value="0">No</option>
                                                                                    <option value="1">Yes</option>                                                                                    
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->                                                                        
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Start Weight*</label>                                                                                
                                                                                <input type="text" name="start_weight" id="start_weight" value="" class="form-control"/>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>End Weight*</label>
                                                                                <input type="text" name="end_weight" id="end_weight" value="" class="form-control"/>
                                                                            </div>
                                                                        </div>                                                                                                                                                    
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Unit Of Messure*</label>
                                                                                <select name="uom" id="uom" class="form-control">
                                                                                    <option value="0">gm</option>
                                                                                    <option value="1">kg</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span--> 
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Currency*</label>
                                                                                <select id="ship_currency_id" required="required" name="CurrencyTypes" class="form-control">
                                                                                    <option value="">Select Currency</option>
                                                                                    @foreach($CurrencyTypes as $CurrencyType)
                                                                                    <option  value="{{$CurrencyType->currency_id}}">{{$CurrencyType->code}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->                                                                                                                                                   
                                                                    </div>
                                                                    <div class="row"><div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Charge Type*</label>
                                                                                <select id="ship_charge_type" required="required" name="charge_type" class="form-control">
                                                                                    @foreach($chargeType as $chargeTypes)
                                                                                    <option  value="{{$chargeTypes->value}}">{{$chargeTypes->master_lookup_name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Local Charge(Intracity)*</label>
                                                                                <input type="text" id="local_charge" required="required" name= "local_charge" class="form-control"/>                                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Regional(Zonal)*</label>
                                                                                <input type="text" id="regional_charge" required="required" name= "regional_charge" class="form-control"/>                                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Metro To Metro*</label>
                                                                                <input type="text" id="metro_to_metro" name="metro_to_metro" class="form-control"/>                                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->                                                                        
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>North East*</label>
                                                                                <input type="text" id="north_east" name="north_east" class="form-control"/>                                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Jammu & Kashmir*</label>
                                                                                <input type="text" id="j_k" name="j_k" class="form-control"/>                                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Rest of India*</label>
                                                                                <input type="text" id="national_charge" name="national_charge" class="form-control"/>                                                                                        
                                                                            </div>
                                                                        </div>
                                                                        <!--/span-->
                                                                    </div>                                                                    
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="submit" class="btn default" aria-hidden="true" value="Submit">
                                                                    <button class="btn yellow" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div id="channel_shipping_charges">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab_history" class="tab-pane">
                                        <div class="table-container" style="">
                                            <div id="datatable_history_wrapper" class="dataTables_wrapper dataTables_extended_wrapper dataTables_extended_wrapper no-footer">
                                                <div class="row hidden" id= "New">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Key Name</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Key Value</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control">
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row" >
                                                    <form id="intigration_form" action="#">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Key Name</label>
                                                                <div class="col-md-9">
                                                                    <input id= "Key_name" name= "Key_name"type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Key Value</label>
                                                                <input type="hidden" name="hidden_key_id" id="hidden_key_id" value="0">
                                                                <div class="col-md-8">
                                                                    <input id= "Key_value" name= "Key_value" type="text" class="form-control">
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <span class="input-group-btn btn-right">
                                                                        <button  aria-expanded="false" data-toggle="dropdown" class="btn btn-default pull-right" type="button" onclick="CredSave()">+</button>
                                                                        <ul role="menu" class="dropdown-menu pull-right">

                                                                            <li>
                                                                                <a href="javascript:;" onclick = "CredSave()"id="CredSave">Save</a>

                                                                            </li>
                                                                            <!-- <li>
                                                                            <a href="javascript:;">Something else here</a>
                                                                            </li> -->
                                                                            <li class="divider">
                                                                            </li>

                                                                        </ul>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="portlet-body">
                                                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Value</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id= "Credentials" >
                                                                @foreach($channelCred as $channelCreds)
                                                                <tr class="odd gradeX">
                                                                    <td>{{$channelCreds->Key_name}}</td>
                                                                    <td>{{$channelCreds->Key_value}}</td>
                                                                    <td class="jqx-grid-cell1">
                                                                        <a href="#" data-toggle="tooltip" title="Edit" onclick= "editKeys({{$channelCreds->channel_configuration_id}})"><i class="fa fa-pencil"></i></a>
                                                                        <!-- <a href="#" data-toggle="tooltip" title="Remove"><i class="fa fa-trash-o"></i></a> -->
                                                                        <a href="javascript:;" id="CreddelID"  onclick= "channelCredDel({{$channelCreds->channel_configuration_id}})" data-toggle="tooltip" title="Remove"><i class="fa fa-trash-o"></i></a>
                                                                    </td>
                                                                </tr>

                                                                @endforeach

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    <!--tabs block end-->
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="tile-body nopadding">
                <div id="jqxgrid1"  style="width:100% !important;"></div>
                <button data-toggle="modal" id="editProduct" class="btn btn-default" data-target="#basicvalCodeModal2" style="display: none" data-url="{{URL::asset('product/editgdsproduct/')}}"></button>
            </div>
            <!--table start-->
            <table id="grid" class="col-sm-12"></table>
            <!--table end-->
        </div>
    </div>
    </div>
</div>
@stop

@include('includes.validators')
@include('includes.jqx')

<link rel="stylesheet" href="http://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="http://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
@section('userscript')
<script type="text/javascript">
    function getAddPage()
    {
        $.get('add', function(data){
            $("#userDiv").html(data);
        });
    }
    function getEditPage(id)
    {
    var serve = window.location.origin;
    $.get(serve + '/gdschannels/edit/' + id, function(data){
    $("#editDiv").html(data);
    });
    }
    function getEditCredPage(id)
    {
    var serve = window.location.origin;
    $.get(serve + '/gdschannels/edit_credentials/' + id, function(data){
    $("#editCredDiv").html(data);
    });
    }

    $(document).ready(function(){
    var channel_id = "<?php echo $channel_edit_id; ?>";
    get_channel_category_charges(channel_id);
    get_mapping_category_list(channel_id);
    var is_parent = 0;
    $("#is_parent").click(function(){
    if ($("#is_parent").is(':checked'))
    {
    is_parent = 1;
    $("#getchannel_categories1").autocomplete({
    minLength : 1,
            source : '/channels/getChannel_categories?channel_id=' + channel_id + '&is_parent=' + is_parent,
            select : function(event, ui) {
            var label = ui.item.label;
            $("#hidden-channel_id").val(ui.item.channel_category_id);
            }
    });
    } else
    {
    is_parent = 0;
    $("#getchannel_categories1").autocomplete({
    minLength : 1,
            source : '/channels/getChannel_categories?channel_id=' + channel_id + '&is_parent=' + is_parent,
            select : function(event, ui) {
            var label = ui.item.label;
            $("#hidden-channel_id").val(ui.item.channel_category_id);
            }
    });
    }
    });
    if ($("#is_parent").is(':checked'))
    {

    }
    else
    {
    $("#getchannel_categories1").autocomplete({
    minLength : 1,
            source : '/channels/getChannel_categories?channel_id=' + channel_id + '&is_parent=' + is_parent,
            select : function(event, ui) {
            var label = ui.item.label;
            $("#hidden-channel_id").val(ui.item.channel_category_id);
            }
    });
    }
    $("#getebutor_categories").autocomplete({
    minLength : 1,
            source : '/channels/getebutor_categories/',
            select : function(event, ui) {
            var label = ui.item.label;
            $("#hidden-ebutor_id").val(ui.item.category_id);
            }
    });
    $("#channel_catgory_1").autocomplete({
    minLength : 1,
            source : '/channels/getChannel_categories?channel_id=' + channel_id,
            select : function(event, ui) {
            var channel_category_id = ui.item.channel_category_id;
            var channel_commission = ui.item.channel_commission;
            var currency = ui.item.currency;
            var charge_type = ui.item.charge_type;
            $('#update_channel_fee').bootstrapValidator('resetForm', true);
            $('#channel_catgory_id1').val(channel_category_id);
            $('#channel_cat_fee1').val(channel_commission);
            $('#channel_category_currency1').val(currency);
            $('#category_chargeType1').val(charge_type);
            }
    });
    $('#update_channel_form').bootstrapValidator({
    message: 'This value is not valid',
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            channnel_name:{
            validators: {
            notEmpty: {
            message: 'Please enter Channel name'
            }
            }
            },
                    channel_description:{
                    validators: {
                    notEmpty: {
                    message: 'Please enter Channel Description'
                    }
                    }
                    },
                    channel_url:{
                    validators: {
                    uri: {
                    message: 'The URL is invalid'
                    },
                            notEmpty: {
                            message: 'Please enter Channel URL'
                            }
                    }
                    },
                    tnc_url:{
                    validators: {
                    uri: {
                    message: 'The URL is invalid'
                    },
                            notEmpty: {
                            message: 'Please enter T&C URL'
                            }
                    }
                    },
                    price_url:{
                    validators: {
                    uri: {
                    message: 'The URL is invalid'
                    },
                            notEmpty: {
                            message: 'Please enter Price URL'
                            }
                    }
                    },
                    shipping_url:{
                    validators: {
                    uri: {
                    message: 'The URL is invalid'
                    },
                            notEmpty: {
                            message: 'Please enter Shipping URL'
                            }
                    }
                    },
                    location:{
                    validators: {
                    notEmpty: {
                    message: 'Please select Location'
                    }
                    }
                    },
                    channel_type:{
                    validators: {
                    notEmpty: {
                    message: 'Please select Channel Type'
                    }
                    }
                    },
                    channel_logo:{
                    validators: {
                    callback:{
                    message: 'Please select valid image',
                            callback:function(value, validator, $field){
                            if (value != ''){
                            var exts = ['jpg', 'jpeg', 'png', 'gif', 'tiff'];
                            var get_ext = value.split('.');
                            // reverse name to check extension
                            get_ext = get_ext.reverse();
                            // check file type is valid as given in ‘exts’ array
                            if ($.inArray (get_ext[0].toLowerCase(), exts) > - 1){
                            return true;
                            } else{
                            return false;
                            }
                            } else{
                            return true;
                            }
                            }
                    }
                    }
                    },
                    channel_disable_logo:{
                    validators: {
                    callback:{
                    message: 'Please select valid image',
                            callback:function(value, validator, $field){
                            if (value != ''){
                            var exts = ['jpg', 'jpeg', 'png', 'gif', 'tiff'];
                            var get_ext = value.split('.');
                            // reverse name to check extension
                            get_ext = get_ext.reverse();
                            // check file type is valid as given in ‘exts’ array
                            if ($.inArray (get_ext[0].toLowerCase(), exts) > - 1){
                            return true;
                            } else{
                            return false;
                            }
                            } else{
                            return true;
                            }
                            }
                    }
                    }
                    },
                    channel_enable_logo:{
                    validators: {
                    callback:{
                    message: 'Please select valid image',
                            callback:function(value, validator, $field){
                            if (value != ''){
                            var exts = ['jpg', 'jpeg', 'png', 'gif', 'tiff'];
                            var get_ext = value.split('.');
                            // reverse name to check extension
                            get_ext = get_ext.reverse();
                            // check file type is valid as given in ‘exts’ array
                            if ($.inArray (get_ext[0].toLowerCase(), exts) > - 1){
                            return true;
                            } else{
                            return false;
                            }
                            } else{
                            return true;
                            }
                            }
                    }
                    }
                    },
            }
    }).on('success.form.bv', function(event) {
    event.preventDefault();
    var val = $('div .main_list li.active').find('a').text();
    val = val.trim();
    if (val == "Basic")
    {

    var channel_name = $("#channnel_name").val();
    //alert(channel_name);
    var channel_description = $("#channel_description").val();
    var channel_url = $("#channel_url").val();
    var favorite = [];
    $.each($("input[name='channel_type']:checked"), function(){
    favorite.push($(this).val());
    });
    var channel_type = favorite.join(", ");
    var tnc_url = $("#tnc_url").val();
    var my_dropzone = $("#my-dropzone").val();
    var price_url = $("#price_url").val();
    var shipping_url = $("#shipping_url").val();
    var channel_logo = $("#image")[0].files[0];
    var disable_logo = $("#image_disable")[0].files[0];
    var enable_logo = $("#image_disable")[0].files[0];
    var location = $("#location").val();
    var channel_logo1 = $("#image").val();
    var disable_logo1 = $("#image_disable").val();
    var formData = new FormData();
    formData.append('channnel_name', channel_name);
    formData.append('channel_url', channel_url);
    formData.append('price_url', price_url);
    formData.append('shipping_url', shipping_url);
    formData.append('tnc_url', tnc_url);
    formData.append('channel_logo', channel_logo);
    formData.append('channel_type', channel_type);
    formData.append('channel_description', channel_description)
            formData.append('disable_logo', disable_logo);
    formData.append('enable_logo', enable_logo);
    formData.append('location', location);
    var channel_idd = "<?php echo $channel_data->channel_id ?>";
    var url = window.location.origin;
    $.ajax({
    method: "POST",
            url: url + '/channels/update/' + channel_idd,
            data: formData,
            processData: false,
            contentType: false,
            success:function(data){
            $('[href="#tab_meta"]').click();
            }, fail:function(data){
    }
    });
    }
    if (val == "Channel Level Charges")
    {
    $('[href="#tab_images"]').click();
    }
    if (val == "Map Channel Categories")
    {
    $('[href="#tab_fee"]').click();
    }
    if (val == "Channel Category Fee")
    {
    $('[href="#tab_reviews"]').click();
    }
    if (val == "Shipping Fee")
    {
    $('[href="#tab_history"]').click();
    $('#btn_cont_edit').html('Submit');
    }
    if (val == "Integration")
    {
    window.location = "/channels/index";
    }
    });
    $('#channel_level_charges').bootstrapValidator({
    message: 'This value is not valid',
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            excluded: [':disabled'],
            fields: {
            service_type_id:{
            validators: {
            notEmpty: {
            message: 'Please Select Service Type'
            }
            }
            },
                    recurring_interval:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Mode of Payment'
                    }
                    }
                    },
                    ebutor_fee:{
                    validators: {
                    notEmpty: {
                    message: 'Please Enter Ebutor Fee'
                    },
                            numeric: {
                            message: 'The value is not a number',
                                    // The default separators
                                    thousandsSeparator: '',
                                    decimalSeparator: '.'
                            }
                    }
                    },
                    charges:{
                    validators: {
                    notEmpty: {
                    message: 'Please Enter Charges'
                    },
                            numeric: {
                            message: 'The value is not a number',
                                    // The default separators
                                    thousandsSeparator: '',
                                    decimalSeparator: '.'
                            }
                    }
                    },
                    charge_type:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Charge Type'
                    }
                    }
                    },
                    currency_id:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Currency'
                    }
                    }
                    },
                    is_recurring:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Recurring'
                    }
                    }
                    },
            }
    }).on('success.form.bv', function(event) {
    event.preventDefault();
    var serve = window.location.origin;
    var url = 'channelchargesstore';
    var service_type_id = document.getElementById("service_type_id").value;
    var recurring_interval = document.getElementById("recurring_interval").value;
    var ebutor_fee = document.getElementById("ebutor_fee").value;
    var charges = document.getElementById("charges").value;
    var charge_type = document.getElementById("charge_type").value;
    var currency_id = document.getElementById("currency_id").value;
    var is_recurring = document.getElementById("is_recurring").value;
    var charge_idd = document.getElementById("charge_idd").value;
    var id = "<?php echo $channel_edit_id; ?>";
    var datstring = "service_type_id=" + service_type_id + "&recurring_interval=" + recurring_interval + "&ebutor_fee=" + ebutor_fee + "&charges=" + charges + "&charge_type=" + charge_type + "&currency_id=" + currency_id + "&is_recurring=" + is_recurring + "&charge_idd=" + charge_idd;
    var serve = window.location.origin;
    var url = serve + '/channels/channeleditchargesstore/' + id;
    //alert(service_type_id);
    //alert(datstring);
    //return false;
    $.ajax({
    method: "POST",
            url: url,
            data: datstring,
            success:function(data)
            {
            //alert("success")
            getChannelChargesData();
            // $('[href="#tab_images"]').click();
            }, fail:function(data)
    {
    alert("failed");
    }

    });
    $('.close').trigger('click');
    });
    $('#channel_category_map').bootstrapValidator({
    message: 'This value is not valid',
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            category_name:{
            validators: {
            notEmpty: {
            message: 'Please Enter Channel Category Name'
            }
            }
            },
                    ebutor_category_name:{
                    validators: {
                    notEmpty: {
                    message: 'Please Enter Ebutor Category Name'
                    }
                    }
                    },
            }
    }).on('success.form.bv', function(event) {
    event.preventDefault();
    $form_data = $(this).serialize();
    var form_url = "/channels/ebutor_mapping?" + $form_data;
    $.ajax({
    url:form_url,
            success:function(data){
            get_mapping_category_list(channel_id);
            $('[href="#categorylist"]').click();
            $('#update_channel_fee').bootstrapValidator('resetForm', true);
            },
            fail:function(data){
            alert("failed");
            }
    });
    });
    $('#update_channel_fee').bootstrapValidator({
    message: 'This value is not valid',
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            channel_catgory_1:{
            validators: {
            notEmpty: {
            message: 'Please Enter Category Name'
            }
            }
            },
                    channel_cat_fee1:{
                    validators: {
                    notEmpty: {
                    message: 'Please Enter Channel Fee'
                    },
                            numeric: {
                            message: 'The value is not a number',
                                    // The default separators
                                    thousandsSeparator: '',
                                    decimalSeparator: '.'
                            }
                    }
                    },
                    category_chargeType1:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Charge Type'
                    }
                    }
                    },
                    channel_category_currency1:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Currency'
                    }
                    }
                    },
            }
    }).on('success.form.bv', function(event) {
    event.preventDefault();
    $form_data = $(this).serialize();
    var form_url = "/channels/update_category_fee?" + $form_data;
    $.ajax({
    url:form_url,
            success:function(data){
            get_channel_category_charges(channel_id);
            },
            fail:function(data){
            alert("failed");
            }
    });
    });
    $('#add_shipping_charges_form').bootstrapValidator({
    message: 'This value is not valid',
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            shipment_type:{
            validators: {
            notEmpty: {
            message: 'Please Select Shipment Type'
            }
            }
            },
                    start_weight:{
                    validators: {
                    callback:{
                    message: 'Please Enter Start Weight',
                            callback:function(value, validator, $field){
                            var extra_weight = $('#additional_weight').val();
                            if (extra_weight == 0 && value == ''){
                            return false;
                            } else{
                            return true;
                            }
                            }
                    }
                    }
                    },
                    end_weight:{
                    validators: {
                    callback:{
                    message: 'Please Enter End Weight',
                            callback:function(value, validator, $field){
                            var extra_weight = $('#additional_weight').val();
                            if (extra_weight == 0 && value == ''){
                            return false;
                            } else{
                            return true;
                            }
                            }
                    }
                    }
                    },
                    CurrencyTypes:{
                    validators: {
                    notEmpty: {
                    message: 'Please Select Currency'
                    }
                    }
                    },
                    local_charge:{
                    validators: {
                    notEmpty: {
                    message: 'Please Enter Local Charge'
                    },
                            numeric: {
                            message: 'The value is not a number',
                                    // The default separators
                                    thousandsSeparator: '',
                                    decimalSeparator: '.'
                            }
                    }
                    },
            }
    }).on('success.form.bv', function(event) {
    event.preventDefault();
    $form_data = $(this).serialize();
    $shipment_id = $('#shipment_id').val();
    if ($shipment_id == ''){
    var form_url = "/channels/addshipping_charges?" + $form_data;
    } else{
    var form_url = "/channels/updateshipping_charges?" + $form_data;
    }
    $.ajax({
    url:form_url,
            success:function(channel_id){
            get_all_shipping_charges(channel_id);
            $('#shipping_fee_model').modal('hide');
            }
    });
    });
    $('#intigration_form').bootstrapValidator({
    message: 'This value is not valid',
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            Key_name:{
            validators: {
            notEmpty: {
            message: 'Please Enter Key Name'
            }
            }
            },
                    Key_value:{
                    validators: {
                    notEmpty: {
                    message: 'Please Enter Key Value'
                    }
                    }
                    },
            }
    }).on('success.form.bv', function(event) {
    event.preventDefault();
    var Key_name = document.getElementById("Key_name").value;
    var Key_value = document.getElementById("Key_value").value;
    var hidden_key_id = document.getElementById("hidden_key_id").value;
    if (hidden_key_id == "0"){
    var channel_configuration_id = "0";
    //alert("in");
    } else
    {
    //alert("here");

    var channel_configuration_id = hidden_key_id;
    }
    var channel_edit_id = "<?php echo $channel_edit_id; ?>";
    var url = '/channels/categoryEditCredStore/' + channel_edit_id;
    var datastring = "Key_name=" + Key_name + "&Key_value=" + Key_value + "&hidden_key_id=" + hidden_key_id + "&channel_configuration_id=" + channel_configuration_id + "&channel_edit_id" + channel_edit_id;
    $.ajax({
    method: "POST",
            url: url,
            data: datastring,
            success:function(data)
            {
            //alert("success");
            $("#Key_name").val("");
            $("#Key_value").val("");
            $("#hidden_key_id").val("0");
            geteditCredentials(channel_edit_id);
            }, fail:function(data)
    {
    alert("failed");
    }

    });
    });
    $(".modal").on('hide.bs.modal', function(){
    var form_id = $(this).find('form').attr('id');
    $('#' + form_id).bootstrapValidator('resetForm', true);
    $('#' + form_id)[0].reset();
    });
    })
            $("#btn_cont_edit").click(function(){
    $('#update_channel_form').submit();
    });
    // getCategories();
    function get_mapping_category_list(channel_id){
    $.ajax({
    url:"/channels/ebutor_channel_category_list?channel_id=" + channel_id,
            success:function(result){
            $('#mapping_category_list').html(result);
            $('#myTable1').dataTable();
            }
    });
    }
    // getCategories();
    function get_channel_category_charges(channel_id){
    $.ajax(
    {
    url:"/channels/get_channel_category_charges?channel_id=" + channel_id,
            success: function (result)
            {
            //var employees = result;
            // prepare the data
            var source =
            {
            datatype: "json",
                    datafields: [
                    {name: 'id', type: 'varchar'},
                    {name: 'pname', type: 'varchar'},
                            //{name: 'is_active', type: 'string'},
                            {name: 'channel_commission', type: 'varchar'},
                    {name: 'Channel_ChargeType', type: 'varchar'},
                    {name: 'children', type: 'array'},
                    {name: 'expanded', type: 'bool'}
                    ],
                    hierarchy:
            {
            root: 'children'
            },
                    id: 'id',
                    localData: result
            };
            var dataAdapter = new $.jqx.dataAdapter(source);
            $("#treeGrid").jqxTreeGrid(
            {
            width: "100%",
                    source: dataAdapter,
                    filterable: true,
                    sortable: true,
                    //pageable: true,
                    columns: [
                            //{text: 'Parent', datafield: 'name', width: 150},
                            {text: 'Category Name', datafield: 'pname', width: "80%"},
                            //{text: 'Status', datafield: 'is_active', width: "15%"},
                            {text: 'Channel Commission', datafield: 'channel_commission', width: "20%"},
                            //{text: 'Status', datafield: 'stat', width: 150},
                            //{text: 'Actions', datafield: 'actions', width: "10%"}
                    ]
            });
            }

    });
    }
    function getChannelChargesData()
    {
    var serve = window.location.origin;
    var id = 0;
    if (id == "")
    {
    var id = "<?php echo $channel_edit_id; ?>";
    }
    var url = '/channels/getChanneladdChargesData/' + id;
    //var datastring  ="category_name="+category_name+"&category_parent="+category_parent+"&ebutorCategories="+ebutorCategories+"&channel_fee="+channel_fee+"&category_currency="+category_currency+"&chargeType="+chargeType;
    $.ajax({
    method: "POST",
            url: url,
            data: { },
            success:function(data)
            {
            //alert(data);
            //alert("inside getCredentials");
            $("#chargesData").empty("");
            var result = $.parseJSON(data);
            $.each(result, function (temp, val) {

            if (val['charge_type'] == "34001"){
            var val_charge_type = "Percentage";
            } else{
            var val_charge_type = "Value";
            }

            if (val['is_recurring'] == "1"){
            var rec = "No";
            } else{

            var rec = "Yes";
            }

            $('#chargesData').append('<tr><td>' + val['service_name'] + '</td><td>' + val['ebutor_fee'] + '</td><td>' + val['charges'] + '</td><td>' + val_charge_type + '</td><td>' + val['code'] + '</td><td>' + rec + '</td><td>' + val['recurring_interval'] + '</td>' + '<td class="jqx-grid-cell1"><a href="#"  data-channel-idd="' + val['channel_charges_id'] + '"data-toggle="tooltip" class="edit-charge-iddd" title="Edit"><i class="fa fa-pencil"></i></a><a href="#" data-toggle="tooltip" onclick ="channelchargeDel(' + val['channel_charges_id'] + ')" title="Remove"><i class="fa fa-trash-o"></i></a></td></tr>');
            //console.log(temp);
            //console.log(val);
            });
            //alert(result);

            //alert("in getChannelChargesData");

            }, fail:function(data)
    {
    alert("failed");
    }

    });
    }


    function geteditCredentials(geteditCredentials)
    {
    //alert(geteditCredentials);
    var serve = window.location.origin;
    var url = '/channels/getChannelEditCred/' + geteditCredentials;
    //var datastring  ="category_name="+category_name+"&category_parent="+category_parent+"&ebutorCategories="+ebutorCategories+"&channel_fee="+channel_fee+"&category_currency="+category_currency+"&chargeType="+chargeType;


    $.ajax({
    method:"POST",
            url: url,
            dataType:"json",
            success:function(data)
            {
            $("#hidden_key_id").val("0");
            //alert("inside getCredentials");
            //var result = $.parseJSON(data);
            $("#Credentials").empty();
            $.each(data, function (temp, val) {
            //alert();
            $('#Credentials').append('<tr><td>' + val['Key_name'] + '</td><td>' + val['Key_value'] + '</td><td class="jqx-grid-cell1"><a href="javascript:;" class="edit-key-id" edit-key-id="' + val['channel_configuration_id'] + '"data-toggle="tooltip" onclick="editKeys(' + val['channel_configuration_id'] + ')" title="Edit"><i class="fa fa-pencil"></i></a><a href="#" data-toggle="tooltip" onclick ="channelCredDel(' + val['channel_configuration_id'] + ')" title="Remove"><i class="fa fa-trash-o"></i></a></td></tr>');
            //console.log(temp);
            //console.log(val);
            });
            //alert(result);

            }, fail:function(data)
    {
    alert("failed");
    }

    });
    }

    function editKeys(channel_configuration_id)
    {
    var serve = window.location.origin;
    var url = '/channels/getKeyCred/' + channel_configuration_id;
    //$("#service_type_id").val(data.service_type_id);
    $.ajax({
    method: "POST",
            url: url,
            dataType:"json",
            success:function(data)
            {
            $("#hidden_key_id").val(channel_configuration_id);
            $("#Key_name").val("");
            $("#Key_value").val("");
            $("#Key_name").val(data.Key_name);
            $("#Key_value").val(data.Key_value);
            }, fail:function(data)
    {
    alert("failed");
    }


    });
    }
    function CredSave(){
    $('#intigration_form').submit();
    }
    function getcategoriesName(el) {
    var parentCategory = $(el).closest('tr').find('td:eq(0) span:last').text();
    $('#addCategory_parent_id').find('option').prop('disabled', true);
    $('#addCategory_parent_id').find('option').filter(function () {
    return ($(this).text() == parentCategory);
    }).prop({'selected': true, 'disabled': false});
    }



    $(document).on("click", ".edit-charge-iddd", function(){
    $channel_charge_id = $(this).attr('data-channel-idd');
    //alert();
    //console.log($channel_charge_id);
    $.ajax({
    type:"post",
            dataType:"json",
            url:"getchannelcharges/" + $channel_charge_id,
            success:function(data)
            {

            $("#service_type_id").val(data.service_type_id);
            $("#recurring_interval").val(data.recurring_interval);
            $("#ebutor_fee").val(data.eseal_fee);
            $("#charges").val(data.charges);
            $("#charge_type").val(data.charge_type);
            $("#currency_id").val(data.currency_id);
            $("#is_recurring").val(data.is_recurring);
            $("#charge_idd").val(data.channel_charges_id);
            // $("select#service_type_id option").filter(":selected").text(data.service_type_id);
            $("#add_charges").trigger("click");
            }

    });
    //alert();
    //$( "#add_charges" ).trigger( "click" );
    });
    $(document).on('click', ".edit-charge-iddd", function(){
    $channel_charge_id = $(this).attr('data-channel-idd');
    //console.log($channel_charge_id);
    $.ajax({
    type:"post",
            dataType:"json",
            url:"/channels/getchannelcharges/" + $channel_charge_id,
            success:function(data)
            {
            $("#service_type_id").val(data.service_type_id);
            $("#recurring_interval").val(data.recurring_interval);
            $("#ebutor_fee").val(data.eseal_fee);
            $("#charges").val(data.charges);
            $("#charge_type").val(data.charge_type);
            $("#currency_id").val(data.currency_id);
            $("#is_recurring").val(data.is_recurring);
            $("#charge_idd").val(data.channel_charges_id);
            // $("select#service_type_id option").filter(":selected").text(data.service_type_id);
            $("#add_charges").trigger("click");
            }
    });
    //alert();
    //$( "#add_charges" ).trigger( "click" );
    });
    function getCategories()
    {
    url = '/product/getcategorieslist';
    // Send the data using post
    var posting = $.get(url);
    // Put the results in a div
    posting.done(function (data) {
    //console.log(data);
    });
    }

    // getCategories();
    function get_all_shipping_charges(channel_id){
    $.ajax({
    url:"/channels/get_all_shipping_charges?channel_id=" + channel_id,
            success:function(result){
            $('#channel_shipping_charges').html(result);
            }
    });
    }
    $(document).ready(function ()
    {
    get_all_shipping_charges(<?php echo $channel_edit_id; ?>);
    $('#add_shipping_charges').click(function(e){
    $("#shipment_id").val('');
    $('#shipping_fee_model').modal('show');
    return false;
    });
    $(document).on('click', '.edit_shippingch', function(){
    $shipping_charge_id = $(this).attr('data-shipping_charge_id');
    $.ajax({
    url:"/channels/get_shipping_charges?shipping_charge_id=" + $shipping_charge_id,
            dataType:'json',
            success:function(data){
            $("#shipment_id").val(data[0].id);
            $("#shipment_type").val(data[0].shipment_type);
            $("#additional_weight").val(data[0].additional_weight_flag);
            $("#ship_currency_id").val(data[0].currency_id);
            $("#ship_charge_type").val(data[0].charge_type);
            $("#start_weight").val(data[0].start_weight);
            $("#end_weight").val(data[0].end_weight);
            $("#uom").val(data[0].uom);
            $("#local_charge").val(data[0].intracity);
            $("#regional_charge").val(data[0].regional);
            $("#metro_to_metro").val(data[0].metro_to_metro);
            $("#north_east").val(data[0].north_east);
            $("#j_k").val(data[0].j_k);
            $("#national_charge").val(data[0].rest_of_india);
            $('#shipping_fee_model').modal('show');
            return false;
            }
    });
    });
    $(document).on('click', '.delete_shippingch', function(){
    $shipping_charge_id = $(this).attr('data-shipping_charge_id');
    var con = confirm("are you sure you want to delete Shipping Fee?");
    if (con == true){
    $.ajax({
    url:"/channels/delete_shipping_charges?shipping_charge_id=" + $shipping_charge_id,
            dataType:'json',
            success:function(data){
            get_all_shipping_charges(<?php echo $channel_edit_id; ?>);
            $('#shipping_fee_model').modal('hide');
            }
    });
    }
    });
    $('#add_charges').click(function(e){
    $('#myModal1').modal('show'); return false;
    });
    $('#modalbox_2').click(function(e){ $('#myModal2').modal('show'); return false; });
    });
    function channelchargeDel(delID)
    {
    var decission = confirm("Are you sure you want to Delete.");
    if (decission == true){
    url = "/channels/delchannelCharge/" + delID;
    channel_edit_id = "<?php echo $channel_edit_id; ?>";
    $.ajax({
    method: "POST",
            url: url,
            data: { },
            success:function(data)
            {
            //alert("success");
            getChannelChargesData();
            //geteditCredentials(channel_edit_id);

            }, fail:function(data)
    {
    alert("failed");
    }

    });
    }
    }
    function channelCredDel(CreddelID)
    {
    url = "/channels/delchannelCred/" + CreddelID;
    channel_edit_id = "<?php echo $channel_edit_id; ?>";
    var con = confirm("are you sure you want to delete?");
    if (con == true){
    $.ajax({
    method: "POST",
            url: url,
            data: { },
            success:function(data)
            {
            //alert("success");
            //getChannelChargesData();

            geteditCredentials(channel_edit_id);
            }, fail:function(data)
    {
    alert("failed");
    }

    });
    }
    }
    function deleteEntityType(delid)
    {
    var channel_id = "<?php echo $channel_edit_id; ?>";
    var con = confirm("are you sure you want to delete?");
    if (con == true){
    $.ajax({
    url:"/channels/delcat/" + delid,
            type:"POST",
            success:function()
            {
            alert("Your category deleted successfully");
            get_mapping_category_list(channel_id);
            //location.reload();
            },
            error:function(data)
            {
            alert("failed to delete");
            }
    });
    }
    }
    $("#Cancel_btn").click(function(){
    window.location.href = "/channels/index";
    });
</script>
@stop