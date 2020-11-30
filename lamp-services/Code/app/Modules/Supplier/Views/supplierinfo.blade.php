<div class="tab-pane active" id="tab_11">
    <form id="suppliersinfo" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <h4 id='vendor_info_id'>{{$vendor}} Information</h4>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">
                        @if($vendor == 'Vehicle')
                            {{'Vehicle Model Name'}}
                            <span class="required" aria-required="true">*</span></label>
                             <select class="form-control select2me" id="vehicle_name" name="vehicle_name">
                                <option value="">Select Vehicle Model Name</option> 

                                @foreach($vehicle_models as $val )
                                @if(isset($supplier_data->vehicle_model) && $val->value == $supplier_data->vehicle_model)

                                    <option value="{{$val->value}}" selected value_type="{{$val->name}}">{{$val->name}}</option>
                                @else
                                <option value="{{$val->value}}" value_type="{{$val->name}}">{{$val->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        @else
                            {{'Organization Name'}}
                            <span class="required" aria-required="true">*</span></label>
                             <input type="text" class="form-control" id="organization_name" value="@if(isset($supplier_data->business_legal_name)){{$supplier_data->business_legal_name}}@endif" name="organization_name">

                        @endif
                     </div>
            </div>
            @if($vendor == 'Vehicle')
             <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Vehicle Registration No. <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="reg_no" value="@if(isset($vehicle_data->reg_no)){{$vehicle_data->reg_no}}@endif" name="reg_no">
                </div>
            </div> 
            @endif
			<?php
			$orgtype = ($vendor == 'Vehicle')?'Vehicle Permit Type':'Organization Type';
			if($vendor == 'Service Provider'){$orgtype = 'Service Type';}
			if($vendor == 'Space'){$orgtype = 'Space Type';}
			?>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">
	                {{$orgtype}} 
					<span class="required" aria-required="true">*</span></label>
                    <select class="form-control select2me" id="organization_type" name="organization_type">
                        <option value="">Select {{$orgtype}}</option>    
                        @foreach($company_data as $companyVal )
                        @if(isset($supplier_data->business_type_id) && $companyVal->value== $supplier_data->business_type_id)
                            <option value="{{$companyVal->value}}" selected >{{$companyVal->company_type}}</option>
                        @else
                        <option value="{{$companyVal->value}}" >{{$companyVal->company_type}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 vendor_specific" style='display:none;'>
                <div class="form-group">
                    <label class="control-label">Supplier Reference Code </label>
                    <input type="text" class="form-control" id="reference_erp_code" 
                           value="@if(isset($erp_code)){{$erp_code}}@endif"   name="reference_erp_code" readonly="">
                </div>
            </div>
        </div>
        <div class="row">

            
            <div class="col-md-4 vendor_specific" style='display:none;'>
                <div class="form-group">
                    <label class="control-label">Supplier Type <span class="required" aria-required="true">*</span></label>
                        <div class="row">
                            <div class="col-md-12">  
<select class="form-control select2me" id="supplier_type" name="supplier_type">
                        <option value="">Select Supplier Type</option> 
                        @foreach($suppliers_types as $supVal )
                        @if(isset($supplier_data->supplier_type) && $supplier_data->supplier_type==$supVal->value)
                         <option value="{{$supVal->value}}"  selected>{{$supVal->account_type}}</option>
                        @else
                         <option value="{{$supVal->value}}" >{{$supVal->account_type}}</option>
                        @endif
                       
                        @endforeach
                    </select>




                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 vendor_specific" style='display:none;'>
                <div class="form-group">
                    <label class="control-label">Supplier Rank <span class="required" aria-required="true">*</span></label>
                        <div class="row">
                            <div class="col-md-12">  
                 <select class="form-control select2me" name="supplier_rank" id="supplier_rank">
                  <option value="">Select Supplier Rank</option>
                  @foreach($suppliers_rank as $suppliers_rank)
                  @if(isset($supplier_data->sup_rank) && $supplier_data->sup_rank==$suppliers_rank->value)
                         <option value="{{$suppliers_rank->value}}"  selected>{{$suppliers_rank->account_type}}</option>
                        @else
                              <option value="{{$suppliers_rank->value}}">{{$suppliers_rank->account_type}}</option>
                        @endif
                  @endforeach
                </select>




                        </div>
                    </div>
                </div>
            </div>

        </div>
        <h4>
            @if($vendor == 'Vehicle')
            {{'Driver Contact Information'}}
            @else
            {{'Contact Information'}}
            @endif
        </h4>
        @if($vendor == 'Vehicle')
        <div class="row">
            <div class="col-md-4">
                <select class="form-control select2me" id="driver_contact_old" name="driver_contact_old" @if($responseSource == "edit") disabled @endif>
                        <option value="">Select Driver Contact Information</option>    
                    @foreach($driver_contact as $val )
                    @if(isset($supplier_data->driver_contact) && $val->user_id == $supplier_data->driver_contact)
                    <option value="{{$val->user_id}}" selected >{{$val->fullname}}</option>
                    @else
                    <option value="{{$val->user_id}}" >{{$val->fullname}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <input type="hidden" class="form-control" id="vehicle_id" value="@if(isset($supplier_data->vehicle_id)){{$supplier_data->vehicle_id}}@endif" name="vehicle_id">
            <div class="col-md-4">
                <button type="button" class="btn green-meadow btnn" id="add_new_user" @if($responseSource == "edit") disabled @endif >Add New</button>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">First Name <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="org_firstname" value="@if(isset($supplier_data->firstname)){{$supplier_data->firstname}}@endif" name="org_firstname">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Last Name <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->lastname)){{$supplier_data->lastname}}@endif" id="org_lastname" name="org_lastname">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->email_id)){{$supplier_data->email_id}}@endif" id="org_email" name="org_email">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Mobile <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="org_mobile" value="@if(isset($supplier_data->mobile_no)){{$supplier_data->mobile_no}}@endif" name="org_mobile" maxlength="11">
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label class="control-label">Landline </label>
                            <input type="text" class="form-control" value="@if(isset($supplier_data->landline_no)){{$supplier_data->landline_no}}@endif"  id="org_landline" name="org_landline">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label">EXT Number </label>
                            <input type="text" class="form-control" value="@if(isset($supplier_data->landline_ext)){{$supplier_data->landline_ext}}@endif"  id="org_extnumber" name="org_extnumber">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($vendor == 'Vehicle')
        <h4>Driver License Details</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Driving License No. </label>
                    <input type="text" class="form-control" id="license_no" value="@if(isset($vehicle_data->license_no)){{$vehicle_data->license_no}}@endif" name="license_no">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">License Expiry Date</label>
                    <input type="text" class="form-control" id="license_exp_date" value="@if(isset($vehicle_data->license_exp_date)){{$vehicle_data->license_exp_date}}@endif" name="license_exp_date">
                </div>
            </div>
        </div>
        @endif        
        
        <h4>
            @if($vendor == 'Vehicle')
                            {{'Driver Address'}}
            @elseif($vendor == 'Space')   
             {{'Owner Address'}}
                        @else
                            {{'Registered Office Address'}}
            @endif
        </h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->address1)){{$supplier_data->address1}}@endif"  id="org_address1" name="org_address1">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Address 2</label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->address2)){{$supplier_data->address2}}@endif"  id="org_address2" name="org_address2">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control pincode" value="@if(isset($supplier_data->pincode)){{$supplier_data->pincode}}@endif"  id="org_pincode" name="org_pincode" maxlength="6">
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                    <select class="form-control select2me" id="org_state" name="org_state">
                        <option value="">Select State</option>    
                        @foreach($states_data as $stateVal )
                        
                        @if(isset($supplier_data->state_id) && $supplier_data->state_id==$stateVal->id)
                         <option value="{{$stateVal->id}}"  selected>{{$stateVal->state_name}}</option>
                        @else
                         <option value="{{$stateVal->id}}" >{{$stateVal->state_name}}</option>
                        @endif
                       
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control"  value="@if(isset($supplier_data->city)){{$supplier_data->city}}@endif"  id="org_city" name="org_city">
                </div>
            </div>


            <?php $country = 'ee' ?>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Country <span class="required" aria-required="true">*</span></label>
                    <select class="form-control select2me" id="org_country" name="org_country">
                        <option value="">Select Country</option>
                        @if(isset($countries))
                            @foreach($countries as $country_value)
                            @if(isset($supplier_data->country) && $supplier_data->country==$country_value['country_id'])
                                  <option value="{{$country_value['country_id']}}" selected>{{$country_value['name']}}</option>
                                @else
                                   <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                                   @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                    <h4> @if($vendor == 'Vehicle')
                            {{'Owner Address'}}
                        @else
                            {{'Billing Address'}}
                            @endif
                    <span style="font-weight:normal !important; margin-left:20px;">
                        <input type="checkbox"  id="org_billingaddress_chk" name="org_billingaddress_chk">
                            @if($vendor == 'Vehicle')
                                            {{'Same as Driver Address'}}
                            @elseif($vendor == 'Space')
                            {{'Same as Owner Address'}}
                                        @else
                                            {{'Same As Registered Office Address'}}
                            @endif
                    </span> </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_add1)){{$supplier_data->sup_add1}}@endif" id="org_billingaddress_address1" name="org_billingaddress_address1">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Address 2</label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_add1)){{$supplier_data->sup_add2}}@endif" id="org_billingaddress_address2" name="org_billingaddress_address2">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control pincode" value="@if(isset($supplier_data->sup_pincode)){{$supplier_data->sup_pincode}}@endif" id="org_billingaddress_pincode" name="org_billingaddress_pincode" maxlength="6">
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                    <select class="form-control select2me" id="org_billingaddress_state" name="org_billingaddress_state">
                        <option value="">Select State</option> 

                        @foreach($states_data as $stateVal )
                        <!--<option value="{{$stateVal->id}}" >{{$stateVal->state_name}}</option>-->
                         @if(isset($supplier_data->sup_state) && $supplier_data->sup_state==$stateVal->id)
                         <option value="{{$stateVal->id}}"  selected>{{$stateVal->state_name}}</option>
                        @else
                         <option value="{{$stateVal->id}}" >{{$stateVal->state_name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_city)){{$supplier_data->sup_city}}@endif"  id="org_billingaddress_city" name="org_billingaddress_city">
                </div>
            </div>

            <?php $country = 'ee' ?>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Country <span class="required" aria-required="true">*</span></label>
                    <select class="form-control select2me" id="org_billingaddress_country" name="org_billingaddress_country">
                        <option value="">Select Country</option>
                        @if(isset($countries))
                            @foreach($countries as $country_value)
                            @if(isset($supplier_data->sup_country) && $supplier_data->sup_country==$country_value['country_id'])
                                  <option value="{{$country_value['country_id']}}" selected>{{$country_value['name']}}</option>
                                @else
                                   <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                                   @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

        </div>
        <h4>Bank Details</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Account Name </label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_account_name)){{$supplier_data->sup_account_name}}@endif" id="org_bank_acname" name="org_bank_acname">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Bank Name </label>
                    <input type="text" class="form-control"  value="@if(isset($supplier_data->sup_bank_name)){{$supplier_data->sup_bank_name}}@endif"  id="org_bank_name" name="org_bank_name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Account No </label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_account_no)){{$supplier_data->sup_account_no}}@endif"  id="org_bank_acno" name="org_bank_acno">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Account Type </label>
                    <select class="form-control select2me" id="org_bank_actype" name="org_bank_actype">
                        <option value="">Select Account Type</option>
                        @if(isset($account_data))
                            @foreach($account_data as $account_value)
                                @if(isset($supplier_data->sup_account_type) && $supplier_data->sup_account_type== $account_value->id )
                                     <option value="{{$account_value->id}}" selected>{{$account_value->account_type}}</option>
                                @else
                                     <option value="{{$account_value->id}}">{{$account_value->account_type}}</option>
                                @endif
                           
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">IFSC Code </label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_ifsc_code)){{$supplier_data->sup_ifsc_code}}@endif" id="org_bank_ifsc" name="org_bank_ifsc">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Branch Name</label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_branch_name)){{$supplier_data->sup_branch_name}}@endif"  id="org_bank_branch" name="org_bank_branch">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">MICR Code</label>
                    <input type="text" class="form-control" value="@if(isset($supplier_data->sup_micr_code)){{$supplier_data->sup_micr_code}}@endif"  id="org_micr_code" name="org_micr_code">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Currency Code </label>
                    <select class="form-control select2me" id="org_curr_code" name="org_curr_code">
                        <option value="">Select Currency Code</option>
                        @if(isset($currency_data))
                            @foreach($currency_data as $currency_value)
                                @if(isset($supplier_data->sup_currency_code) && $supplier_data->sup_currency_code==$currency_value->id)
                                    <option value="{{$currency_value->id}}" selected>{{$currency_value->currency_name}}</option>
                                @else
                                    <option value="{{$currency_value->id}}">{{$currency_value->currency_name}}</option>
                                @endif
                            
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <h4>Relationship Manager</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Relationship Manager <span class="required" aria-required="true">*</span></label>
 <select class="form-control select2me" id="org_rm" name="org_rm">
                        <option value="">Select Relationship Manager</option>
                        @if(isset($rm_data))

                            @foreach($rm_data as $rm_value)
                                @if(isset($supplier_data->rel_manager) && $supplier_data->rel_manager==$rm_value->id)
                                    <option value="{{$rm_value->id}}" selected>{{$rm_value->username}}</option>
                                @else
                                    <option value="{{$rm_value->id}}">{{$rm_value->username}}</option>
                                @endif

                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-12 text-center">			
                <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">Save & Continue</button>
                <button type="button" id="cancelsuppinfo" class="btn green-meadow">Cancel</button>
            </div>
        </div>
    </form>
	 @include('Manufacturers::approval')
</div>
