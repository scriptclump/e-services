@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> ADD WAREHOUSE </div>
        <div class="tools"> </div>
        <input type="hidden" id="save_id" name="save_id">
      </div>
      <div class="portlet-body">
        <div class="tabbable-line">
          <ul class="nav nav-tabs ">
            <li class="active"><a id="tab1" href="#tab_11" data-toggle="tab">Warehouse Information</a></li>
            <li class=""><a id="tab2" href="#tab_22" data-toggle="tab">Documents</a></li>
            <li class=""><a id="tab3" href="#tab_33" data-toggle="tab">Serviceable Locations</a></li>
            <!--li class=""><a id="tab4" href="#tab_44" data-toggle="tab">PJP's</a></li-->
          </ul>
          <div class="tab-content headings">
            <div class="tab-pane active" id="tab_11" >
              <form id="submit_form_wh">
                <input type="hidden" name="wh_lp_id" id="wh_lp_id" value="" />
                <input type="hidden" name="LpId" id="LpId" value="" />
                <input type="hidden" name="wh_latitude" id="wh_latitude" value="" />
                <input type="hidden" name="wh_logitude" id="wh_logitude" value="" />
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="hidden" name="bu_ids" id="bu_ids" value="" />
               
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Warehouse Type <span class="required" aria-required="true">*</span></label>
                      <select name="wh_type" id="wh_type" class="form-control">
                        <option value="">--Select warehouse type--</option>
                        
                                          @foreach($warehouse_type as $key => $w_type)
                                          
                        <option value="{{$w_type->value}}">{{$w_type->name}}</option>
                        
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
                       <input type="text" name="displayname" id="displayname" class="form-control">
                    </div>
                </div>      
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Warehouse Name <span class="required" aria-required="true">*</span></label>
                      <input type="text" name="wh_name" id="wh_name" class="form-control">
                    </div>
                  </div>
                    <div class="col-md-6" id="hub_list" style="display: none;">
                        <div class="form-group">
                            <label class="control-label">Hubs</label>
                            <select name="hubs[]" id="hubs" class="form-control select2me" multiple="true">
                                <option value="">--Select warehouse type--</option>
                                @if(isset($getHubDetails))
                                    @foreach($getHubDetails as $hub)
                                        <option value="{{ $hub->le_wh_id }}">{{ $hub->lp_wh_name }}</option>
                                    @endforeach
                                @endif                                                
                            </select>
                        </div>
                    </div>                  
                </div>
                <div class="row">
                  <div class="col-md-6">
                  <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="control-label">Warehouse Code <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="wh_code"  id="wh_code" class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                      <select name="wh_state" id="wh_state" class="form-control">
                        <option value="">--Select State--</option>
                        
                                          @foreach($states as $key => $state)
                                          
                        <option value="{{$state->state_id}}">{{$state->state}}</option>
                        
                                          @endforeach
                                       
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                          <!-- <input type="text" name="wh_city" id="wh_city" class="form-control"> -->

                          <select name="wh_city" id="wh_city" class="form-control">
                                       
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="wh_address1"  id="wh_address1" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Address 2 </label>
                          <input type="text" name="wh_address2" id="wh_address2" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">FSSAI</label>
                          <input type="text" name="fssai" id="fssai" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="wh_pincode" id="wh_pincode" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="control-label">Margin <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="margin" id="margin" class="form-control">
                        </div>
                      </div>
                    
                        <div class="col-md-6">
                        <div class="form-group">
                                <label>Price Group</label>
                              <select name="price_group_id" id="price_group_id"class="form-control select2me " placeholder="Select Price Group">
                                  <option value=""> </option>  
                                    @foreach($priceGroup as $value)
                                    <option value="{{$value->value }}">{{ $value->master_lookup_name}}</option>
                                    @endforeach
                             </select>
                            </div>
                        </div>
                     </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group" style="margin-bottom: 0;">
                            <div class="mt-checkbox-list">
                              <label class="mt-checkbox" style="margin-right: 0;">
                                <input type="checkbox" id="is_apob_id" name="is_apob_id">Is APOB
                              </label>
                            </div>
                          </div>                       
                        </div>
                      <div class="col-md-5">
                            <div class="form-group" style="margin-bottom: 0;">
                                <div class="mt-checkbox-list">
                                    <label class="mt-checkbox" style="">
                                        <input type="checkbox" id="limit_check" checked name="limit_check">Credit Limit Check
                                    </label>
                                </div>
                            </div>                       
                        </div>
                        <div class="col-md-4">
                          <div class="form-group" style="margin-bottom: 0;">
                            <div class="mt-checkbox-list">
                              <label class="mt-checkbox" style="">
                                <input type="checkbox" id="billing" checked name="billing">Is Billing
                              </label>
                            </div>
                          </div>                       
                        </div>
                      </div>

                     <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Jurisdiction<span class=""></span></label>
                          <input type="text" name="Jurisdiction_id" id="Jurisdiction_id" class="form-control">
                        </div>
                      </div>
                       
                     </div>
                  </div>
                  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBq_oSY3B2sPg9h606CT_TLYJ5COErzW-A&libraries=places"></script> 
                  <script type="text/javascript">
                                                window.onload = function () {
                                                
                                                    var latt = $( "#wh_latitude" ).val();
                                                    var logg = $( "#wh_logitude" ).val();
                                                    if(latt == '')
                                                    {
                                                      latt = 17.3850;  
                                                    }
                                                    if(logg == '')
                                                    {
                                                      logg = 78.4867;  
                                                    }
                                                    var mapOptions = {
                                                        center: new google.maps.LatLng(latt, logg),
                                                        zoom: 8,
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
                                                    
                                                    google.maps.event.addListener(map,'mousemove',function() {
                                                                    google.maps.event.trigger(map, 'resize');
                                                    });
                                                    
                                                    var input = document.getElementById("keyword");
                                                    var autocomplete = new google.maps.places.Autocomplete(input);
                                                    autocomplete.bindTo("bounds", map);
                                                
                                                    var marker = new google.maps.Marker({map: map});
                                                
                                                    google.maps.event.addListener(autocomplete, "place_changed", function()
                                                    {
                                                    var place = autocomplete.getPlace();
                                                    var search_lat = place.geometry.location.lat();
                                                    var search_lng = place.geometry.location.lng();
                                                    $('#wh_lat').val(search_lat);
                                                    $('#wh_log').val(search_lng);
                                                
                                                    if (place.geometry.viewport) {
                                                    map.fitBounds(place.geometry.viewport);
                                                    } else {
                                                    map.setCenter(place.geometry.location);
                                                    map.setZoom(15);
                                                    }
                                                
                                                    marker.setPosition(place.geometry.location);
                                                    });
                                                
                                                google.maps.event.addListener(map, "click", function(event)
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
                    <div class="input-icon"> <i class="fa fa-bars" style="position: absolute;top: -250px;left: 2px;"></i>
                      <input type="text" class="form-control" name="keyword" id="keyword" style="position: absolute;top:-250px; left:4px;z-index: 2; width:260px;" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Country<span class="required" aria-required="true">*</span></label>
                      <select name="wh_country" id="wh_country" class="form-control">
                        <option value="">--Select Country--</option>
                        
                                          @foreach($countries as $key => $country_value)
                                          
                        <option value="{{$country_value->country_id}}">{{$country_value->country}}</option>
                        
                                          @endforeach
                                      
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Latitude</label>
                          <input type="text" name="wh_lat" id="wh_lat" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Longitude</label>
                          <input type="text" name="wh_log" id="wh_log" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Contact Name<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="contact_name" value="" id="contact_name" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Phone Number<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="phone_no" value="" id="phone_no" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Email<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="email" value="" id="email" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <input type="button" name="" id="save_warehouse" class="btn green-meadow" value="Save">
                    <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a> </div>
                </div>
              </form>
            </div>
            <div class="tab-pane" id="tab_22">
              <form id="saveDocForm" method="POST"  files="true" enctype ="multipart/form-data">
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label class="control-label">TIN Number <span class="required" aria-required="true">*</span></label>
                      <input type="text" id="tin_number" name="tin_number" class="form-control">
                      <input type="hidden" name="le_wh_id" id="le_wh_id">
                      <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    </div>
                  </div>
                </div>
                <div class="row VAT">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">TIN Proof <span class="required TINVAT_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="fileinput fileinput-new" data-provides="fileinput"> <span class="btn default btn-file btn green-meadow" style="width:110px !important;"> <span class="fileinput-new">Choose File </span> <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span> </span>
                            <div class="fileinput-preview fileinput-exists thumbnail" style=" height: 33px; margin-left:9px; margin-top:10px;z-index:99;position: relative; display: -webkit-inline-box;"> <img src="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif" alt="" class="tinvat_files_id" /></div>
                            <br />
                            <input id="tinvat_files" type="file" class="upload" name="tin_files" style="margin-top: -45px !important; height:45px;  position: absolute;opacity: 0;"/>
                            <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px; font-size:12px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span> </div>
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
                        <div class="col-md-12">
                          <div class="fileinput fileinput-new" data-provides="fileinput"> <span class="btn default btn-file btn green-meadow" style="width:110px !important;"> <span class="fileinput-new">Choose File </span> <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span> </span>
                            <div class="fileinput-preview fileinput-exists thumbnail" style=" height: 33px; margin-left:9px; margin-top:10px; z-index:99; position: relative; display: -webkit-inline-box;"> <img src="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif" alt="" class="tinvat_files_id" /></div>
                            <br />
                            <input id="tinvat_files" type="file" class="upload" name="apob_files" style="margin-top: -45px !important; height:45px;  position: absolute;opacity: 0;"/>
                            <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px; font-size:12px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span> </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <input type="button" name="" id="save_docs" class="btn green-meadow" value="Save">
                    <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a> </div>
                </div>
              </form>
            </div>
            <div class="tab-pane" id="tab_33">
            <!-- // The Code for Servicable Locations -->
            <div id="service_area">
              <form id="savePinForm">
                <div class="row">
                  <div class="col-md-5">
                    <label class="mt-radio mt-radio-outline">Add by Pincode Range
                      <input type="radio" name="locations" value="Range">
                      <span></span> </label>
                    <label class="mt-radio mt-radio-outline">Add by Pincode
                      <input type="radio" name="locations" value="Pincode">
                      <span></span> </label>
                  </div>
                  <div class="col-md-5"> &nbsp; </div>
                  <div class="col-md-2"> 
                    <!--  <input type="button" name="" id="export_locs" class="btn green-meadow" value="Export"> --> 
                    <a href="javascript:void(0)" class="btn green-meadow pull-right" data-toggle="modal" data-target="#basicvalCodeModal" >Import</a> </div>
                </div>
                <div id="range" style="display:none">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-5">
                          <div class="form-group">
                            <label class="control-label">Start Range <span class="required" aria-required="true">*</span></label>
                            <input type="text" id="start_range" name="start_range" class="form-control">
                            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                            <input type="hidden" name="temp_data" id="temp_data">
                            <input type="hidden" name="le_wh_id" id="le_wh_id1">
                            <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{$legal_entity_id}}" />
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
                            <a title="Add" class="btn blue" id="add_pincode_range" >Add</a> </div>
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
                
                <div class="row">
                  <div class="col-md-12">
                    <div class="portlet box" id="form_wizard_1">
                      <div class="portlet-body form">
                        <div class="form-wizard">
                          <div class="form-body">
                            <div class="box">
                              <div class="tile-body nopadding">
                                <div id="pincode_grid">
                                  <table id="warehouse_table">
                                  </table>
                                  <input type="hidden" name="pincode_locations" id="pincode_locations">
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
                    <input type="button" name="" id="save_locations" class="btn green-meadow" value="Save">
                    <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a> </div>
                </div>
              </form>
            </div>
            <!-- // The Code for Spoke Area -->
            <div id="spoke_area">
              <div class="actions pull-right" style="margin-bottom:10px;">
                  <a role="button" id="exportBeat" class="btn green-meadow">Export Beat</a>
                  @if(isset($dcAddBeat) && $dcAddBeat == 1)
                    <a href="javascript:void(0)" class="btn green-meadow" data-toggle="modal" data-target="#basicvalCodeModal5" onclick="displayadd()" >Add Beat</a>
                  @endif
                  @if(isset($dcAddSpoke) && $dcAddSpoke == 1)
                    <a class="btn green-meadow" data-toggle="modal" data-target="#basicvalCodeModal3" >Add Spoke</a>
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
            </div>
            <div class="tab-pane" id="tab_44" style="padding-top:0px !important;">
              <div class="actions pull-right"> <a href="javascript:void(0)" class="btn green-meadow" data-toggle="modal" data-target="#basicvalCodeModal1" >Add PJP</a> </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="portlet box" id="form_wizard_1">
                    <div class="portlet-body form">
                      <div class="form-wizard">
                        <div class="form-body">
                          <div class="box">
                            <div class="tile-body nopadding">
                              <div id="pjp_grid">
                                <table id="pjp_table">
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
              <div class="row">
                <div class="col-md-12 text-center">
                  <input type="button" name="" id="done" class="btn green-meadow" value="Done">
                  <a href="/warehouse" id="cancel3" class="btn default"> Cancel </a> </div>
              </div>
            </div>
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
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">Import File</h4>
      </div>
      <div class="modal-body">
        <form id="importExcel" method="POST"  files="true" enctype ="multipart/form-data">
          <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{$legal_entity_id}}" />
          <input type="hidden" name="le_wh_id" id="le_wh_id2" >
          <div class="row">
           <div class="form-group">
             
                <div class="col-md-12  text-center" style="margin-bottom:10px;"> <a href="/warehouse/downloadPinSample" class="btn green-meadow btn-block" role="button" id="exportPin">Download Sample</a> </div>
                </div>
            <div class="form-group">
              <div class="col-md-12">
                <div class="fileinput fileinput-new" data-provides="fileinput" style=""> <span class="fileUpload btn green-meadow btn-block" > <span class="fileinput-new">Choose File </span> <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span> </span>
                  <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100% !important; text-align: center; margin:0 auto;  height: 33px; margin-top:10px;"> <img src="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif" alt="" class="tinvat_files_id" /></div>
                  <input id="import_files" type="file" class="upload" name="import_file" style="margin-top: -35px !important; height:35px;  position: absolute;opacity: 0;"/>
                  <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; text-align: center; width:100%;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span> </div>
              </div>
            </div>
           
          </div>
          <!--<div class="row">
            <div class="col-md-12 text-center">
              <input type="button" name="" id="import_pin_button" class="btn green-meadow btn-block" value="Import">
            </div>
          </div>-->
        </form>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.modal -->


  <div class="modal modal-scroll fade in" id="basicvalCodeModal2" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="basicvalCode">Add PJP Pincode Areas</h4>
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
                       <i class="fa fa-plus" aria-hidden="true"></i>
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
                        <input type="text" name="area_city" id="area_city" class="form-control">
                     </div>
                  </div>
              <div class="col-md-6">
                   <div class="form-group">
                      <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                      <select name="area_state" id="area_state" class="form-control">
                         <option value="">Please select State.</option>
                         @if(isset($states))
                             @foreach($states as $state)
                             <option value="{{$state->state_id}}" >{{$state->state}}</option>
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

<!-- Modal to Add Spoke -->
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
                                    <input type="text" name="hub_name" id="hub_name" value="" class="form-control">
                                    <input type="hidden" name="hub_id" id="hub_id" value="">
                                    <!-- <select id="hub" name="hub_id" class="form-control select2me">
                                            <option value="{{ $hub->le_wh_id }}">{{ $hub->lp_wh_name }}</option>
                                    </select> -->
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

<!-- Modal to Add Beat -->
<div class="modal modal-scroll fade in" id="basicvalCodeModal5" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
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
                                <select class="form-control select2me" name="spoke" id="spoke">
                                    <option value="0">Please Select...</option>
                                    @if(isset($spokes) && !empty($spokes))
                                        @foreach($spokes as $spoke)
                                        <option value="{{ $spoke->spoke_id }}">{{ $spoke->spoke_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
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
                                <input type="hidden" name="pjp_le_wh_id" id="pjp_le_wh_id">
                                <input type="text" name="pjp_name" id="pjp_name" placeholder="Beat" class="form-control" onchange="addSpokeBeat()">
                                <input type="hidden" name="pjps" id="pjps" value="@if(isset($pjps)) {{$pjps}} @endif">
                                <div id="beat_exist" name="beat_exist" style="display:none;color:#e02222;font-size:12px;padding-left: 15px;">Beat already exists</div>
                                
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

<div class="modal modal-scroll fade in" id="basicvalCodeModal6" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Edit Spoke</h4>
            </div>
            <div class="modal-body">  
                <!-- hub
                spoke_name
                spoke_id
                current_hub_id -->
                <form id="update_spoke_form">
                    <div id="map_spoke">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hub <span class="required" aria-required="true">*</span></label>
                                    <select id="hub" name="hub_id" class="form-control select2me" disabled="disabled">
                                        <option value="0">--Select--</option>
                                                    <option value="{{ $hub->le_wh_id }}" selected="true">{{ $hub->lp_wh_name }}</option>
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
        <h4 class="modal-title" id="basicvalCode4">Add PJP</h4>
      </div>
      <div class="modal-body">
        <div class="modal-body" id="lookupsDiv"> </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.modal --> 
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
}
.fileinput {
    display: inherit !important;
}
.help-block {
    text-align: center;
    padding: 0px 10px;
}
.tabbable-line>.tab-content{padding-top:20px !important;}
.portlet.box .portlet-body {
     border: 0px solid #ccc !important;
}

.glyphicon-remove{color: #e02222 !important;}
.glyphicon-ok {
    color: #3c763d !important;
}
.has-feedback label~.form-control-feedback {
    top: 34px !important;
}
#dvMap{height:304px !important; width:100% !important;}
.fileinput-exists .fileinput-new, .fileinput-new .fileinput-exists{
    display: run-in !important;
}
.thumbnail img {display:run-in!important; max-height: 100% !important;}
.thumbnail{padding: 0px !important; border: 0px !important;}
.mt-checkbox, .mt-radio{margin-right: 20px;}
</style>
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('js/helper.js')}}
@stop
@section('userscript') 
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/getBusinessUnitsDowpDown.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$('#basicvalCodeModal').on('hide.bs.modal',function(){
    console.log('resetForm');
    $('#importExcel').bootstrapValidator('resetForm', true);
    $('#importExcel')[0].reset();   
});
$(document).ready(function (){
  makePopupEditAjax($('#basicvalCodeModal2'), 'pjp_pincode_area_id');
});
$('#done').click(function (){
  window.location = "/warehouse";
});
</script> 
<script type="text/javascript">
  $(document).ready(function(){
  $('#basicvalCodeModal2').on('shown.bs.modal', function () {
    $('#map_pincode').show();
    $('#map_area').hide();
  });
    $('#addArea').click(function (){
      $('#map_area').show();
      $('#map_pincode').hide();
    });
    $('#add_pjp_pincode').click(function(){
       $('#map_area').hide();
      $('#map_pincode').show();
    });
  });
</script>

<script type="text/javascript">
  $(document).ready(function (){
     $('#basicvalCodeModal1').on('hide.bs.modal',function(){
          console.log('resetForm');
          $('#savePJPForm').bootstrapValidator('resetForm', true);
          $('#savePJPForm')[0].reset();   
      });
     $('#basicvalCodeModal2').on('hide.bs.modal',function(){
          console.log('resetForm');
          $('#basicvalCodeModal2').find('.select2-container').select2('val', '');
          $('#savePJPAreaForm').bootstrapValidator('resetForm', true);
          $('#savePJPAreaForm')[0].reset();   
      });
     $('#basicvalCodeModal4').on('hide.bs.modal',function(){
          console.log('resetForm');
          $('#editPJPForm').bootstrapValidator('resetForm', true);
          $('#editPJPForm')[0].reset();   
      });

  });
</script> 
<script type="text/javascript">
  $(function (){

    $('#map_pincode_area').click(function (){
      var formValid = $('#mapAreaForm').formValidation('validate');
        formValid = formValid.data('formValidation').$invalidFields.length;
    if(formValid != 0){
        return false;
      }
   else{
       var data = $('#mapAreaForm').serialize();
      $.ajax({
           url: '/warehouse/mapArea',
           data: data,
           type: 'POST',
           success: function (result)
           {   
            console.log(result);
            var response = JSON.parse(result);
            if(response.status == 1){
              alert(response.message);
              $('#mapAreaForm').bootstrapValidator('resetForm', true);
              $('#mapAreaForm')[0].reset();
              $('#map_pincode').show();
              $('#map_area').hide();
            }
            else{
              alert(response.message);
            }
           }
        });
      } 
  });
  });
</script>
<script type="text/javascript">
function editSpoke(spoke_id)
{
    $.get('/warehouse/editspoke/' + spoke_id, function (response) {
        if (response.length){  
            var result = JSON.parse(response);
            if(typeof result == 'object')
            {
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
        $('#basicvalCodeModal5').modal('show');
        $('#basicvalCodeModal5').find('#spoke').val(spokeId).prop('selected', true);
        $('#basicvalCodeModal5').find('#spoke').select2().select2('val',spokeId);
    }
}     
</script>
<script type="text/javascript">
$(document).ready(function (){

  $('#basicvalCodeModal5').on('hide.bs.modal', function () {
      console.log('resetForm');
      $('#basicvalCodeModal5').find('.select2-container').select2('val', 0);
      $('#savePJPForm').bootstrapValidator('resetForm', true);
      $('#savePJPForm')[0].reset();
      $('[id="spokes_input"]').hide();
      $('#spokes_select').show();
      $('#spoke_button').show();
  });

   $('#tab2').attr('class', 'disabled');
   $('#tab2').click(function(event){
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });
   $('#tab3').attr('class', 'disabled');
   $('#tab3').click(function(event){
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });
   $('#tab4').attr('class', 'disabled');
   $('#tab4').click(function(event){
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });
   $('#tab1').click(function(event){
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });
   
   $('#save_warehouse').click(function (){
        var formValid = $('#submit_form_wh').formValidation('validate');
            formValid = formValid.data('formValidation').$invalidFields.length;
             if(formValid != 0){
               return false;
             }
          else{
           $.ajax({
               url: '/warehouse/saveCustomWarehouse',
               data: $('#submit_form_wh').serialize(),
               type: 'POST',
               success: function (result)
               {   
                   var response = JSON.parse(result);
                   if(response.status == true){
                       alert(response.message);
                       $('#le_wh_id').attr('value',response.le_wh_id);
                       $('#le_wh_id1').attr('value',response.le_wh_id);                      
                       $('#le_wh_id2').attr('value',response.le_wh_id); 
                       $('#le_wh_id3').attr('value',response.le_wh_id); 
                       $('#pjp_le_wh_id').attr('value',response.le_wh_id); 
                       $('#tab1').attr('class','disabled');
                       $('#tab2').removeClass('disabled');
                       $('#tab3').removeClass('disabled');
                       $('#tab2').trigger('click');
                       $('#wh_type').attr('disabled', true);

                       $('#hub_name').val($('#wh_name').val());
                       $('#hub_id').val($('#le_wh_id').val());

                        $('#update_spoke_form').find('select#hub').append($('<option>', {
                            value: response.le_wh_id,
                            text: $('#wh_name').val()
                        })).select2().select2('val',response.le_wh_id);
                        
                       // Adding link to download Beats from Excel
                       $('#exportBeat').attr('href',"/warehouse/exportspokes/"+response.le_wh_id); 
                       $('#save_id').val(response.le_wh_id);
                       console.log($('#save_id').val());
                       $.get('/warehouse/getPJPs/', function (res_pin) {
                         var pjps = res_pin;
                         pjpgrid(pjps);
                       });
                       
                   }
                }
           });
       }
   });
   $('#submit_form_wh').formValidation({
           framework: 'bootstrap',
               icon: {
                 validating: 'glyphicon glyphicon-refresh'
             },
           fields: {
               wh_name:{
                   validators: {
                       notEmpty: {
                           message: ' '
                       },
                       regexp: {
                        regexp: /^[a-zA-Z0-9 \-,\#]+$/i,
                        message: ' '
                     },
                       remote: {
                        url: '/warehouse/checkUnique',
                        data: {wh_name: $('[name="wh_name"]').val()},
                        type: 'POST',
                        delay: 2000,
                        message: 'Warehouse name already exists'
                  }
                }
               },
               wh_code:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 _"!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        }
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
               wh_address1:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        }
                   }
               },
              
              wh_address2:{
                  validators:{
                     regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        },
                  }
              },
                wh_pincode:{
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
               /*wh_city:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       },
                    regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
                   }
               },*/
               wh_city:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
               wh_state:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
               price_group_id:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
               wh_type:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
               margin:{
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
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },                 
               wh_country:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
               wh_log:{
                validators:{
                  between: {
                        min: -180,
                        max: 180,
                        message : ' '
                    }
                }
               },
               wh_lat:{
                validators: {
                    between: {
                        min: -90,
                        max: 90,
                        message: ' '
                    }
                }
               },
               contact_name:{
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
                  regexp: /^\d{10}$/ ,
                  message: ' '
                }
              }
            }
           }
       }).on('success.form.fv', function(event) {
             event.preventDefault();
             console.log('here in success');
       }); 
     
       $('#mapAreaForm').formValidation({
        framework: 'bootstrap',
            icon: {
              validating: 'glyphicon glyphicon-refresh'
          },
         fields: {
               pincode_value1:{
                validators: {
                    notEmpty: {
                           message: ' '
                       },
                  }
                },
                area_name:{
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
                area_city:{
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
               area_state:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
              }
            }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
    });

        $('#savePinForm').formValidation({
           framework: 'bootstrap',
               icon: {
                 validating: 'glyphicon glyphicon-refresh'
             },
           fields: {
               start_range:{
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
               end_range:{
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
                                if(value <= start_range){
                                  return false;
                                }
                                if(value > maxval){
                                  return false;
                                }
                                else{
                                  return true;
                                }
                             }
                      }
                   }
               },
               pin_code:{
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
               }
             }
       }).on('success.form.fv', function(event) {
             event.preventDefault();
             console.log('here in success');
       }); 
       $('#saveDocForm').formValidation({
           framework: 'bootstrap',
               icon: {
                 validating: 'glyphicon glyphicon-refresh'
             },
           fields: {
            tin_number:{
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
          tin_files:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 2*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 10 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            },
          apob_files:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 2*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 10 MB at maximum.'
                    }
                }
            }
             }
       }).on('success.form.fv', function(event) {
             event.preventDefault();
             console.log('here in success');
       }); 

       $('#importExcel').formValidation({
           framework: 'bootstrap',
               icon: { 
                 validating: 'glyphicon glyphicon-refresh'
             },
           fields: {
           
          import_file:{
              validators: {
                file: {
                      extension: 'csv,xls,xlsx',
                      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,csv',
                      maxSize: 10*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (csv,xlsx,xls) and 10 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            }
             }
       }).on('success.form.fv', function(event) {
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
            pjp_name:{
              validators: {
                notEmpty: {
                            message: 'Please give beat name'
                     },
                /* remote: {
                      url: '/warehouse/checkUniquePJP',
                            data: function(validator) {
                               return {
                                   pjp_name: $('[name="pjp_name"]').val(),
                                   le_wh_id: $('[name="pjp_le_wh_id"]').val()
                               };
                            },
                      type: 'POST',
                            delay: 1000,
                            message: 'Beat already exists'
                    }*/
                }
            }
           }
       }).on('success.form.fv', function(event) {
             event.preventDefault();
            console.log('On Success of after validating the Beat Details');
            var legal_entity_id = $('#legal_entity_id').val();
            var le_wh_id = $('#le_wh_id').val();
            var data = $('#savePJPForm').serialize();
            $.ajax({
                url: '/warehouse/savePJP/' + le_wh_id + '/' + legal_entity_id,
                data: data,
                type: 'POST',
                success: function (result)
                {
                  if(result != null)
                  {

                    var response = JSON.parse(result);
                    alert(response.message);
                    $("#pjp_table").igGrid("dataBind");
                    $('#pjp_pincode_area_id').attr('value', response.pjp_pincode_area_id);
                    $('#basicvalCodeModal5').modal('hide');
                    $('#savePJPForm').trigger("reset");

                    $('#basicvalCodeModal5').on('hide.bs.modal', function () {
                        console.log('resetForm');
                        $('#savePJPForm').bootstrapValidator('resetForm', true);
                        $('#savePJPForm')[0].reset();
                    });
                  }
                  else
                  {
                    alert("Something, Went Wrong. Please Try Again");
                  }
                }
            });
       });  
   
   $('#save_locations').click(function (){
    var pincode_locations = $('#pincode_locations').val();
    if(pincode_locations !== ''){
     $.ajax({
           url: '/warehouse/savePinLocations',
           data: $('#savePinForm').serialize(),
           type: 'POST',
           success: function (result)
           {   
               var response = JSON.parse(result);
               alert(response.message);
               $('#tab4').removeClass('disabled');
               $('#tab4').trigger('click');
           }
       });
    }
    else{
      window.location = "/warehouse";
    }
   });

  $('#save_docs').click(function (){
    var formValid = $('#saveDocForm').formValidation('validate');
      formValid = formValid.data('formValidation').$invalidFields.length;
       if(formValid != 0){
        //$('#save_docs').attr('disabled',true);
         return false;
       }
    else{
      //$('#save_docs').attr('disabled',false);
      var form = document.forms.namedItem("saveDocForm"); 
      var formdata = new FormData(form);
    $.ajax({
        url: '/warehouse/saveDocs',
        data: formdata,
        type: $(form).attr('method'),
        processData :false,
        contentType:false,
        success: function (result)
        {
               var response = JSON.parse(result);
               console.log(response);
               if(response.status == true){
               alert(response.message);
               //$('#tab2').attr('class','disabled');
               //$('#tab3').removeClass('disabled');
               $('#tab3').trigger('click');
               }
               else{
                return false;
               }
        }
       });
      } 
   });
   $('#wh_type').change(function(){
       var temp = $(this).val();
       if(temp == 118001)
       {
           $('#hub_list').show();
          $('#tab3').text("Serviceable Locations");
          $('#service_area').show();
          $('#spoke_area').hide();

       }else{ // // If this is the Hub
           $('#hub_list').hide();
           $('#tab3').text("Spoke");
           $('#service_area').hide();
           $('#spoke_area').show();
       }
   });
})
$(document).ready(function (){
  var pin = $('#pincode_locations').val();
  if(pin != ''){
  var pins = JSON.parse(pin);
  
  var temp = $('#temp_data').val();
  console.log('pins :'); console.log(pins);
  $.each(pins, function(id, val){
            temp = temp + '##'  +val.pincode;
      });
  $('#temp_data').attr('value',temp);
  }
});
 $(function () {  
    console.log('we are before call'); 
     var legal_entity_id = $('#legal_entity_id').val();
     var le_wh_id = $('#le_wh_id').val();     
            grid();
    
    });

    function grid(){
      console.log('we are in grid call');
      console.log('pinonload:');
      // console.log($.parseJSON($('#pincode_locations').val()));

      var inputPins = [];
      if($('#pincode_locations').val() != '') 
        inputPins = $.parseJSON($('#pincode_locations').val());
      console.log('inputPins');
      if(inputPins == null){
        inputPins.pincode = '';
        inputPins.state = '';
        inputPins.city = '';
        inputPins.actions ='';
      }
    $('#warehouse_table').igGrid({
            dataSource: inputPins,
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            renderCheckboxes: true,
            columns: [
            //{ headerText: "wh_serviceables_id", key: "wh_serviceables_id", dataType: "string", width: "0%" },
            { headerText: "Pincode", key: "pincode", dataType: "string", width: "25%" },
            { headerText: "State", key: "state", dataType: "string", width: "30%" },
            { headerText: "City", key: "city", dataType: "string", width: "30%" },
            { headerText: "Action", key: "actions", dataType: "string", width: "15%" },
            ],
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
                    name: "RowSelectors",
                    enableCheckBoxes: true,
                    enableRowNumbering: false
            },
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

  $('#add_pjp_grid').click(function (){
    var legal_entity_id = $('#legal_entity_id').val();
        var le_wh_id = $('#le_wh_id').val();
    var formValid = $('#savePJPForm').formValidation('validate');
    formValid = formValid.data('formValidation').$invalidFields.length;
    if(formValid != 0){
        return false;
      }
   else{
            /*var data = $('#savePJPForm').serialize();
     $.ajax({
           url: '/warehouse/savePJP/'+le_wh_id+ '/'+legal_entity_id,
           data: data,
           type: 'POST',
           success: function (result)
           {   
               var response = JSON.parse(result);
               alert(response.message);
               console.log(response.data);
               $('#pjp_pincode_area_id').attr('value',response.pjp_pincode_area_id);
                 $.get('/warehouse/getPJPs/'+le_wh_id+ '/' + legal_entity_id, function (res_pin) {
                  var pjps = res_pin;
                  pjpgrid(pjps);
                  $('.close').trigger('click');
                  $('#basicvalCodeModal1').on('hide.bs.modal',function(){
                  console.log('resetForm');
                  $('#savePJPForm').bootstrapValidator('resetForm', true);
                  $('#savePJPForm')[0].reset();   
              });
            });  
          }     
            });*/

  }
  });
  $('#savePJPAreaForm').formValidation({
     framework: 'bootstrap',
         icon: {
           validating: 'glyphicon glyphicon-refresh'
       },
     fields: {
      pincode_value:{
                validators: {
                  notEmpty: {
                          message: ' '
                         }
                     }
                 },
      'pin_area[]' : {
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

  $('#add_area_grid').click(function (){
     var legal_entity_id = $('#legal_entity_id').val();
     var le_wh_id = $('#le_wh_id').val();
     var formValid = $('#savePJPAreaForm').formValidation('validate');
    formValid = formValid.data('formValidation').$invalidFields.length;
    if(formValid != 0){
        return false;
      }
   else{
     var data = $('#savePJPAreaForm').serialize();
     $.ajax({
           url: '/warehouse/savePJPArea',
           data: data,
           type: 'POST',
           success: function (result)
           {   
               var response = JSON.parse(result);
               alert(response.message);
                 $.get('/warehouse/getPJPs/'+le_wh_id+ '/' + legal_entity_id, function (res_pin) {
                  var pjps = res_pin;
                  pjpgrid(pjps);
                  $('.close').trigger('click');
                  $('#basicvalCodeModal1').on('hide.bs.modal',function(){
                  console.log('resetForm');
                  $('#savePJPForm').bootstrapValidator('resetForm', true);
                  $('#savePJPForm')[0].reset();   
              });
            });
           }
          });
   }
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
                        features: [
                            {
                                name: 'Paging',
                                type: 'local',
                                pageSize: 10,
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
                        primaryKey: 'pjp_pincode_area_id',
                        width: '100%',
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
                    initialDataBindDepth: 0,
                    localSchemaTransform: false

            });
}

  /*function pjpgrid(data){
    console.log('we are in grid call');
    $('#pjp_table').igTreeGrid({
            dataSource: data,
            dataSourceType: 'json',
            autoGenerateColumns: false,
            responseDataKey: 'Records',
            //generateCompactJSONResponse: false,
            //enableUTCDates: true,
            //renderCheckboxes: true,
            columns: [
            //{ headerText: "wh_serviceables_id", key: "wh_serviceables_id", dataType: "string", width: "0%" },
            { headerText: "pjp_pincode_area_id", key: "pjp_pincode_area_id", id:"pjp_pincode_area_id", dataType: "string", width: "10%", hidden:"true" },
            { headerText: "Beat", key: "pjp_name", dataType: "string", width: "15%" },
            { headerText: "Service days", key: "days", dataType: "string", width: "30%" },
            { headerText: "Pincode", key: "pincode", dataType: "string", width: "15%" },
            { headerText: "Area", key: "areas", dataType: "string", width: "25%" },
            { headerText: "Action", key: "actions", dataType: "string", width: "15%" },
            ],
            childDataKey: "pincode_area",
            initialExpandDepth: 0,
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
            primaryKey: 'pjp_pincode_area_id',
            width: '100%',
            height: '400px',
            //initialDataBindDepth: 0,
            //localSchemaTransform: false

    });
  }*/

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
                  
                  var le_wh_id = $('#le_wh_id').val();
                  $("#pjp_table").igGrid("dataBind");
 
                  //  NOTE: The below code is to Load the Hierarchial Grid for the first time only. 
                  // $.get('/warehouse/getspokes/'+le_wh_id, function (res_pin) {
                  //   var pjps = res_pin;
                  //   pjpgrid(pjps);
                  // });
              }else{
                  $('#spokes_input').children().addClass('has-error');
              }
  }
      });
    });


    function deletePin(id)
    {
    var decision = confirm("Are you sure you want to Delete.");
    if (decision == true)
           $.ajax({
            url: '/warehouse/deletePin/'+id,
            success: function (result){
              var response = JSON.parse(result);
              /*console.log(response); */
              if(response.status == true){
                
                alert(response.message);
                var pins = $.parseJSON($('#pincode_locations').val());
                var delData = response.delData;
                deleteRow(delData.pincode);
            }
        }
    });
}
 function deleteRow(rowId) { 
            var pins = JSON.parse($('#pincode_locations').val());
            var temp = $('#temp_data').val();
            console.log(temp);
            console.log('rowId ',rowId);           
            var temp = temp.replace(rowId,' ');
            $('#temp_data').attr('value',temp);
            var tempPins = new Array()
                jQuery.each(pins, function(i, val) {
                 if(val.pincode == rowId) // delete index
                 {
                    delete pins[i];                    
                 }else{
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
   
  function deletePJP(pjp_pin_area_id)
    {
    var decision = confirm("Are you sure you want to Delete.");
    if (decision == true)
           $.ajax({
            url: '/warehouse/deletePJP/' +pjp_pin_area_id,
            success: function (result){
              var response = JSON.parse(result);
              /*console.log(response); */
              if(response.status == 1){
                alert(response.message);
                var legal_entity_id = $('#legal_entity_id').val();
                var le_wh_id = $('#le_wh_id').val();
                $.get('/warehouse/getPJPs/'+le_wh_id+ '/' + legal_entity_id, function (res_pin) {
                  var pjps = res_pin;
                  pjpgrid(pjps);
                });
            }
            else{
              alert(response.message);
            }
        }
    });
    
}
 function deletePJPArea(pin_area_id)
    {
    var decision = confirm("Are you sure you want to Delete.");
    if (decision == true)
           $.ajax({
            url: '/warehouse/deletePJPArea/' +pin_area_id ,
            success: function (result){
              var response = JSON.parse(result);
              /*console.log(response); */
              if(response.status == 1){
                alert(response.message);
                var legal_entity_id = $('#legal_entity_id').val();
                var le_wh_id = $('#le_wh_id').val();
                $.get('/warehouse/getPJPs/'+le_wh_id+ '/' + legal_entity_id, function (res_pin) {
                  var pjps = res_pin;
                  pjpgrid(pjps);
                });
            }
            else{
              alert(response.message);
            }
        }
    });
}  
   
   $(document).ready(function(){
       $('input[type="radio"]').click(function(){
           if($(this).attr("value")=="Range"){
               $("#pincode").hide();
                $('#savePinForm').formValidation('enableFieldValidators', 'end_range', true);
               $('#savePinForm').formValidation('enableFieldValidators', 'start_range', true);
               $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', false);
               $("#range").show();
           }
           if($(this).attr("value")=="Pincode"){
               $("#range").hide();
                $('#savePinForm').formValidation('enableFieldValidators', 'end_range', false);
               $('#savePinForm').formValidation('enableFieldValidators', 'start_range', false);
               $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', true);
               $("#pincode").show();
           }
       });
   });
   
  $('#add_pincode_range').click(function (){
    var formValid = $('#savePinForm').formValidation('validate');
    formValid = formValid.data('formValidation').$invalidFields.length;
    if(formValid != 0){
      return false;
    }
    else if($('#start_range').val() == "" || $('#end_range').val() == ""){
      
        return false;
    }
    else{
     var start_range = $('#start_range').val();
     var end_range = $('#end_range').val();
     var pin = start_range;
     var pincodes = new Array();
     var ex_pin = $('#pincode_locations').val();
    if(ex_pin != ''){
      var pins = JSON.parse(ex_pin);
     }
     else{
      pins = [];
     }
     console.log(pincode_locations);  
     var temp = $('#temp_data').val();
     var message = '';
     console.log('temp:'); console.log(temp);

     for(pin;pin<=end_range;pin++)
     {
       if(temp.indexOf(pin) != -1){
       message = message + "Pincode :" +pin+ " already added \n";
       console.log('here');
       }
       else{
        $.get('/warehouse/getPinLocations/' + pin, function (res_pin) {
          var response = JSON.parse(res_pin);
         if(temp == ''){
           temp = response.pincode;
         }
         else{
         temp = temp +'##' + response.pincode;
         }
         $('#temp_data').attr('value',temp);
          del_action = '<span style="padding-left:20px;" ><a href="javascript:void(0);" class="check-toggler" onclick="deleteRow('+response.pincode+')"><i class="fa fa-trash-o circle"></i></a></span><input type="hidden" id="pincodes['+pincode+']" name="pincodes[]" value="'+response.pincode+'">';
           //$("#warehouse_table").append();
          var pin_data = {'pincode' : response.pincode , 'state' : response.state,'city' : response.city, 'actions' : del_action};
          pins.push(pin_data);
          //pincode_locations.push(pin_data);
          $('#pincode_locations').val(JSON.stringify(pins));
         console.log('else');
         console.log('pins: ');console.log(pins);
         grid();
        });
       }
     }
     $('#start_range').attr('value',null);
    $('#end_range').attr('value',null);
    $(".glyphicon").hide();
     if(message != ''){
       alert(message);
     }
   
     
    } 
  });
   
   $('#add_pincode').click(function (){
       $('#savePinForm').formValidation('enableFieldValidators', 'end_range', false);
    $('#savePinForm').formValidation('enableFieldValidators', 'start_range', false);
    $('#savePinForm').formValidation('enableFieldValidators', 'pin_code', true);
      var formValid = $('#savePinForm').formValidation('validate');
    formValid = formValid.data('formValidation').$invalidFields.length;
    if(formValid != 0){
      return false;
    }
    else if($('#pin_code').val() == ""){
        return false;
    }
    else{
     var temp = $('#temp_data').val();
     var pincode = $('#pin_code').val();
     var pincodes = new Array();
      var ex_pin = $('#pincode_locations').val();
      if(ex_pin != ''){
      var pins = JSON.parse(ex_pin);
     }
     else{
      pins = [];
     }
     console.log(temp.indexOf(pincode));
     if(temp.indexOf(pincode) != -1){
       alert('Pincode '+pincode+' already added. Please enter a new Pincode')
     }
     else{
      var save_id=$('#save_id').val();
      $.get('/warehouse/checkhubpins/' + pincode+'_'+save_id, function (res) {
                    
        if(res ==1)
        { 
         $.get('/warehouse/getPinLocations/' + pincode, function (res_pin) {
            console.log(res_pin);
            var response = JSON.parse(res_pin);
         
            if(temp == ''){
               temp = response.pincode;
             }
             else{
                temp = temp +'##' + response.pincode;
             }
           $('#temp_data').attr('value',temp);
            del_action = '<span style="padding-left:20px;" ><a href="javascript:void(0);" class="check-toggler" onclick="deleteRow('+response.pincode+')"><i class="fa fa-trash-o circle"></i></a></span><input type="hidden" id="pincodes['+pincode+']" name="pincodes[]" value="'+response.pincode+'">';
            //$("#warehouse_table").append();
              var pin_data = { 'pincode' : response.pincode , 'state' : response.state,'city' : response.city,'actions' : del_action};
              pins.push(pin_data);
             $('#pincode_locations').val(JSON.stringify(pins));
                     console.log('else');
                     console.log('pins: ');console.log(pins);
                     grid();
          });
        }
        else
        {
            alert('Pincode "' + pincode + '" already associated with Hub "'+res+'". Please enter a new Pincode');
        }
     });
     }
    $('#pin_code').attr('value',null);
    $(".glyphicon").hide();
    }
   });
   
       $('#import_pin_button').click(function (){
        
      var form = document.forms.namedItem("importExcel"); 
      var formdata = new FormData(form);
    //console.log(form);
    
    $.ajax({
        url: '/warehouse/importPinSample',
        data: formdata,
        type: $(form).attr('method'),
        processData :false,
        contentType:false,
        success: function (result)
        {
         var response = JSON.parse(result);
        var message = response.message;
        console.log(response);
        alertmessage =  '';
         $.each(message, function(id,val){
            alertmessage = alertmessage + val.message +"\n"
         });
         alert(alertmessage);
        var ex_pin = $('#pincode_locations').val();
          if(ex_pin != ''){
          var pins = JSON.parse(ex_pin);
         }
         else{
          pins = [];
         }
         var pincode_cities = response.pincode_cities;
         $.each(pincode_cities, function(id,val){
          console.log(val);
          del_action = '<span style="padding-left:20px;" ><a href="javascript:void(0);" class="check-toggler" onclick="deletePin('+val.wh_serviceables_id+')"><i class="fa fa-trash-o circle"></i></a></span>';
          var pin_data = {'pincode' : val.pincode , 'state' : val.state,'city' : val.city, 'actions' : del_action};
          pins.push(pin_data);
          //pincode_locations.push(pin_data);
          $('#pincode_locations').val(JSON.stringify(pins));
          $('.close').trigger('click');
          });
          grid();
         //window.location = "/warehouse";
        }
       });
    });

</script> 
<script type="text/javascript">
  $('#tab4').click(function (){
     var wh_id = $('#le_wh_id3').val();
     console.log(wh_id);
     if(wh_id == ''){
      return false;
     }
     else{
     var legal_entity_id = $('#legal_entity_id').val();
     console.log('Legal Id:'); console.log(legal_entity_id);
     $('#pincode_value').empty();
     $('#pincode_value1').empty();
      $.get('/warehouse/getSavedPincodes/' + wh_id + '/' + legal_entity_id , function (res_pin) {
          $.each(res_pin, function (k,v) {
                $('#pincode_value').append($("<option>").attr('value', v.pincode).text(v.pincode));
          });
           $.each(res_pin, function (k,v) {
                $('#pincode_value1').append($("<option>").attr('value', v.pincode).text(v.pincode));
          });
      });
      $.get('/warehouse/getPJPs/'+wh_id+ '/' + legal_entity_id, function (res_pin) {
            var pjps = res_pin;
            pjpgrid(pjps);
          });
    }
  });
  $('#pincode_value').change(function (){
    var pincode =  $(this).val();
    console.log(pincode);
    $('#pin_area').empty();
    $('#pin_area').select2({placeholder: "Please Select..."});
    var beat_id = $('#pjp_pincode_area_id').val();
    $.get('/warehouse/getPincodeAreas/' + pincode+'/'+beat_id, function (res_pin) {
//    $.get('/warehouse/getPincodeAreas/' + pincode , function (res_pin) {
          $.each(res_pin, function (k,v) {
                $('#pin_area').append($("<option>").attr('value', v.city_id).text(v.area));
          });
      });

  });
</script> 
<script type="text/javascript">

  function editPJP(id)
{
  console.log(id);
     $.get('/warehouse/editPJP/'+id,function(response){ 
            $("#basicvalCode4").html('Edit Beat');
            
            $("#lookupsDiv").html(response);
            
            $("#editPJP").click();
        });
}
  function addPJPArea(id)
{
  console.log(id);
     var le_wh_id = $('#le_wh_id').val();
     $.get('/warehouse/addPJPArea/'+id+'/'+le_wh_id,function(response){ 
            $("#basicvalCode4").html('Add PJP Area');
            
            $("#lookupsDiv").html(response);
            
            $("#addPJPArea").click();
        });
}

function addSpokeBeat(){
        var beat=$('#spoke').val();
        var pjp_name=$('#pjp_name').val();
        var le_wh_id=$('#pjp_le_wh_id').val();
               

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
</script> 
<script type="text/javascript">
    $.ajaxSetup({
        headers:
        {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });


$('#wh_state').on('change', function() {
        var state_id=$(this).val();
        var token  = $("#csrf-token").val();
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getcitiesbystateid',
        data:{
            state_id:state_id,
        },
        success: function (respData)
        { 
        $("#wh_city").html(respData);  
        }
    });
});


   $('#wh_city').change(function (){

        var dcfcid='';
        var ctyid=$('#wh_city').val();
        var stateid=$('#wh_state').val();
        var token  = $("#csrf-token").val();
        var dcs ='';

      if(ctyid!='' && ctyid!=null){  
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getdcfccode',
        data:{
            dcfcid:dcfcid,
            ctyid:ctyid,
            stateid:stateid,
            dcs:dcs,
        },
        success: function (respData)
        { 
        respData=JSON.parse(respData); 
        if(respData.status==200){
            $("#wh_code").val(respData.code); 
        }else{
            console.log(respData.code+'console.log(respData.code)console.log(respData.code)');
            $("#wh_code").val(respData.code);  
            } 
        }
    });
 }
    });
</script> 
@stop
@extends('layouts.footer')