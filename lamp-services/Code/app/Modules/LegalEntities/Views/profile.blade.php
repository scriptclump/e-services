@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> PROFILE </div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span>
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        </div>
      </div>
      <div class="portlet-body">
        <div class="tab-pane" id="tab_1_3">
          <div class="row profile-account">
          <form id="getImage">
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <div class="col-md-2">
              <ul class="ver-inline-menu tabbable margin-bottom-10">
                <li>
                @if(isset($user_data->profile_picture) && !empty($user_data->profile_picture))
                <img class="mg-responsive pic-bordered" style="width:100%; height:100% " id="profile_pic" src="{{ $user_data->profile_picture }}" alt /> 
                @else
                <img class="mg-responsive pic-bordered" style="width:100%; height:100% " id="profile_pic" src="{{ URL::asset('/img/avatar5.png') }}" alt /> 
                @endif
                <a class="profile-edit" id="edit_file" href="#"> <i class="fa fa-pencil" style="color:#fff;"></i> </a> </li><input type="file" id="file_open" name="file" style="display:none">
                <li class="active"> <a data-toggle="tab" href="#tab_1-1"> Basic Information</a> 
<a href="javascript:;" id="edit1" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                <span class="after"> </span> </li>
                <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{!! $legal_entity_id !!}">
                @if(isset($master_user) && $master_user == 0)

                @else
                <li> <a data-toggle="tab" href="#tab_2-2">Documents </a> 
