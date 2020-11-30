<div class="tab-pane" id="tab_insurance_info"> 
    <div class="basicInfoOverlay"></div>

    <div id="insurance_preview" style="margin-top:11px;" class="form-horizontal">
        <div class="form-body">
            <div class="row">
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>Spouse Name:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_spouse_name">
                           {{($InsuranceArray['spouse_name'] != "null") ? $InsuranceArray['spouse_name'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>Spouse DOB</strong>:</label>
                    <div class="col-md-7">
                       <p class="form-control-static" id="preview_spouse_dob">
                            @if($InsuranceArray['spouse_dob'] != "00-00-0000")
                            {{$InsuranceArray['spouse_dob']}}
                            @endif
                          
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>Child1 Name:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_child_one_name">
                           {{(isset($InsuranceArray['child_one_name'])) ? $InsuranceArray['child_one_name'] : '' }}
                        </p>
                    </div>
                </div>                              
            </div>
            <div class="row">
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>Child1 DOB:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_child_one_dob">
                            @if($InsuranceArray['child_one_dob'] != "00-00-0000")
                            {{$InsuranceArray['child_one_dob']}}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>Child2 Name</strong>:</label>
                    <div class="col-md-7">
                       <p class="form-control-static" id="preview_child_two_name">
                           {{($InsuranceArray['child_two_name'] != "null") ? $InsuranceArray['child_two_name'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>Child2 DOB:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_child_two_dob">
                            @if($InsuranceArray['child_two_dob'] != "00-00-0000")
                            {{$InsuranceArray['child_two_dob']}}
                            @endif
                        </p>
                    </div>
                </div>                              
            </div>
            <div class="row">
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;text-align: left;">
                    <label class="control-label col-md-5"><strong>Card Number:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_card_number">
                           {{(isset($InsuranceArray['card_number'])) ? $InsuranceArray['card_number'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                    <label class="control-label col-md-5"><strong>TPA:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_tpa">
                           {{(isset($InsuranceArray['tpa'])) ? $InsuranceArray['tpa'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style" style="word-break: break-all;text-align: left;">
                    <label class="control-label col-md-5"><strong>TPA Contact #:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_tpa_contact_number">
                           {{($InsuranceArray['tpa_contact_number'] != 0) ? $InsuranceArray['tpa_contact_number'] : '' }}
                        </p>
                    </div>
                </div>
                
            </div>
        </div>       
    </div>
     <div id="insurance_edit" style="display:none" class="form-horizontal">
        <form action="#" class="submit_form form-horizontal" id="emp_ensurance_info_form" method="get">
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4 middle_style " style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>Spouse Name:</strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control diable_insu" name="spouse_name" id="spouse_name" value="{{(isset($InsuranceArray['spouse_name'])) ? $InsuranceArray['spouse_name'] : '' }}">
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style " style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>Spouse DOB</strong>:</label>
                        <div class="col-md-7">
                            @if($InsuranceArray['spouse_dob'] != "00-00-0000")
                             <input type="text" class="form-control diable_insu" name="spouse_dob" id="spouse_dob" value="{{(isset($InsuranceArray['spouse_dob'])) ? $InsuranceArray['spouse_dob'] : '' }}">
                            @else
                             <input type="text" class="form-control diable_insu" name="spouse_dob" id="spouse_dob" value="">
                             @endif
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style " style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>Child1 Name:</strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control diable_insu" name="child_one_name" id="child_one_name" value="{{(isset($InsuranceArray['child_one_name'])) ? $InsuranceArray['child_one_name'] : '' }}">
                        </div>
                    </div>                              
                </div>
                <div class="row">
                    <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>Child1 DOB:</strong></label>
                        <div class="col-md-7">
                            @if($InsuranceArray['child_one_dob'] != "00-00-0000")
                            <input type="text" class="form-control diable_insu" name="child_one_dob" id="child_one_dob" value="{{(isset($InsuranceArray['child_one_dob'])) ? $InsuranceArray['child_one_dob'] : '' }}">
                            @else
                             <input type="text" class="form-control diable_insu" name="child_one_dob" id="child_one_dob" value="">
                             @endif
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>Child2 Name</strong>:</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control diable_insu" name="child_two_name" id="child_two_name" value="{{(isset($InsuranceArray['child_two_name'])) ? $InsuranceArray['child_two_name'] : '' }}">
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style" style="word-break: break-all; text-align: left;">
                        <label class="control-label col-md-5"><strong>Child2 DOB:</strong></label>
                        <div class="col-md-7">
                            @if($InsuranceArray['child_two_dob'] != "00-00-0000")
                            <input type="text" class="form-control diable_insu" name="child_two_dob" id="child_two_dob" value="{{(isset($InsuranceArray['child_two_dob'])) ? $InsuranceArray['child_two_dob'] : '' }}">
                            @else
                             <input type="text" class="form-control diable_insu" name="child_two_dob" id="child_two_dob" value="">
                             @endif
                        </div>
                    </div>                              
                </div>
                <div class="row">
                    <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>Card Number:</strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="card_number" id="card_number" value="{{(isset($InsuranceArray['card_number'])) ? $InsuranceArray['card_number'] : '' }}">
                           
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>TPA:</strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="tpa" id="tpa" value="{{(isset($InsuranceArray['tpa'])) ? $InsuranceArray['tpa'] : '' }}">
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style" style="word-break: break-all;">
                        <label class="control-label col-md-5"><strong>TPA Contact #:</strong></label>
                        <div class="col-md-7">
                            @if($InsuranceArray['tpa_contact_number'] != "0")
                                <input type="text" class="form-control" name="tpa_contact_number" id="tpa_contact_number" value="{{(isset($InsuranceArray['tpa_contact_number'])) ? $InsuranceArray['tpa_contact_number'] : '' }}">
                            @else
                                <input type="text" class="form-control" name="tpa_contact_number" id="tpa_contact_number" value="">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <hr />
                    <div class="col-md-12 text-center"> 
                        <input type="submit" class="btn green-meadow saveusers" value="Update" id="saveusers"/> 
                        <input type="button" class="btn green-meadow" value="Cancel" id="cancel_insurance" /> 
                    </div>
                </div>
            </div>   
        </form>     
    </div>
</div>