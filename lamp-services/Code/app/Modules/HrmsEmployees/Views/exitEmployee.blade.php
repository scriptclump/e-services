<div class="tab-pane" id="tab_4-5">
    <div class="overlay"></div>
    <div class="container">
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <form method="POST" id ="exit_from_data">
                    <div id="loadingmessage" class=""></div>




        <span class="bread-color"><b> Employee Information </b></span>
        <div id="default_show1" style="margin-top:11px;" class="form-horizontal">
            <div class="form-body">
                            <div class="row">
                                <div class="form-group col-md-4 middle_style">
                                <label class="control-label col-md-5"><strong>Emp Type: </strong></label>
                                    <div class="col-md-7">
                                        <p class="form-control-static" id="preview_emp_type">
                                            @foreach($employment_types as $value)
                                            @if($userData['employment_type'] == $value['value'])
                                            {{$value['master_lookup_name']}}
                                            @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>  
                            </div>
                    <div class="row">
                    <div class="form-group col-md-4 middle_style">
                    <label class="control-label col-md-5"><strong>PreFix:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_prefix">
                                    @foreach($prefix as $value)
                                    @if($userData['prefix'] == $value)
                                    {{$value}}
                                    @endif
                                    @endforeach
                                </p>
                            </div>
                    </div>
                    <div class="form-group col-md-4 middle_style">
                            <label class="control-label col-md-5"><strong>First Name</strong>:</label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_first_name">{{(isset($userData['firstname'])) ? $userData['firstname'] : '' }}</p>
                            </div>
                    </div>
                    <div class="form-group col-md-4 middle_style">
                            <label class="control-label col-md-5"><strong>Middle Name</strong>:</label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_middle_name">{{(isset($userData['middlename'])) ? $userData['middlename'] : '' }}</p>
                            </div>
                    </div>                           
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Last Name:</strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_last_name">{{(isset($userData['lastname'])) ? $userData['lastname'] : '' }}</p>
                        </div>
                        </div> 
                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Emp Code: </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_emp_code">{{(isset($userData['emp_code'])) ? $userData['emp_code'] : '' }}</p>
                        </div>
                    </div> 
                    <div class="form-group col-md-4 middle_style">
                        <label class="control-label col-md-5"><strong>Office Email: </strong></label>
                        <div class="col-md-7">
                            <p class="form-control-static" id="preview_office_email_id">
                                {{(isset($userData['office_email'])) ? $userData['office_email'] : '' }}
                            </p>
                        </div>
                    </div>              
                    </div>
                 
                    
                    <div class="row">  
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Role : </strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_role">{{$userData['role_name']}}</p>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Manager : </strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_rmp">{{$userData['reporting_manager_name']}}</p>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Department:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_department">{{$userData['department_name']}}</p>
                            </div>
                        </div>  
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Designation:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_designation">{{$userData['designation_name']}}</p>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Emp Group:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="emp_group" name="emp_group">
                                    @foreach($emp_group as $value)
                                    @if($userData['emp_group_id'] == $value['emp_group_id'])
                                    {{$value['group_name']}}
                                    @endif
                                    @endforeach
                                </p>
                            </div>
                        </div> 
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Business Unit:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_cost">{{$cost_name}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">                                        
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Date Of Joining:</strong></label>
                            <div class="col-md-7">
                            @if($userData['doj'] == 0000-00-00)
                                <p class="form-control-static" id="preview_doj"></p>
                                    @else
                                    <p class="form-control-static" id="preview_doj">
                                    {{(isset($userData['doj'])) ? $userData['doj'] : '' }}</p>
                            @endif
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Date Of Birth:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_dob">
                                    {{(isset($userData['dob'])) ? $userData['dob'] : '' }}</p>
                            </div>
                        </div> 
                        <div class="form-group col-md-4">
                            <label class="control-label col-md-5"><strong>Gender:</strong></label>
                            <div class="col-md-7">
                                <p class="form-control-static" id="preview_gender">
                                    @foreach($gender as $value)
                                    @if($userData['gender'] == $value)
                                    {{$value}}
                                    @endif
                                    @endforeach
                                </p>
                            </div>
                        </div>               
                    </div>
            </div>       
        </div> 


                <div id="default_show1" style="margin-top:11px;" class="form-horizontal">
                <div class="form-body">
                    <div class="row">
                        @if($appDropdown!= "")
                        <div class="row">
                            <div class="col-md-4">
                                <span class="bread-color"><b> Approval For {{$approvaldata['data'][0]['nextStatus']}} </b></span>
                            </div>
                        </div>
                        <br /><br />
                        @if($approvaldata['currentStatusId'] == "57148")
                        <div class="row"> 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><strong>Date Of Joining</strong><span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" id="join_date" name="join_date" class="form-control"/> 
                                </div>
                            </div>
                        </div>
                        @endif
                        

                        @if($approvaldata['currentStatusId'] == "57155")
                        <div class="row"> 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><strong>Date Of Exit</strong><span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" id="employee_exit_date" name="employee_exit_date" class="form-control"/> 
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($approvaldata['currentStatusId'] == "57152")
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                <label><strong>Employee Type:</strong></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>{{$empType}}</label>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><strong>Office Email</strong><span class="required">*</span></label>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" id="employee_email_id" name="employee_email_id" class="form-control" placeholder="employee@ebutor.com"/> 
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-2">
                                <span><strong>Approval For</strong></span>
                                <input type ="hidden" id="currentStatusId" name = "currentStatusId" value  ="{{$approvaldata['currentStatusId']}}">
                                <input type="hidden" id="hidden_emp_id" name="hidden_emp_id" value="{{ $userData['emp_id'] }}">

                                <input type="hidden" id="nextstatusname" name="nextstatusname" value="{{ $approvaldata['data'][0]['nextStatus'] }}">
                                <input type="hidden" id="condition" name="condition" value="{{ $approvaldata['data'][0]['condition'] }}">

                                <input type="hidden" id="last_name_approval" name="last_name_approval" value="{{(isset($userData['firstname'])) ? $userData['firstname'] : '' }}">
                                <input type="hidden" id="first_name_approval" name="first_name_approval" value="{{(isset($userData['lastname'])) ? $userData['lastname'] : '' }}">

                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" id="appStatus" name ="nextStatusId">
                                        @foreach($appDropdown as $appValue)
                                        <option value="{{$appValue['nextStatusId'] . "," . $appValue['isFinalStep']}}" data-status="{{$appValue['nextStatus']}}" data-condition="{{$appValue['condition']}}">{{$appValue['condition']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <span><strong>Comment</strong></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control" id="comments" name="comments"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button class="btn green-meadow saveusers" id="approval_process_data">Submit</button>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="loader"></div>
                    </div>
                </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>              