<a href="javascript:;" id="edit3" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default"><i class="fa fa-pencil"></i>
                    </a>
                </li>
                <li> <a data-toggle="tab" href="#tab_3-3">Business Information</a> 
                    <a href="javascript:;" id="edit2" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default">
                        <i class="fa fa-pencil"></i>
                    </a>
                </li>
                <li> <a data-toggle="tab" href="#tab_4-4">Bank Information </a> 
                <a href="javascript:;" id="edit4" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default">
                        <i class="fa fa-pencil"></i>
                    </a>
                </li>
                @endif                
              </ul>
            </div>
            </form>
            <div class="col-md-10">
              <div class="tab-content">
                <div id="tab_1-1" class="tab-pane active">
                <div id="default_show1" style="margin-top:11px;">
                
                 <div class="row form-group">
                 
                     <label class="control-label col-md-2">First Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_firstname">@if(isset($user_data->firstname)) {!! $user_data->firstname !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Last Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_lastname">@if(isset($user_data->lastname)) {!! $user_data->lastname !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Email :</label>
                     <div class="col-md-10">
                      <p class="form-control-static">@if(isset($user_data->email_id)) {!! $user_data->email_id !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Mobile No :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_mobile_no">@if(isset($user_data->mobile_no)) {!! $user_data->mobile_no !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Password :</label>
                     <div class="col-md-10">
                       <p class="form-control-static"> <a data-toggle="modal" data-target="#changePasswordModal" id="clickChangePassword" >Change Password</a> </p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Email Subscription :</label>
                      <div class="col-md-10">
                     @if(isset($preference->email_subscription) && $preference->email_subscription == 1)
                       <p class="form-control-static" id="p_email_sub"> Yes</p> 
                     @elseif(isset($preference->email_subscription) && $preference->email_subscription == 0)
                        <p class="form-control-static" id="p_email_sub"> No</p> 
                     @else
                       <p class="form-control-static" id="p_email_sub"> Yes</p>
                      @endif
                       </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">SMS Subscription :</label>
                     <div class="col-md-10">
                     @if(isset($preference->sms_subscription) && $preference->sms_subscription == 1)
                       <p class="form-control-static" id="p_sms_sub"> Yes</p> 
                    @elseif(isset($preference->sms_subscription) && $preference->sms_subscription == 0)
                        <p class="form-control-static" id="p_sms_sub"> No</p> 
                    @else
                        <p class="form-control-static" id="p_sms_sub"> Yes</p> 
                    @endif
                    </div>
                </div>
                </div>     
                <div id="show_onedit1" style="display:none">
                   <div class="col-md-12 actions " style="margin-bottom:10px;">
                </div>
                   <form id="BasicInfo">
                   <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{!! $legal_entity_id !!}">
                   <div class="row form-group">
                     <label class="control-label col-md-2">First Name :</label>
                     <div class="col-md-10">
                       <input type="text" id="firstname" name="firstname" value="@if(isset($user_data->firstname)){!! $user_data->firstname !!} @endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Last Name :</label>
                     <div class="col-md-10">
                       <input type="text" id="lastname" name="lastname" value="@if(isset($user_data->lastname)){!! $user_data->lastname !!}@endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Email :</label>
                     <div class="col-md-10">
                      <input type="text" disabled="true" class="form-control" value="@if(isset($user_data->email_id)) {!! $user_data->email_id !!} @endif" /> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Mobile No :</label>
                     <div class="col-md-10">
                       <input type="text" id="mobile_no" name="mobile_no" value="@if(isset($user_data->mobile_no)){!! $user_data->mobile_no !!}@endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Password :</label>
                     <div class="col-md-10">
                       <p class="form-control-static"> <a data-toggle="modal" data-target="#changePasswordModal" id="clickChangePassword" >Change Password</a> </p> </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">Email Subscription :</label>
                     <div class="col-md-10">
                     <label class="mt-checkbox mt-checkbox-outline" style="margin-top: 9px;">
                     @if(isset($preference->email_subscription) && $preference->email_subscription == 1)
                       <input type="checkbox" value="1" name="email_sub" checked="true" id="email_sub" />
                       @elseif(isset($preference->email_subscription) && $preference->email_subscription == 0)
                          <input type="checkbox" value="1" name="email_sub" id="email_sub" />
                       @else
                       <input type="checkbox" value="1" checked="true" name="email_sub" id="email_sub" />
                      @endif
                        <span></span>
                       </label>
                       </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">SMS Subscription :</label>
                     <div class="col-md-10">
                        <label class="mt-checkbox mt-checkbox-outline" style="margin-top: 9px;">
                          @if(isset($preference->sms_subscription) && $preference->sms_subscription == 1)
                          <input type="checkbox" value="1" name="sms_sub" checked="true" id="sms_sub" />
                          @elseif(isset($preference->sms_subscription) && $preference->sms_subscription == 0)
                          <input type="checkbox" value="1" name="sms_sub" id="sms_sub" />
                          @else
                          <input type="checkbox" value="1" checked="true" name="sms_sub" id="sms_sub" />
                          @endif
                          <span></span>
                    </label>
                       </div>
                    </div>
                   </form>
                     <div class="margiv-top-10" align="center"> 
                       <a href="javascript:;" class="btn green" id="save_basic_info"> Save Changes </a> 
                       <a href="javascript:;" id="cancel1" class="btn default"> Cancel </a> 
                    </div>
                  </div>
                </div>
                <div id="tab_2-2" class="tab-pane">
                <div id="default_show3">
               
                <div class="row">
                  <div class="col-md-2 box-outer">
                  <?php
                     $extn = '';
                     
                        if(isset($pan_file->doc_name))
                        {    
                          $ext1 = strrchr($pan_file->doc_name,".");
                          //
                          $ext1 = explode(".", $ext1);
                          if(isset($ext1[1]))
                          {
                          $extn = $ext1[1];
                        }
                      }
                      if(isset($pan_file->doc_url)){
                      $pan_url = URL::to($pan_file->doc_url);
                    }
                    ?>
                    @if(isset($pan_file->doc_url))
                    @if($extn == 'jpg' || $extn == 'png' || $extn == 'jpeg')
                    <a href="@if(isset($pan_file->doc_url)){{$pan_file->doc_url}}@endif" target="blank"><img src="@if(isset($pan_file->doc_url)){{$pan_file->doc_url}}@endif"  width="150px" height="200px"></a>
                    @else
                    <iframe width="150px" height="200px" src='https://docs.google.com/viewer?url={{$pan_url}}&embedded=true' frameborder="0px"></iframe>
                    @endif
                    <p id="doc_name" >{{$pan_file->doc_name}}</p>
                    @else                                
                    <a href="#"><img src="{{ URL::asset('/uploads/LegalEntities/noImage.jpg') }}"  width="150px" height="200px"></a>
                    @endif
                  </div>
                  <div class="col-md-2 box-outer">
                  <?php
                     $extn2 = '';
                        if(isset($tin_file->doc_name))
                        {    
                          $ext1 = strrchr($tin_file->doc_name,".");
                          //
                          $ext1 = explode(".", $ext1);
                          if(isset($ext1[1]))
                          {
                          $extn2 = $ext1[1];
                        }
                      }
                      if(isset($tin_file->doc_url)){
                      $tin_url = URL::to($tin_file->doc_url);
                      }
                    ?>
                    @if(isset($tin_file->doc_url))
                    @if($extn2 == 'jpg' || $extn2 == 'png' || $extn2 == 'jpeg')
                    <a href="@if(isset($tin_file->doc_url)){{$tin_file->doc_url}}@endif" target="blank"><img src="{{$tin_file->doc_url}}"  width="150px" height="200px"></a>
                    @else
                    <iframe width="150px" height="200px" src='https://docs.google.com/viewer?url={{$tin_url}}&embedded=true' frameborder="0px"></iframe>
                    @endif
                    <p id="doc_name" class="span2">@if(isset($tin_file->doc_name)){{$tin_file->doc_name}}@endif</p>
                    @else                                
                    <a href="#"><img src="{{ URL::asset('/uploads/LegalEntities/noImage.jpg') }}"  width="150px" height="200px"></a>
                    @endif
                  </div>
                  </div>
                </div>
                <div id="show_onedit3" style="display:none">
                <form id="DocumentsForm" role="form" method="POST"  files="true" enctype ="multipart/form-data">
                <div class="row">
                  <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{!! $legal_entity_id !!}">             
                   <div class="col-md-2 box-outer">
                   @if(isset($pan_file->doc_url))
                    @if($extn == 'jpg' || $extn == 'png' || $extn == 'jpeg')
                    <a href="@if(isset($pan_file->doc_url)){{$pan_file->doc_url}}@endif" target="blank"><img src="@if(isset($pan_file->doc_url)){{$pan_file->doc_url}}@endif"  width="150px" height="200px"></a>
                    @else
                    <iframe width="150px" height="200px" src='https://docs.google.com/viewer?url={{$pan_url}}&embedded=true' frameborder="0px"></iframe>
                    @endif
                    <p id="doc_name" >@if(isset($pan_file->doc_name)){{$pan_file->doc_name}}@endif</p>
                  @else                                
                    <a href="#"><img src="{{ URL::asset('/uploads/LegalEntities/noImage.jpg') }}"  width="150px" height="200px"></a>
                    @endif
                                       <div class="fileinput fileinput-new" data-provides="fileinput">
                                          <span class="btn default btn-file btn green-meadow span3" style="width:110px !important;">
                                          <span class="fileinput-new">Choose File </span>
                                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                          </span>
                                         
                                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"> <img src="@if(isset($pan_file->doc_url)){{$pan_file->doc_url}}@endif" alt="" class="tinvat_files_id" /></div>
                                          <input type="hidden" name="pan_doc_id" value="@if(isset($pan_file->doc_id)){{$pan_file->doc_id}}@endif">
                                          <br />
                                          <input id="pan_proof" type="file" class="upload" name="pan_proof" style="margin-top: -27px !important;  position: absolute;opacity: 0;"/>
                                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word;">&nbsp;<a href="javascript:;" class=" fileinput-exists" data-dismiss="fileinput"></a></span>
                                       </div>
                                    </div>

                  <div class="col-md-2 box-outer">
                  @if(isset($tin_file->doc_url))
                   @if($extn2 == 'jpg' || $extn2 == 'png' || $extn2 == 'jpeg')
                    <a href="@if(isset($tin_file->doc_url)){{$tin_file->doc_url}}@endif" target="blank"><img src="{{$tin_file->doc_url}}"  width="150px" height="200px"></a>
                    @else
                    <iframe width="150px" height="200px" src='https://docs.google.com/viewer?url={{$tin_url}}&embedded=true' frameborder="0px"></iframe>
                    @endif
                    <p id="doc_name" class="span2">@if(isset($tin_file->doc_name)){{$tin_file->doc_name}}@endif</p>
                    @else                                
                    <a href="#"><img src="{{ URL::asset('/uploads/LegalEntities/noImage.jpg') }}"  width="150px" height="200px"></a>
                    @endif
                  
                                       <div class="fileinput fileinput-new" data-provides="fileinput">
                                          <span class="btn default btn-file btn green-meadow span3" style="width:110px !important;">
                                          <span class="fileinput-new">Choose File </span>
                                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                            </span>
                                         
                                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"> <img src="@if(isset($tin_file->doc_url)){{$tin_file->doc_url}}@endif" alt="" class="tinvat_files_id" /></div>
                                          <input type="hidden" name="tin_doc_id" value="@if(isset($tin_file->doc_id)) {{$tin_file->doc_id}}@endif">
                                          <br />
                                          <input id="tin_proof" type="file" class="upload" name="tin_proof" style="margin-top: -27px !important;  position: absolute;opacity: 0;"/>
                                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word;">&nbsp;<a href="javascript:;" class=" fileinput-exists" data-dismiss="fileinput"></a></span>
                                       </div>
                                    </div>
                  </div>
                </form>
                   <div class="margiv-top-10" align="center"> 
                       <a href="javascript:;" id="update3" class="btn green-meadow"> Update </a> 
                       <a href="javascript:;" id="cancel3" class="btn default"> Cancel </a> 
                    </div>
                </div>
                </div>
                <div id="tab_3-3" class="tab-pane">
                 <div id="default_show2" style="margin-top:11px;">
                 <div class="row form-group">
                     <label class="control-label col-md-2">Business Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_busnsName">@if(isset($business_info->business_legal_name)){!! $business_info->business_legal_name !!}@endif </p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Business Type :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_busnsType">@if(isset($business_info->business_type)) {!! $business_info->business_type !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Address 1 :</label>
                     <div class="col-md-10">
                      <p class="form-control-static" id="p_addr1">@if(isset($business_info->address1)){!! $business_info->address1 !!}@endif </p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Address 2 :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_addr2">@if(isset($business_info->address2)) {!! $business_info->address2 !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">City :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_city"> @if(isset($business_info->city)) {!! $business_info->city !!}@endif </p> 
                      </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">State :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_state">@if(isset($business_info->state)) {!! $business_info->state !!} @endif</p> 
                      </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">PIN :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_pin">@if(isset($business_info->pincode)) {!! $business_info->pincode !!}@endif </p> 
                      </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">PAN :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_pan">@if(isset($business_info->pan_number)) {!! $business_info->pan_number !!}@endif </p> 
                      </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">TIN :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_tin">@if(isset($business_info->tin_number)) {!! $business_info->tin_number !!} @endif</p> 
                      </div>
                    </div>
    
                </div>     
                <div id="show_onedit2" style="display:none">
                   <div class="col-md-12 actions " style="margin-bottom:10px;">
                  </div>
                   <form id="BusinessInfo">
                   <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{!! $legal_entity_id !!}">
                   <div class="row form-group">
                     <label class="control-label col-md-2">Business Name :</label>
                     <div class="col-md-10">
                       <input type="text" id="businessname" name="businessname" value="@if(isset($business_info->business_legal_name)){!! $business_info->business_legal_name !!}@endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Business Type :</label>
                     <div class="col-md-10">
                      <select class="form-control select2me" id="business_type" name="business_type">
                         @foreach ($business_types as $key => $business_type)
                         @if(isset($business_info) && $business_type->business_type_id == $business_info->business_type_id)
                              <option value="{{$business_type->business_type_id}}" selected>{{ $business_type->business_type }}</option>
                            @else
                             <option value="{{$business_type->business_type_id}}">{{ $business_type->business_type }}</option>
                            @endif
                          @endforeach
                        </select>
                       </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Address 1 :</label>
                     <div class="col-md-10">
                       <input type="text" id="address1" name="address1" value="@if(isset($business_info->address1)) {!! $business_info->address1 !!} @endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Address 2 :</label>
                     <div class="col-md-10">
                       <input type="text" id="address2" name="address2" value="@if(isset($business_info->address2)) {!! $business_info->address2 !!} @endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">City :</label>
                     <div class="col-md-10">
                       <input type="text" id="city" name="city" value="@if(isset($business_info->city)) {!! $business_info->city !!} @endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">State :</label>
                     <div class="col-md-10">
                     <select class="form-control select2me" id="state_id" name="state_id">
                         @foreach ($states as $key => $state)
                          @if(isset($business_info->state_id) && $state->state_id == $business_info->state_id)
                              <option value="{{ $state->state_id  }}" selected>{{ $state->state }}</option>
                            @else
                              <option value="{{ $state->state_id  }}">{{ $state->state }}</option>
                            @endif
                          @endforeach
                        </select>
                       </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">PIN :</label>
                     <div class="col-md-10">
                       <input type="text" id="pincode" name="pincode" value="@if(isset($business_info->pincode)) {!! $business_info->pincode !!} @endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">PAN :</label>
                     <div class="col-md-10">
                       <input type="text" id="pan" name="pan" value="@if(isset($business_info->pan_number)) {!! $business_info->pan_number !!} @endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">TIN :</label>
                     <div class="col-md-10">
                       <input type="text" id="tin" name="tin" value="@if(isset($business_info->tin_number)) {!! $business_info->tin_number !!} @endif" class="form-control"> </div>
                    </div>
                   </form>
                     <div class="margiv-top-10" align="center"> 
                       <a href="javascript:;" id="save_business_info" class="btn green"> Save Changes </a> 
                       <a href="javascript:;" id="cancel2" class="btn default"> Cancel </a> 
                    </div>
                  </div>
                </div>
                <div id="tab_4-4" class="tab-pane">
                   <div id="default_show4" style="margin-top:11px;">
                <div class="row form-group">
                     <label class="control-label col-md-2">Account No :</label>
                     <div class="col-md-10">
                      <p class="form-control-static" id="p_ac_no"> @if(isset($bank_details->account_no)){!! $bank_details->account_no !!}@endif</p> </div>
                    </div>
                 <div class="row form-group">
                     <label class="control-label col-md-2">Account Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_ac_name">@if(isset($bank_details->account_name)) {!! $bank_details->account_name !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Account Type :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_ac_type">@if(isset($bank_details->account_type_name)) {!! $bank_details->account_type_name !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Bank Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_bank_name">@if(isset($bank_details->bank_name)) {!! $bank_details->bank_name !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">IFSC Code :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_ifsc"> @if(isset($bank_details->ifsc_code)) {!! $bank_details->ifsc_code !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Branch Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_brnch"> @if(isset($bank_details->branch_name)) {!! $bank_details->branch_name !!} @endif </p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">City :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_b_city"> @if(isset($bank_details->city)) {!! $bank_details->city !!} @endif </p> </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">MICR Code :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_micr">@if(isset($bank_details->micr_code)) {!! $bank_details->micr_code !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Currency :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_curr">@if(isset($bank_details->currency_code_name)) {!! $bank_details->currency_code_name !!} @endif</p> </div>
                    </div>
                </div>     
                <div id="show_onedit4" style="display:none">
                <div class="col-md-12 actions " style="margin-bottom:10px;">
                </div>
                   <form id="BankInfo">
                   <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{!! $legal_entity_id !!}">
                   <div class="row form-group">
                     <label class="control-label col-md-2">Account No :</label>
                     <div class="col-md-10">
                       <input type="text" id="account_no" name="account_no" value="@if(isset($bank_details->account_no)){!! $bank_details->account_no !!}@endif" class="form-control"> </div>
                    </div>
                   <div class="row form-group">
                     <label class="control-label col-md-2">Account Name :</label>
                     <div class="col-md-10">
                       <input type="text" id="account_name" name="account_name" value="@if(isset($bank_details->account_name)){!! $bank_details->account_name !!}@endif" class="form-control"> </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">Account Type :</label>
                     <div class="col-md-10">
                     <select class="form-control select2me" id="account_type" name="account_type">
                       @foreach ($account_types as $key => $account_type)
                       @if(isset($bank_details) && $account_type->id == $bank_details->account_type)
                          <option value="{{ $account_type->id  }}" selected>{{ $account_type->account_type_name }}</option>
                        @else
                          <option value="{{ $account_type->id  }}">{{ $account_type->account_type_name }}</option>
                        @endif
                      @endforeach
                     </select>
                       <!-- <input type="text" name="account_type" value="@if(isset($bank_details->account_type)){!! $bank_details->account_type !!}@endif" class="form-control"> --> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Bank Name :</label>
                     <div class="col-md-10">
                     <select class="form-control select2me" id="bank_name" name="bank_name">
                     @foreach( $bank_name as $key => $value)
                       @if(isset($bank_details->bank_name))
                         @if($value->bank_name == $bank_details->bank_name))
                          <option value="{{$value->bank_name}}" selected>{{$value->bank_name}}</option>
                         @else
                          <option value="{{$value->bank_name}}">{{$value->bank_name}}</option>
                          @endif
                          @else
                           <option value="{{$value->bank_name}}">{{$value->bank_name}}</option>
                        @endif
                       @endforeach
                     </select>
                       <!-- <input type="text" id="bank_name" name="bank_name" value="@if(isset($bank_details->bank_name)){!! $bank_details->bank_name !!}@endif" class="form-control"> --> 
                       </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">IFSC Code :</label>
                     <div class="col-md-10">
                     <select class="form-control select2me"  id="ifsc" name="ifsc_code">
                     <option value="@if(isset($bank_details->ifsc_code)){!! $bank_details->ifsc_code !!}@endif">@if(isset($bank_details->ifsc_code)){!! $bank_details->ifsc_code !!}@endif</option>
                     </select>

                       <!-- <input type="text" id="ifsc_code" name="ifsc_code" value="@if(isset($bank_details->ifsc_code)){!! $bank_details->ifsc_code !!}@endif" class="form-control"> --> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Branch Name :</label>
                     <div class="col-md-10">
                       <input type="text" id="branch_name" name="branch_name" value="@if(isset($bank_details->branch_name)){!! $bank_details->branch_name !!}@endif" class="form-control"> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">City :</label>
                     <div class="col-md-10">
                       <input type="text" id="b_city" name="b_city" value="@if(isset($bank_details->city)){!! $bank_details->city !!}@endif" class="form-control"> </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">MICR Code :</label>
                     <div class="col-md-10">
                       <input type="text" id="micr_code" name="micr_code" value="@if(isset($bank_details->micr_code)){!! $bank_details->micr_code !!}@endif" class="form-control"> </div>
                    </div>
                     <div class="row form-group">
                     <label class="control-label col-md-2">Currency :</label>
                     <div class="col-md-10">
                       <select class="form-control" id="currency" name="currency" id="currency_code">
                         @foreach ($currencyCodes as $key => $currencyCode)
                          @if(isset($bank_details))
                            @if($currencyCode->id == $bank_details->currency_code)
                              <option value="{{ $currencyCode->id  }}" selected>{{ $currencyCode->currency_name }}</option>
                              @else
                              <option value="{{ $currencyCode->id  }}">{{ $currencyCode->currency_name }}</option>
                              @endif
                            @else
                              <option value="{{ $currencyCode->id  }}">{{ $currencyCode->currency_name }}</option>
                            @endif
                          @endforeach
                        </select>
                      </div>
                    </div>
                   </form>
                     <div class="margiv-top-10" align="center"> 
                       <a href="javascript:;" id="save_bank_info" class="btn green"> Save Changes </a> 
                       <a href="javascript:;" id="cancel4" class="btn default"> Cancel </a> 
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--end col-md-9--> 
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
                                       <label class="control-label col-md-4">Old Password :</label>
                                       <div class="col-md-8">
                                         <input type="password" name="oldpassword" class="form-control"></div>
                                      </div>
                                      <div class="row form-group" id="reset_div">
                                       <label class="control-label col-md-4 new_password">New Password :</label>
                                       <div class="col-md-8">
                                         <input type="password" name="newpassword" id="newpassword" class="form-control" onkeyup="checkNewPassword()" >
                                         <i id="reset_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="newpassword" style="display: block;"></i>
                                         <p id="new_pass_msg" style="display: none;color: #a94442;margin-left: 160px;font-size:small">New password cannot be the same as default password.</p>
                                       </div>
                                      </div>
                                      <div class="row form-group" id="confirm_div">
                                       <label class="control-label col-md-4 confirm_password">Confirm Password :</label>
                                       <div class="col-md-8">
                                         <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" onkeyup="checkConfirmPassword()" >
                                       <i id="confirm_pass_wrong" class="form-control-feedback glyphicon glyphicon-remove" data-fv-icon-for="confirmpassword" style="display: block;"></i>
                                         <p id="confirm_pass_msg" style="display: none;color: #a94442;margin-left: 160px;font-size:small">New password cannot be the same as default password.</p>
                                       </div>
                                      </div>

                                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />   
                              </form>
                                <div class="margiv-top-10" align="center">          
                                    <input type="button"  id="change_password_button" class="btn green-meadow" value = "Change Password" />
                                </div>    
                            </div>
                            
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
@section('style')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<style>
.control-label{
	text-align:right !important;
	 padding-top: 9px;
	}
 .glyphicon-remove, .glyphicon-ok, .glyphicon-refresh{right: 10px !important;}
 .btn-icon-only {
    position: absolute;
    right: 0px;
    z-index: 9;
    width: 28px;
    height: 28px;
    line-height:2px;
    background-color:#f5f6fa;
    border:0px;
    top: 0px; 
  }
  .ver-inline-menu li.active a, .ver-inline-menu li.active i {
    color: #fff !important;
  }
  .ver-inline-menu li.active i {
    background: none !important;
}
.ver-inline-menu li.active a {
    border-left: solid 0px #0c91e5 !important;
}

  .fileinput .thumbnail {display: none !important;}
.ver-inline-menu li i {
    width: 12px !important;
    height: 18px !important;
    display: inline-block !important;
    font-size: 12px !important;
    padding: 2px 0px 0px 0px !important;
    margin: 0 0px 0 0 !important;
    text-align: center;
    background: none !important; 
}
.ver-inline-menu li i{
    color:#557386;
}
.box-outer{  
  font-size:10px; 
  border: 1px solid #e1e7ee;
  padding:10px 10px 0px 10px;
  margin-right:15px;
 
}
.box-outer .fileinput-filename{
   width: 15.5em;
  word-wrap: break-word;
  text-align: center;
}
.box-outer .form-control-feedback, .box-outer .help-block {color: #a94442}

.box-outer p{line-height: 30px; margin-bottom:0px; text-align: center; } 
.box-outer .span2{line-height: 27px !important;} 
.box-outer .span3{margin-left:21px;} 
.help-block {width: auto !important;}
.glyphicon-remove{
  color:#a94442!important;
}
.glyphicon-ok{
  color:#3c763d!important;   
}
.error_validation{
  color:#a94442!important;
}
</style>
@stop
@section('userscript')
{{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
{{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function (){
  $('#edit1').click(function (){
    $('#default_show1').hide();
    $('#show_onedit1').show();
  });
   $('#edit3').click(function (){
    $('#default_show3').hide();
    $('#show_onedit3').show();
  });
   $('#cancel3').click(function (){
    $('#default_show3').show();
    $('#show_onedit3').hide();
  });
  function updateDoc(id){
    $('#opendocs('+id+')').trigger('click');
  }
  $('#cancel1').click(function (){
    $('#BasicInfo').bootstrapValidator('resetForm', true);
    $('#BasicInfo')[0].reset(); 
    $('#default_show1').show();
    $('#show_onedit1').hide();
  });
  $('#edit2').click(function (){
    $('#default_show2').hide();
    $('#show_onedit2').show();
  });
  $('#cancel2').click(function (){
    $('#BusinessInfo').bootstrapValidator('resetForm', true);
    $('#BusinessInfo')[0].reset(); 
    $('#default_show2').show();
    $('#show_onedit2').hide();
  });
   $('#edit4').click(function (){
    $('#default_show4').hide();
    $('#show_onedit4').show();
  });
  $('#cancel4').click(function (){
    $('#BankInfo').bootstrapValidator('resetForm', true);
    $('#BankInfo')[0].reset(); 
    $('#default_show4').show();
    $('#show_onedit4').hide();
  });
  $('#edit_file').click(function (){
    $('#file_open').trigger('click');
  });
  $('#clickChangePassword').click(function (){
    $('#changePassword').show();
  });

  $('#getImage').formValidation({
         framework: 'bootstrap',
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
         profile_pic:{
              validators: {
                file: {
                      extension: 'jpeg,jpg,png',
                      type: 'image/jpeg,image/png',
                      maxSize: 5*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (jpeg,png,jpg) and 5 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            },
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
      });

  $('#file_open').on('change' ,function (e){
    var profile_pic = $('#profile_pic').attr('src');
    var headerPic = $('#profile_pic_default').attr('src');
    var form = document.forms.namedItem("getImage"); 
      var formdata = new FormData(form);
      console.log(profile_pic); console.log(headerPic);
       $.ajax({
        url: '/legalentity/saveProfilePic',
        data: formdata,
        type: 'POST',
        processData :false,
        contentType:false,
        success: function (result)
        {
            var response = JSON.parse(result);
            if(response.status == true){
            $('#profile_pic').attr('src',response.path);            
            $('#profile_pic_default').attr('src',response.path);
            }
            else{
            alert(response.message);
            $('#profile_pic').attr('src',profile_pic);            
            $('#profile_pic_default').attr('src',headerPic);
            }
        }
      });
  });

$('#change_password_button').click(function (){
    var formValid = $('#changePasswordForm').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        return false;
      }
   else{
    console.log('inelse');
    $('#change_password_button').attr('disabled',false);
    $.ajax({
        url: '/legalentity/changePassword',
        data: $('#changePasswordForm').serialize(),
        type: 'POST',
        success: function (result)
        {
          var response = JSON.parse(result);
          if (response.status == true)
            {
              alert(response.message);
              $('.close').trigger('click');
            }
            else{
              alert(response.message);
              $('.close').trigger('click');
            }
        }
      });
    }
  });
 $('#changePasswordModal').on('hide.bs.modal',function(){
    console.log('resetForm');
    $('#changePasswordForm').bootstrapValidator('resetForm', true);
    $('#changePasswordForm')[0].reset(); 
  });
$('#changePasswordForm').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#change_password_button',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          oldpassword: {
              validators: {
                  notEmpty: {
                        message: 'Please enter password'
                    },
                  remote: {
                        url: '/legalentity/checkPassword',
                        data: {oldpassword: $('[name="oldpassword"]').val()},
                        type: 'POST',
                        delay: 2000,
                        message: 'Incorrect Password. Please try again..'
                  }
              }
          },
          newpassword: {
              validators: {
                  notEmpty: {
                        message: 'Password cannot be empty'
                    },
                  stringLength: {
                        min: 5,
                        max: 14,
                        message: 'Password must be 4 to 14 characters in length.'
                    }
                }
            },
          confirmpassword: {
              validators: {
                  notEmpty: {
                        message: 'Confirm password cannot be empty'
                    },
                  identical: {
                      field: 'newpassword',
                      message: 'Password doesn\'t match.'
                    }
                }
            },
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
      });

    function checkNewPassword(){
        var new_password = $('#newpassword').val();
        if(new_password == 'ebutor@123' || new_password == 'Ebutor@123'){
            $('#new_pass_msg').css('display','block');
            $('#change_password_button').attr('disabled',true);
            $('.new_password').addClass('error_validation');
            $('#reset_wrong').css('display','block');
            $('[data-fv-icon-for="newpassword"]').removeClass('form-control-feedback glyphicon glyphicon-ok');
            $('[data-fv-icon-for="newpassword"]').addClass('form-control-feedback glyphicon glyphicon-remove');
            $('#reset_div').addClass('has-error');
            $('#reset_div').removeClass('has-success');

        }else{
            $('#new_pass_msg').css('display','none');
            $('#reset_wrong').css('display','none');
            $('.new_password').removeClass('error_validation');
        }
    }

    function checkConfirmPassword(){
        var confirm_password = $('#confirmpassword').val();
        if(confirm_password == 'ebutor@123' || confirm_password == 'Ebutor@123'){
            $('#confirm_pass_msg').css('display','block');
            $('#change_password_button').attr('disabled',true);
            $('.confirm_password').addClass('error_validation');
            $('#confirm_pass_wrong').css('display','block');
            $('[data-fv-icon-for="confirmpassword"]').removeClass('form-control-feedback glyphicon glyphicon-ok');
            $('[data-fv-icon-for="confirmpassword"]').addClass('form-control-feedback glyphicon glyphicon-remove');
            $('#confirm_div').addClass('has-error');
            $('#confirm_div').removeClass('has-success');

        }else{
            $('#confirm_pass_msg').css('display','none');
            $('#confirm_pass_wrong').css('display','none');
            $('.confirm_password').removeClass('error_validation');
        }
    }

    $('#confirmpassword').keyup(function(){
        checkConfirmPassword();
        checkNewPassword();
    });
    $('#newpassword').keyup(function(){
        checkNewPassword();
        checkConfirmPassword();
    });

    $('#save_basic_info').click(function (){
    var formValid = $('#BasicInfo').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        return false;
      }
   else{
    console.log('inelse');
    $('#save_basic_info').attr('disabled',false);
    $.ajax({
        url: '/legalentity/saveBasicInfo',
        data: $('#BasicInfo').serialize(),
        type: 'POST',
        success: function (result)
        {
          var response = JSON.parse(result);
          var saveInfoData = response.data;
          if (response.status == true)
            {
              console.log(saveInfoData);
              alert(response.message);
              document.getElementById("p_firstname").innerHTML = saveInfoData.firstname;
              document.getElementById("p_lastname").innerHTML = saveInfoData.lastname;
              document.getElementById("p_mobile_no").innerHTML = saveInfoData.mobile_no;
              /*document.getElementById("p_email_sub").innerHTML = saveInfoData.email_sub_val;
              document.getElementById("p_sms_sub").innerHTML = saveInfoData.sms_sub_val;*/
              $('#firstname').attr('value',saveInfoData.firstname);
              $('#lastname').attr('value',saveInfoData.lastname);
              $('#mobile_no').attr('value',saveInfoData.mobile_no);
              if(saveInfoData.email_sub == 1){
                $('#email_sub').attr('checked',true);
                document.getElementById("p_email_sub").innerHTML = "Yes";
              }
              else{
               $('#email_sub').attr('checked',false); 
               document.getElementById("p_email_sub").innerHTML = "No";
              }
              if(saveInfoData.sms_sub == 1){
                $('#sms_sub').attr('checked',true);
                document.getElementById("p_sms_sub").innerHTML = "Yes";
              }
              else{
               $('#sms_sub').attr('checked',false); 
               document.getElementById("p_sms_sub").innerHTML = "No";
              }
              $('#BasicInfo').bootstrapValidator('resetForm', true);
              $('#header_username').text(saveInfoData.firstname+ ' ' + saveInfoData.lastname);
              $('#BasicInfo')[0].reset(); 
              $('#default_show1').show();
              $('#show_onedit1').hide();
            }
            else{
              alert(response.message);
            }
        }
      });
    }
  });
    $('#BasicInfo').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#save_basic_info',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          firstname: {
              validators: {
                 notEmpty: {
                        message: 'First Name is required'
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: 'Invalid characters for First Name'
                }
              }
            },
             lastname: {
              validators: {
                 notEmpty: {
                        message: 'Last Name is required'
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: 'Invalid characters for Last Name'
                }
              }
            },
            mobile_no: {
              validators: {
                 notEmpty: {
                        message: 'Phone Number is required'
                },
                 regexp: {
                  regexp: /^\d{10}$/ ,
                        message: 'Please enter valid Mobile Number'
                }
              }
            }
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
  });

    $('#DocumentsForm').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#save_basic_info',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
         tin_proof:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 2*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                    }
                }
            },
         pan_proof:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 2*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                    }
                }
            }
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
  }); 
  $('#update3').click(function (){
    var formValid = $('#DocumentsForm').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        return false;
      }
   else{
    var form = document.forms.namedItem("DocumentsForm"); 
    var formdata = new FormData(form);
    console.log(form); 
    $.ajax({
        url: '/legalentity/updateDocs',
        data: formdata,
        type: $(form).attr('method'),
        processData :false,
        contentType:false,
        success: function (result)
        {
          var response = JSON.parse(result);
          if(response.status == ""){
            $('#show_onedit3').hide();
            $('#default_show3').show();
          }
          else if(response.status == 1){
            var id = $('#legal_entity_id').val();
            alert(response.message);
            location.reload();
          }
          else if(response.status == 0){
            alert(response.message);
            $('#show_onedit3').show();
            $('#default_show3').hide();
          }
          else{
            return false;
          }
        }
      });
    }
  });
  $('#save_business_info').click(function (){
     var formValid = $('#BusinessInfo').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        return false;
      }
   else{
    console.log('inelse');
    $('#save_business_info').attr('disabled',false);
    $.ajax({
        url: '/legalentity/updateBusinessInfo',
        data: $('#BusinessInfo').serialize(),
        type: 'POST',
        success: function (result)
        {
          var response = JSON.parse(result);
          var saveInfoData = response.data;
          console.log(response);
          var bussData = response.data;
          if (response.status == true)
            {
              alert(response.message);
              document.getElementById("p_busnsName").innerHTML = bussData.businessname;
              document.getElementById("p_busnsType").innerHTML = bussData.business_type_name;
              document.getElementById("p_addr1").innerHTML = bussData.address1;
              document.getElementById("p_addr2").innerHTML = bussData.address2;
              document.getElementById("p_city").innerHTML = bussData.city;
              document.getElementById("p_state").innerHTML = bussData.state;
              document.getElementById("p_pin").innerHTML = bussData.pincode;
              document.getElementById("p_pan").innerHTML = bussData.pan;
              document.getElementById("p_tin").innerHTML = bussData.tin;
              $('#businessname').attr('value',bussData.businessname);
              //$('#business_type').attr('value',bussData.business_type);
              $('#address1').attr('value',bussData.address1);
              $('#address2').attr('value',bussData.address2);
              $('#city').attr('value',bussData.city);
              $('#state_id').attr('value',bussData.state_id);
              $('#pincode').attr('value',bussData.pincode);
              $('#pan').attr('value',bussData.pan);
              $('#tin').attr('value',bussData.tin);
              $('#BusinessInfo').bootstrapValidator('resetForm', true);
              $('#BusinessInfo')[0].reset(); 
              $('#default_show2').show();
              $('#show_onedit2').hide();
            }
            else{
              alert(response.message);
            }
        }
      });
    }
  });

   $('#BusinessInfo').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#save_business_info',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          businessname: {
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
              }
          },
          business_type: {
              validators: {
                  notEmpty: {
                        message: ' '
                    }
                }
            },
          address1: {
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/]+$/,
                            message: ' '
                        },
                }
            },
          address2: {
              validators: {
                  regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/]+$/,
                            message: ' '
                    },
                }
            },
          city: {
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
                }
            },
          state_id: {
              validators: {
                  notEmpty: {
                        message: ' '
                    }
                }
            },
          pincode:{
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^\d{6}$/,
                            message: ' '
                        },
                }
            },
          pan:{
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^[a-zA-Z0-9]+$/i,
                            message: ' '
                    },
                   stringLength: {
                        min: 10,
                        max: 10,
                        message: ' '
                  }
                }
            },
          tin:{
              validators: {
                  notEmpty: {
                        message: ' '
                    },
                  regexp: {
                            regexp: /^[0-9]+$/i,
                            message: ' '
                    },
                  stringLength: {
                        min: 11,
                        max: 11,
                        message: ' '

                  }
                }
            }
        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
          //document.getElementById("calculate").disabled = false;
          //return false;
      });

     $('#save_bank_info').click(function (){
     var formValid = $('#BankInfo').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        return false;
      }
   else{
    console.log('inelse');
    $('#save_bank_info').attr('disabled',false);
    $.ajax({
        url: '/legalentity/saveBankInfo',
        data: $('#BankInfo').serialize(),
        type: 'POST',
        success: function (result)
        {
          var response = JSON.parse(result);
          var saveInfoData = response.data;
          console.log(response);
          var bankData = response.data;
          if (response.status == true)
            {
              alert(response.message);
              document.getElementById("p_ac_name").innerHTML = bankData.account_name;
              document.getElementById("p_bank_name").innerHTML = bankData.bank_name;
              document.getElementById("p_ac_no").innerHTML = bankData.account_no;
              document.getElementById("p_ac_type").innerHTML = bankData.account_type_name;
              document.getElementById("p_ifsc").innerHTML = bankData.ifsc_code;
              document.getElementById("p_brnch").innerHTML = bankData.branch_name;
              document.getElementById("p_b_city").innerHTML = bankData.b_city;
              document.getElementById("p_micr").innerHTML = bankData.micr_code;
              document.getElementById("p_curr").innerHTML = bankData.currency_code_name;
              document.getElementById("account_type").value = bankData.account_type;
              $('#default_show4').show();
              $('#show_onedit4').hide();
              $('#account_name').attr('value',bankData.account_name);
              //$('#bank_name').attr('value',bankData.bank_name);
              document.getElementById("bank_name").selectedIndex = bankData.bank_name;
              //$('#bank_name').select($("<option>").attr('value', bankData.bank_name).text(bankData.bank_name));
              $('#account_no').attr('value',bankData.account_no);
              $('#account_type').attr('value',bankData.account_type);
              $('#ifsc_code').attr('value',bankData.ifsc_code);
              $('#branch_name').attr('value',bankData.branch_name);
              $('#b_city').attr('value',bankData.b_city);
              $('#micr_code').attr('value',bankData.micr_code);
              $('#currency_code').attr('value',bankData.currency_code);
              $('#BankInfo').bootstrapValidator('resetForm', true);
              $('#BankInfo')[0].reset(); 
              
            }
            else{
              alert(response.message);
            }
        }
      });
    }
  });

  $('#BankInfo').formValidation({
         framework: 'bootstrap',
            button: {
            selector: '#save_bank_info',
            disabled: 'disabled'
           },
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
          bank_name: {
              validators: {
                 notEmpty: {
                        message: ' '
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
              }
            },
             account_name: {
              validators: {
                 notEmpty: {
                        message: ' '
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ' '
                }
              }
            },
            account_no: {
              validators: {
                 notEmpty: {
                        message: ' '
                },
                 regexp: {
                  regexp: '^[0-9]*$',
                        message: ' '
                }
              }
            },
            account_type: {
              validators: {
                 notEmpty: {
                        message: ' '
                }
              }
            },
            ifsc_code: {
              validators: {
                 notEmpty: {
                        message: ' '
                },
                regexp: {
                      regexp: /^[a-zA-Z0-9]+$/i,
                      message: ' '
                }
              }
            },
            branch_name: {
              validators: {
                 notEmpty: {
                        message: ' '
                },
                regexp: {
                      regexp: /^[a-zA-Z0-9 "!?.\-]+$/,
                      message: ''
                }
              }
            },
            b_city:{
               validators: {
                 notEmpty: {
                        message: ' '
                },
                regexp: {
                      regexp: '^[a-zA-Z .]+$',
                      message: ''
                }
              }
            },
             micr_code: {
              validators: {
                 notEmpty: {
                        message: ' '
                },
                regexp: {
                      regexp: /^[a-zA-Z0-9]+$/i,
                      message: ' '
                }
              }
            },
            currency: {
              validators: {
                 notEmpty: {
                        message: ' '
                }
              }
            }

        }
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
  });

});
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers:
        {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });

</script> 
<script type="text/javascript">
  $(document).ready(function (){
    $('#bank_name').change(function (){
      $('#ifsc').select2('val', '');
      $.get( "/profile/ifscs/"+$(this).val(), function( data ) {
        $.each(data, function (k,v) {
                $('#ifsc').append($("<option>").attr('value', v.ifsc).text(v.ifsc));
            });
        });
    });
     $('#ifsc').change(function (){
      console.log($(this).val());
      $.get( "/profile/bank_info/"+$(this).val(), function( bank_info ) {
        var bank_info = JSON.parse(bank_info);
        console.log(bank_info);
        $('#branch_name').attr('value',bank_info.branch);
        $('#micr_code').attr('value',bank_info.micr);
        $('#b_city').attr('value',bank_info.city);

      });
    });
  });
</script>
@stop
@extends('layouts.footer')