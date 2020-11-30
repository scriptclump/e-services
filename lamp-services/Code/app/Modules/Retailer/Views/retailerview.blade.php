
<div class="row">
    <div class="col-md-6">
        <h3 class="borderbot" style="border-bottom:2px solid #f1f3f7; line-height:50px;	margin-top:-10px;">{{trans('retailers.tab.retailer_info')}}</h3>    
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht ">{{trans('retailers.grid.shop_name')}}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <input type="text" class="form-control" id="retailer_name" value="@if(isset($retailers->business_legal_name)){{$retailers->business_legal_name}}@endif" name="retailer_name">
                <input type="hidden" value="{{$retailers->legal_entity_id}}" id="legal_entity_id" name="legalEntityId" />
                <input type="hidden" value="{{$retailers->mobile_no}}" id="cu_mobile_no" name="cu_mobile_no" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.retailer_type')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <select class="form-control select2me" id="customer_type" name="legal_entity_type_id" @if($dcfc_access) disabled = "disabled" @endif>
                    <option value="">{{trans('retailers.form_fields.business_type')}}</option>    
                    @foreach($businessTypes as $businessType)
                    @if($businessType->value == $retailers->legal_entity_type_id)
                    <option value="{{$businessType->value}}" selected="">
                        {{$businessType->master_lookup_name}}</option>
                    @else
                    <option value="{{$businessType->value}}">
                        {{$businessType->master_lookup_name}}</option>     
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.segment_type')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <select class="form-control select2me" id="business_type" name="business_type_id" @if($dcfc_access) disabled = "disabled" @endif>
                    <option value="">{{trans('retailers.form_fields.select_segment_type')}}</option>    
                    @foreach($segmentTypes as $segmentType)
                    @if($segmentType->value == $retailers->business_type_id)
                    <option value="{{$segmentType->value}}" selected="">
                        {{$segmentType->master_lookup_name}}</option>
                    @else
                    <option value="{{$segmentType->value}}">
                        {{$segmentType->master_lookup_name}}</option>                        
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.volume_class')}}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <select class="form-control select2me" id="volume" name="volume">
                    <option value="">--Please Select--</option>
                    @if(isset($volumes))
                    @foreach($volumes as $volume)
                    @if($volume->master_lookup_name == $retailers->volume_class)
                    <option value="{{ $volume->value }}" selected="selected">{{ $volume->master_lookup_name }}</option>
                    @else
                    <option value="{{ $volume->value }}">{{ $volume->master_lookup_name }}</option>
                    @endif
                    @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.shutters')}}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="number" name="shutters" min="0" max="9" value="@if(isset($retailers->No_of_shutters)){{$retailers->No_of_shutters}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.business_time')}}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-icon right">
                            <i class="fa fa-clock-o" style="line-height: 10px"></i>
                            <input type="text" class="form-control" name="business_start_time" id="business_start_time" value="@if(isset($retailers->business_start_time)){{$retailers->business_start_time}}@endif" />
                        </div>
                    </div>
                    <div class="col-md-1" style="line-height:30px">{{trans('retailers.form_fields.to')}}</div>
                    <div class="col-md-6">
                        <div class="input-icon right" style="width: 100%"> 
                            <i class="fa fa-clock-o" style="line-height: 10px"></i>
                            <input type="text" class="form-control" name="business_end_time" id="business_end_time"  value="@if(isset($retailers->business_end_time)){{$retailers->business_end_time}}@endif" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.other_suppliers')}}</label>
            <div class="col-md-8 rowbotmarg">
                <?php
                $masterManf = $retailers->master_manf;
                if ($masterManf != '')
                {
                    $masterManf = explode(',', $masterManf);
                } else
                {
                    $masterManf = [];
                }
                ?>
                <select name="master_manf[]" class="form-control select2me" multiple="true">
                    <option value="">{{trans('retailers.form_validate.default_select')}}</option>
                    @foreach($masterManufacturers as $manufacturers)
                    @if(in_array($manufacturers->value, $masterManf))
                    <option value="{{ $manufacturers->value }}" selected="selected">{{ $manufacturers->master_lookup_name }}</option>
                    @else
                    <option value="{{ $manufacturers->value }}">{{ $manufacturers->master_lookup_name }}</option>
                    @endif
                    @endforeach
                </select>      </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.smart_phone')}}</label>
            <div class="col-md-8 rowbotmarg">
                <div class="row" style="line-height:30px;">
                    <div class="col-md-5">
                        <select name="smartphone" class="form-control">
                            <option value="">{{trans('retailers.form_validate.default_select')}}</option>
                            <option value="1" <?php if ($retailers->smartphone == 'YES'){ echo 'selected="selected"'; } ?>>{{trans('retailers.form_fields.yes')}}</option>
                            <option value="0" <?php if ($retailers->smartphone == 'NO'){ echo 'selected="selected"'; } ?>>{{trans('retailers.form_fields.no')}}</option>
                        </select></div>
                    <div class="col-md-5"><label class="control-label" >{{trans('retailers.form_fields.internet_availabilty')}} <input type="checkbox" name="network" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->network) && $retailers->network == 'YES') echo 'checked="true"'; ?> /></label>
                    </div>
                    <div class="col-md-1">
                    </div>

                </div>
            </div>
        </div>
        <?php // echo "<pre>";print_R($retailers);die; ?>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.latitude')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="number" step="0.001" min="-90" max="90" name="latitude" id="wh_lat" value="@if(isset($retailers->latitude)){{$retailers->latitude}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.longitude')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="number" step="0.001" min="-180" max="180" name="longitude" id="wh_log" value="@if(isset($retailers->longitude)){{$retailers->longitude}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Total Orders</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->orders)){{$retailers->orders}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.created_by')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->created_by)){{$retailers->created_by}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.updated_by')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->updated_by)){{$retailers->updated_by}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Created At</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->created_at)){{$retailers->created_at}}@endif"  />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Updated At</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->updated_at)){{$retailers->updated_at}}@endif"  />
            </div>
        </div>
		<div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.gstin')}}</label>
            <div class="col-md-8 rowbotmarg">
				<input type="hidden" id="gst_state_codes" value="@if(isset($gst_state_codes)){{$gst_state_codes}}@endif" />
                <input class="form-control" name="gstin" type="text" value="@if(isset($retailers->gstin)){{$retailers->gstin}}@endif"  maxlength="15" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">{{trans('retailers.form_fields.fssai')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input type="hidden" id="fssai" value="@if(isset($retailers->fssai)){{$retailers->fssai}}@endif" />
                <input class="form-control" name="fssai" type="text" value="@if(isset($retailers->fssai)){{$retailers->fssai}}@endif"  maxlength="14" />
            </div>
        </div>       
    </div>

    <div class="col-md-6">
        <h3 class="borderbot" style="border-bottom:2px solid #f1f3f7; line-height:50px;	margin-top:-10px;">{{trans('retailers.form_fields.registered_address')}}</h3>

        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.address1')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($retailers->address1)){{$retailers->address1}}@endif"  id="org_address1" name="org_address1">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.address2')}}</label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($retailers->address2)){{$retailers->address2}}@endif"  id="org_address2" name="org_address2">      </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.pincode')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" maxlength="6" value="@if(isset($retailers->pincode)){{$retailers->pincode}}@endif"  id="pincode" name="org_pincode" />
                <input type="hidden" value="@if(isset($retailers->pincode)){{$retailers->pincode}}@endif" id="previous_area_pincode" />
                <input type="hidden" value="@if(isset($retailers->pincode)){{$retailers->pincode}}@endif" id="previous_beat_pincode" />
            </div>
        </div>
        <!--div class="form-group">
            <label class="col-md-3 control-label rowlinht">Hubs<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="hubs" name="hub_id">
                    <option value="">Select Hub...</option>    
                    @foreach($hubsList as $hub)
                    @if(property_exists($hub, 'le_wh_id') && $hub->le_wh_id == $retailers->hub_id)
                    <option value="{{$hub->le_wh_id}}" selected="">{{$hub->lp_wh_name}}</option>
                    @else
                    <option value="{{$hub->le_wh_id}}" >{{$hub->lp_wh_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Spokes<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="spoke_id" name="spoke_id">
                    <option value="">Select Spoke...</option>
                    @foreach($spokesList as $spoke)
                    @if(property_exists($spoke, 'spoke_id') && ($spoke->spoke_id == $retailers->spoke_id))
                    <option value="{{$spoke->spoke_id}}" selected="">{{$spoke->spoke_name}}</option>
                    @else
                    <option value="{{$spoke->spoke_id}}" >{{$spoke->spoke_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div-->
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.beat')}} <span class="required" aria-required="true">*</span></label>
            
                <input type="hidden" name="ret_id" id="ret_id" value="@if(isset($retailers->retid)){{$retailers->retid}}@endif">
            <div class="col-md-9 rowbotmarg">
				<input type="hidden" name="hub_id" value="@if(isset($retailers->hub_id)){{ $retailers->hub_id }}@endif" />
				<input type="hidden" name="spoke_id" value="@if(isset($retailers->spoke_id)){{ $retailers->spoke_id }}@endif" />
				<input type="hidden" id="hubsSpokesCollection" value="@if(isset($hubsSpokesCollection)){{$hubsSpokesCollection}}@endif" />
                <select class="form-control select2me" id="beat" name="beat" >
                    <option value="">{{trans('retailers.form_validate.default_beat')}}</option>    
                    @foreach($beats as $beat)
                    @if(property_exists($beat, 'pjp_pincode_area_id') && $beat->pjp_pincode_area_id == $retailers->beat_id)
                    <option value="{{$beat->pjp_pincode_area_id}}" selected="">{{$beat->pjp_name}}</option>
                    @else
                    <option value="{{$beat->pjp_pincode_area_id}}" >{{$beat->pjp_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>        
		<div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.area_name')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="area_id" name="area_id">
                    <option value="">{{trans('retailers.form_validate.select_area')}}</option>    
                    @foreach($areas as $area)
                    @if(property_exists($area, 'city_id') && $area->city_id == $retailers->area_id)
                    <option value="{{$area->city_id}}" selected="">{{$area->officename}}</option>
                    @else
                    <option value="{{$area->city_id}}" >{{$area->officename}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div> 
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.locality')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($retailers->locality)){{$retailers->locality}}@endif"  id="locality" name="locality"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.landmark')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($retailers->landmark)){{$retailers->landmark}}@endif"  id="landmark" name="landmark"></div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.state')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="org_state" name="org_state">
                    <option value="">{{trans('retailers.form_validate.default_state')}}</option>    

                    @foreach($states_data as $stateVal )
                    @if($stateVal->zone_id == $retailers->state_id)
                    <option value="{{$stateVal->zone_id}}" selected="selected">{{$stateVal->name}}</option>
                    @else
                    <option value="{{$stateVal->zone_id}}" >{{$stateVal->name}}</option>
                    @endif
                    @endforeach
                </select>      </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.city')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control"  value="@if(isset($retailers->city)){{$retailers->city}}@endif"  id="org_city" name="org_city">      </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.grid.country')}} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="org_country" name="org_country">
                    <option value="">{{trans('retailers.form_validate.default_country')}}</option>
                    @if(isset($countries))
                    @foreach($countries as $country_value)
                    @if($country_value['country_id']==$retailers->country)
                    <option value="{{$country_value['country_id']}}" selected>{{$country_value['name']}}</option>
                    @else
                    <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                    @endif
                    @endforeach
                    @endif
                </select>      
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">{{trans('retailers.form_fields.available_ecash')}}(<i class="fa fa-inr" aria-hidden="true"></i>)</label>
            <div class="col-md-9 rowbotmarg">
                <div class="input-group">
                    @if(isset($ecash))
                        <input type="text" class="form-control" id="ecash_edit" name="ecash_available" value="{{$ecash}}" @if(!$ecashavailEditAccess) disabled="disabled" @endif/>
                        <div class="input-group-addon">
                            @if($ecashavailEditAccess)
                                <button  type="button" class="action" id="edit_ecash_available"> <i class="fa fa-pencil" aria-hidden="true"></i></button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Fridge<input type="checkbox" name="is_fridge" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->is_fridge) && $retailers->is_fridge == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>

        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Visi cooler<input type="checkbox" name="is_visicooler" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->is_visicooler) && $retailers->is_visicooler == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>
        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Deep freezer<input type="checkbox" name="is_deepfreezer" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->is_deepfreezer) && $retailers->is_deepfreezer == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>

        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Milk<input type="checkbox" name="is_milk" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->is_milk) && $retailers->is_milk == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>

        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Ice cream<input type="checkbox" name="is_icecream" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->is_icecream) && $retailers->is_icecream == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>

        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Vegetables<input type="checkbox" name="is_vegetables" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->is_vegetables) && $retailers->is_vegetables == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>
        <div class="form-group">
             <div class="col-md-4"><label class="control-label" >Notification<input type="checkbox" name="sms_notification" class="checkbox" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->sms_notification) && $retailers->sms_notification == 1) echo 'checked="true"'; ?> /></label>
                    </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Parent Legal Entity</label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" name="parent_le" id="parent_le" @if($dcfc_access) disabled = "disabled" @endif >
                    <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                    @foreach($businessunit as $bu)
                    <option value="{{$bu->legal_entity_id}}" {{($retailers->parent_le_id == $bu->legal_entity_id) ? 'selected = "true"' : ''}}>{{$bu->display_name}}</option>
                    @endforeach
                </select>
            </div>
        </div> 
        <div class="form-group">
            <label class="col-md-3 control-label">{{trans('retailers.form_fields.available_creditlimit')}}</label>
            <div class="col-md-4 rowbotmarg">
            @if(isset($creditlimit))
                <input type="number" min="0" class="form-control" id="creditlimit" name="creditlimit" value="{{$creditlimit}}" @if(!$creditlimitEditAccess) disabled="disabled" @endif />
            @endif
            </div>
            <div class="col-md-4 rowbotmarg">
            @if(isset($creditlimit) && ($creditlimitEditAccess))
            <button type="button" class="btn green-meadow" id="update_loc">Update LOC</button>
            @endif
            </div>
        </div>
        <script type="text/javascript" src={{$mapurl}}></script>
                                    <script type="text/javascript">
window.onload = function () {

var latt = Number($("#wh_lat").val());
var logg = Number($("#wh_log").val());
console.log(latt);
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
        <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    <div id="dvMap"></div>
                    <div class="input-icon">
                        <i class="fa fa-bars" style="position: absolute;top: -250px;left: 2px;"></i>
                        <input type="text" class="form-control" name="keyword" id="keyword" style="position: absolute;top:-250px; left:4px;z-index: 2; width:260px;" />
                    </div>
                </div>
            </div>
        </div>
        

    </div>
</div>