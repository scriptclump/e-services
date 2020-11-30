@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> EDIT WAREHOUSE </div>
                @if($data->lp_wh_name && $isFc == 1 && $data->dc_type == 118001)
                <span style="margin: 9px 10px 5px 6px;position: absolute;font-weight:bold;">
                    ( {{ $data->lp_wh_name }} ) Fullfillment Center </span>
                @elseif($data->lp_wh_name && $isFc == 0 && $data->dc_type == 118001) <span style="margin: 9px 10px 5px 6px;position: absolute;font-weight:bold;">
                    ( {{ $data->lp_wh_name }} ) Distribution Center</span>
                @elseif($data->dc_type == 118002)
                <span style="margin: 9px 10px 5px 6px;position: absolute;font-weight:bold;">( {{ $data->lp_wh_name }} )</span>
                @endif
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active"><a id="tab1" href="#tab_11" data-toggle="tab">Warehouse Information</a></li>
                        <li class=""><a id="tab2" href="#tab_22" data-toggle="tab">Documents</a></li>
                        @if($data->dc_type == 118001)
                        <li class=""><a id="tab3" href="#tab_33" data-toggle="tab">Serviceable Locations</a></li>
                        @endif
                        @if($data->dc_type == 118002)
                        <li class=""><a id="tab4" href="#tab_44" data-toggle="tab">Spokes</a></li>
                        @endif
                    </ul>
                    <?php
                    $bp = url('uploads/Suppliers_Docs');
                    $base_path = $bp . "/";
                    ?>
                    <div class="tab-content headings">
                        <div class="tab-pane active" id="tab_11">
                            <form id="edit_warehouse">
                                <input type="hidden" name="wh_lp_id" id="wh_lp_id" value="" />
                                <input type="hidden" name="LpId" id="LpId" value="" />
                                <input type="hidden" name="wh_latitude" id="wh_latitude" value="" />
                                <input type="hidden" name="wh_logitude" id="wh_logitude" value="" />
                                <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{$data->legal_entity_id}}" />
                                <input type="hidden" name="le_wh_id" id="le_wh_id" value="{{$id}}">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
					  <input type="hidden" name="bu_ids" id="bu_ids" value="{{$data->bu_id}}" />
                                <div class="row">
                               <div class="col-md-6">
                                        <div class="form-group">
                                          <label class="control-label">Warehouse Type <span class="required" aria-required="true">*</span></label>
                                          <select name="wh_type" id="wh_type" class="form-control">
                                            <option value="">--Select warehouse type--</option>
                                            
                                              @foreach($warehouse_type as $key => $w_type)
                                                @if($data->dc_type==$w_type->value)
                                                    <option value="{{$w_type->value}}" selected>{{$w_type->name}}</option>
                                                @else
                                                    <option value="{{$w_type->value}}">{{$w_type->name}}</option>
                                                @endif
                                                
                                              @endforeach
                                                           
                                          </select>
                                        </div>
                                    </div>
									<div class="col-md-3">
                                        <div class="form-group">
                                          <label class="control-label">Business Unit<span class="required" aria-required="true">*</span></label>
                                            <select class="form-control select2me" name="businessUnit1" id="businessUnit1"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Display Name<span class="required" aria-required="true">*</span></label>
                                            <input type="text" name="displayname" id="displayname" value="{{$data->display_name}}" class="form-control">
                                        </div>
                                    </div>
								</div>	
								<div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Warehouse Name <span class="required" aria-required="true">*</span></label>
                                            <input type="text" name="wh_name" id="wh_name" value="{{$data->lp_wh_name}}" class="form-control">
                                        </div>
                                    </div>                                   
                                    <input type="hidden" name="isfc" id="isfc" value="{{$isFc}}"/>
                                    <input type="hidden" name="is_virtual" id="is_virtual" value="{{$is_virtual}}" />
                                    @if($data->dc_type == 118001)
                                        @if($isFc == 0 &&  $is_virtual==0)
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">FC's</label>
                                                <select name="fcs[]" id="fcs" class="form-control select2me" multiple="true">
                                                    <option value="">--Select Fc--</option>
                                                    @if(isset($getFcDetails))
                                                        @foreach($getFcDetails as $fc)
                                                            @if(isset($getFcData) && !empty($getFcData) && in_array($fc->le_wh_id,$getFcData))
                                                            <option value="{{$fc->le_wh_id}}"
                                                            selected="true">{{ $fc->business_legal_name }}</option>
                                                            @else
                                                                <option value="{{$fc->le_wh_id}}">{{$fc->business_legal_name}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        @elseif($isFc == 0 && $is_virtual==1)
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">DC's</label>
                                                <select name="dcundervirtualdc[]" id="dcundervirtualdc" class="form-control select2me" multiple="true">
                                                    <option value="">--Select Dc--</option>
                                                    @if(isset($fcDc))
                                                        @foreach($fcDc as $dc)
                                                            @if(isset($fcDCDetails) && !empty($fcDCDetails) && in_array($dc->le_wh_id,$fcDCDetails))
                                                            <option value="{{$dc->le_wh_id}}"
                                                            selected="true">{{ $dc->business_legal_name }}</option>
                                                            @else
                                                                <option value="{{$dc->le_wh_id}}">{{$dc->business_legal_name}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        @else($isFc == 1)
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">DC's</label>
                                                <select name="dcs[]" id="dcs" class="form-control select2me" >
                                                    <option value="">--Select Dc--</option>
                                                    @if(isset($fcDc))
                                                        @foreach($fcDc as $dc)
                                                            @if(isset($fcDCDetails) && !empty($fcDCDetails) && in_array($dc->le_wh_id,$fcDCDetails))
                                                            <option value="{{$dc->le_wh_id}}"
                                                            selected="true">{{ $dc->business_legal_name }}</option>
                                                            @else
                                                                <option value="{{$dc->le_wh_id}}">{{$dc->business_legal_name}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                    <div class="col-md-3">
                                        @if($data->dc_type == 118001)
                                        <div class="form-group">
                                            <label class="control-label">Hubs</label>
                                            <select name="hubs[]" id="hubs" class="form-control select2me" multiple="true">
                                                <option value="">--Select warehouse type--</option>
                                                @if(isset($getHubDetails))
                                                    @foreach($getHubDetails as $hub)
                                                        @if(isset($getHubData) && !empty($getHubData) && in_array($hub->le_wh_id, $getHubData))
                                                            <option value="{{ $hub->le_wh_id }}" selected="true">{{ $hub->lp_wh_name }}</option>
                                                        @else
                                                            <option value="{{ $hub->le_wh_id }}">{{ $hub->lp_wh_name }}</option>
                                                        @endif    
                                                    @endforeach
                                                @endif                                                
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Warehouse Code <span class="required" aria-required="true">*</span></label>
                                                    <input type="text" name="wh_code" value="{{$data->le_wh_code}}"  id="wh_code" class="form-control" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                                                    <select name="wh_state" id="wh_state" class="form-control">
                                                        <option value="">Please select State.</option>
                                                        @if(isset($states))
                                                        @foreach($states as $state)
                                                        @if($state->state_id == $data->state)
                                                        <option value="{{$state->state_id}}" selected="true">{{$state->state}}</option>
                                                        @else
                                                        <option value="{{$state->state_id}}" >{{$state->state}}</option>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                                                    <input type="text" name="wh_address1" value="{{$data->address1}}"  id="wh_address1" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Address 2 </label>
                                                    <input type="text" name="wh_address2" value="{{$data->address2}}" id="wh_address2" class="form-control">
                                                </div>
                                            </div>
                                             <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="control-label">FSSAI</label>
                                              <input type="text" value="{{$data->fssai}}" name="fssai" id="fssai" class="form-control">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                                                    <input type="text" name="wh_pincode" value="{{$data->pincode}}" id="wh_pincode" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                                                    <input type="text" name="wh_city" value="{{$data->city}}" id="wh_city" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Margin <span class="required" aria-required="true">*</span></label>
                                                    <input type="text" name="margin" value="{{$data->margin}}" id="margin" class="form-control">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                <label>Price Group</label>
                                                <select name="price_group_id" id="price_group_id"class="form-control select2me " placeholder="Select Price Group">
                                                    @if(isset($priceGroup))
                                                        @foreach($priceGroup as $value)
                                                        @if($priceGroup_id  == $value->value)
                                                        <option value="{{$value->value}}" selected="true" selected>{{$value->master_lookup_name}}</option>
                                                        @else
                                                        <option value="{{$value->value}}" >{{$value->master_lookup_name}}</option>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                               
                                                </select>
                                                </div>
                                                </div>
                                            
                                        </div>
                                    <div class="row">
                                          <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="control-label">Jurisdiction<span class=""></span></label>
                                              <input type="text" name="Jurisdiction_edit" id="Jurisdiction_edit" value="{{$data->jurisdiction}}" class="form-control">
                                            </div>
                                          </div>
                                     </div>
                                    </div>
                                    <script type="text/javascript" src={{$mapurl}}></script>
                                    <script type="text/javascript">
window.onload = function () {

var latt = Number($("#wh_lat").val());
var logg = Number($("#wh_log").val());
if ( latt == '' )
{
latt = 17.3850;
}
if ( logg == '' )
{
logg = 78.4867;
}
var mapOptions = {
center: new google.maps.LatLng(latt, logg),
zoom: 18,
mapTypeId: google.maps.MapTypeId.ROADMAP
};
var infoWindow = new google.maps.InfoWindow();
var latlngbounds = new google.maps.LatLngBounds();
var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);

//var myLatLng = {lat: 17.3850, lng: 78.4867};
var myLatLng = {lat: latt, lng: logg};
var marker = new google.maps.Marker({
position: myLatLng,
map: map,
title: 'Your Warehouse'
});

google.maps.event.addListener(map, 'mousemove', function () {
google.maps.event.trigger(map, 'resize');
});

var input = document.getElementById("keyword");
var autocomplete = new google.maps.places.Autocomplete(input);
autocomplete.bindTo("bounds", map);

var marker = new google.maps.Marker({map: map});

google.maps.event.addListener(autocomplete, "place_changed", function ()
{
var place = autocomplete.getPlace();
var search_lat = place.geometry.location.lat();
var search_lng = place.geometry.location.lng();
$('#wh_lat').val(search_lat);
$('#wh_log').val(search_lng);

if ( place.geometry.viewport ) {
   map.fitBounds(place.geometry.viewport);
} else {
   map.setCenter(place.geometry.location);
   map.setZoom(15);
}

marker.setPosition(place.geometry.location);
});

google.maps.event.addListener(map, "click", function (event)
{
marker.setPosition(event.latLng);
});

google.maps.event.addListener(map, 'click', function (e) {
//alert("Latitude: " + e.latLng.lat() + "\r\nLongitude: " + e.latLng.lng());
$('#wh_lat').val(e.latLng.lat());
$('#wh_log').val(e.latLng.lng());
});
}
                                    </script>
                                    <div class="col-md-6">
                                        <div id="dvMap"></div>
                                        <div class="input-icon">
                                            <i class="fa fa-bars" style="position: absolute;top: -250px;left: 2px;"></i>
                                            <input type="text" class="form-control" name="keyword" id="keyword" style="position: absolute;top:-250px; left:4px;z-index: 2; width:260px;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Country<span class="required" aria-required="true">*</span></label>
                                            <select name="wh_country" id="wh_country" class="form-control">
                                                <option value="">Please select Country</option>
                                                @if(isset($countries))
                                                @foreach($countries as $country_value)
                                                @if($data->country == $country_value->country_id)
                                                <option value="{{$country_value->country_id}}" selected="true">{{$country_value->country}}</option>
                                                @else
                                                <option value="{{$country_value->country_id}}">{{$country_value->country}}</option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Latitude</label>
                                                    <input type="text" name="wh_lat" value="{{$data->latitude}}" id="wh_lat" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Longitude</label>
                                                    <input type="text" name="wh_log" id="wh_log" value="{{$data->longitude}}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Contact Name<span class="required" aria-required="true">*</span></label>
                                            <input type="text" name="contact_name" value="@if(isset($data->contact_name)){{$data->contact_name}}@endif" id="contact_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Phone Number<span class="required" aria-required="true">*</span></label>
                                            <input type="text" name="phone_no" value="@if(isset($data->phone_no)){{$data->phone_no}}@endif" id="phone_no" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Email<span class="required" aria-required="true">*</span></label>
                                            <input type="text" name="email" value="@if(isset($data->email)){{$data->email}}@endif" id="email" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline"> Is Active
                                                <input type="checkbox" name="is_active" <?php if($data->status) { ?> checked="true" <?php } ?> />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline"> Is APOB
                                                <input type="checkbox" name="is_apob_id" <?php if($data->is_apob) { ?> checked="true" <?php }?> />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline">Credit Limit Check
                                                <input type="checkbox" name="CreditLimit_Check" <?php if($credit_limit_check) { ?> checked="true" <?php }?> />
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline">Is Billing
                                                <input type="checkbox" name="billing" <?php if($is_billing) { ?> checked="true" <?php }?>/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline">Is Disabled
                                                <input type="checkbox" name="is_disabled" <?php if($data->is_disabled) { ?> checked="true" <?php }?>/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline">Send FF OTP
                                                <input type="checkbox" name="send_ff_otp" <?php if($data->send_ff_otp) { ?> checked="true" <?php }?>/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>   
                                </div>
                                <div class="row">
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mt-checkbox mt-checkbox-outline">Is Bin Using
                                                <input type="checkbox" name="is_bin_using" <?php if($data->is_binusing) { ?> checked="true" <?php }?>/>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <!-- <label class="control-label" >PDP</label> -->
                                                <select class="form-control select2me" name="is_days" id="is_days">
                                                    <option value=''>Please Select PDP</option>
                                                    @foreach($days as $day)
                                                    <option value='{{$day}}' <?php if($data->wh_pdp==$day) {  echo "selected";  }?> >{{$day}}</option>
                                                    @endforeach
                                                </select>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="appt_time"></label>
                                            PDP Slot :  <input id="appt_time" type="time" name="appt_time" <?php if($data->wh_pdp_slot) { ?> seleted="seleted" <?php }?> value="{{$data->wh_pdp_slot}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        @if(isset($updateDCInfo) && $updateDCInfo == 1)
                                            <input type="button" name="" id="update_warehouse" class="btn green-meadow" value="Update">
                                        @endif
                                        <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="tab_22">
                            <form id="saveDocForm" method="POST"  files="true" enctype ="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="control-label">TIN Number <span class="required" aria-required="true">*</span></label>
                                            <input type="text" value="{{$data->tin_number}}" id="tin_number" name="tin_number" class="form-control">
                                            <input type="hidden" name="le_wh_id" id="le_wh_id" value="{{$id}}">
                                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row VAT">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">TIN Proof <span class="required TINVAT_F" aria-required="true">*</span></label>
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput" >
                                                        <span class="btn default btn-file btn green-meadow" style="width:110px !important;">
                                                            <span class="fileinput-new">Choose File </span>
                                                            <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                                        </span>

                                                        <?php
                                                        $extn = '';
                                                        if (isset($tinDoc->doc_name))
                                                        {
                                                            $ext1 = strrchr($tinDoc->doc_name, ".");
                                                            //
                                                            $ext1 = explode(".", $ext1);
                                                            if (isset($ext1[1]))
                                                            {
                                                                $extn = $ext1[1];
                                                            }
                                                        }
                                                        ?>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="height: 33px; margin-left:9px; margin-top:10px;  z-index:99; position: relative; display: -webkit-inline-box;">
                                                            @if($extn == 'png' || $extn == 'jpg' || $extn == 'jpeg')<a href="@if(isset($tinDoc->doc_url)){{$tinDoc->doc_url}}@endif" target="blank"> <img src="@if(isset($tinDoc->doc_url)){{$tinDoc->doc_url}}@endif" alt="" class="tinvat_files_id"/></a> 
                                                            @elseif($extn == 'doc' || $extn == 'docx')
                                                            <a target="_blank" class="tinvat_files_id" href="@if(isset($tinDoc->doc_url)){{$tinDoc->doc_url}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a>
                                                            @elseif($extn == 'pdf')
                                                            <a target="_blank" class="tinvat_files_id" href="@if(isset($tinDoc->doc_url)){{$tinDoc->doc_url}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;" /></a>
                                                            @endif</div>
                                                        <input type="hidden" id="tin_doc_id" name="tin_doc_id" value="@if(isset($tinDoc->doc_id)) {{$tinDoc->doc_id}} @endif">
                                                        <br />
                                                        <input id="tinvat_files" type="file" class="upload" name="tin_files" style="margin-top: -45px !important; height:45px;  position: absolute;opacity: 0;"/>
                                                        @if(isset($tinDoc->doc_name))
                                                        <div class="col-md-6" style="padding-left:0px !important;">
                                                            <span class="fileinput-filename" id="span_tin" style="font-size:12px; text-align:center;">{{$tinDoc->doc_name}}<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                        </div>
                                                        @else
                                                        <span class="fileinput-filename" id="span_tin" style="white-space:normal !important; word-wrap:break-word; width:333px;"><a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">APOB Proof</label>
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <span class="btn default btn-file btn green-meadow" style="width:110px !important;">
                                                            <span class="fileinput-new">Choose File </span>
                                                            <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                                        </span>
                                                        <?php
                                                        $extn = '';
                                                        if (isset($apobDoc->doc_name))
                                                        {
                                                            $ext1 = strrchr($apobDoc->doc_name, ".");
                                                            //
                                                            $ext1 = explode(".", $ext1);
                                                            if (isset($ext1[1]))
                                                            {
                                                                $extn = $ext1[1];
                                                            }
                                                        }
                                                        ?>
                                                        <div class="fileinput-preview fileinput-exists thumbnail" style=" height: 33px; margin-left:9px; margin-top:10px; z-index:99; position: relative; display: -webkit-inline-box;">
                                                            @if($extn == 'png' || $extn == 'jpg' || $extn == 'jpeg')<a href="@if(isset($apobDoc->doc_url)){{$apobDoc->doc_url}}@endif" target="blank"> <img src="@if(isset($apobDoc->doc_url)){{$apobDoc->doc_url}}@endif" alt="" class="tinvat_files_id"/></a>
                                                            @elseif($extn == 'doc' || $extn == 'docx')
                                                            <a target="_blank" class="tinvat_files_id" href="@if(isset($apobDoc->doc_url)){{$apobDoc->doc_url}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a>
                                                            @elseif($extn == 'pdf')
                                                            <a target="_blank" class="tinvat_files_id" href="@if(isset($apobDoc->doc_url)){{$apobDoc->doc_url}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;" /></a>
                                                            @endif
                                                        </div>
                                                        <br />
                                                        <input type="hidden" name="apob_doc_id" value="@if(isset($apobDoc->doc_id)){{$apobDoc->doc_id}}@endif">
                                                        <input id="tinvat_files" type="file" class="upload" name="apob_files" style="margin-top: -45px !important; height:45px;  position: absolute;opacity: 0;"/>
                                                        @if(isset($apobDoc->doc_name))
                                                        <div class="col-md-6" style="padding-left:0px !important;">
                                                            <span class="fileinput-filename" style="font-size:12px; text-align:center;">&nbsp;{{$apobDoc->doc_name}}<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                        </div>
                                                        @else
                                                        <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; font-size: 12px; width:333px;">&nbsp;<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        @if(isset($updateDCDocInfo) && $updateDCDocInfo == 1)
                                            <input type="button" name="" id="save_docs" class="btn green-meadow" value="Update">
                                        @endif
                                        <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a> 
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="tab-pane" id="tab_33">
                            <form id="savePinForm">
                                <div class="row">
                                    <div class="col-md-5">
                                        @if(isset($dcAddPJP) && $dcAddPJP == 1)
                                        <label class="mt-radio mt-radio-outline">Add by Pincode Range
                                            <input type="radio" name="locations" value="Range">
                                            <span></span>
                                        </label>
                                        <label class="mt-radio mt-radio-outline">Add by Pincode
                                            <input type="radio" name="locations" value="Pincode">
                                            <span></span>
                                        </label>
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        &nbsp;
                                    </div>
                                    <div class="col-md-2">
                                       <!--  <input type="button" name="" id="export_locs" class="btn green-meadow" value="Export"> -->
                                        <a href="/warehouse/exportPin/{{$id}}/1" role="button" id="exportPin" class="btn green-meadow">Export</a>
                                        @if(isset($dcImportPincodes) && $dcImportPincodes == 1)
                                        <a href="javascript:void(0)" class="btn green-meadow" data-toggle="modal" data-target="#basicvalCodeModal" >Import</a>
                                        @endif
                                    </div>
                                </div>
                                <div id="range" style="display:none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="control-label">Start Range <span class="required" aria-required="true">*</span></label>
                                                        <input type="text" id="start_range" name="start_range" class="form-control">
                                                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                                        <input type="hidden" name="temp_data" id="temp_data">
                                                        <input type="hidden" name="le_wh_id" id="le_wh_id1" value="{{$le_wh_id}}">
                                                        <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{$data->legal_entity_id}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="control-label">End Range <span class="required" aria-required="true">*</span></label>
                                                        <input type="text" id="end_range" name="end_range" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label" style="display:block">&nbsp;</label>
                                                        <a title="Add" class="btn blue" id="add_pincode_range" >Add</a>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="pincode" style="display:none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group" id="pincode_error">
                                                        <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                                                        <input type="text" id="pin_code" name="pin_code" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="control-label" style="display:block">&nbsp;</label>
                                                        <a title="Add" class="btn blue" id="add_pincode" >Add</a>
                                                        <!-- <span align="center"><i class="fa fa-plus-circle" aria-hidden="true"></i></span> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box" id="form_wizard_1">
                                            <div class="portlet-body form">
                                                <div class="form-wizard">
                                                    <div class="form-body">
                                                        <div class="box">
                                                            <div class="tile-body nopadding">
                                                                <div id="pincode_grid">
                                                                    <table id="warehouse_table"></table>
                                                                    <input type="hidden" name="pincode_locations" id="pincode_locations" value="{{$pincode_locations}}">
                                                                </div>                                
                                                            </div>                         
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        @if($data->dc_type == 118001)
                                           <!--  <input type="button" name="" id="done" class="btn green-meadow" value="Done"> -->
                                        @endif
                                        @if(isset($dcUpdatePincodes) && $dcUpdatePincodes == 1)
                                            <input type="button" name="" id="save_locations" class="btn green-meadow" value="Save">
                                        @endif
                                        <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if($data->dc_type == 118002)
                        <div class="tab-pane" id="tab_44">
                            <div class="actions pull-right" style="margin-bottom:10px;">
                                <a href="/warehouse/exportspokes/{{$id}}" role="button" class="btn green-meadow">Export Beat</a>
                                @if(isset($dcAddSpoke) && $dcAddSpoke == 1)
                                    <a href="javascript:void(0)" class="btn green-meadow" data-toggle="modal" data-target="#basicvalCodeModal3" >Add Spoke</a>
                                @endif                                
                                @if(isset($dcAddPJP) && $dcAddPJP == 1)
                                    <a href="javascript:void(0)" onclick="displayadd()" class="btn green-meadow" data-toggle="modal" data-target="#basicvalCodeModal1" >Add Beat</a>
                                @endif                                
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet box" id="form_wizard_1">
                                        <div class="portlet-body form">
                                            <div class="form-wizard">
                                                <div class="form-body">
                                                    <div class="box">
                                                        <div class="tile-body nopadding">
                                                            <div id="pjp_grid">
                                                                <table id="pjp_table"></table>
                                                            </div>                                
                                                        </div>                         
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <input type="button" name="" id="done" class="btn green-meadow" value="Done">
                                    <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a>
                                </div>
                            </div>

                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="basicvalCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Import File</h4>
            </div>
            <div class="modal-body">  
                <form id="importExcel" method="POST"  files="true" enctype ="multipart/form-data">
                    <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{$data->legal_entity_id}}" />
                    <input type="hidden" name="le_wh_id" id="le_wh_id" value="{{$id}}">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-12 text-center" style="margin-bottom:10px;">
                                <a href="/warehouse/exportPin/{{$id}}/0" class="btn green-meadow btn-block" role="button" id="exportPin">Download Sample</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <span class="btn default btn-file btn green-meadow btn-block">
                                        <span class="fileinput-new">Upload </span>
                                        <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                    </span>

                                    <input id="import_files" type="file" class="upload" name="import_file" style="margin-top: -35px !important;  position: absolute;opacity: 0; height: 35px !important "/>
                                    <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; font-size: 12px; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <input type="button" name="" id="import_pin_button" class="btn green-meadow btn-block" value="Save">
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal modal-scroll fade in" id="basicvalCodeModal1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h4 class="modal-title" id="basicvalCode">Add Beat</h4>
            </div>
            <div class="modal-body">  
                <form id="savePJPForm">
                    <div class="row">
                        <div class="col-md-6" id="spokes_select">
                            <div class="form-group">
                                <label class="control-label">Spoke<span class="required" aria-required="true">*</span></label>
                                <select class="form-control select2me" name="spoke" id="spoke" onChange="spokeBeat()">
                                    <option value="0">Please Select...</option>
                                    @if(isset($spokes) && !empty($spokes))
                                        @foreach($spokes as $spoke)
                                        <option value="{{ $spoke->spoke_id }}">{{ $spoke->spoke_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                               
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                                <label class="control-label">Pincode<span class="required" aria-required="true">*</span></label>
                                 <input type="text" class="form-control" placeholder="Pincode" name="pincode" value="" />
                        </div>        
                        </div>
                        <div class="col-md-6" id="spokes_input">
                            <div class="form-group">
                                <label class="control-label">Spoke<span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" placeholder="Spoke" name="spoke_name" value="" />
                            </div>
                        </div>
<!--                        <div class="col-md-2" id="spoke_button">
                            <label class="control-label">&nbsp;</label>
                            <a class="btn green-meadow" href="javascript:void(0);" id="add_spoke">
                                <span style="color:#fff !important;"><i class="fa fa-plus" style="color:#fff !important;" aria-hidden="true"></i></span>
                            </a>
                        </div>-->
                        <div class="col-md-4" id="spokes_input">
                            <label class="control-label">&nbsp;</label>
                            <a class="btn green-meadow" href="javascript:void(0);" id="add_spoke_name">
                                <span style="color:#fff !important;"><i class="fa fa-check" style="color:#fff !important;" aria-hidden="true"></i></span>
                            </a>
                            <a class="btn green-meadow" href="javascript:void(0);" id="close_spoke_name">
                                <span style="color:#fff !important;"><i class="fa fa-close" style="color:#fff !important;" aria-hidden="true"></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Beat Name <span class="required" aria-required="true">*</span></label>
                                <input type="text" id="pjp_name" name="pjp_name" placeholder="Beat" class="form-control" onChange="spokeBeat()">
                                <div id="beat_exist" name="beat_exist" style="display:none;color:#e02222;font-size:12px;padding-left: 15px;">Beat already exists</div>
                                <input type="hidden" id="spoke_beat" name="spoke_beat">
                                <input type="hidden" name="pjps" id="pjps" value="@if(isset($pjps)) {{$pjps}} @endif">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Relationship Manager <span class="required" aria-required="true">*</span></label>
                                <select class="form-control select2me" name="rm_id" id="rm_id">
                                    <option value="0">Please Select...</option>
                                    @foreach($rm_ids as $rmId)
                                    <option value="{{ $rmId->user_id }}">{{ $rmId->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">PDP <span class="required" aria-required="true">*</span></label>
                                <select class="form-control" name="pdp" id="pdp">
                                    <option value=" ">Please Select...</option>
                                    <option value="Mon">Mon</option>
                                    <option value="Tue">Tue</option>
                                    <option value="Wed">Wed</option>
                                    <option value="Thu">Thu</option>
                                    <option value="Fri">Fri</option>
                                    <option value="Sat">Sat</option>
                                    <option value="Sun">Sun</option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">PDP Slot<span class="required" aria-required="true">*</span></label>
                                <select class="form-control" name="pdp_slot" id="pdp_slot">
                                    <option value="0">Please select...</option>
                                    @foreach ($slot as $slotdata)
                                    <option value="{{ $slotdata->value }}">{{ $slotdata->master_lookup_name }}</option>
                                    @endforeach
                                </select>
                            </div>                            
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">                           
                            <div class="form-group">
                                <label>Select Day</label>
                                <div class="">
                                    <label class="mt-checkbox mt-checkbox-outline"> Monday
                                        <input type="checkbox" value="Mon" name="week[]" />
                                        <span></span>
                                    </label>
                                    <label class="mt-checkbox mt-checkbox-outline"> Tuesday
                                        <input type="checkbox" value="Tue" name="week[]" />
                                        <span></span>
                                    </label>
                                    <label class="mt-checkbox mt-checkbox-outline"> Wednesday
                                        <input type="checkbox" value="Wed" name="week[]" />
                                        <span></span>
                                    </label>
                                    <label class="mt-checkbox mt-checkbox-outline"> Thursday
                                        <input type="checkbox" value="Thu" name="week[]" />
                                        <span></span>
                                    </label>
                                    <label class="mt-checkbox mt-checkbox-outline"> Friday
                                        <input type="checkbox" value="Fri" name="week[]" />
                                        <span></span>
                                    </label>
                                    <label class="mt-checkbox mt-checkbox-outline"> Saturday
                                        <input type="checkbox" value="Sat" name="week[]" />
                                        <span></span>
                                    </label>
                                    <label class="mt-checkbox mt-checkbox-outline"> Sunday
                                        <input type="checkbox" value="Sun" name="week[]" />
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            &nbsp;
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a role="button" id="add_pjp_grid" class="btn green-meadow">Add</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-scroll fade in" id="basicvalCodeModal2" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
                <h4 class="modal-title" id="basicvalCode">Add Beat Pincode Areas</h4>
            </div>
            <div class="modal-body">  
                <form id="savePJPAreaForm">
                    <div id="map_pincode">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                                    <select class="form-control select2me" name="pincode_value" id="pincode_value">
                                        <option value="">--Select--</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="pjp_pincode_area_id" id="pjp_pincode_area_id" value="">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="control-label">Area <span class="required" aria-required="true">*</span></label>
                                    <select id="pin_area" name="pin_area[]" class="form-control select2me" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">&nbsp;</label>
                                <a class="btn green-meadow" href="javascript:void(0);" id="addArea">
                                    <span style="color:#fff !important;"><i class="fa fa-plus" style="color:#fff !important;" aria-hidden="true"></i></span>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a role="button" id="add_area_grid" class="btn green-meadow">Add</a>
                            </div>
                        </div>
                    </div>
                </form>
                <form id="mapAreaForm" >
                    <div id="map_area" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                                    <select class="form-control" name="pincode_value1" id="pincode_value1">
                                        <option value="">--Select--</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="pjp_pincode_area_id" id="pjp_pincode_area_id" value="">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Area <span class="required" aria-required="true">*</span></label>
                                    <input type="text" name="area_name" id="area_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                                    <input type="text" name="area_city" value= "{{$data->city}}" id="area_city" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                                    <select name="area_state" id="area_state" class="form-control">
                                        <option value="">Please select State.</option>
                                        @if(isset($states))
                                        @foreach($states as $state)
                                        @if($state->state_id == $data->state)
                                        <option value="{{$state->state_id}}" selected="true">{{$state->state}}</option>
                                        @else
                                        <option value="{{$state->state_id}}" >{{$state->state}}</option>
                                        @endif
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a role="button" id="add_pjp_pincode" class="btn green-meadow"><i class="fa fa-exchange"></i></a>
                                <a role="button" id="map_pincode_area" class="btn green-meadow">Save</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-scroll fade in" id="basicvalCodeModal3" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Add Spoke</h4>
            </div>
            <div class="modal-body">  
                <form id="add_spoke_hub_form">
                    <div id="map_spoke">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hub <span class="required" aria-required="true">*</span></label>
                                    <select id="hub" name="hub_id" class="form-control select2me">
                                        <!--<option value="0">--Select--</option>-->
                                        @if(isset($getCurrentHubData))
                                            @foreach($getCurrentHubData as $hub)
                                                <option value="{{ $hub->le_wh_id }}">{{ $hub->lp_wh_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Spoke <span class="required" aria-required="true">*</span></label>
                                    <input type="text" name="spoke_name" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn green-meadow">Add</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<button data-toggle="modal" id="update_spoke" class="btn btn-default" data-target="#basicvalCodeModal5" style="display: none" ></button>
<div class="modal modal-scroll fade in" id="basicvalCodeModal5" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Move Spoke to Hub</h4>
            </div>
            <div class="modal-body">  
                <form id="update_spoke_hub_form">
                    <div id="map_spoke">
                        <div class="row">
                            <!--<div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">DC <span class="required" aria-required="true">*</span></label>
                                    <select class="form-control select2me" name="dc" id="dc_update_spoke">
                                        <option value="">--Select--</option>
                                        @if(isset($getDcDetails))
                                            @foreach($getDcDetails as $dc)
                                                @if(isset($id) && !empty($dcId) && in_array($dc->le_wh_id, $dcId))
                                                    <option value="{{ $dc->le_wh_id }}" selected="true">{{ $dc->lp_wh_name }}</option>
                                                @else
                                                    <option value="{{ $dc->le_wh_id }}">{{ $dc->lp_wh_name }}</option>
                                                @endif    
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="pjp_pincode_area_id" id="pjp_pincode_area_id" value="">-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hub <span class="required" aria-required="true">*</span></label>
                                    <select id="hub" name="hub_id" class="form-control select2me">
                                        <option value="0">--Select--</option>
                                        @if(isset($getHubDetails))
                                            @foreach($getHubDetails as $hub)
                                                @if(isset($id) && $hub->le_wh_id == $id)
                                                    <option value="{{ $hub->le_wh_id }}" selected="true">{{ $hub->lp_wh_name }}</option>
                                                @else
                                                    <option value="{{ $hub->le_wh_id }}">{{ $hub->lp_wh_name }}</option>
                                                @endif    
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Spoke <span class="required" aria-required="true">*</span></label>
                                    <input type="text" name="spoke_name" id="spoke_name" class="form-control" value="" readonly="true" />
                                    <input type="hidden" name="spoke_id" id="spoke_id" class="form-control" value="" />
                                    <input type="hidden" id="current_hub_id" class="form-control" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a role="button" id="update_spoke_hub" class="btn green-meadow">Update</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="basicvalCodeModal6" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Edit Spoke</h4>
            </div>
            <div class="modal-body">  
                <form id="update_spoke_form">
                    <div id="map_spoke">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hub <span class="required" aria-required="true">*</span></label>
                                    <select id="hub" name="hub_id" class="form-control select2me" disabled="disabled">
                                        <option value="0">--Select--</option>
                                        @if(isset($getHubDetails))
                                            @foreach($getHubDetails as $hub)
                                                @if(isset($id) && $hub->le_wh_id == $id)
                                                    <option value="{{ $hub->le_wh_id }}" selected="true">{{ $hub->lp_wh_name }}</option>
                                                @else
                                                    <option value="{{ $hub->le_wh_id }}">{{ $hub->lp_wh_name }}</option>
                                                @endif    
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Spoke <span class="required" aria-required="true">*</span></label>
                                    <input type="text" name="spoke_name" id="spoke_name" class="form-control" value="" />
                                    <input type="hidden" name="spoke_id" id="spoke_id" class="form-control" value="" />
                                    <input type="hidden" id="current_hub_id" class="form-control" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button accesskey="submit" class="btn green-meadow">Update</button>
                                <!--<a role="submit" id="update_spoke_button" class="btn green-meadow">Update</a>-->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<button data-toggle="modal" id="editPJP" class="btn btn-default" data-target="#basicvalCodeModal4" 
        style="display: none" data-url="{{URL::asset('/warehouse/editPJP')}}"></button>
<button data-toggle="modal" id="addPJPArea" class="btn btn-default" data-target="#basicvalCodeModal4" style="display: none" data-url="{{URL::asset('/warehouse/addPJPArea')}}"></button>
<div class="modal fade" id="basicvalCodeModal4" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode4">Add Beat</h4>
            </div>
            <div class="modal-body" id="lookupsDiv">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('style') 
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .fileinput-filename {
        display: table-column !important;
    }
    .help-block {
        width: 270px !important;
        text-align: center;
        padding: 0px 10px;
    }
    .fileinput {
        display: inherit !important;
    }

    /**/.tabbable-line > .nav-tabs > li > a:visited{color:#737373 !important;}
    .portlet.box .portlet-body {
        border: 0px solid #ccc !important;
    }

    .glyphicon-remove{color: #e02222 !important;}
    .glyphicon-ok {
        color: #3c763d !important;
    }
    .has-feedback label~.form-control-feedback{ top: 35px !important;
                                                right: -4px !important;}

    .form-control-feedback{top: 40px !important;
                           right: 10px !important;}

    #dvMap{height:304px !important; width:100% !important;}
    .fileinput-exists .fileinput-new, .fileinput-new .fileinput-exists{
        display: run-in !important;
    }
    .thumbnail img {display:run-in!important; max-height: 100% !important;}
    .thumbnail{padding: 0px !important; border: 0px !important;}
    .mt-checkbox, .mt-radio{margin-right: 3px;}
	i.fa-plus{color:#5b9bd1 !important;}
</style>
@stop
@section('userscript')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('js/helper.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>

<script type="text/javascript">

$('#basicvalCodeModal').on('hide.bs.modal', function () {
    console.log('resetForm');
    $('#importExcel').bootstrapValidator('resetForm', true);
    $('#importExcel')[0].reset();
});
$('#done').click(function () {
    window.location = "/warehouse";
});
</script>
<script type="text/javascript">
    $(document).ready(function () {

        $('#basicvalCodeModal2').on('shown.bs.modal', function () {
            $('#map_pincode').show();
            $('#map_area').hide();
        });
        $('#addArea').click(function () {
            $('#map_area').show();
            $('#map_pincode').hide();
        });
        $('#add_pjp_pincode').click(function () {
            $('#map_area').hide();
            $('#map_pincode').show();
        });
        $('[id="spokes_input"]').hide();
        $('#add_spoke').click(function () {
            $('[id="spokes_input"]').show();
            $('#spokes_select').hide();
            $('#spoke_button').hide();
        });
        $('#close_spoke_name').click(function () {
            $('[id="spokes_input"]').hide();
            $('#spokes_select').show();
            $('#spoke_button').show();
        });
        $('#add_spoke_name').click(function () {
            var spoke_name = $.trim($('[name="spoke_name"]').val());
            var le_wh_id = $('#le_wh_id').val();
            if(spoke_name == '' && le_wh_id > 0)
            {
                $('#spokes_input').children().addClass('has-error');
            }else{
                $('#spokes_input').children().removeClass('has-error');
                $.ajax({
                    url: '/warehouse/addspoke',
                    data: { 'hub_id': le_wh_id, 'spoke_name' : spoke_name },
                    type: 'POST',
                    success: function (result)
                    {
                        if(result > 0)
                        {
                            $('[name="spoke_name"]').val('');
                            $('[id="spokes_input"]').hide();
                            $('#spokes_select').show();
                            $('#spoke_button').show();                            
                            var newOption = $('<option>');
                            newOption.attr('value', result).text(spoke_name);
                            $('#spoke').append(newOption);
                            $('#spoke > [value="' + result + '"]').attr("selected", "true");
                            $("#spoke").select2('data', {id: result, text: spoke_name});
                        }else{
                            $('#spokes_input').children().addClass('has-error');
                        }
                    }
                });
            }
        });
        $('#basicvalCodeModal3').on('hide.bs.modal', function () {
            $('#add_spoke_hub_form').bootstrapValidator('resetForm', true);
            $('#basicvalCodeModal3').find('[name="spoke_name"]').val('');
            $('#add_spoke_hub_form').find('[name="hub_id"]').val($('#le_wh_id').val());
//            $('#add_spoke_hub_form')[0].reset();
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#basicvalCodeModal1').on('hide.bs.modal', function () {
            console.log('resetForm');
            $('#basicvalCodeModal1').find('.select2-container').select2('val', 0);
            $('#savePJPForm').bootstrapValidator('resetForm', true);
            $('#savePJPForm')[0].reset();
            $('[id="spokes_input"]').hide();
            $('#spokes_select').show();
            $('#spoke_button').show();
        });
        $('#basicvalCodeModal2').on('hide.bs.modal', function () {
            console.log('resetForm');
            $('#basicvalCodeModal2').find('.select2-container').select2('val', '');
            $('#savePJPAreaForm').bootstrapValidator('resetForm', true);
            $('#savePJPAreaForm')[0].reset();
            $('[id="spokes_input"]').hide();
            $('[id="spokes_select"]').show();
            $('[id="spoke_button"]').show();
        });
        $('#basicvalCodeModal4').on('hide.bs.modal', function () {
            console.log('resetForm');
            $('#editPJPForm').bootstrapValidator('resetForm', true);
            $('#editPJPForm')[0].reset();
        });

    });
</script>
<script type="text/javascript">
    function getPins() {
        var wh_id = $('#le_wh_id').val();
        var legal_entity_id = $('#legal_entity_id').val();
        $('#pincode_value').empty();
        $('#pincode_value1').empty();
        $.get('/warehouse/getSavedPincodes/' + wh_id + '/' + legal_entity_id, function (res_pin) {
            $('#pincode_value').html($("<option>").attr('value', '').text('--Select--'));
            $.each(res_pin, function (k, v) {
                $('#pincode_value').append($("<option>").attr('value', v.pincode).text(v.pincode));
            });
            $('#pincode_value1').html($("<option>").attr('value', '').text('--Select--'));
            $.each(res_pin, function (k, v) {
                $('#pincode_value1').append($("<option>").attr('value', v.pincode).text(v.pincode));
            });
        });

    }
    $(function () {
        $('#tab4').click(function () {
            $('#pincode_value').empty();
            $('#pincode_value1').empty();
            getPins();
        });
    });
    $('#pincode_value').change(function () {
        var pincode = $(this).val();
        var beat_id = $('#pjp_pincode_area_id').val();
        $.get('/warehouse/getPincodeAreas/' + pincode+'/'+beat_id, function (res_pin) {
            $('#pin_area').empty();
            $('#pin_area').select2({placeholder: "Please Select..."});
            $.each(res_pin, function (k, v) {
                $('#pin_area').append($("<option>").attr('value', v.city_id).text(v.area));
            });
        });
    });
</script>
<script type="text/javascript">
    $(function () {

        $('#map_pincode_area').click(function () {
            var formValid = $('#mapAreaForm').formValidation('validate');
            formValid = formValid.data('formValidation').$invalidFields.length;
            if ( formValid != 0 ) {
                return false;
            }
            else {
                var data = $('#mapAreaForm').serialize();
                $.ajax({
                    url: '/warehouse/mapArea',
                    data: data,
                    type: 'POST',
                    success: function (result)
                    {
                        console.log(result);
                        var response = JSON.parse(result);
                        if ( response.status == 1 ) {
                            alert(response.message);
                            $('#mapAreaForm').bootstrapValidator('resetForm', true);
                            $('#mapAreaForm')[0].reset();
                            $('#map_pincode').show();
                            $('#map_area').hide();
                            $('#basicvalCodeModal2').find('.select2-container').select2('val', '');
                        }
                        else {
                            alert(response.message);
                        }
                    }
                });
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        makePopupEditAjax($('#basicvalCodeModal2'), 'pjp_pincode_area_id');
    });
    $(document).ready(function () {
        $('#edit_warehouse').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                wh_name: {
                    validators: {
                        remote: {
                            url: '/warehouse/checkUnique',
                            data: {wh_name: $('[name="wh_name"]').val(), le_wh_id: $('[name="le_wh_id"]').val()},
                            type: 'POST',
                            delay: 2000,
                            message: 'Warehouse name already exists'
                        },
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 \-,\#]+$/i,
                            message: ' '
                        }
                    }
                },
                wh_code: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }/*,
                        regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        }*/
                    }
                },
                wh_address1: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }/*,
                        regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        }*/
                    }
                },
                fssai: {
                  validators: {
                    regexp: {
                      regexp: /^\d{14}$/ ,
                      message: 'Please enter valid fssai number'
                    }
                  }
                },
                wh_address2: {
                    validators: {
                        regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        },
                    }
                },
                wh_pincode: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^\d{6}$/,
                            message: ' '
                        }
                    }
                },
                wh_city: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: '^[a-zA-Z .]+$',
                            message: ' '
                        }
                    }
                },
                wh_state: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
                price_group_id: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },

                margin: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
                 wh_type: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
				businessUnit1: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
                displayname:{
                    validators:{
                        notEmpty:{
                            message:' '
                        }
                    }
                }, 
                wh_country: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
                wh_log: {
                    validators: {
                        between: {
                            min: -180,
                            max: 180,
                            message: ' '
                        }
                    }
                },
                wh_lat: {
                    validators: {
                        between: {
                            min: -90,
                            max: 90,
                            message: ' '
                        }
                    }
                },
                contact_name: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: '^[a-zA-Z .]+$',
                            message: ' '
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                            message: ' '
                        }
                    }
                },
                phone_no: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^\d{10}$/,
                            message: ' '
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });
        $('#mapAreaForm').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                pincode_value1: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                    }
                },
                area_name: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 \.]+$/,
                            message: ' '
                        }
                    }
                },
                area_city: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: '^[a-zA-Z .]+$',
                            message: ' '
                        }
                    }
                },
                area_state: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });
        $('#savePinForm').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                start_range: {
                    enabled: false,
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^\d{6}$/,
                            message: ' '
                        }
                    }
                },
                pin_code: {
                    enabled: false,
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^\d{6}$/,
                            message: ' '
                        }
                    }
                },
                end_range: {
                    enabled: false,
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^\d{6}$/,
                            message: ' '
                        },
                        callback: {
                            callback: function (value, validator, $field) {
                                var start_range = $('#start_range').val();
                                var maxval = parseInt(start_range) + 500;
                                console.log(maxval);
                                if ( value <= start_range ) {
                                    return false;
                                }
                                if ( value > maxval ) {
                                    return false;
                                }
                                else {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });
        $('#saveDocForm').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                tin_number: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9]+$/i,
                            message: ' '
                        },
                    }
                },
                tin_files: {
                    validators: {
                        file: {
                            extension: 'doc,docx,pdf,jpeg,jpg,png',
                            type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                            maxSize: 2 * 1024 * 1024, // 5 MB
                            message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                        },
                        callback: {
                            callback: function (value, validator, $field) {
                                doc_id = $('#tin_doc_id').val();
                                span_tin = $('#span_tin').text();
                                console.log(doc_id);
                                console.log(value);
                                if ( value == "" && doc_id == "" ) {
                                    return false;
                                }
                                else if ( doc_id !== null ) {
                                    return true;
                                }
                                else if ( value !== "" ) {
                                    return true;
                                }
                                else {
                                    return false;
                                }
                            },
                            message: ' '
                        }
                    }
                },
                apob_files: {
                    validators: {
                        file: {
                            extension: 'doc,docx,pdf,jpeg,jpg,png',
                            type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                            maxSize: 2 * 1024 * 1024, // 5 MB
                            message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });

        $('#importExcel').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                import_file: {
                    validators: {
                        file: {
                            extension: 'csv,xls,xlsx',
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,csv',
                            maxSize: 10 * 1024 * 1024, // 5 MB
                            message: 'The selected file is not valid, it should be (csv,xlsx,xls) and 10 MB at maximum.'
                        },
                        notEmpty: {
                            message: 'Please select a file to import'
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });

        $('#savePJPForm').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                'week[]': {
                    validators: {
                        choice: {
                            min: 1,
                            max: 7,
                            message: ' '
                        }
                    }
                },
                rm_id: {
                    validators: {
                        callback: {
                            message: 'Please select relationship manager',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                spoke: {
                    validators: {
                        callback: {
                            message: 'Please select Spoke',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                pjp_name: {
                    validators: {
                        notEmpty: {
                            message: 'Please give beat name'
                        }
                    }
                },
                pdp: {
                    validators: {
                        callback: {
                            message: 'Please select Pdp',
                            callback: function (value, validator) {
                                return value != ' ';
                            }
                        }
                    }
                }/*,
                pdp_slot: {
                    validators: {
                        callback:{
                            message: 'Please select pdp slot',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                }*/

            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });
        $('#add_spoke_hub_form').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                hub_id: {
                    validators: {
                        callback: {
                            message: 'Please Select Hub',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                spoke_name: {
                    validators: {
                        remote: {
                            url: '/warehouse/checkUniqueSpoke',
                            data: function(validator, $field, value) {
                                return {
                                    le_wh_id: validator.getFieldElements('hub_id').val(),
                                    spoke_name: validator.getFieldElements('spoke_name').val(),
                                    spoke_id: validator.getFieldElements('spoke_id').val()
                                };
                            },
                            type: 'POST',
                            delay: 2000,
                            message: 'Spoke name already exists'
                        },
                        notEmpty: {
                            message: 'Please provide spoke name'
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
            var data = $('#add_spoke_hub_form').serialize();
            $.ajax({
                url: '/warehouse/addspoke',
                data: data,
                type: 'POST',
                success: function (result)
                {
                    if(result > 0)
                    {
                        var spoke_name = $.trim($('#add_spoke_hub_form').find('[name="spoke_name"]').val());
                        $('[name="spoke_name"]').val('');
                        $('[id="spokes_input"]').hide();
                        $('#spokes_select').show();
                        $('#spoke_button').show();                            
                        var newOption = $('<option>');
                        newOption.attr('value', result).text(spoke_name);
                        $('#spoke').append(newOption);
                        $('#spoke > [value="' + result + '"]').attr("selected", "true");
                        $("#spoke").select2('data', {id: result, text: spoke_name});
                        $('#basicvalCodeModal3').modal('hide');
                        $("#pjp_table").igGrid("dataBind");
                    }else{
                        $('#spokes_input').children().addClass('has-error');
                    }
                }
            });
        });
        
        $('#update_spoke_hub_form').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                hub_id: {
                    validators: {
                        callback: {
                            message: 'Please Select Hub',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        },
                        remote: {
                            url: '/warehouse/checkUniqueSpoke',
                            data: function(validator, $field, value) {
                                return {
                                    le_wh_id: validator.getFieldElements('hub_id').val(),
                                    spoke_name: validator.getFieldElements('spoke_name').val(),
                                    spoke_id: validator.getFieldElements('spoke_id').val()
                                };
                            },
                            type: 'POST',
                            delay: 2000,
                            message: 'Hub already has the spoke with same name or the serviceable pincodes are not maching, please check and move.'
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
        });
        
        $('#update_spoke_form').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                spoke_name: {
                    validators: {
                        remote: {
                            url: '/warehouse/checkUniqueSpoke',
                            data: function(validator, $field, value) {
                                return {
                                    le_wh_id: validator.getFieldElements('hub_id').val(),
                                    spoke_name: validator.getFieldElements('spoke_name').val(),
                                    spoke_id: validator.getFieldElements('spoke_id').val()
                                };
                            },
                            type: 'POST',
                            delay: 2000,
                            message: 'Spoke name already exists'
                        },
                        notEmpty: {
                            message: 'Please provide spoke name'
                        }
                    }
                }
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            console.log('here in success');
            var data = $('#update_spoke_form').serialize();
            $.ajax({
                url: '/warehouse/updatespoke',
                data: data,
                type: 'POST',
                success: function (result)
                {
                    var response = JSON.parse(result);
                    if ( response.status == 1 ) {
                        alert(response.message);
                        $('#basicvalCodeModal6').modal('hide');
                        $("#pjp_table").igGrid("dataBind");
                        $('#update_spoke_form').bootstrapValidator('resetForm', true);
                        $('#update_spoke_form')[0].reset();
                    }else{
                        alert(response.message);
                    }
                }
            });
        });
    });

</script>
<script type="text/javascript">
    $('#addPJPArea').click(function () {

    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
	
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/bussinessunits',
            type: 'GET',                                             
            success: function (rs) 
            {
                var buid = $("#bu_ids").val();
                $("#businessUnit1").html(rs);
                $("#businessUnit1").select2().select2('val',buid);
            }
        });
	
	
	$('#wh_type').attr('disabled', true);
        var pins = JSON.parse($('#pincode_locations').val());
        var temp = $('#temp_data').val();
        $.each(pins, function (id, val) {
            temp = temp + '##' + val.pincode;
        });
        $('#temp_data').attr('value', temp);
    });
    $(function () {
        console.log('we are before call');
        var legal_entity_id = $('#legal_entity_id').val();
        var le_wh_id = $('#le_wh_id').val();
        grid();
        $.get('/warehouse/getPJPs', function (res_pin) {
            var data = res_pin;
            pjpgrid(data);
        });


    });
    
    function pjpgrid(data)
    {
        var le_wh_id = $('#le_wh_id').val();
        $('#pjp_table').igGrid({
            dataSource: "/warehouse/getallspokesbeats/"+le_wh_id,
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            recordCountKey: 'totalSpokesCount',
            columns: [
                { headerText: "pjp_pincode_area_id", key: "pjp_pincode_area_id", id:"pjp_pincode_area_id", dataType: "number", width: "10%", hidden:"true" },
                { headerText: "Spoke", key: "spoke_name", dataType: "string", width: "20%" },
                { headerText: "Beat", key: "pjp_name", dataType: "string", width: "20%" },
                { headerText: "Service days", key: "days", dataType: "string", width: "20%" },
                { headerText: "Relationship Manager", key: "rm_name", dataType: "string", width: "30%" },
                { headerText: "Actions", key: "actions", dataType: "string", width: "10%" }
            ],
            features: [
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'actions', allowFiltering: false},
                        {columnKey: 'profile_picture', allowFiltering: false}
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'Phone', allowSorting: false},
                        {columnKey: 'Action', allowSorting: false}
                    ]

                },
                {
                    recordCountKey: 'TotalRecordsCount',
//                    chunkIndexUrlKey: 'page',
//                    chunkSizeUrlKey: 'pageSize',
//                    chunkSize: 20,
                    name: 'AppendRowsOnDemand',
                    loadTrigger: 'auto',
                    type: 'local',
                    initialDataBindDepth: 0,
                    localSchemaTransform: false,
                    showHeaders: true,
                    fixedHeaders: true,
                    name: 'Paging',
//                    type: "local",
                    pageSize: 20
                }
            ],
            primaryKey: 'pjp_pincode_area_id',
            width: '100%',
//            height: '570px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            type: 'local',
            showHeaders: true,
            fixedHeaders: true
        });
    }

    function grid() {
        console.log('we are in grid call');
        console.log('pinonload:');
        console.log($.parseJSON($('#pincode_locations').val()));
        $('#warehouse_table').igGrid({
            dataSource: $.parseJSON($('#pincode_locations').val()),
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            renderCheckboxes: true,
            columns: [
                //{ headerText: "wh_serviceables_id", key: "wh_serviceables_id", dataType: "string", width: "0%" },
                {headerText: "Pincode", key: "pincode", dataType: "string", width: "25%"},
                {headerText: "State", key: "state", dataType: "string", width: "30%"},
                {headerText: "City", key: "city", dataType: "string", width: "30%"},
                {headerText: "Action", key: "actions", dataType: "string", width: "15%"},
            ],
            features: [
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'actions', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'action', allowSorting: false},
                    ]

                },
               /* {
                    name: "RowSelectors",
                    enableCheckBoxes: true,
                    enableRowNumbering: false
                },*/
                {
                    name: "Selection",
                    mode: 'row',
                    multipleSelection: true
                },
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 10
                }],
            primaryKey: 'pincode',
            width: '100%',
            height: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false

        });
    }

    $('#add_pjp_grid').click(function () {
        var legal_entity_id = $('#legal_entity_id').val();
        var le_wh_id = $('#le_wh_id').val();
        var formValid = $('#savePJPForm').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else {
            var data = $('#savePJPForm').serialize();
            $.ajax({
                url: '/warehouse/savePJP/' + le_wh_id + '/' + legal_entity_id,
                data: data,
                type: 'POST',
                success: function (result)
                {
                    var response = JSON.parse(result);
                    alert(response.message);
                    console.log(response.data);
                    $("#pjp_table").igGrid("dataBind");
                    $('#pjp_pincode_area_id').attr('value', response.pjp_pincode_area_id);
                    $('#basicvalCodeModal1').modal('hide');
                    $('#basicvalCodeModal1').on('hide.bs.modal', function () {
                        console.log('resetForm');
                        $('#savePJPForm').bootstrapValidator('resetForm', true);
                        $('#savePJPForm')[0].reset();
                    });
                }
            });
            console.log(data);
        }
    });

