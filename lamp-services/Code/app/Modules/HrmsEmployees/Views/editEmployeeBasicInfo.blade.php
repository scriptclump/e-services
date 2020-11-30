<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="tab11"> 
    <div class="basicInfoOverlay"></div>
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
                    <label class="control-label col-md-5"><strong>Personal Email: </strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_email_id">
                            {{(isset($userData['email_id'])) ? $userData['email_id'] : '' }}
                        </p>
                    </div>
                </div> 
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Mobile Number: </strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_mno">
                            {{(isset($userData['mobile_no'])) ? $userData['mobile_no'] : '' }}
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Alt Mobile: </strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_alternative_mno">
                            {{(isset($userData['alternative_mno'])) ? $userData['alternative_mno'] : '' }}
                        </p>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Ext Number: </strong></label>
                    <div class="col-md-7">
                    @if($userData['landline_ext'] == 0)
                        <p class="form-control-static" id="preview_extension_no">
                        </p>
                        @else
                         <p class="form-control-static" id="preview_extension_no">
                            {{(isset($userData['landline_ext'])) ? $userData['landline_ext'] : '' }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>UAN Number: </strong></label>
                    <div class="col-md-7">
                    @if($userData['uan_number'] == 0)
                     <p class="form-control-static" id="preview_uan_no">
                    </p>
                    @else
                    <p class="form-control-static" id="preview_uan_no">
                            {{(isset($userData['uan_number'])) ? $userData['uan_number'] : '' }}
                    </p>
                    @endif
                        
                    </div>
                </div> 
            </div> 
            <div class="row">  
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Role: </strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_role">{{$userData['role_name']}}</p>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Manager: </strong></label>
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
            

            <div class="row">
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Marital Status:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_marital">
                            @foreach($marital as $value)
                            @if($userData['marital_status'] == $value)
                            {{$value}}
                            @endif
                            @endforeach
                        </p>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Nationality:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_nationality">
                            {{(isset($userData['nationality'])) ? $userData['nationality'] : '' }} </p>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Blood  Group:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_blood_group">
                            {{(isset($userData['blood_group'])) ? $userData['blood_group'] : '' }} </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Father Name:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_fathername">{{(isset($userData['father_name'])) ? $userData['father_name'] : '' }} </p>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Mother Name:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_mothername">{{(isset($userData['mother_name'])) ? $userData['mother_name'] : '' }} </p>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Grade:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_grade">{{(isset($userData['grade'])) ? $userData['grade'] : '' }} </p>
                    </div>
                </div>
            </div>

            <div class="row ">
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Password:</strong></label>
                    <div class="col-md-7">
                       <p class="form-control-static"> <a data-toggle="modal" data-target="#changePasswordModal" id="clickChangePassword" >Change Password</a> </p> 
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Aadhar Card:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_aadhar">{{$userData['aadhar_number']}}</p>
                        @if(isset($userData['aadhar_image']) && ($userData['aadhar_image']!=''))
                        <a href="{{$userData['aadhar_image']}}" id="preview_aadhar_image" target=_blank>Doc Link</a>
                        @endif

                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5"><strong>Pan Card:</strong></label>
                    <div class="col-md-7">
                        <p class="form-control-static" id="preview_pan_no">
                            {{(isset($userData['pan_card_number'])) ? $userData['pan_card_number'] : '' }}</p>
                        @if(isset($userData['pan_card_image']) && ($userData['pan_card_image']!=''))
                        <a href="{{$userData['pan_card_image']}}" id="preview_pan_image" target=_blank>Doc Link</a>
                        @endif
                    </div>
                </div>
            </div>

        </div>       
    </div>




    <div id="edit_basic_info"  style="display:none">                         
        <form action="#" class="submit_form form-horizontal" id="submit_form" method="get" enctype="multipart/form-data">
            <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
            <input type="hidden" id="user_id" name="emp_id" value="{{(isset($userData['emp_id'])) ? $userData['emp_id'] : '' }}">
            <div class="form-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Emp Type <span class="required">*</span></label>
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="employment_type" id="employment_type">
                            <option value="0">Please select</option>
                                    @foreach($employment_types as $value)
                                    @if($userData['employment_type'] == $value['value'])
                                    <option value="{{$value['value']}}" selected>{{$value['master_lookup_name']}}</option>
                                    @else
                                    <option value="{{$value['value']}}">{{$value['master_lookup_name']}}</option>
                                    @endif
                                    @endforeach
                            </select>
                            @else
                                @foreach($employment_types as $value)
                                    @if($userData['employment_type'] == $value['value'])
                                        <input type="hidden" class="form-control" name="employment_type" value="{{$value['value']}}" />
                                        <input type="text" class="form-control" readonly="true" value="{{$value['master_lookup_name']}}">
                                    @endif
                                @endforeach
                            @endif
                                    
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> PreFix  <span class="required">*</span></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" name="prefix" id="prefix">
                                <option value="0">Please select.</option>
                                @foreach($prefix as $value)
                                @if($userData['prefix'] == $value)
                                <option value="{{$value}}" selected>{{$value}}</option>
                                @else
                                <option value="{{$value}}">{{$value}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">First Name <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="firstname" name="firstname" value="{{(isset($userData['firstname'])) ? $userData['firstname'] : '' }}"/>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Middle Name</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="middlename" name="middlename" value="{{(isset($userData['middlename'])) ? $userData['middlename'] : '' }}"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Last Name <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="lastname" name="lastname" value="{{(isset($userData['lastname'])) ? $userData['lastname'] : '' }}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Employee Code <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" readonly="true" class="form-control" id="emp_code" name="emp_code" value="{{(isset($userData['emp_code'])) ? $userData['emp_code'] : '' }}"/>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Office Email <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="office_email" value="{{(isset($userData['office_email'])) ? $userData['office_email'] : '' }}" readonly=""/>
                        </div>
                    </div>                            
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Personal Email <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="email_id" value="{{(isset($userData['email_id'])) ? $userData['email_id'] : '' }}"/>
                            <div  id="email_error" style="color: #a94442;">
                            </div>
                        </div>
                    </div> 
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Mobile Number <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="mobile_no" value="{{(isset($userData['mobile_no'])) ? $userData['mobile_no'] : '' }}" />
                        </div>
                    </div> 
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Alt Mobile</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="alternative_mno" value="{{(isset($userData['alternative_mno'])) ? $userData['alternative_mno'] : '' }}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Ext Number</label>
                        <div class="col-md-7">
                            @if($userData['landline_ext'] == 0)
                            <input type="text" class="form-control" name="landline_ext" id="landline_ext" value=""/>
                            @else
                            <input type="text" class="form-control" name="landline_ext" value="{{(isset($userData['landline_ext'])) ? $userData['landline_ext'] : '' }}" />
                            @endif
                        </div>
                    </div>  
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">UAN Number</label>
                        <div class="col-md-7">
                        @if($userData['uan_number'] == 0)
                            <input type="text" class="form-control" name="uan_number" value="" />
                            
                            @else
                            <input type="text" class="form-control" name="uan_number" value="{{(isset($userData['uan_number'])) ? $userData['uan_number'] : '' }}" />
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <?php $roleId = (isset($userData['role_id'])) ? explode(',', $userData['role_id']) : ''; ?>
                        <?php // echo "<pre>";print_R($roleId);die; ?>
                        <label class="control-label col-md-5"> Role <span class="required">*</span></label>
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="role_id[]">
                                <option value="">Please Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{$role->role_id}}" <?php echo in_array($role->role_id, $roleId) ? 'selected' : '' ?>>{{$role->name}}</option>
                                @endforeach
                            </select> 
                            @else
                                @foreach($roles as $role)
                                    @if($role->role_id == $roleId[0])
                                    <input type="hidden" class="form-control" name="role_id[]" value="{{$role->role_id}}" />
                                    <input type="text" class="form-control" readonly="true" value="{{$role->name}}">
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Manager<span class="required">*</span>
                        </label>
                        <input type="hidden" id="reporting_manager_data" value="{{ $userData['reporting_manager_id'] }}" />
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="reporting_manager_id">
                                <option value="">Please Select Manager</option>
                                @if($reportingMangers!="")
                                @foreach($reportingMangers as $manager)
                                <option value="{{$manager->user_id}}" {{ ($userData['reporting_manager_id'] == $manager->user_id) ? 'selected = "true"' : '' }}>{{$manager->name}}</option>
                                @endforeach
                                @endif
                            </select>
                            @else
                            @if($reportingMangers!="")
                                @foreach($reportingMangers as $manager)
                                @if($userData['reporting_manager_id'] == $manager->user_id)
                                    <input type="hidden" class="form-control" name="reporting_manager_id" value="{{$manager->user_id}}" />
                                    <input type="text" class="form-control" readonly="true" value="{{$manager->name}}">
                                @endif
                                @endforeach
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Department<span class="required">*</span> </label>
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="department" id="department">
                             <option value="">Please Select Department</option>
                                    @foreach($getDepartments as $departments)
                                    <option value="{{$departments->value}}" {{ ($userData['department'] == $departments->value) ? 'selected = "true"' : '' }}>{{$departments->master_lookup_name}}</option>
                                    @endforeach
                            </select>
                            @else
                                @foreach($getDepartments as $departments)    @if($userData['department'] == $departments->value)
                                    <input type="hidden" class="form-control" name="department" value="{{$departments->value}}" />
                                    <input type="text" class="form-control" readonly="true" value="{{$departments->master_lookup_name}}">
                                @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Designation<span class="required">*</span></label>
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="designation">
                                <option value="">Please Select Designation</option>
                                @foreach($getDesignations as $designations)
                                <option value="{{$designations->value}}" {{ ($userData['designation'] == $designations->value) ? 'selected = "true"' : '' }}>{{$designations->master_lookup_name}}</option>
                                @endforeach
                            </select>
                            @else
                                @foreach($getDesignations as $designations)     @if($userData['designation'] == $designations->value)
                                    <input type="hidden" class="form-control" name="designation" value="{{$designations->value}}" />
                                    <input type="text" class="form-control" readonly="true" value="{{$designations->master_lookup_name}}">
                                @endif
                                @endforeach
                            @endif
                        </div>
                    </div> 
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Emp Group<span class="required">*</span></label>
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="emp_group_id" id="emp_group_id">
                            <option value="0">Please select...</option>
                                @foreach($emp_group as $value)
                                @if($userData['emp_group_id'] == $value['emp_group_id'])
                                <option value="{{$value['emp_group_id']}}" selected>{{$value['group_name']}}</option>
                                @else
                                <option value="{{$value['emp_group_id']}}" >{{$value['group_name']}}</option>
                                @endif
                                @endforeach
                            </select>
                            @else
                                @foreach($emp_group as $value)
                                @if($userData['emp_group_id'] == $value['emp_group_id'])
                                    <input type="hidden" class="form-control" name="emp_group_id" value="{{$value['emp_group_id']}}" />
                                    <input type="text" class="form-control" readonly="true" value="{{$value['group_name']}}">
                                @endif
                                @endforeach
                            @endif
                        </div>
                    </div> 
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Business Unit <span class="required">*</span></label>
                        <div class="col-md-7">
                            @if($editColAccess == '1')
                            <select class="form-control select2me" name="business_unit_id">
                            <option value="">Please Select Business Unit</option>
                                @foreach($buCollection as $businessUnit)
                                <option value="{{$businessUnit->bu_id}}" {{ ($userData['business_unit_id'] == $businessUnit->bu_id) ? 'selected = "true"' : '' }}>{{$businessUnit->bu_name}}</option>
                                @endforeach
                            </select>
                            @else
                                @foreach($buCollection as $businessUnit)
                                @if($userData['business_unit_id'] == $businessUnit->bu_id)
                                    <input type="hidden" class="form-control" name="business_unit_id" value="{{$businessUnit->bu_id}}" />
                                    <input type="text" class="form-control" readonly="true" value="{{$businessUnit->bu_name}}">
                                @endif
                                @endforeach
                            @endif
                        </div>
                    </div>                                                   
                </div>
                <div class="row">
                <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Date Of Joining</label>
                        <div class="col-md-7">
                        @if($userData['doj'] == 0000-00-00)
                            <input type="text" class="form-control" name="doj" id="doj" value="" readonly/>
                            @else
                             <input type="text" class="form-control" name="doj" id="doj" value="{{(isset($userData['doj'])) ? $userData['doj'] : '' }}" readonly/>
                            @endif
                        </div>
                </div>                 
                <div class="form-group col-md-4">
                    <label class="control-label col-md-5">Date Of Birth <span class="required">*</span></label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="dob" id="dob" autocomplete="off" value="{{(isset($userData['dob'])) ? $userData['dob'] : '' }}" />
                    </div>
                </div>
                <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Gender<span class="required">*</span> </label>
                        <div class="col-md-7">
                            <select class="form-control select2me" name="gender" id="gender">
                                <option value="0">Please select.</option>
                                @foreach($gender as $value)
                                @if($userData['gender'] == $value)
                                <option value="{{$value}}" selected>{{$value}}</option>
                                @else
                                <option value="{{$value}}">{{$value}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                </div>
                </div>

                <div class="row">
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Marital Status <span class="required">*</span></label>
                        <div class="col-md-7">
                            <select class="form-control select2me" name="marital_status" id="marital_status">
                                <option value="0">Please select.</option>
                                @foreach($marital as $value)
                                @if($userData['marital_status'] == $value)
                                <option value="{{$value}}" selected>{{$value}}</option>
                                @else
                                <option value="{{$value}}">{{$value}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Nationality </label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="nationality" id="nationality" value="{{(isset($userData['nationality'])) ? $userData['nationality'] : '' }}" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Blood  Group</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="blood_group" id="blood_group" value="{{(isset($userData['blood_group'])) ? $userData['blood_group'] : '' }}"  />
                        </div>
                    </div>
                </div> 
                

                <div class="row"> 
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5">Father Name <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="father_name" value="{{(isset($userData['father_name'])) ? $userData['father_name'] : '' }}"  />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Mother Name <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="mother_name" value="{{(isset($userData['mother_name'])) ? $userData['mother_name'] : '' }}" />
                        </div>
                    </div>
                    @if($editColAccess == 1)
                    <div class="form-group col-md-4">
                        <label class="control-label col-md-5"> Grade </label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="grade" value="{{(isset($userData['grade'])) ? $userData['grade'] : '' }}"/>
                        </div>
                    </div>
                    @endif

                </div>
                <div class="row"> 
                    <div class="form-group col-md-4" id="hide_img_fld">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <label class="control-label col-md-5">Aadhar Card<span class="required">*</span></label>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="aadhar_number" id="aadhar_number" value="{{$userData['aadhar_number'] }}" readonly="true" />
                                @if(isset($userData['aadhar_image']) && ($userData['aadhar_image']!=''))
                                    <?php
                                    $extension = pathinfo($userData['aadhar_image'], PATHINFO_EXTENSION);
                                    ?>
                                    @if($extension=='pdf')
                                        <a href={{$userData['aadhar_image']}} class="timeline-badge-userpic" id="edit_aadhar_image"  src="{{(isset($userData['aadhar_image'])) ? $userData['aadhar_image'] : '' }}" height="50px" width="80px"> <i class="fa fa-download"></i>  </a>
                                    @else
                                       <!-- <input type="text" style="display: none" id="edit_aadhar_image" name="edit_aadhar_image" value="{{(isset($userData['aadhar_image'])) ? $userData['aadhar_image'] : '' }}">
                                       <img class="timeline-badge-userpic" id="show_edit_aadhar" name="show_edit_aadhar"  src="{{(isset($userData['aadhar_image'])) ? $userData['aadhar_image'] : '' }}"  height="50px" width="80px"> -->
                                    @endif
                                     
                                @endif
                                <!-- <a class="profile-edit" id="edit_aadhar_file" name="edit_aadhar_file" href="#"> <i class="fa fa-pencil" style="color:#fff;"></i> </a>
                                <input type="file" id="file_open_aadhar"  value="{{(isset($userData['aadhar_image'])) ? $userData['aadhar_image'] : '' }}"  name="aadhar_file" style="display:none"> -->
                            </div> 
                        </div>
                    </div> 
                    <div class="form-group col-md-4" id="hide_img_fld">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <label class="control-label col-md-5">Pan Card</label>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="pan_card_number" id="pan_card_number" value="{{$userData['pan_card_number'] }}" readonly="true" />
                                @if(isset($userData['pan_card_image']) && ($userData['pan_card_image']!=''))
                                    <?php
                                    $extension = pathinfo($userData['pan_card_image'], PATHINFO_EXTENSION);
                                    ?>
                                    @if($extension=='pdf')
                                       <a href={{$userData['pan_card_image']}} class="timeline-badge-userpic" id="edit_pan_card_image"  src="{{(isset($userData['pan_card_image'])) ? $userData['pan_card_image'] : '' }}" height="50px" width="80px"> <i class="fa fa-download"></i>  </a>
                                    @else
                                      <img class="timeline-badge-userpic" id="edit_pan_card_image"  src="{{(isset($userData['pan_card_image'])) ? $userData['pan_card_image'] : '' }}" height="50px" width="80px">
                                    @endif
                                     
                                @endif
                                <!-- <a class="profile-edit" id="edit_pan_card_file" href="#"> <i class="fa fa-pencil" style="color:#fff;"></i> </a>
                                <input type="file" id="file_open_pan" name="pan_file" style="display:none"> -->
                            </div> 
                        </div>
                    </div> 
                </div>

                    <hr />
                    <div class="col-md-12 text-center"> 
                        <input type="submit" class="btn green-meadow saveusers" value="Update" id="saveusers"/> 
                        <input type="button" class="btn green-meadow" value="Cancel" id="cancel1" /> 
                    </div>
                    <div class="basicInfoLoader"></div>
            </div>
        </form>
    </div>


     <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="wizardCode" aria-hidden="true" style="display: none;">
                <div class="modal-dialog wide">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="modal-title" id="wizardCode">Change Password</h3>
                        </div>
                        <div class="modal-body" >
                            <div class="welcome">
                              <form id="changePasswordForm">
                                     <div class="row form-group">
                                       <label class="control-label col-md-4">Old Password:</label>
                                       <div class="col-md-8">
                                         <input type="password" name="oldpassword" class="form-control"></div>
                                      </div>
                                      <div class="row form-group" id="reset_div">
                                         <label class="control-label col-md-4 new_password">New Password:</label>
                                         <div class="col-md-8">
                                            <input type="password" id="newpassword" name="newpassword"  class="form-control"  >
                                            <i id="reset_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="newpassword" style="display: block;"></i>
                                            <p id="new_pass_msg" style="display: none;color: #a94442;margin-right: 160px;font-size:small">New password cannot be the same as default password.</p>
                                        </div>
                                      </div>    

                                      <div class="row form-group" id="confirm_div">
                                       <label class="control-label col-md-4 confirm_password">Confirm Password:</label>
                                       <div class="col-md-8">
                                         <input type="password" name="confirmpassword" id="confirmpassword"  class="form-control">
                                         <i id="confirm_pass_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="confirmpassword" style="display: block;"></i>
                                            <p id="confirm_pass_msg" style="display: none;color: #a94442;margin-right: 160px;font-size:small">New password cannot be the same as default password.</p>
                                     </div>
                                      </div>

                                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                <input type="hidden" id = "empid_update_password" name="empid_update_password" value="{{$userData['emp_id']}}" />   
                              </form>
                                <div class="margiv-top-10" align="center">          
                                    <input type="button"  id="change_password_button" class="btn green-meadow" value = "Change Password" />
                                </div>    
                            </div>
                            
                        </div>
                    </div>   
                </div>
    </div>
</div>
