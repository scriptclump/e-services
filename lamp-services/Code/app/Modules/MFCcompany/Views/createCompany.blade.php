<?php  //echo "<pre>";print_r($details);die;  ?>
<div class="row">
    <div class="col-md-6">
        <h4 class="borderbot" style="border-bottom:2px solid #f1f3f7; line-height:50px;	margin-top:-10px;">Business Information</h4> 
        <div class="form-group">
            <label class="col-md-4 control-label rowlinht ">Business Legal Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8 rowbotmarg">
                
                 <input type="hidden" name="le_hidden_id" id="le_hidden_id" value="@if(isset($details[0]->legal_entity_id)){{$details[0]->legal_entity_id}}@endif" class="form-control">

                <input type="text" class="form-control" id="business_name" value="@if(isset($details[0]->business_legal_name)){{$details[0]->business_legal_name}}@endif" name="business_name">

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
                <input class="form-control" type="text" readonly="true" value=""  />
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
    </div>
    <div class="col-md-6">
        <h4 class="borderbot" style="border-bottom:2px solid #f1f3f7; line-height:50px;	margin-top:-10px;">Registered Address</h4>

        <div class="form-group">
            <label class="col-md-3 control-label rowlinht">Address<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9 rowbotmarg">
                <input type="text" class="form-control" value="@if(isset($details[0]->address1)){{$details[0]->address1}}@endif"  id="org_address1" name="org_address1">
            </div>
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
        
    </div>
</div>