//    $('#add_spoke_hub').click(function () {
//        var formValid = $('#add_spoke_hub_form').formValidation('validate');
//        formValid = formValid.data('formValidation').$invalidFields.length;
//        console.log('formValid');
//        console.log(formValid);
//        if ( formValid != 0 ) {
//            return false;
//        }
//        else {
//            var data = $('#add_spoke_hub_form').serialize();
//            $.ajax({
//                url: '/warehouse/addspoke',
//                data: data,
//                type: 'POST',
//                success: function (result)
//                {
//                    if(result > 0)
//                    {
//                        var spoke_name = $.trim($('#add_spoke_hub_form').find('[name="spoke_name"]').val());
//                        $('[name="spoke_name"]').val('');
//                        $('[id="spokes_input"]').hide();
//                        $('#spokes_select').show();
//                        $('#spoke_button').show();                            
//                        var newOption = $('<option>');
//                        newOption.attr('value', result).text(spoke_name);
//                        $('#spoke').append(newOption);
//                        $('#spoke > [value="' + result + '"]').attr("selected", "true");
//                        $("#spoke").select2('data', {id: result, text: spoke_name});
//                        $('#basicvalCodeModal3').modal('hide');
//                        $("#pjp_table").igGrid("dataBind");
//                    }else{
//                        $('#spokes_input').children().addClass('has-error');
//                    }
//                }
//            });
//        }
//    });

    $('#update_spoke_hub').click(function () {
        var currentSpokeId = $('#basicvalCodeModal5').find('#current_hub_id').val();
        var spokeId = $('#basicvalCodeModal5').find('#spoke_id').val();
        if(currentSpokeId == spokeId)
        {
            alert('Please select another hub to move the spoke.')
        }else{
            var formValid = $('#update_spoke_hub_form').formValidation('validate');
            formValid = formValid.data('formValidation').$invalidFields.length;
            if ( formValid != 0 ) {
                return false;
            }
            else {
                var decision = confirm("Are you sure you want to move spoke from current hub, which will move all the beats into another hub.");
                if ( decision == true )
                {
                    var data = $('#update_spoke_hub_form').serialize();
                    $.ajax({
                        url: '/warehouse/movespoke',
                        data: data,
                        type: 'POST',
                        success: function (result)
                        {
                            if(result > 0)
                            {
                                $('#basicvalCodeModal5').modal('hide');
                                $("#pjp_table").igGrid("dataBind");
                                $('#update_spoke_hub_form').bootstrapValidator('resetForm', true);
                                $('#update_spoke_hub_form')[0].reset();
                            }
                        }
                    });
                }else{
                    $('#basicvalCodeModal5').modal('hide');
                    $('#update_spoke_hub_form').bootstrapValidator('resetForm', true);
                    $('#update_spoke_hub_form')[0].reset();
                }
            }
        }        
    });

    $('#update_spoke_button').click(function () {
        var formValid = $('#update_spoke_form').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else {
            var data = $('#update_spoke_form').serialize();
            $.ajax({
                url: '/warehouse/updatespoke',
                data: data,
                type: 'POST',
                success: function (result)
                {
                    var response = JSON.parse(result);
                    if ( response.status == 1 ) {
                        alert(response.message);
                        $('#basicvalCodeModal6').modal('hide');
                        $("#pjp_table").igGrid("dataBind");
                        $('#update_spoke_form').bootstrapValidator('resetForm', true);
                        $('#update_spoke_form')[0].reset();
                    }else{
                        alert(response.message);
                    }
                }
            });
        }    
    });
    $('#savePJPAreaForm').formValidation({
        framework: 'bootstrap',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            pincode_value: {
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            'pin_area[]': {
                validators: {
                    notEmpty: {
                        message: ' '
                    },
                    remote: {
                        url: '/warehouse/checkUniquePJPArea',
                        data: {pin_area: $('[name="pin_area[]"]').val(), le_wh_id: $('[name="le_wh_id"]').val()},
                        type: 'POST',
                        delay: 2000,
                        message: 'Area already exists'
                    }

                }
            }
        }
    });

    $('#add_area_grid').click(function () {
        var formValid = $('#savePJPAreaForm').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else {
            var data = $('#savePJPAreaForm').serialize();
            $.ajax({
                url: '/warehouse/savePJPArea',
                data: data,
                type: 'POST',
                success: function (result)
                {
                    var response = JSON.parse(result);
                    alert(response.message);
                    $("#pjp_table").igGrid("dataBind");
                    $('#basicvalCodeModal2').modal('hide');
                    $('#basicvalCodeModal2').on('hide.bs.modal', function () {
                        $('#savePJPAreaForm').bootstrapValidator('resetForm', true);
                        $('#savePJPAreaForm')[0].reset();
                    });
                }
            });
        }
    });
    function pjpgrid2(data) {
        var le_wh_id = $('#le_wh_id').val();
        var legal_entity_id = $('#legal_entity_id').val();
        $('#pjp_table').igHierarchicalGrid({
            dataSource: '/warehouse/getspokes/'+le_wh_id,
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: false,
            renderCheckboxes: true,
            columns: [
                    { headerText: "Spoke Id", key: "spoke_id", dataType: "string", width: "0%" },
                    { headerText: "Spoke Name", key: "spoke_name", dataType: "string", width: "80%" },
                    { headerText: "Actions", key: "actions", dataType: "string", width: "20%" }
                    ],
                    columnLayouts: [
                    {
                        dataSource: '/warehouse/getPJPs',
                        dataSourceType: 'json',
                        autoGenerateColumns: false,
                        responseDataKey: 'Records',
                        columns: [
                            { headerText: "pjp_pincode_area_id", key: "pjp_pincode_area_id", id:"pjp_pincode_area_id", dataType: "number", width: "10%", hidden:"true" },
                            { headerText: "Beat", key: "pjp_name", dataType: "string", width: "40%" },
                            { headerText: "Service days", key: "days", dataType: "string", width: "20%" },
                            { headerText: "Relationship Manager", key: "rm_name", dataType: "string", width: "30%" },
                            { headerText: "Actions", key: "actions", dataType: "string", width: "10%" }
                        ],
//                        columnLayouts: [
//                        {
//                            dataSource: '/warehouse/getChildPJPAreas',
//                            autoGenerateColumns: false,
//                            columns: [
//                                {headerText: "Pincode", key: "pincode", dataType: "string", width: "40%"},
//                                {headerText: "Area", key: "areas", dataType: "string", width: "40%"},
//                                {headerText: "Action", key: "actions", dataType: "string", width: "20%"}
//                            ],
//                            features: [
//                                {
//                                            name: 'Paging',
//                                            type: 'local',
//                                            pageSize: 10,
//                                            recordCountKey: 'TotalRecordsCount',
//                                            pageIndexUrlKey: "page",
//                                            pageSizeUrlKey: "pageSize"
//                                        }
//                                    ],
//                                    primaryKey: 'pincode_area_id',
//                                    width: '100%',
////                                    height: '100%',
//                                    initialDataBindDepth: 0,
//                                    localSchemaTransform: false
//                        }],
                        features: [
                            {
                                name: 'Paging',
                                type: 'local',
                                pageSize: 20,
                                recordCountKey: 'TotalRecordsCount'
                            },
                            {
                                name: 'Sorting',
                                type: 'local',
                                persist: false,
                                columnSettings: [
                                {
                                    columnKey: 'action', allowSorting: false }
                                ]
                            },
                            {
                                name: "Filtering",
                                type: "local",
                                mode: "simple",
                                filterDialogContainment: "window",
                                columnSettings: [
                                    {columnKey: 'actions', allowFiltering: false }
                                ]
                            }
                        ],
                        //height: '400px',
                        primaryKey: 'pjp_pincode_area_id',
                        width: '100%',
//                        height: '100%',
                        initialDataBindDepth: 0,
                        localSchemaTransform: false
                    }],
                    features: [
                    {
                    name: "Filtering",
                            type: "local",
                            mode: "simple",
                            filterDialogContainment: "window",
                            columnSettings: [
                            {columnKey: 'actions', allowFiltering: false },
                            ]
                    },
                    {
                    name: 'Sorting',
                            type: 'local',
                            persist: false,
                            columnSettings: [
                            {columnKey: 'action', allowSorting: false },
                            ]

                    },
                    {
                    name: 'Paging',
                            type: "local",
                            pageSize: 10
                    }],
                    primaryKey: 'spoke_id',
                    width: '100%',
        //            height: '100%',
                    initialDataBindDepth: 0,
                    localSchemaTransform: false

            });
        
        
        
//        $('#pjp_table').igTreeGrid({
//            dataSource: data,
//            dataSourceType: 'json',
//            autoGenerateColumns: false,
//            responseDataKey: 'Records',
//            //generateCompactJSONResponse: false,
//            //enableUTCDates: true,
//            //renderCheckboxes: true,
//            columns: [
//                //{ headerText: "wh _serviceables_id", key: "wh_serviceables_id", dataType: "string", width: "0%" },
//                {headerText: "pjp_pincode_area_id", key: "pjp_pincode_area_id", id: "pjp_pincode_area_id", dataType: "string", width: "0%", hidden: "true"},
//                {headerText: "PJP Name", key: "pjp_name", dataType: "string", width: "15%"},
//                {headerText: "Service days", key: "days", dataType: "string", width: "20%"},
//                {headerText: "Relationship Manager", key: "rm_name", dataType: "string", width: "20%"},
//                {headerText: "Pincode", key: "pincode", dataType: "string", width: "15%"},
//                {headerText: "Area", key: "areas", dataType: "string", width: "20%"},
//                {headerText: "Action", key: "actions", dataType: "string", width: "10%"}
//            ],
//            childDataKey: "pincode_area",
//            initialExpandDepth: 0,
//            features: [
//                {
//                    name: "Filtering",
//                    type: "local",
//                    mode: "simple",
//                    filterDialogContainment: "window",
//                    columnSettings: [
//                        {columnKey: 'actions', allowFiltering: false},
//                    ]
//                },
//                {
//                    name: 'Sorting',
//                    type: 'local',
//                    persist: false,
//                    columnSettings: [
//                        {columnKey: 'action', allowSorting: false},
//                    ]
//
//                },
//                {
//                    name: 'Paging',
//                    type: "local",
//                    pageSize: 10
//                }],
//            primaryKey: 'pjp_pincode_area_id',
//            width: '100%',
//            height: '400px',
//            //initialDataBindDepth: 0,
//            //localSchemaTransform: false
//
//        });
    }

    function deleteRow(rowId) {
        var pins = JSON.parse($('#pincode_locations').val());
        var temp = $('#temp_data').val();
        console.log(temp);
        console.log('rowId ', rowId);
        var temp = temp.replace(rowId, ' ');
        $('#temp_data').attr('value', temp);
        var tempPins = new Array()
        jQuery.each(pins, function (i, val) {
            if ( val.pincode == rowId ) // delete index
            {
                delete pins[i];
            } else {
                tempPins.push(pins[i]);
            }
        });
        var pin = [];
        console.log(tempPins);
        $('#pincode_locations').val(JSON.stringify(pin));
        $('#pincode_locations').val(JSON.stringify(tempPins));
        var gridVar = $("#warehouse_table").data("igGrid");
        gridVar.dataSource.deleteRow(rowId);
        gridVar.commit();
        grid();
    }
    function deletePin(id)
    {
        var decision = confirm("Are you sure you want to Delete.");
        if ( decision == true )
            $.ajax({
                url: '/warehouse/deletePin/' + id,
                success: function (result) {
                    var response = JSON.parse(result);
                    /*console.log(response); */
                    if ( response.status == true ) {

                        alert(response.message);
                        var pins = $.parseJSON($('#pincode_locations').val());
                        var delData = response.delData;
                        deleteRow(delData.pincode);
                    }
                }
            });
    }

    function deletePJP(pjp_pin_area_id)
    {
        var decision = confirm("Are you sure you want to Delete.");
        if ( decision == true )
            $.ajax({
                url: '/warehouse/deletePJP/' + pjp_pin_area_id,
                success: function (result) {
                    var response = JSON.parse(result);
                    /*console.log(response); */
                    if ( response.status == 1 ) {
                        alert(response.message);
//                        var legal_entity_id = $('#legal_entity_id').val();
//                        var le_wh_id = $('#le_wh_id').val();
//                        $.get('/warehouse/getPJPs', function (res_pin) {
//                            var pjps = res_pin;
//                            pjpgrid(pjps);
//                        });
                        $("#pjp_table").igGrid("dataBind");
                    }
                    else {
                        alert(response.message);
                    }
                }
            });

    }
    function deletePJPArea(pin_area_id)
    {
        var decision = confirm("Are you sure you want to Delete.");
        if ( decision == true )
            $.ajax({
                url: '/warehouse/deletePJPArea/' + pin_area_id,
                success: function (result) {
                    var response = JSON.parse(result);
                    /*console.log(response); */
                    if ( response.status == 1 ) {
//                        alert(response.message);
//                        var legal_entity_id = $('#legal_entity_id').val();
//                        var le_wh_id = $('#le_wh_id').val();
//                        $.get('/warehouse/getPJPs', function (res_pin) {
//                            var pjps = res_pin;
//                            pjpgrid(pjps);
//                        });
                        $("#pjp_table").igGrid("dataBind");
                    }
                    else {
                        alert(response.message);
                    }
                }
            });

    }
