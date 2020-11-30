<div class="tab-pane" id="tab_77">
<div class="tab-pane active" id="tab_77">
<?php
$cancel = str_replace(' ','_',$vendor).'_add_cancel';
?>
    @if($vendor != 'Space')
    <form id="additionalinfo" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <h4 id='vendor_info_id'>{{$vendor}} Additional Information</h4>

        <div class="row">
           <!--  <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Vehicle Registration No. <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="reg_no" value="@if(isset($vehicle_data->reg_no)){{$vehicle_data->reg_no}}@endif" name="reg_no">
                </div>
            </div> -->
                <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Vehicle Type<span class="required" aria-required="true">*</span></label>
                            <select class="form-control select2me" id="vehicle_type" name="vehicle_type">
                                <option value="">Select Vehicle Type</option> 
                                @if(!empty($vehicle_type['value']) && isset($vehicle_data)&& $vehicle_data->vehicle_type == $vehicle_type['value'])   
                                    <option value="{{$vehicle_type['value']}}" selected="">{{$vehicle_type['name']}}</option>
                                @else
                                 <option value="{{$vehicle_type['value']}}">{{$vehicle_type['name']}}</option>
                                @endif
                            </select>
                        </div>
                </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Registration Expiry Date </label>
                    <input type="text" class="form-control" id="reg_exp_date" value="@if(isset($vehicle_data->reg_exp_date)){{$vehicle_data->reg_exp_date}}@endif" name="reg_exp_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Length <span class="required" aria-required="true">*</span></label>
                            <input type="text" class="form-control" value="@if(isset($vehicle_data->length)){{$vehicle_data->length}}@endif"  id="length" name="length">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Breadth<span class="required" aria-required="true">*</span> </label>
                            <input type="text" class="form-control" value="@if(isset($vehicle_data->breadth)){{$vehicle_data->breadth}}@endif"  id="breadth" name="breadth">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Height<span class="required" aria-required="true">*</span> </label>
                            <input type="text" class="form-control" value="@if(isset($vehicle_data->height)){{$vehicle_data->height}}@endif"  id="height" name="height">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">LBH UoM <span class="required" aria-required="true">*</span></label>
                            <select class="form-control select2me" id="veh_lbh_uom" name="veh_lbh_uom">
                                <option value="">Select</option>    
                                @foreach($veh_lbh_uom as $val )
                                @if(isset($vehicle_data->veh_lbh_uom) && $val->value== $vehicle_data->veh_lbh_uom)
                                    <option value="{{$val->value}}" selected >{{$val->name}}</option>
                                @else
                                <option value="{{$val->value}}" >{{$val->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Pollution Check Expiry Date </label>
                    <input type="text" class="form-control" id="poll_exp_date" value="@if(isset($vehicle_data->poll_exp_date)){{$vehicle_data->poll_exp_date}}@endif" name="poll_exp_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Safety Sticker Expiry Date </label>
                    <input type="text" class="form-control" id="safty_exp_date" value="@if(isset($vehicle_data->safty_exp_date)){{$vehicle_data->safty_exp_date}}@endif" name="safty_exp_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label class="control-label">Weight<span class="required" aria-required="true">*</span></label>
                            <input type="text" class="form-control" value="@if(isset($vehicle_data->veh_weight)){{$vehicle_data->veh_weight}}@endif"  id="veh_weight" name="veh_weight">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label">Weight UOM <span class="required" aria-required="true">*</span></label>
                            <select class="form-control select2me" id="veh_weight_uom" name="veh_weight_uom">
                                <option value="">Select Wt UoM</option>    
                                @foreach($veh_weight_uom as $val )
                                @if(isset($vehicle_data->veh_weight_uom) && $val->value== $vehicle_data->veh_weight_uom)
                                    <option value="{{$val->value}}" selected >{{$val->name}}</option>
                                @else
                                <option value="{{$val->value}}" >{{$val->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Vehicle Insurance No. <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="insurance_no" value="@if(isset($vehicle_data->insurance_no)){{$vehicle_data->insurance_no}}@endif" name="insurance_no">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Insurance Expiry Date</label>
                    <input type="text" class="form-control" id="insurance_exp_date" value="@if(isset($vehicle_data->insurance_exp_date)){{$vehicle_data->insurance_exp_date}}@endif" name="insurance_exp_date">
                </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Body Type<span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="body_type" value="@if(isset($vehicle_data->body_type)){{$vehicle_data->body_type}}@endif" name="body_type">
                </div>
            </div> -->
            <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Body Type<span class="required" aria-required="true">*</span></label>
                            <select class="form-control select2me" id="body_type" name="body_type">
                                <option value="">Select Body Type</option>    
                                @foreach($body_type as $val )
                                @if(isset($vehicle_data->body_type) && $val->value== $vehicle_data->body_type)
                                    <option value="{{$val->value}}" selected >{{$val->name}}</option>
                                @else
                                <option value="{{$val->value}}" >{{$val->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Fitness Expiry Date </label>
                    <input type="text" class="form-control" id="fit_exp_date" value="@if(isset($vehicle_data->fit_exp_date)){{$vehicle_data->fit_exp_date}}@endif" name="fit_exp_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Vehicle Provider </label>
                    
                    <select class="form-control select2me" id="veh_provider" name="veh_provider">
                        <option value="">Select Vehicle Provider</option>    
                        @foreach($veh_provider as $val )
                        @if(isset($vehicle_data->veh_provider) && $val->value== $vehicle_data->veh_provider)
                            <option value="{{$val->value}}" selected >{{$val->name}}</option>
                        @else
                        <option value="{{$val->value}}" >{{$val->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @if(isset($vehicle_data->hub_id) )
            <input type="hidden" name="bu_ids" id="bu_list_id" value="{{$vehicle_data->hub_id}}" />
            @endif
            			    <div class="col-md-4">
                                        <div class="form-group">
                                          <label class="control-label">Hubs<span class="required" aria-required="true">*</span></label>
                                            <select class="form-control select2me" name="hublist1" id="hublist1"></select>
                                        </div>
                                    </div>
        </div>

        <br/>
        <div class="row">
            <div class="col-md-12 text-center">			
                <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">Save</button>
                <button type="button" id="{{$cancel}}" class="btn green-meadow">Cancel</button>
            </div>
        </div>
        
    </form>
        @elseif($vendor == 'Space')
        <form id="spaceadditionalinfo" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <h4 id='vendor_info_id'>{{$vendor}} Additional Information</h4>    
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Area(sq ft) <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="area" value="@if(isset($space_data->area)){{$space_data->area}}@endif" name="area">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Space Provider <span class="required" aria-required="true">*</span></label>
                    
                    <select class="form-control select2me" id="space_provider" name="space_provider">
                        <option value="">Select Space Provider</option>    
                        @foreach($space_provider as $val )
                        @if(isset($space_data->space_provider) && $val->value== $space_data->space_provider)
                            <option value="{{$val->value}}" selected >{{$val->name}}</option>
                        @else
                        <option value="{{$val->value}}" >{{$val->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @if(isset($space_data->hub_id) )
            <input type="hidden" name="bu_ids" id="bu_list_id" value="{{$space_data->hub_id}}" />
            @endif
            			    <div class="col-md-4">
                                        <div class="form-group">
                                          <label class="control-label">Hubs<span class="required" aria-required="true">*</span></label>
                                            <select class="form-control select2me" name="hublist1" id="hublist1"></select>
                                        </div>
                                    </div>

        </div>
        <br/>
        <div class="row">
            <div class="col-md-12 text-center">			
                <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">Save</button>
                <button type="button" id="{{$cancel}}" class="btn green-meadow">Cancel</button>
            </div>
        </div>
        
    </form>        
        @endif   
</div>
</div>
