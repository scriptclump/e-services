<div class="row">
    <div class="col-md-6 ">
        <h3 class="borderbot" style="border-bottom:2px solid #f1f3f7; line-height:50px;	margin-top:-10px;">
        {{ trans('retailers.tab.retailer_info') }}</h3>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht">Shop Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                <input type="text" class="form-control" id="retailer_name" value="@if(isset($retailers->business_legal_name)){{$retailers->business_legal_name}}@endif" readonly />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht">{{ trans('retailers.form_fields.retailer_type') }} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                <select class="form-control select2me" id="business_type" name="legal_entity_type_id" disabled="true">
                    <option value="">{{ trans('retailers.form_fields.business_type') }}</option>

                    @foreach($businessTypes as $businessType)
                    @if($businessType->value == $retailers->legal_entity_type_id)

                    <option value="{{$businessType->value}}" selected=""> {{$businessType->master_lookup_name}}</option>
                    @else
                    <option value="{{$businessType->value}}"> {{$businessType->master_lookup_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht"> {{ trans('retailers.form_fields.segment_type') }} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                <select class="form-control select2me" id="business_type" name="business_type_id" disabled="true">
                    <option value="">{{ trans('retailers.form_fields.segment_type_select') }}</option>

                    @foreach($segmentTypes as $segmentType)
                    @if($segmentType->value == $retailers->business_type_id)

                    <option value="{{$segmentType->value}}" selected=""> {{$segmentType->master_lookup_name}}</option>

                    @else

                    <option value="{{$segmentType->value}}"> {{$segmentType->master_lookup_name}}</option>

                    @endif
                    @endforeach

                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht">{{ trans('retailers.form_fields.volume_class') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                <select class="form-control select2me" id="volume" name="volume" disabled="true">

                    @if(isset($volumes))
                    @foreach($volumes as $volume)
                    @if($volume->value == $retailers->volume_class)

                    <option value="{{ $volume->value }}" selected="selected">{{ $volume->master_lookup_name }}</option>

                    @else

                    <option value="{{ $volume->value }}">{{ $volume->master_lookup_name }}</option>

                    @endif
                    @endforeach
                    @endif

                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht">{{ trans('retailers.form_fields.shutters') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                <input class="form-control" type="number" name="shutters" value="@if(isset($retailers->No_of_shutters)){{$retailers->No_of_shutters}}@endif" readonly />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht">{{ trans('retailers.form_fields.business_time') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-icon right"> <i class="fa fa-clock-o" style="line-height: 10px"></i>
                            <input type="text" class="form-control" name="business_start_time" id="business_start_time" value="@if(isset($retailers->business_start_time)){{$retailers->business_start_time}}@endif" readonly />
                        </div>
                    </div>
                    <div class="col-md-1" style="line-height:30px">{{ trans('retailers.form_fields.to') }}</div>
                    <div class="col-md-6">
                        <div class="input-icon right" style="width: 100%"> <i class="fa fa-clock-o" style="line-height: 10px"></i>
                            <input type="text" class="form-control" name="business_end_time" id="business_end_time"  value="@if(isset($retailers->business_end_time)){{$retailers->business_end_time}}@endif" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht">{{ trans('retailers.form_fields.other_suppliers') }}</label>
            <div class="col-md-8">
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
                <select class="form-control select2me" id="master_manf" multiple="true" disabled="true">
                    <option value="">{{ trans('retailers.form_validate.default_select') }}</option>

                    @foreach($masterManufacturers as $manufacturers)
                    @if(in_array($manufacturers->value, $masterManf))

                    <option value="{{ $manufacturers->value }}" selected="selected">{{ $manufacturers->master_lookup_name }}</option>

                    @else

                    <option value="{{ $manufacturers->value }}">{{ $manufacturers->master_lookup_name }}</option>

                    @endif
                    @endforeach

                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht rowlinht">{{ trans('retailers.form_fields.smart_phone') }}</label>
            <div class="col-md-8 rowbotmarg">
                <div class="row" style="line-height:30px;">
                    <div class="col-md-5">
                        <select name="preference_value" class="form-control" disabled="true">
                            <option value="">{{ trans('retailers.form_validate.default_select') }}</option>
                            <option value="1" <?php if ($retailers->smartphone == 1){ echo 'selected="selected"'; } ?>>Yes</option>
                            <option value="0" <?php if ($retailers->smartphone == 0){ echo 'selected="selected"'; } ?>>No</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="control-label" >{{ trans('retailers.form_fields.internet_availabilty') }}
                            <input type="checkbox" name="network" class="checkbox" disabled="true" style="margin-left:10px;margin-top:10px" <?php if (isset($retailers->network) && $retailers->network == 1) echo 'checked="true"'; ?> />
                        </label>
                    </div>
                    <div class="col-md-1"> </div>
                </div>
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht rowlinht">{{trans('retailers.form_fields.created_by')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->created_by)){{$retailers->created_by}}@endif"  />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht rowlinht">{{trans('retailers.form_fields.updated_by')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($retailers->updated_by)){{$retailers->updated_by}}@endif"  />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht rowlinht">{{trans('retailers.form_fields.latitude')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="number" readonly="true" step="0.001" min="-90" max="90" name="latitude" value="@if(isset($retailers->latitude)){{$retailers->latitude}}@endif"  />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht rowlinht">{{trans('retailers.form_fields.longitude')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="number" readonly="true" step="0.001" min="-180" max="180" name="longitude" value="@if(isset($retailers->longitude)){{$retailers->longitude}}@endif"  />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 control-label rowlinht rowlinht">{{trans('retailers.form_fields.gstin')}}</label>
            <div class="col-md-8 rowbotmarg">
                <input type="hidden" id="gst_state_codes" value="@if(isset($gst_state_codes)){{$gst_state_codes}}@endif" />
                <input class="form-control" name="gstin" type="text" readonly="true" value="@if(isset($retailers->gstin)){{$retailers->gstin}}@endif"  maxlength="15" />
            </div>
        </div>
        
    </div>
    <div class="col-md-6">
        <h3 class="borderbot" style="border-bottom:2px solid #f1f3f7; line-height:50px;	margin-top:-10px;">{{ trans('retailers.form_fields.registered_address') }}</h3>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht  ">{{ trans('retailers.grid.address1') }} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 ">
                <input type="text" class="form-control" value="@if(isset($retailers->address1)){{$retailers->address1}}@endif"  id="org_address1" name="org_address1"  readonly="true" />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht ">{{ trans('retailers.grid.address2') }} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 ">
                <input type="text" class="form-control" value="@if(isset($retailers->address2)){{$retailers->address2}}@endif"  id="org_address2" name="org_address2" readonly />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{ trans('retailers.grid.hubs') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="hubs" name="hub_id" disabled="true">
                    <option value="">{{ trans('retailers.form_validate.default_hub') }}</option>    
                    @foreach($hubsList as $hub)
                    @if(property_exists($hub, 'le_wh_id'))
                    <option value="{{$hub->le_wh_id}}" selected="">{{$hub->lp_wh_name}}</option>
                    @else
                    <option value="{{$hub->le_wh_id}}" >{{$hub->lp_wh_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{ trans('retailers.grid.spokes') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="spoke_id" name="spoke_id" disabled="true">
                    <option value="">{{ trans('retailers.form_validate.default_spoke') }}</option>
                    @foreach($spokesList as $spoke)
                    @if(property_exists($spoke, 'spoke_id') && $spoke->spoke_id == $retailers->spoke_id)
                    <option value="{{$spoke->spoke_id}}" selected="">{{$spoke->spoke_name}}</option>
                    @else
                    <option value="{{$spoke->spoke_id}}" >{{$spoke->spoke_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{ trans('retailers.grid.beat') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select class="form-control select2me" id="beat" name="beat" disabled="true">
                    <option value="">{{ trans('retailers.form_validate.default_beat') }}</option>    
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
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht ">{{ trans('retailers.grid.locality') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 ">
                <input type="text" class="form-control" value="@if(isset($retailers->locality)){{$retailers->locality}}@endif"  id="locality" name="locality" readonly />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht ">{{ trans('retailers.grid.landmark') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 ">
                <input type="text" class="form-control" value="@if(isset($retailers->landmark)){{$retailers->landmark}}@endif"  id="landmark" name="landmark" readonly />
            </div>
        </div>
        <div class="form-group row">
            <label class=" col-md-3 control-label rowlinht">{{ trans('retailers.grid.state') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                <select class="form-control select2me" id="org_state" name="org_state" disabled="true">
                    <option value="">{{ trans('retailers.grid.default_state') }}</option>


                    @foreach($states_data as $stateVal )
                    @if($stateVal->zone_id==$retailers->state_id)

                    <option value="{{$stateVal->zone_id}}" selected="">{{$stateVal->name}}</option>

                    @else

                    <option value="{{$stateVal->zone_id}}" >{{$stateVal->name}}</option>

                    @endif
                    @endforeach

                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{ trans('retailers.grid.pincode') }}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" maxlength="6" value="@if(isset($retailers->pincode)){{$retailers->pincode}}@endif"  id="pincode" name="org_pincode" readonly />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{ trans('retailers.grid.city') }} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" readonly value="@if(isset($retailers->city)){{$retailers->city}}@endif"  id="org_city" name="org_city">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{ trans('retailers.grid.country') }} <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                <select class="form-control select2me" id="org_country" name="org_country" disabled="true">
                    <option value="">{{ trans('retailers.form_validate.default_country') }}</option>

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
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.form_fields.available_ecash')}}(<i class="fa fa-inr" aria-hidden="true"></i>)</label>
            <div class="col-md-9 rowbotmarg">
            @if(isset($ecash))
                <input type="text" class="form-control" name="ecash_available" readonly="true" value="{{$ecash}}">
            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label rowlinht">{{trans('retailers.form_fields.available_creditlimit')}}</label>
            <div class="col-md-4 rowbotmarg">
            @if(isset($creditlimit))
                <input type="number" min="0" class="form-control" id="creditlimit" readonly="true" name="creditlimit" value="{{$creditlimit}}" @if(!$creditlimitEditAccess) disabled="disabled" @endif />
            @endif
            </div>
        </div>
</div>
</div>