</script>

<script type="text/javascript">

    $('#update_warehouse').click(function () {
        var formValid = $('#edit_warehouse').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else {
            var id = $('#le_wh_id').val();
            var hub_ids=$('#hubs').val();
            if($('#wh_type').val() == 118001 && hub_ids != null){
                console.log('hub data',hub_ids);
                var validatehub=false;
                $.ajax({
                    url:'/warehouse/hubvalidation/'+id,
                    data: $('#edit_warehouse').serialize(),
                    type:'POST',
                    success:function(res){
                        var response=JSON.parse(res);
                        console.log(response);
                        if(response.status == true){
                            validatehub=true;
                            console.log('iam here');
                            $.ajax({
                                url: '/warehouse/updateCustomWarehouse/' + id,
                                data: $('#edit_warehouse').serialize(),
                                type: 'POST',
                                success: function (result)
                                {
                                    var response = JSON.parse(result);
                                    if ( response.status == true ) {
                                        alert(response.message);
                                        $('#tab2').trigger('click');
                                    }
                                }
                            });   
                        }else{
                            validatehub=false;
                            alert(response.message);
                        }
                    }
                });
            }else{
                $.ajax({
                    url: '/warehouse/updateCustomWarehouse/' + id,
                    data: $('#edit_warehouse').serialize(),
                    type: 'POST',
                    success: function (result)
                    {
                        var response = JSON.parse(result);
                        if ( response.status == true ) {
                            alert(response.message);
                            $('#tab2').trigger('click');
                        }
                    }
                });
            }
        }
    });

    $('#save_docs').click(function () {
        var formValid = $('#saveDocForm').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            //$('#save_docs').attr('disabled',true);
            return false;
        }
        else {

            var form = document.forms.namedItem("saveDocForm");
            var formdata = new FormData(form);
            //console.log(form);

            $.ajax({
                url: '/warehouse/updateDocs',
                data: formdata,
                type: $(form).attr('method'),
                processData: false,
                contentType: false,
                success: function (result)
                {
                    var response = JSON.parse(result);
                    console.log(response);
                    alert(response.message);
                    $('#tab2').attr('class', 'disabled');
                    $('#tab3').removeClass('disabled');
                    $('#tab3').trigger('click');
                }
            });
        }
    });

    $(document).ready(function () {
        $('input[type="radio"]').click(function () {
            if ( $(this).attr("value") == "Range" ) {
                $("#pincode").hide();
                $('#savePinForm').formValidation('enableFieldValidators', 'end_range', true);
                $('#savePinForm').formValidation('enableFieldValidators', 'start_range', true);
                $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', false);
                $("#range").show();
            }
            if ( $(this).attr("value") == "Pincode" ) {
                $("#range").hide();
                $('#savePinForm').formValidation('enableFieldValidators', 'end_range', false);
                $('#savePinForm').formValidation('enableFieldValidators', 'start_range', false);
                $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', true);
                $("#pincode").show();
            }
        });
    });

    $('#add_pincode_range').click(function () {
        $('#savePinForm').formValidation('enableFieldValidators', 'end_range', true);
        $('#savePinForm').formValidation('enableFieldValidators', 'start_range', true);
        $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', false);
        var formValid = $('#savePinForm').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else if ( $('#start_range').val() == "" || $('#end_range').val() == "" ) {
            return false;
        }
        else {
            var start_range = $('#start_range').val();
            var end_range = $('#end_range').val();
            var pin = start_range;
            var pincodes = new Array();
            var pins = JSON.parse($('#pincode_locations').val());
            console.log(pincode_locations);
            var temp = $('#temp_data').val();
            var message = '';
            console.log('temp:');
            console.log(temp);

            for (pin; pin <= end_range; pin++)
            {
                if ( temp.indexOf(pin) != -1 ) {
                    message = message + "Pincode :" + pin + " already added \n";
                    console.log('here');
                }
                else {
                    $.get('/warehouse/getPinLocations/' + pin, function (res_pin) {
                        var response = JSON.parse(res_pin);
                        if ( temp == '' ) {
                            temp = response.pincode;
                        }
                        else {
                            temp = temp + '##' + response.pincode;
                        }
                        $('#temp_data').attr('value', temp);
                        del_action = '<span style="padding-left:20px;" ><a href="javascript:void(0);" class="check-toggler" onclick="deleteRow(' + response.pincode + ')"><i class="fa fa-trash-o circle"></i></a></span><input type="hidden" id="pincodes[' + pincode + ']" name="pincodes[]" value="' + response.pincode + '">';
                        //$("#warehouse_table").append();
                        var pin_data = {'pincode': response.pincode, 'state': response.state, 'city': response.city, 'actions': del_action};
                        pins.push(pin_data);
                        //pincode_locations.push(pin_data);
                        $('#pincode_locations').val(JSON.stringify(pins));
                        $('#pin_code').val('');
                        console.log('else');
                        console.log('pins: ');
                        console.log(pins);
                        grid();
                    });
                }
            }
            $('#start_range').attr('value', null);
            $('#end_range').attr('value', null);
            $(".glyphicon").hide();
            if ( message != '' ) {
                alert(message);
            }


        }
    });

    $('#add_pincode').click(function () {
        $('#savePinForm').formValidation('enableFieldValidators', 'end_range', false);
        $('#savePinForm').formValidation('enableFieldValidators', 'start_range', false);
        $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', true);
        var formValid = $('#savePinForm').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else if ( $('#pin_code').val() == "" ) {
            return false;
        }
        else {
            var temp = $('#temp_data').val();
            var pincode = $('#pin_code').val();
            var le_wh_id = $('#le_wh_id').val();
            var pincodes = new Array();
            var pins = JSON.parse($('#pincode_locations').val());
            console.log(temp.indexOf(pincode));
            if ( temp.indexOf(pincode) != -1 ) {
                alert('Pincode ' + pincode + ' already added. Please enter a new Pincode');
            }
            else {
                $.get('/warehouse/checkhubpins/' + pincode+'_'+le_wh_id, function (res) {
                    
                    if(res ==1)
                    { 
                        $.get('/warehouse/getPinLocations/' + pincode, function (res_pin) {
                            console.log(res_pin);
                            var response = JSON.parse(res_pin);

                            if ( temp == '' ) {
                                temp = response.pincode;
                            }
                            else {
                                temp = temp + '##' + response.pincode;
                            }
                            $('#temp_data').attr('value', temp);
                            del_action = '<span style="padding-left:20px;" ><a href="javascript:void(0);" class="check-toggler" onclick="deleteRow(' + response.pincode + ')"><i class="fa fa-trash-o circle"></i></a></span><input type="hidden" id="pincodes[' + pincode + ']" name="pincodes[]" value="' + response.pincode + '">';
                            //$("#warehouse_table").append();
                            var pin_data = {'pincode': response.pincode, 'state': response.state, 'city': response.city, 'actions': del_action};
                            pins.push(pin_data);
                            $('#pincode_locations').val(JSON.stringify(pins));
                            console.log('else');
                            console.log('pins: ');
                            console.log(pins);
                            grid();
                        });
                    }
                    else
                    {
                        alert('Pincode "' + pincode + '" already associated with Hub "'+res+'". Please enter a new Pincode');
                    }
                });
             
            }
            $('#pin_code').attr('value', null);
            $(".glyphicon").hide();
        }
    });


    $("#warehouse_table").on('click', '#remCF2', function () {
        $(this).parent().parent().remove();
    });

    $('#save_locations').click(function () {
        var pincode_locations = JSON.parse($('#pincode_locations').val());
        if ( pincode_locations.length !== 0 ) {
            $.ajax({
                url: '/warehouse/savePinLocations',
                data: $('#savePinForm').serialize(),
                type: 'POST',
                success: function (result)
                {
                    var response = JSON.parse(result);
                    alert(response.message);
                    window.location = "/warehouse";
                }

            });
        }
        else {
            window.location = "/warehouse";
        }
    });

    $('#import_pin_button').click(function () {

        var formValid = $('#importExcel').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
        if ( formValid != 0 ) {
            return false;
        }
        else {

            var form = document.forms.namedItem("importExcel");
            var formdata = new FormData(form);
            //console.log(form);

            $.ajax({
                url: '/warehouse/importExcel',
                data: formdata,
                type: $(form).attr('method'),
                processData: false,
                contentType: false,
                success: function (result)
                {
                    var response = JSON.parse(result);
                    var message = response.message;
                    alertmessage = '';
                    $.each(message, function (id, val) {
                        alertmessage = alertmessage + val.message + "\n"
                    });
                    alert(alertmessage);
                    var ex_pin = $('#pincode_locations').val();
                    if ( ex_pin != '' ) {
                        var pins = JSON.parse(ex_pin);
                    }
                    else {
                        pins = [];
                    }
                    var pincode_cities = response.pincode_cities;
                    var pincode_delete = response.pincode_delete;
                    if ( typeof pincode_cities !== undefined && pincode_cities.length > 0 ) {
                        $.each(pincode_cities, function (id, val) {
                            console.log(val);
                            del_action = '<span style="padding-left:20px;" ><a href="javascript:void(0);" class="check-toggler" onclick="deletePin(' + val.wh_serviceables_id + ')"><i class="fa fa-trash-o circle"></i></a></span>';
                            var pin_data = {'pincode': val.pincode, 'state': val.state, 'city': val.city, 'actions': del_action, 'wh_serviceables_id': val.wh_serviceables_id};
                            pins.push(pin_data);
                            //pincode_locations.push(pin_data);
                            $('#pincode_locations').val(JSON.stringify(pins));
                        });
                    }
                    if ( typeof pincode_delete !== undefined && pincode_delete.length > 0 ) {
                        $.each(pincode_delete, function (id, val) {
                            var pins = JSON.parse($('#pincode_locations').val());
                            rowId = val.pincode;
                            var temp = $('#temp_data').val();
                            console.log(temp);
                            console.log('rowId ', rowId);
                            var temp = temp.replace(rowId, ' ');
                            $('#temp_data').attr('value', temp);
                            var tempPins = new Array()
                            jQuery.each(pins, function (i, val) {
                                if ( val.pincode == rowId ) // delete index
                                {
                                    delete pins[i];
                                } else {
                                    tempPins.push(pins[i]);
                                }
                            });
                            var pin = [];
                            console.log(tempPins);
                            $('#pincode_locations').val(JSON.stringify(pin));
                            $('#pincode_locations').val(JSON.stringify(tempPins));
                            var gridVar = $("#warehouse_table").data("igGrid");
                            gridVar.dataSource.deleteRow(rowId);
                            gridVar.commit();
                        });
                    }
                    $('.close').trigger('click');
                    grid();
                }
            });
        }
    });
