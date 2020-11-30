<?php  //echo "<pre>";print_r($details);die;  ?>
<div class="row">
    <div class="col-md-6">
        <h4 class="borderbot">Business Information</h4>
       <div class="form-group">
            <label class="col-md-4 control-label rowlinht ">Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">              
                <input type="text" class="form-control" id="screen_name" value="@if(isset($details[0]->display_name)){{$details[0]->display_name}}@endif" name="screen_name">
            </div>
        </div>         
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht ">Business Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                <input type="hidden" name='user_hidden_id' id="user_hidden_id" value="@if(isset($details[0]->user_id)){{$details[0]->user_id}}@endif" class="form-control">
                 <input type="hidden" name="le_hidden_id" id="le_hidden_id" value="@if(isset($details[0]->legal_entity_id)){{$details[0]->legal_entity_id}}@endif" class="form-control">

                <input type="text" class="form-control" id="legalentity_name" value="@if(isset($details[0]->business_legal_name)){{$details[0]->business_legal_name}}@endif" name="legalentity_name">

               <!--  <input type="hidden" name="user_id" id="user_id" value="@if(isset($details[0]->user_id)){{$details[0]->user_id}}@endif" class="form-control">

                <input type="hidden" name="le_wh_id" id="le_wh_id" value="@if(isset($details[0]->le_wh_id)){{$details[0]->le_wh_id}}@endif" class="form-control"> -->

            </div>
        </div>       
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Created By</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" name="created_by" id="created_by" value="@if(isset($details[0]->created_byname)){{$details[0]->created_byname}}@endif" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Updated By</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" type="text" readonly="true" value="@if(isset($details[0]->updated_byname)){{$details[0]->updated_byname}}@endif" />
             <!--    <input type="text" name="hidden_user_id" id="hidden_user_id" value="@if(isset($details[0]->user_id)){{$details[0]->user_id}}@endif" class="form-control"> -->
            </div>
        </div>       
		<div class="form-group">
            <label class="col-md-4 control-label rowlinht">GSTIN</label>
            <div class="col-md-8 rowbotmarg">
				<input type="hidden" id="gst_state_codes" value=""/>
                <input class="form-control" name="gstin" style="text-transform:uppercase" type="text" value="@if(isset($details[0]->gstin)){{$details[0]->gstin}}@endif"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">FC Code</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" name="le_code" type="text" readonly="true" value="@if(isset($details[0]->le_code)){{$details[0]->le_code}}@endif"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">FSSAI Number</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" name="lic_num" type="text"  value="@if(isset($details[0]->fssai)){{$details[0]->fssai}}@endif"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Deposit</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" name="cashback" type="text" readonly="true" value="@if(isset($details[0]->cashback_deposite)){{$details[0]->cashback_deposite}}@endif"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Temporary Credit Limit</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" name="creditlimit" type="text" readonly="readonly" value="@if(isset($details[0]->creditlimit)){{$details[0]->creditlimit}}@endif"/>
            </div>
        </div>
         <div class="form-group">
            <label class="col-md-4 control-label rowlinht">Available Cashback</label>
            <div class="col-md-8 rowbotmarg">
                <input class="form-control" name="available_cashback" type="text" readonly="true" value="@if(isset($details[0]->cashback)){{$details[0]->cashback}}@endif"/>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <h4 class="borderbot">Registered Address</h4>

        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Business Type</label>
            <div class="col-md-9 rowbotmarg">
                <input class="form-control" type="text" readonly="true" name="ware_house_id" id="ware_house_id" value="@if(isset($details[0]->Warehouse)){{$details[0]->Warehouse}}@endif" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Warehouse Code</label>
            <div class="col-md-9 rowbotmarg">
                <input class="form-control" type="text" readonly="true" name="business_Type" id="business_Type" value="@if(isset($details[0]->le_wh_code)){{$details[0]->le_wh_code}}@endif" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Address1<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($details[0]->address1)){{$details[0]->address1}}@endif"  id="org_address1" name="org_address1">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Address2</label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($details[0]->address2)){{$details[0]->address2}}@endif"  id="org_address2" name="org_address2">      </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Pincode<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" maxlength="6" value="@if(isset($details[0]->pincode)){{$details[0]->pincode}}@endif"  id="pincode" name="org_pincode" />
            </div>
        </div>
        
        
       
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">State<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                    <select name = "satename" id="state_id" class="form-control select2me">
                       
                        @foreach($state as $value)
                        <option value = "{{$value->zone_id}}" @if($details[0]->state_id == $value->zone_id) {{ "selected" }} @endif>{{$value->name}}</option>
                   @endforeach                                                    
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">City<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control"  value="@if(isset($details[0]->city)){{$details[0]->city}}@endif"  id="org_city" name="org_city">      </div>
        </div>


        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Applied Cashback</label>
            <div class="col-md-9 rowbotmarg">
                <input class="form-control" name="applied_cashback" type="text" readonly="true" value="@if(isset($details[0]->applied_cashback)){{$details[0]->applied_cashback}}@endif"/>
            </div>
        </div>

    <div class="form-group">
        <div class="col-md-9">
        <div class="form-group">
            <div class="mt-checkbox-list">
                    <input type="checkbox" id="is_self_tax_update" value="@if(isset($details[0]->is_self_tax)){{$details[0]->is_self_tax}}@endif"  name="is_self_tax_update"  @if(isset($details[0]->is_self_tax) && $details[0]->is_self_tax==1){{'checked'}}@endif >&nbsp;&nbsp;Self Billing              
            </div>
        </div>
    </div>
</div>

      <!--   <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Country<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <select name = "countryname" id="countryname" class="form-control select2me">
                        @foreach($country as $value)
                        <option value = "{{$value->country_id}}">{{$value->name}}</option>
                   @endforeach                                                    
                </select>    
            </div>
        </div> -->     
    </div>
</div>


