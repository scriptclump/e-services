@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Add Employee </div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
            </div>      
            <div class="overlay"></div>      
            <div class="portlet-body">
                <div id="form-wiz" class="portlet-body">
                    <div class="tabbable-line">                                    
                        <form action="#" class="submit_form form-horizontal" id="submit_form" method="get" enctype="multipart/form-data">
                            <h4 ><strong>Basic Information </strong></h4>
                            <div class="form-body">
                                <input type="hidden" id="getuserid" name="user_id" value="" />
                                <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}"> 
                                 <div class="row">   
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Emp Type <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="employment_type" id="employment_type">
                                                <option value="0">Please select</option>
                                                @foreach($employment_types as $value)
                                                <option value="{{$value['value']}}">{{$value['master_lookup_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>           


                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">PreFix <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="prefix" id="prefix">
                                                <option value="0">Please select.</option>
                                                <option value="Mr.">Mr.</option>
                                                <option value="Ms.">Ms.</option>
                                                <option value="Mrs.">Mrs.</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">First Name <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control " name="firstname"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Middle Name</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control " name="middlename"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Last Name <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="lastname" />
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Email Id <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="email_id" id="email_id" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Mobile Number <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="mobile_no"/>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Alt Mobile</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="alternative_mno"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Extension Number</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="landline_ext"/>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">UAN Number</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="uan_number"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Role <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="role_id[]">
                                                <option value="">Please select</option>
                                                @foreach($roles as $role)
                                                <option value="{{$role->role_id}}">{{$role->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Reporting Manager <span class="required">*</span>
                                        </label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="reporting_manager_id" id="reporting_manager_id">
                                                <option value="">Please Select Reporting Manager</option>                                                    
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Department <span class="required">*</span> </label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="department" id="department">
                                                <option value="">Please Select Department</option>
                                                @foreach($getDepartments as $departments)
                                                <option value="{{$departments->value}}">{{$departments->master_lookup_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Designation <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="designation" id="designation">
                                                <option value="">Please Select Designation</option>
                                                @foreach($getDesignations as $designations)
                                                <option value="{{$designations->value}}">{{$designations->master_lookup_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Emp Group <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="emp_group_id" id="emp_group_id">
                                                <option value="0">Please Select Group</option>
                                                @foreach($emp_group as $value)
                                                <option value="{{$value['emp_group_id']}}" >{{$value['group_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Business Unit <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="business_unit_id">
                                                <option value="">Please Select Business Unit</option>
                                                @foreach($buCollection as $businessUnit)
                                                <option value="{{$businessUnit->bu_id}}">{{$businessUnit->bu_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Date Of Birth <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" autocomplete="off" name="dob" id="dob"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Gender <span class="required">*</span> </label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="gender" id="gender">
                                                <option value="0">Please Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Marital Status <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" name="marital_status" id="marital_status">
                                                <option value="0">Please Select Marital Status</option>
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Nationality </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="nationality" id="nationality"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Blood Group</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="blood_group" id="blood_group"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Father Name <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="father_name"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Mother Name <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="mother_name"/>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    <div class="form-group col-md-4" id="hide_img_fld">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <label class="control-label col-md-5">Aadhar Card<span class="required">*</span></label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control " name="aadhar_number" id=aadhar_number/>
                                                <input type="file" id="aadhar_image" name="aadhar_image">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4" id="hide_img_fld">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <label class="control-label col-md-5">Pan Card </label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" id="pan_card_number" name="pan_card_number"/>
                                                <input type="file" id="pan_card_image" name="pan_card_image">
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <h4 ><strong>Current Address </strong></h4>
                            <div class="form-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Address1 </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="cur_add" id="cur_add"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Address2 </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="cur_add2" id="cur_add2"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">City </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="cur_city" id="cur_city"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">State </label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" id="cut_state" name="cut_state">
                                                <option value="">Please Select State</option>    
                                                @foreach($states as $stateVal )
                                                <option value="{{$stateVal['id']}}" >{{$stateVal['state_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5"> Country</label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" id="cur_country" name="cur_country">
                                                <option value="">Please Select Country </option>
                                                @if(isset($countries))
                                                @foreach($countries as $country_value)
                                                <option value="{{$country_value['id']}}">{{$country_value['country_name']}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Zip/Postal Code </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="cur_pincode" id="cur_pincode"/>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <h4 ><strong>Permanent Address </strong></h4>
                            <div class="form-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Address1 </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="per_add" id="per_add"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Address2 </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="per_add2" id="per_add2"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5"> City</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="per_city" id="per_city"/>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">State </label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" id="per_state" name="per_state">
                                                <option value="">Please Select State</option>    
                                                @foreach($states as $stateVal )
                                                <option value="{{$stateVal['id']}}">{{$stateVal['state_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Country </label>
                                        <div class="col-md-7">
                                            <select class="form-control select2me" id="per_country" name="per_country">
                                                <option value="">Please Select Country</option>
                                                @if(isset($countries))
                                                @foreach($countries as $country_value)
                                                <option value="{{$country_value['id']}}">{{$country_value['country_name']}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Zip/Postal Code </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="per_pincode" id="per_pincode"/>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <h4 ><strong>Emergency Info </strong></h4>
                            <div class="form-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Name <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="emergency_name" id="emergency_name"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5"> Relation</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="emergency_relation" id="emergency_relation"/>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5"> Contact1 <span class="required">*</span></label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="emergency_contact_one" id="emergency_contact_one"/>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-md-5">Contact2 </label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" name="emergency_contact_two" id="emergency_contact_two"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top:5px;">
                                <hr />
                                <div class="col-md-12 text-center"> 
                                    <input type="submit" class="btn green-meadow saveusers" value="Save" id="saveusers"/> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
            <div class="loader"></div>           
        </div>
    </div>
</div>
@stop
@section('style')
<style type="text/css">
    .overlay {
        background: #e9e9e9;
        display: none;
        position: absolute;
        top: 0;
        right: 15px;
        bottom: 0;
        left: 0;
        opacity: 0.5;
        z-index:999;
        height: 100%;
    }
    .loader {
        margin:1em auto;
        display: none;
        font-size: 10px;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        position: absolute;
        text-indent: -9999em;
        -webkit-animation: load5 1.1s infinite ease;
        animation: load5 1.1s infinite ease;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        z-index:999;
        top:50%;
        left:50%;
    }
    @-webkit-keyframes load5 {
        0%,
        100% {
            box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        87.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
        }
    }
    @keyframes load5 {
        0%,
        100% {
            box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        87.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
        }
    }

</style>
@stop
@section('script')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@include('includes.validators')
@include('includes.jqx')

<script>
$(document).ready(function () {
    var date_input = $('input[name="dob"]'); //our date input has the name "date"
    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
    var options = {
        format: 'dd-M-yyyy',
        container: container,
        todayHighlight: true,
        endDate: '+0d',
        autoclose: true,
    };
    date_input.datepicker(options).on('changeDate', function (e) {
        // Revalidate the date field
        $('#submit_form').formValidation('revalidateField', 'dob');
    });
});
</script>
<script type="text/javascript">
    
    function addNew(newFilter, filters)
    {
        filters.push(newFilter);
        return filters;
    }
   
     /* Bootsrap From Validations */
    $("#submit_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            firstname: {
                validators: {
                    notEmpty: {
                        message: "First name is required."
                    }, stringLength: {
                        min: 4,
                        max: 20,
                        message: "First name minimum 4 and maximum 20 characters."
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: "First name can consist of characters and spaces only."
                    },
                }
            },
            lastname: {
                validators: {
                    notEmpty: {
                        message: "Last name is required."
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: "Last name can consist of characters and spaces only."
                    }
                }
            },
            father_name: {
                validators: {
                    notEmpty: {
                        message: "Father name is required."
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: "Father name can consist of characters and spaces only."
                    }
                }
            },
            mother_name: {
                validators: {
                    notEmpty: {
                        message: "Mother name is required."
                    },regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: "Mother name can consist of characters and spaces only."
                    }
                }
            },
            email_id: {
                validators: {
                    notEmpty: {
                        message: "Email is required."
                    },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: "Invalid email formate."
                    }
                }
            },
            mobile_no: {
                validators: {
                    notEmpty: {
                        message: "Mobile number is required."
                    },
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    },remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                            url: '/employee/validatemobileno',
                            type: 'POST',
                            data: function (validator, $field, value) {
                                return  {
                                    mobile_no : value,
                                    aadhar_number: $("#aadhar_number").val(),
                                    employment_type : $("#employment_type").val()
                                };
                            },
                            delay: 1000, // Send Ajax request every 1 seconds
                            message: "Mobile number already exists. "
                        }
                }
            },
            reporting_manager_id: {
                validators: {
                    callback: {
                        message: "Reporting manager is required.",
                        callback: function (value, validator) {
                            return value > 0;
                        }
                    }
                }
            },
            business_unit_id: {
                validators: {
                    callback: {
                        message: "Business unit is required.",
                        callback: function (value, validator) {
                            return value > 0;
                        }
                    }
                }
            },
            'role_id[]': {
                validators: {
                    callback: {
                        message: "Role is required.",
                        callback: function (value, validator) {
                            $('#submit_form').data('bootstrapValidator').resetField('reporting_manager_id');
                            return value != null;
                        }
                    }
                }
            },
            employment_type: {
                validators: {
                    callback: {
                        message: "Employment type is required.",
                        callback: function (value, validator) {
                            return value > 0;
                        }
                    }
                }
            },
            emp_group_id:
            {
                validators: {
                    callback: {
                        message: "Employee group is required.",
                        callback: function (value, validator) {
                            return value > 0;
                        }
                    }
                }
            },
            // aadhar_number: {
            //     validators: {
            //         notEmpty: {
            //             message: "Aadhar number is required."
            //         },
            //         stringLength: {
            //             min: 12,
            //             max: 12,
            //             message: "Aadhar must have 12 digits."
            //         },
            //         remote: {
            //             headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
            //             url: '/employee/validateaadharno',
            //             type: 'POST',
            //             data: function (validator, $field, value) {
            //                 return  {
            //                     aadhar_number: value,
            //                     employment_type : $("#employment_type").val()
            //                 };
            //             },
            //             delay: 1000, // Send Ajax request every 1 seconds
            //             message: "Aadhar number already exists."
            //         }

            //     }
            // },
            // aadhar_image: {
            //     validators: {
            //         file: {
            //             extension: 'pdf,doc,docx,jpeg,png,jpg,JPG',
            //             type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
            //             maxSize: 2048 * 1024,
            //             message: 'The selected file is not valid'
            //         },
            //         notEmpty: {
            //             message: "Please choose the file."
            //         },
            //     }
            // },

            dob: {
                validators: {
                    notEmpty: {
                        message: 'Date of birth is required'
                    }
                }
            },
            prefix:
                    {
                        validators: {
                            callback: {
                                message: "PreFix is required.",
                                callback: function (value, validator) {
                                    if (isNaN(value) == false)
                                    {
                                        return value > 0;
                                    }
                                    return true;

                                }
                            }
                        }
                    },
            department:
                    {
                        validators: {
                            callback: {
                                message: "Department is required.",
                                callback: function (value, validator) {
                                    return value > 0;
                                }
                            }
                        }
                    },
            designation:
                    {
                        validators: {
                            callback: {
                                message: "Designation is required.",
                                callback: function (value, validator) {
                                    return value > 0;
                                }
                            }
                        }
                    },
            gender: {
                validators: {
                    callback: {
                        message: "Gender is required.",
                        callback: function (value, validator) {
                            if (isNaN(value) == false)
                            {
                                return value > 0;
                            }
                            return true;
                        }
                    }
                }
            },
            marital_status: {
                validators: {
                    callback: {
                        message: "Marital status is required.",
                        callback: function (value, validator) {
                            if (isNaN(value) == false)
                            {
                                return value > 0;
                            }
                            return true;
                        }
                    }
                }
            },
            blood_group: {
                validators: {
                    regexp: {
                        regexp: /^[a-z()+-\s]+$/i,
                        message: "Blood group can consist of characters only."
                    }
                }
            },
            emp_group_id: {
                validators: {
                    callback: {
                        message: "Employee group is required.",
                        callback: function (value, validator) {
                            return value > 0;
                        }
                    }
                }
            },
            cur_city: {
                validators: {
                    regexp: {
                        regexp: '^[A-Za-z]*$',
                        message: "Please enter letters only."
                    }
                }
            },
            cur_pincode:{
                validators: {
                    regexp: {
                        regexp: '^[0-9]{6,6}$',
                        message: "Please enter 6 digits."
                    }  
                }
            },

            per_city: {
                validators: {
                    regexp: {
                        regexp: '^[A-Za-z]*$',
                        message: "Please enter letters only."
                    }
                }
            },
            per_pincode:{
                validators: {
                    regexp: {
                        regexp: '^[0-9]{6,6}$',
                        message: "Please enter 6 digits."
                    } 
                }
            },
            uan_number:{
                validators: {
                    regexp: {
                        regexp: '^[0-9]{12,12}$',
                        message: "Please enter 12 digits."
                    }  
                }
            },
            emergency_contact_one: {
                validators: {
                    notEmpty: {
                        message: "Mobile number is required."
                    },
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    },
                }
            },
            emergency_contact_two: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    },
                }
            },
            emergency_name: {
                validators: {
                    notEmpty: {
                        message: "Name is required."
                    },
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    },
                }
            },
            emergency_relation: {
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    },
                }
            },
            alternative_mno: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    },
                }
            },
            landline_ext: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{3,4}$',
                        message: "Please enter 3 or 4 digits only."
                    },
                }
            },
            // pan_card_number: {
            //     validators: {
            //         regexp: {
            //             regexp: '^[A-Z0-9]{10}$',
            //             message: "Please enter correct pan number."
            //         },
            //     }
            // },
            // pan_card_image: {
            //     validators: {
            //         file: {
            //             extension: 'pdf,doc,docx,jpeg,png,jpg,JPG',
            //             type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
            //             maxSize: 2048 * 1024,
            //             message: 'The selected file is not valid'
            //         },
            //     }
            // }
        }
    }).on('change', '[name="employment_type"]', function(e) {
            $('#submit_form').formValidation('revalidateField', 'aadhar_number');
            $('#submit_form').formValidation('revalidateField', 'mobile_no');
    }).on('change', '[name="aadhar_number"]', function(e) {
            $('#submit_form').formValidation('revalidateField', 'employment_type');
            $('#submit_form').formValidation('revalidateField', 'mobile_no');
    }).on('change', '[name="mobile_no"]', function(e) {
            $('#submit_form').formValidation('revalidateField', 'employment_type');
            $('#submit_form').formValidation('revalidateField', 'aadhar_number');
    }).on('success.form.bv', function (event) {
        event.preventDefault();
        console.log($('#submit_form'));
        var form_data = '';
        var getuserId = '';
        var form_data = new FormData($('#submit_form')[0]);
        console.log(form_data);
        $.ajax({
            headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
            url: '/employee/saveusers',
            data: form_data,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function ()
            {
                $('[class="loader"]').show();
                $(".overlay").show();
            },
            complete: function ()
            {
                $('[class="loader"]').hide();
                $(".overlay").hide();
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.status) {
                    $("#getuserid").val(data.user_id);
                    $("#email_error").html('');
                    $('a[href="#tab22"]').tab('show');
                    $('#email_id').prop('readonly', true);
                }

                $('#flass_message').text(data.message);
                $('div.alert').show();
                $('div.alert').removeClass('hide');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                $('html, body').animate({scrollTop: '0px'}, 500);
                window.location = "/employee/dashboard";
            },
            error: function (error) {
                console.log(error);
                alert("Email id is already existed.");
            }
        });
    });

    $('[name="role_id[]"]').change(function () {
        $('[name="reporting_manager_id"]').empty();
        $('[name="reporting_manager_id"]')
                .append($("<option></option>")
                        .attr("value", '')
                        .text('Please Select...'));
        $('[name="reporting_manager_id"]').select2({placeholder: "Please Select..."});
        var token = $("#csrf_token").val();
        var datastring = {'role_id': $(this).val(), "emp_id": 0};
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/employee/getreportingmanagers',
            data: datastring,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                var data = JSON.stringify(response);
                data = JSON.parse(data);
                if (data.length > 0)
                {
                    $.each(data, function (key, value) {
                        $('[name="reporting_manager_id"]')
                                .append($("<option></option>")
                                        .attr("value", value.user_id)
                                        .text(value.name));
                    });
                }
            }
        });
    });

</script>
@stop