</script>
<script type="text/javascript">

     
    function editPJP(id)
    {
        console.log(id);
        $.get('/warehouse/editPJP/' + id, function (response) {
            $("#basicvalCode4").html('Edit Beat');
            $("#lookupsDiv").html(response);
            $('#edit_rm_id').select2();
            $("#editPJP").click();
        });

    }
    function addPJPArea(id)
    {
        console.log(id);
        var le_wh_id = $('#le_wh_id').val();
        $.get('/warehouse/addPJPArea/' + id + '/' + le_wh_id, function (response) {
            $("#basicvalCode4").html('Add Beat Area');

            $("#lookupsDiv").html(response);

            $("#addPJPArea").click();
        });
    }
    function editSpoke(spoke_id)
    {
        $.get('/warehouse/editspoke/' + spoke_id, function (response) {
            if (response.length){  
                var result = JSON.parse(response);
                if(typeof result == 'object')
                {
//                    $('#basicvalCodeModal5').find('#dc_update_spoke').select2().select2('val',result.dc_id);
                    $('#basicvalCodeModal6').find('#hub').select2().select2('val',result.hub_id);
                    $('#basicvalCodeModal6').find('#spoke_name').val(result.spoke_name);
                    $('#basicvalCodeModal6').find('#spoke_id').val(result.spoke_id);
                    $('#basicvalCodeModal6').find('#current_hub_id').val(result.hub_id);
                }else{
                    console.log('invalid json data');
                }
            }else{
                console.log('No data from ajax');
            }
        });
        $('#basicvalCodeModal6').modal('show');
    }
    function movePJP(spoke_id)
    {
        $.get('/warehouse/editspoke/' + spoke_id, function (response) {
            if (response.length){  
                var result = JSON.parse(response);
                if(typeof result == 'object')
                {
//                    $('#basicvalCodeModal5').find('#dc_update_spoke').select2().select2('val',result.dc_id);
                    $('#basicvalCodeModal5').find('#hub').select2().select2('val',result.hub_id);
                    $('#basicvalCodeModal5').find('#spoke_name').val(result.spoke_name);
                    $('#basicvalCodeModal5').find('#spoke_id').val(result.spoke_id);
                    $('#basicvalCodeModal5').find('#current_hub_id').val(result.hub_id);
                }else{
                    console.log('invalid json data');
                }
            }else{
                console.log('No data from ajax');
            }
        });
        $("#basicvalCodeModal5").modal('show');
    }
    
    function addPJP(spokeId)
    {
        if(spokeId > 0)
        {
            $('#basicvalCodeModal1').modal('show'); 
            $('#basicvalCodeModal1').find('#spoke').val(spokeId).prop('selected', true);
            $('#basicvalCodeModal1').find('#spoke').select2().select2('val',spokeId);
        }
    }
    $.ajaxSetup({
        headers:
        {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });

    function spokeBeat(){
        var beat=$('#spoke').val();
        var pjp_name=$('#pjp_name').val();
        var le_wh_id=$('#le_wh_id').val();
        

        $.ajax({
                url:'/warehouse/checkUniquePJP',
                data:{'le_wh_id': le_wh_id, 'pjp_name' : pjp_name,'spoke':beat},
                type:'POST',
                success:function(dataResult){

                    var result=JSON.parse(dataResult);
                    
                    if(result.valid == true){
                        
                        $('#beat_exist').css('display','none'); 
                        $("#add_pjp_grid").css('display','block');

                    }else {
                        
                        $('#beat_exist').css('display','block');
                        $("#add_pjp_grid").css('display','none');

                    }

                }

        });
    }
    function displayadd(){
        $('#beat_exist').css('display','none');
        $("#add_pjp_grid").css('display','block');
    }
    $('#fcs').change(function(){
        console.log('ddd');
        var fcval=$('#fcs').val();
        if(fcval!=null){
            $.ajax({
                url:'/warehouse/fcvalidation/'+fcval,
                data: $('#edit_warehouse').serialize(),
                type:'POST',
                success:function(res){
                    var res=JSON.parse(res);
                    console.log(res);
                    if(res.status == true){
                        $('#update_warehouse').prop('disabled',false);                      
                    }else{
                        alert(res.message);
                        $('#update_warehouse').prop('disabled',true);                      
                    }
                }
            });
        }
    });
</script> 

@stop
@extends('layouts.footer')

