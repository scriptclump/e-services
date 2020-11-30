<div class="tab-pane" id="emp_personal_details"> 
    <div id="emp_personal_info_show1" style="margin-top:11px;" class="form-horizontal">
        <div class="form-body">
            <h4 ><strong>Current Address </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_cu_address">{{(isset($empPersonalInfo['cu_address'])) ? $empPersonalInfo['cu_address'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_city">{{(isset($empPersonalInfo['cu_city'])) ? $empPersonalInfo['cu_city'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_state">{{(isset($empPersonalInfo['cu_state'])) ? $empPersonalInfo['cu_state'] : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_country">{{(isset($empPersonalInfo['cu_country'])) ? $empPersonalInfo['cu_country'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_zip_code">{{(isset($empPersonalInfo['cu_zip_code'])) ? $empPersonalInfo['cu_zip_code'] : '' }}</p>
                        </div>
                    </div>
                   
                </div>
            </div>

        <h4 ><strong>Permanent Address </strong></h4>
             <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_pe_address">{{(isset($empPersonalInfo['pe_address'])) ? $empPersonalInfo['pe_address'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_city">{{(isset($empPersonalInfo['pe_city'])) ? $empPersonalInfo['pe_city'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_state">{{(isset($empPersonalInfo['pe_state'])) ? $empPersonalInfo['pe_state'] : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_country">{{(isset($empPersonalInfo['pe_country'])) ? $empPersonalInfo['pe_country'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_zip_code">{{(isset($empPersonalInfo['pe_zip_code'])) ? $empPersonalInfo['pe_zip_code'] : '' }}</p>
                        </div>
                    </div>
                   
                </div>
            </div>
            <h4 ><strong>Reference Address1 </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Relationship </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_one_relation">{{(isset($empPersonalInfo['ref_one_relation'])) ? $empPersonalInfo['ref_one_relation'] : '' }}</p>
                             
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Mobile Number </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_one_contact_no">{{(isset($empPersonalInfo['ref_one_contact_no'])) ? $empPersonalInfo['ref_one_contact_no'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_ref_one_address">{{(isset($empPersonalInfo['ref_one_address'])) ? $empPersonalInfo['ref_one_address'] : '' }}</p>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_one_city">{{(isset($empPersonalInfo['ref_one_city'])) ? $empPersonalInfo['ref_one_city'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_one_state">{{(isset($empPersonalInfo['ref_one_state'])) ? $empPersonalInfo['ref_one_state'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_one_country">{{(isset($empPersonalInfo['ref_one_country'])) ? $empPersonalInfo['ref_one_country'] : '' }}</p>
                        </div>
                    </div>                
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_one_pin_code">{{(isset($empPersonalInfo['ref_one_pin_code'])) ? $empPersonalInfo['ref_one_pin_code'] : '' }}</p>
                        </div>
                    </div> 
                </div>
            </div> 
             <h4 ><strong>Reference Address2 </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Relationship </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_two_relation">{{(isset($empPersonalInfo['ref_two_relation'])) ? $empPersonalInfo['ref_two_relation'] : '' }}</p>
                             
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Mobile Number </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_two_contact_no">{{(isset($empPersonalInfo['ref_two_contact_no'])) ? $empPersonalInfo['ref_two_contact_no'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_ref_two_address">{{(isset($empPersonalInfo['ref_two_address'])) ? $empPersonalInfo['ref_two_address'] : '' }}</p>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_two_city">{{(isset($empPersonalInfo['ref_two_city'])) ? $empPersonalInfo['ref_two_city'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_two_state">{{(isset($empPersonalInfo['ref_two_state'])) ? $empPersonalInfo['ref_two_state'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_two_country">{{(isset($empPersonalInfo['ref_two_country'])) ? $empPersonalInfo['ref_two_country'] : '' }}</p>
                        </div>
                    </div>                
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_ref_two_pin_code">{{(isset($empPersonalInfo['ref_two_pin_code'])) ? $empPersonalInfo['ref_two_pin_code'] : '' }}</p>
                        </div>
                    </div> 
                </div>
            </div>    
        </div>       
    </div>
    <div id="edit_personal_info" style="display: none;" >
        <form action="#" class="submit_form form-horizontal" id="emp_personal_info" method="get">
        <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
          <input type="hidden" id="emp_personal_id" name="emp_personal_id" value="{{(isset($userData['emp_id'])) ? $userData['emp_id'] : '' }}">
        <div class="form-body">
             <h4 ><strong>Current Address </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="cu_address" id="cu_address" value="{{(isset($empPersonalInfo['cu_address'])) ? $empPersonalInfo['cu_address'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="cu_city" id="cu_city" value="{{(isset($empPersonalInfo['cu_city'])) ? $empPersonalInfo['cu_city'] : '' }}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="cu_state" name="cu_state">
                                <option value="">Select State</option>    
                                @foreach($states as $stateVal )
                                    @if($stateVal['id'] == $empPersonalInfo['cu_state_id'])
                                        <option value="{{$stateVal['id']}}" selected >{{$stateVal['state_name']}}</option>
                                    @else
                                        <option value="{{$stateVal['id']}}" >{{$stateVal['state_name']}}</option>
                                    @endif                                
                                @endforeach
                            </select>                           
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="cu_country" name="cu_country">
                                <option value="">Select Country</option>
                                @if(isset($countries))
                                    @foreach($countries as $country_value)
                                        @if($empPersonalInfo['cu_country_id'] == $country_value['id'])
                                            <option value="{{$country_value['id']}}" selected>{{$country_value['country_name']}}</option>
                                        @else
                                            <option value="{{$country_value['id']}}">{{$country_value['country_name']}}</option>
                                        @endif       
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="cu_zip_code" id="cu_zip_code" value="{{$empPersonalInfo['cu_zip_code']}}"/>
                        </div>
                    </div>
                   
                </div>
            </div>

            <h4 ><strong>Permanent Address </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="pe_address" id="pe_address" value="{{(isset($empPersonalInfo['pe_address'])) ? $empPersonalInfo['pe_address'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="pe_city" id="pe_city" value="{{(isset($empPersonalInfo['pe_city'])) ? $empPersonalInfo['pe_city'] : '' }}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="pe_state" name="pe_state">
                                <option value="">Select State</option>    
                                @foreach($states as $stateVal )
                                    @if($stateVal['id'] == $empPersonalInfo['pe_state_id'])
                                        <option value="{{$stateVal['id']}}" selected >{{$stateVal['state_name']}}</option>
                                    @else
                                        <option value="{{$stateVal['id']}}" >{{$stateVal['state_name']}}</option>
                                    @endif                                
                                @endforeach
                            </select>                           
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="pe_country" name="pe_country">
                                <option value="">Select Country</option>
                                @if(isset($countries))
                                    @foreach($countries as $country_value)
                                        @if($empPersonalInfo['pe_country_id'] == $country_value['id'])
                                            <option value="{{$country_value['id']}}" selected>{{$country_value['country_name']}}</option>
                                        @else
                                            <option value="{{$country_value['id']}}">{{$country_value['country_name']}}</option>
                                        @endif       
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="pe_zip_code" id="pe_zip_code" value="{{$empPersonalInfo['pe_zip_code']}}"/>
                        </div>
                    </div>
                   
                </div>
            </div>
            <h4 ><strong>Reference Address1 </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Relationship <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_relation" id="ref_one_relation" value="{{$empPersonalInfo['ref_one_relation']}}"/>
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5" style="
    padding-left: 10px;""> <strong>Mobile Number <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_contact_no" id="ref_one_contact_no" value="{{$empPersonalInfo['ref_one_contact_no']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_address" id="ref_one_address" value="{{$empPersonalInfo['ref_one_address']}}"/>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_city" id="ref_one_city" value="{{$empPersonalInfo['ref_one_city']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="ref_one_state" name="ref_one_state">
                                <option value="">Select State</option>    
                                @foreach($states as $stateVal )
                                    @if($stateVal['id'] == $empPersonalInfo['ref_one_state_id'])
                                        <option value="{{$stateVal['id']}}" selected >{{$stateVal['state_name']}}</option>
                                    @else
                                        <option value="{{$stateVal['id']}}" >{{$stateVal['state_name']}}</option>
                                    @endif                                
                                @endforeach
                            </select>       
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                             <select class="form-control select2me" id="ref_one_country" name="ref_one_country">
                                <option value="">Select Country</option>
                                @if(isset($countries))
                                    @foreach($countries as $country_value)
                                        @if($empPersonalInfo['ref_one_country_id'] == $country_value['id'])
                                            <option value="{{$country_value['id']}}" selected>{{$country_value['country_name']}}</option>
                                        @else
                                            <option value="{{$country_value['id']}}">{{$country_value['country_name']}}</option>
                                        @endif       
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>                
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_pin_code" id="ref_one_pin_code"  value="{{$empPersonalInfo['ref_one_pin_code']}}"/>
                        </div>
                    </div> 
                </div>
            </div> 

            <h4 ><strong>Reference Address2 </strong></h4>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Relationship <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_relation" id="ref_two_relation" value="{{$empPersonalInfo['ref_two_relation']}}"/>
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5" style="
    padding-left: 10px;"> <strong>Mobile Number <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_contact_no" id="ref_two_contact_no" value="{{$empPersonalInfo['ref_two_contact_no']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_address" id="ref_two_address" value="{{$empPersonalInfo['ref_two_address']}}"/>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_city" id="ref_two_city" value="{{$empPersonalInfo['ref_two_city']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="ref_two_state" name="ref_two_state">
                                <option value="">Select State</option>    
                                @foreach($states as $stateVal )
                                    @if($stateVal['id'] == $empPersonalInfo['ref_two_state_id'])
                                        <option value="{{$stateVal['id']}}" selected >{{$stateVal['state_name']}}</option>
                                    @else
                                        <option value="{{$stateVal['id']}}" >{{$stateVal['state_name']}}</option>
                                    @endif                                
                                @endforeach
                            </select>       
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                             <select class="form-control select2me" id="ref_two_country" name="ref_two_country">
                                <option value="">Select Country</option>
                                @if(isset($countries))
                                    @foreach($countries as $country_value)
                                        @if($empPersonalInfo['ref_two_country_id'] == $country_value['id'])
                                            <option value="{{$country_value['id']}}" selected>{{$country_value['country_name']}}</option>
                                        @else
                                            <option value="{{$country_value['id']}}">{{$country_value['country_name']}}</option>
                                        @endif       
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>                
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>Postal Code<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_pin_code" id="ref_two_pin_code"  value="{{$empPersonalInfo['ref_two_pin_code']}}"/>
                        </div>
                    </div> 
                </div>
            </div>   
        </div>
        <div class="row">
            <hr />
            <div class="col-md-12 text-center"> 
                <input type="submit" class="btn green-meadow saveusers" value="Update" id="saveusers"/> 
                <input type="button" class="btn green-meadow" value="Cancel" id="cancel2" /> 
            </div>
        </div>
        </form>
    </div>
</div>