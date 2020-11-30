<div class="tab-pane" id="tab_bank_info"> 
    <div class="overlay"></div>

    <div id="bank_preview" style="margin-top:11px;" class="form-horizontal">
        <div class="form-body">
            <div class="row">
                <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>IFSC Code:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_ifsc_code">
                           {{(isset($bankInfo['ifsc_code'])) ? $bankInfo['ifsc_code'] : '' }}
                        </p>
                    </div>
                </div>

                <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>Bank Name</strong>:</label>
                    <div class="col-md-7">
                       <p class="form-control-static" id="preview_bank_name">
                           {{(isset($bankInfo['bank_name'])) ? $bankInfo['bank_name'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>Branch Name:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_branch_name">
                           {{(isset($bankInfo['branch_name'])) ? $bankInfo['branch_name'] : '' }}
                        </p>
                    </div>
                </div>                              
            </div>
            <div class="row">
            <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>Name:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_acc_name">
                           {{(isset($bankInfo['acc_name'])) ? $bankInfo['acc_name'] : '' }}
                        </p>
                    </div>
                </div>
            <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>Acc Number</strong>:</label>
                    <div class="col-md-7">
                       <p class="form-control-static" id="preview_acc_no">
                           {{(isset($bankInfo['acc_no'])) ? $bankInfo['acc_no'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>Account Type:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_acc_type">
                        @if(isset($account_data))
                            @foreach($account_data as $account_value)
                                @if(isset($bankInfo['acc_type']) && $bankInfo['acc_type']== $account_value->id )
                                    {{$account_value->account_type}}
                                @endif
                            @endforeach
                        @endif
                        </p>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>MICR Code:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_micr_code">
                           {{(isset($bankInfo['micr_code'])) ? $bankInfo['micr_code'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>Currency Code</strong>:</label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_currency_code">
                        @if(isset($currency_data))
                            @foreach($currency_data as $currency_value)
                                @if(isset($bankInfo['currency_code']) && $bankInfo['currency_code']==$currency_value->id){{$currency_value->currency_name}}
                                @endif
                            @endforeach
                        @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>       
    </div>
     <div id="bank_edit" style="display:none" class="form-horizontal">
        <form action="#" class="submit_form form-horizontal" id="emp_bank_info" method="get">
        
            <div class="form-body">
                <div class="row">
                    <div class="form-group bank_ifsc_id col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>IFSC Code<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="{{(isset($bankInfo['ifsc_code'])) ? $bankInfo['ifsc_code'] : '' }}" />
                        </div>
                    </div> 


                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Bank Name<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{(isset($bankInfo['bank_name']))?$bankInfo['bank_name'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Branch Name<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="branch_name" id="branch_name" value="{{(isset($bankInfo['branch_name'])) ? $bankInfo['branch_name'] : '' }}" />
                        </div>
                    </div>                              
                </div>
                <div class="row">

                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Name<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                             <input type="text" class="form-control" name="acc_name" id="acc_name" value="{{(isset($bankInfo['acc_name'])) ? $bankInfo['acc_name'] : '' }}" />
                        </div>
                    </div> 

                     <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Acc Number<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="acc_no" id="acc_no" value="{{(isset($bankInfo['acc_no']))?$bankInfo['acc_no'] : '' }}" />
                        </div>
                    </div>

                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Account Type<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="acc_type" name="acc_type">
                                <option value="0">Select Account Type</option>
                                @if(isset($account_data))
                                    @foreach($account_data as $account_value)
                                        @if(isset($bankInfo['acc_type']) && $bankInfo['acc_type']== $account_value->id )
                                             <option value="{{$account_value->id}}" selected>{{$account_value->account_type}}</option>
                                        @else
                                             <option value="{{$account_value->id}}">{{$account_value->account_type}}</option>
                                        @endif
                                   
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>                      
                </div>
                <div class="row">
                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>MICR Code<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="micr_code" id="micr_code" value="{{(isset($bankInfo['micr_code'])) ? $bankInfo['micr_code'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Currency Code<span class="required">*</span></strong></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" id="currency_code" name="currency_code">
                                <option value="0">Select Currency Code</option>
                                @if(isset($currency_data))
                                    @foreach($currency_data as $currency_value)
                                        @if(isset($bankInfo['currency_code']) && $bankInfo['currency_code']==$currency_value->id)
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
                <div class="row">
                    <hr />
                    <div class="col-md-12 text-center"> 
                        <input type="submit" class="btn green-meadow " value="Update" id="saveusers"/> 
                        <input type="button" class="btn green-meadow" value="Cancel" id="bank_cancel_btn" /> 
                    </div>
                    <div class="loader"></div>
                </div>
            </div>  
        </form>     
    </div>
</div>