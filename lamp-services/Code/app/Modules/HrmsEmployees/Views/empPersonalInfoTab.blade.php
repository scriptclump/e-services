<div class="tab-pane" id="emp_personal_details"> 
<div class="basicInfoOverlay"></div>
    <div id="emp_personal_info_show1" style="margin-top:11px;" class="form-horizontal">
        <div class="form-body">
            <h5 ><strong>Current Address </strong></h5>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address1 </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_cu_address">{{(isset($empPersonalInfo['cu_address'])) ? $empPersonalInfo['cu_address'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address2 </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_cu_address2">{{(isset($empPersonalInfo['cu_address2'])) ? $empPersonalInfo['cu_address2'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_city">{{(isset($empPersonalInfo['cu_city'])) ? $empPersonalInfo['cu_city'] : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_state">{{(isset($empPersonalInfo['cu_state'])) ? $empPersonalInfo['cu_state'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_cu_country">{{(isset($empPersonalInfo['cu_country'])) ? $empPersonalInfo['cu_country'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            @if($empPersonalInfo['cu_zip_code'] == 0)
                            <p class="form-control-static" id="preview_cu_zip_code"></p>
                            @else
                            <p class="form-control-static" id="preview_cu_zip_code">{{(isset($empPersonalInfo['cu_zip_code'])) ? $empPersonalInfo['cu_zip_code'] : '' }}</p>
                            @endif
                        </div>
                    </div>
                    
                   
                </div>
            </div>

        <h5 ><strong>Permanent Address </strong></h5>
             <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address1 </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_pe_address">{{(isset($empPersonalInfo['pe_address'])) ? $empPersonalInfo['pe_address'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address2 </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_pe_address2">{{(isset($empPersonalInfo['pe_address2'])) ? $empPersonalInfo['pe_address2'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_city">{{(isset($empPersonalInfo['pe_city'])) ? $empPersonalInfo['pe_city'] : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_state">{{(isset($empPersonalInfo['pe_state'])) ? $empPersonalInfo['pe_state'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_pe_country">{{(isset($empPersonalInfo['pe_country'])) ? $empPersonalInfo['pe_country'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                        @if($empPersonalInfo['pe_zip_code'] ==0)
                        <p class="form-control-static" id="preview_pe_zip_code"></p>
                        @else
                            <p class="form-control-static" id="preview_pe_zip_code">{{(isset($empPersonalInfo['pe_zip_code'])) ? $empPersonalInfo['pe_zip_code'] : '' }}</p>
                         @endif
                        </div>
                    </div>
                </div>
            </div>






            <h5><strong>Emergency Info </strong></h5>
             <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Name </strong></label>
                        <div class="col-md-7">
                             <p class="form-control-static" id="preview_emergency_name">{{(isset($empPersonalInfo['emergency_name'])) ? $empPersonalInfo['emergency_name'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>Relation </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_emergency_relation">{{(isset($empPersonalInfo['emergency_relation'])) ? $empPersonalInfo['emergency_relation'] : '' }}</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>Contact1</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_emergency_contact1">{{(isset($empPersonalInfo['emergency_contact_one'])) ? $empPersonalInfo['emergency_contact_one'] : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Contact2</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_emergency_contact2">{{(isset($empPersonalInfo['emergency_contact_two'])) ? $empPersonalInfo['emergency_contact_two'] : '' }}</p>
                        </div>
                    </div>
                </div>
            </div>




            <h5 ><strong>Reference Address1 </strong></h5>
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
             <h5 ><strong>Reference Address2 </strong></h5>
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
             <h5 ><strong>Current Address </strong></h5>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address1 </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="cu_address" id="cu_address" value="{{(isset($empPersonalInfo['cu_address'])) ? $empPersonalInfo['cu_address'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address2 </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="cu_address2" id="cu_address2" value="{{(isset($empPersonalInfo['cu_address2'])) ? $empPersonalInfo['cu_address2'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="cu_city" id="cu_city" value="{{(isset($empPersonalInfo['cu_city'])) ? $empPersonalInfo['cu_city'] : '' }}"/>
                        </div>
                    </div>
                </div>
                <div class="row">

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
                            @if($empPersonalInfo['cu_zip_code'] == 0)
                            <input type="text" class="form-control" name="cu_zip_code" id="cu_zip_code" value=""/>
                            @else
                            <input type="text" class="form-control" name="cu_zip_code" id="cu_zip_code" value="{{$empPersonalInfo['cu_zip_code']}}"/>
                            @endif
                        </div>
                    </div>
                   
                </div>
            </div>

            <h5 ><strong>Permanent Address </strong></h5>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address1 </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="pe_address" id="pe_address" value="{{(isset($empPersonalInfo['pe_address'])) ? $empPersonalInfo['pe_address'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address2 </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="pe_address2" id="pe_address2" value="{{(isset($empPersonalInfo['pe_address2'])) ? $empPersonalInfo['pe_address2'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="pe_city" id="pe_city" value="{{(isset($empPersonalInfo['pe_city'])) ? $empPersonalInfo['pe_city'] : '' }}"/>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
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
                        @if($empPersonalInfo['pe_zip_code'] == 0)
                            <input type="text" class="form-control" name="pe_zip_code" id="pe_zip_code" value=""/>
                            @else
                            <input type="text" class="form-control" name="pe_zip_code" id="pe_zip_code" value="{{$empPersonalInfo['pe_zip_code']}}"/>
                            @endif
                        </div>
                    </div>
                    
                   
                </div>
            </div>



            <h5 ><strong>Emergency Info </strong></h5>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Name <span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="emergency_name" id="emergency_name" value="{{(isset($empPersonalInfo['emergency_name'])) ? $empPersonalInfo['emergency_name'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Relation</strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="emergency_relation" id="emergency_relation" value="{{(isset($empPersonalInfo['emergency_relation'])) ? $empPersonalInfo['emergency_relation'] : '' }}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Contact1<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="emergency_contact_one" id="emergency_contact_one" value="{{(isset($empPersonalInfo['emergency_contact_one'])) ? $empPersonalInfo['emergency_contact_one'] : '' }}"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> Contact2</strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="emergency_contact_two" id="emergency_contact_two" value="{{(isset($empPersonalInfo['emergency_contact_two'])) ? $empPersonalInfo['emergency_contact_two'] : '' }}"/>
                        </div>
                    </div>                   
                </div>
            </div>









            <h5 ><strong>Reference Address1 </strong></h5>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Relationship </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_relation" id="ref_one_relation" value="{{$empPersonalInfo['ref_one_relation']}}"/>
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5" style="padding-left: 10px;"> <strong>Mobile Number </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_contact_no" id="ref_one_contact_no" value="{{$empPersonalInfo['ref_one_contact_no']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_address" id="ref_one_address" value="{{$empPersonalInfo['ref_one_address']}}"/>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_city" id="ref_one_city" value="{{$empPersonalInfo['ref_one_city']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
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
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
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
                        <label class="control-label col-md-5"><strong> Postal Code </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_one_pin_code" id="ref_one_pin_code"  value="{{$empPersonalInfo['ref_one_pin_code']}}"/>
                        </div>
                    </div> 
                </div>
            </div> 

            <h5 ><strong>Reference Address2 </strong></h5>
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Relationship </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_relation" id="ref_two_relation" value="{{$empPersonalInfo['ref_two_relation']}}"/>
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5" style="padding-left: 10px;"> <strong>Mobile Number </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_contact_no" id="ref_two_contact_no" value="{{$empPersonalInfo['ref_two_contact_no']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> <strong>Address </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_address" id="ref_two_address" value="{{$empPersonalInfo['ref_two_address']}}"/>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong> City </strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ref_two_city" id="ref_two_city" value="{{$empPersonalInfo['ref_two_city']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"><strong>State</strong></label>
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
                        <label class="control-label col-md-5"> <strong>Country</strong></label>
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
                        <label class="control-label col-md-5"><strong>Postal Code</strong></label>
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
        <div class="basicInfoLoader"></div>
        </form>
    </div>
</div>