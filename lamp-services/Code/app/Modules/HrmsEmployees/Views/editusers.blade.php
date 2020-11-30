@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message_ajax"></span>
<div class="alert alert-info hide" id="alert_msg_div">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div id="alert_msg"></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                @if($myProfile_id == "")
                <div class="caption"> Employee Profile </div>
                @else
                <div class="caption"> My Profile </div>
                @endif
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
            </div>            
            <div class="portlet-body">
                <div class="tab-pane" id="tab_1_3">
                    <div class="row profile-account">
                        <form id="getImage">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <div class="col-md-2">
                                <ul class="ver-inline-menu tabbable margin-bottom-10">
                                    <li>
                                        @if(isset($userData['profile_picture']) && !empty($userData['profile_picture']))
                                        <img class="mg-responsive pic-bordered" style="width:100%; height:100% " id="profile_pic" src="{{ $userData['profile_picture'] }}" onclick="showhideimg();" alt/> 
                                        @else
                                        <img class="mg-responsive pic-bordered" style="width:100%; height:100% " id="profile_pic" src="{{ URL::asset('/img/avatar5.png') }}" alt /> 
                                        @endif
                                        @if($myProfile_id != '')
                                        <a class="profile-edit" id="edit_file" href="#"> <i class="fa fa-pencil" style="color:#fff;"></i> </a>
                                        @endif
                                    </li><input type="file" id="file_open" name="file" style="display:none">
                                    <li class="active"> <a data-toggle="tab" href="#tab11">Personal Information</a>
                                        @if($editAccess == '1' ||  $myProfile_id != "")
                                        <a href="javascript:;" id="edit1" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                        @endif
                                        <span class="after"> </span>
                                    </li>
                                    <li > <a data-toggle="tab" href="#emp_personal_details">Contact Information</a>
                                        @if($editAccess == '1' ||  $myProfile_id != "")
                                        <a href="javascript:;" id="emp_info_edit" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                        @endif
                                        <span class="after"> </span>
                                    </li>
                                    <li> <a data-toggle="tab" href="#tab22">Documents </a>                                        
                                    </li>
                                    <li > <a data-toggle="tab" href="#tab_bank_info"> Bank Information</a>
                                        @if(($editAccess == '1' ||  $myProfile_id != "") && $editBankdetails == '1')
                                        <a href="javascript:;" id="bank_info_active" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                        <span class="after"> </span>
                                        @endif
                                    </li>
                                    <li style="display: none;"> <a data-toggle="tab" href="#tab_skill_info"> Skills Information</a>
                                        <a href="javascript:;" id="skill_info_active" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                        <span class="after"> </span>
                                    </li>
                                    <li > <a data-toggle="tab" href="#tab_edu_info"> Education Information</a>
                                    </li>
                                    <li > <a data-toggle="tab" href="#tab_certification_info"> Certification Information</a>
                                    </li>
                                    <li > <a data-toggle="tab" href="#tab_skill_info" onclick="loadSkillsData('{{$userData['emp_id']}}','{{$myProfile_id}}','{{$editColAccess}}')"> Skills Information</a>
                                        <span class="after"> </span>
                                    </li>
                                    <li> <a data-toggle="tab" href="#tab_insurance_info"> Insurance Information</a>
                                        @if($editAccess == '1' ||  $myProfile_id != "")
                                        <a href="javascript:;" id="insurance_edit_btn" style="border-left:0px;" class="pull-right btn btn-square btn-icon-only btn-default "><i class="fa fa-pencil"></i></a>
                                        @endif
                                    </li>
                                    <li > <a data-toggle="tab" href="#tab_experience_info"> Experience Information</a>
                                    </li>
                                    @if($appDropdown!= "")
                                    <li> <a data-toggle="tab" href="#tab_4-5" >Approval Process </a>
                                    </li>
                                    @endif
                                    @if($myProfile_id == "")
                                    <li> <a data-toggle="tab" href="#tab_4-6"  onclick="historyapprovalhrmsdata({{$userData['emp_id']}})">Approval History </a>
                                    </li>
                                    @endif

                                    <li> <a data-toggle="tab" href="#tab_4-7">Assets</a>
                                    </li>
                                    
                                   
                                </ul>
                            </div>
                        </form>
                        <div class="col-md-10">
                            <div class="tab-content">
                                @include('HrmsEmployees::editEmployeeBasicInfo')
                                @include('HrmsEmployees::empPersonalInfoTab')
                                @include('HrmsEmployees::documents')
                                @include('HrmsEmployees::empBankInfo')
                                @include('HrmsEmployees::empSkillsInfo')
                                @include('HrmsEmployees::empEducationInfo')
                                @include('HrmsEmployees::empCertificationInfo')
                                @include('HrmsEmployees::empInsuranceInfo')
                                @include('HrmsEmployees::experienceTabInfo')
                                @include('HrmsEmployees::empAssetsInfo')
                                @if($myProfile_id == "")
                                @include('HrmsEmployees::exitEmployee')
                                @include('HrmsEmployees::employeeHistory')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>




<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
 
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <img class="modal-content" id="img01" src="{{ $userData['profile_picture'] }}" height="80%" >
      </div>
      </div>

  </div>
</div>

        </div>
    </div>
</div>
@stop
@section('style')
<style type="text/css">
.modal .modal-header .close {
    right: 16px !important;
}
    .skills_div{
        background-color: #d9edf7;
        height: 40px;
        padding-top: 6px;
    }
    .basicInfoOverlay {
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
    .basicInfoLoader {
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
        top:16em;
        left:48em;
    }
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
        height: 400px;
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
        top:16em;
        left:48em;
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

    #documentType-error, #manuLogo-error, #ref_no-error{color:#e02222 !important}
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

    .container {
        width: 1051px;
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
    .form-horizontal .form-group
     {
            margin-left: 0px !important;
            margin-right: inherit;
            padding-left: inherit;
            margin-bottom: 3px !important;
    }
    .form-horizontal
     {
            margin-left: 0px !important;
            margin-right: inherit;
            padding-left: inherit;
            margin-bottom: 3px !important;
    }






#profile_pic {
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

#profile_pic:hover {opacity: 0.7;}

/* The Modal (background) */


/* Modal Content (image) */
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
}




.modal-content {
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: none !important;
  }


/* Add Animation */
.modal-content, #caption {    
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {-webkit-transform:scale(0)} 
    to {-webkit-transform:scale(1)}
}

@keyframes zoom {
    from {transform:scale(0)} 
    to {transform:scale(1)}
}

/* The Close Button */
.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 80px){
    .modal-content {
        width: 80%;
    }
}

.genra{
    margin-top: 20px;
  }
  .roundedfc{border-radius: 16px !important;overflow-wrap: break-word;
  background:#bdc3c7 !important;font-size: 12px;
}

 .ui-autocomplete{z-index: 99999 !important; background: #fff; height: 250px !important; 
    border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important;
    width:323px !important; white-space: pre-wrap !important;padding-left: 13px; list-style: none;}

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
@section('script')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@include('includes.validators')

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ URL
::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
{{HTML::script('assets/admin/pages/scripts/hrms/empPersonalInfo.js')}}
{{HTML::script('assets/admin/pages/scripts/hrms/empBankInfo.js')}}
{{HTML::script('assets/admin/pages/scripts/hrms/empExperienceTabInfo.js')}}
{{HTML::script('assets/admin/pages/scripts/hrms/empCertificationTabInfo.js')}}
{{HTML::script('assets/admin/pages/scripts/hrms/empEducationTabInfo.js')}}

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
        date_input.datepicker(options).on('changeDate', function(e) {
        // Revalidate the date field
        $('#submit_form').formValidation('revalidateField', 'dob');
        });
        wrap_text();
    });
    var csrf_token = $('#csrf_token').val();
    $(document).ready(function () {
        $('#edit1').click(function ()
        {
            $('#default_show1').hide();
            $('#edit_basic_info').show();
        });
       $('#emp_info_edit').click(function ()
        {
            $('#edit_personal_info').bootstrapValidator('resetForm', true);
            $('#edit_personal_info').show();
            $('#emp_personal_info_show1').hide();
        });

       $('#bank_info_active').click(function ()
        {
            $('#bank_edit').bootstrapValidator('resetForm', true);
            $('#bank_edit').show();
            $('#bank_preview').hide();
            
        });
       $('#skill_info_active').click(function ()
        {
            $('#skill_edit').show();
            $('#skill_preview').hide();
        });
       
       $('#insurance_edit_btn').click(function ()
        {
            $('#insurance_edit').show();
            $('#insurance_preview').hide();
        });


        $('#cancel1').click(function () {
            $('#submit_form').bootstrapValidator('resetForm', true);
            $('#submit_form')[0].reset();
            $('#default_show1').show();
            $('#edit_basic_info').hide();
        });
        $('#cancel2').click(function () {

            $('#emp_personal_info').bootstrapValidator('resetForm', true);
            $('#emp_personal_info')[0].reset();

            $("#cu_country").select2('val', $("#cu_country").val());
            $("#cu_state").select2('val', $("#cu_state").val());
            $("#pe_state").select2('val', $("#pe_state").val());
            $("#pe_country").select2('val', $("#pe_country").val());
            $("#ref_one_state").select2('val', $("#ref_one_state").val());
            $("#ref_one_country").select2('val', $("#ref_one_country").val());
            $("#ref_two_state").select2('val', $("#ref_two_state").val());
            $("#ref_two_country").select2('val', $("#ref_two_country").val());
            $('#edit_personal_info').hide();
            $('#emp_personal_info_show1').show();
        });
         $('#bank_cancel_btn').click(function () {
            $('#emp_bank_info').bootstrapValidator('resetForm', true);
            $('#emp_bank_info')[0].reset();

            $("#currency_code").select2('val', $("#currency_code").val());
            $("#acc_type").select2('val', $("#acc_type").val());
            $('#bank_preview').show();
            $('#bank_edit').hide();
        });
          $('#skill_cancel_btn').click(function () {
            $('#submit_form').bootstrapValidator('resetForm', true);
            $('#submit_form')[0].reset();
            $('#skill_preview').show();
            $('#skill_edit').hide();
        });
        $('#cancel_insurance').click(function () {
            $('#emp_ensurance_info_form').bootstrapValidator('resetForm', true);
            $('#emp_ensurance_info_form')[0].reset();
            $('#insurance_edit').hide();
            $('#insurance_preview').show();
        });
    });
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
            },
                    stringLength: {
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
//                stringLength: {
//                    min: 4,
//                    max: 20,
//                    message: "{{trans('employee.employee_form_validate.users_last_name_length')}}"
//                },
                            regexp: {
                            regexp: /^[a-z\s]+$/i,
                                    message: "Last name can consist of characters and spaces only."
                            }
                    }
                    },
                    mobile_no: {
                    validators: {
                    notEmpty: {
                    message: "Mobile is required."
                    },
                            stringLength: {
                            min: 10,
                                    max: 10,
                                    message: "'Mobile number should be 10 digit."
                            },
                            regexp: {
                            regexp: '^[0-9]*$',
                                    message: "Mobile number must be digits only."
                            },
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                            url: '/employee/validatemobileno',
                            type: 'POST',
                            data: function (validator, $field, value) {
                                return  {
                                    mobile_no : value,
                                    emp_id : $("#user_id").val(),
                                };
                            },
                            delay: 1000, // Send Ajax request every 1 seconds
                            message: "Mobile number already exists. "
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
                    reporting_manager_id: {
                    validators: {
                    callback: {
                    message: "Reporting Manager is required.",
                            callback: function (value, validator) {
                            // console.log(value);
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
                    callback: function(value, validator) {
                    return value > 0;
                    }
            }
            }
            }, 
            // aadhar_number:{
            //     validators: {
            //         notEmpty: {
            //             message: "Aadhar card number is required."
            //         },
            //         stringLength: {
            //             min: 12,
            //             max: 12,
            //             message: "Aadhar card must have 12 digits."
            //         },
            //         remote: {
            //             headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
            //             url: '/employee/validateaadharno',
            //             type: 'POST',
            //             data: function (validator, $field, value) {
            //                 return  {
            //                     aadhar_number: value,
            //                     emp_id: $('#user_id').val()
            //                 };
            //             },
            //             delay: 1000, // Send Ajax request every 1 seconds
            //             message: "Aadhar number already exists."
            //         }
            //     }
            // },edit_aadhar_image: {
            //     validators: {
            //         file: {
            //             extension: 'jpg,jpeg,pdf,doc,docx,png,JPG',
            //             type: 'image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
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
                        callback: function(value, validator) {
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
                    callback: function(value, validator) {
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
                    callback: function(value, validator) {
                    return value > 0;
                    }
            }
            }
            },
                    gender:{
                    validators: {
                    callback: {
                    message: "Gender is required.",
                            callback: function(value, validator) {
                            if (isNaN(value) == false)
                            {
                            return value > 0;
                            }
                            return true;
                            }
                    }
                    }
                    },
                    marital_status:{
                    validators: {
                    callback: {
                    message: "Marital status is required.",
                            callback: function(value, validator) {
                            if (isNaN(value) == false)
                            {
                            return value > 0;pan_
                            }
                            return true;
                            }
                    }
                    }
                    },
                    blood_group:{
                    validators: {
                    regexp: {
                        regexp: /^[a-z()+-\s]+$/i,
                        message: "Please enter letters only."
                    }
                    }
                    },
                    nationality: {
                    validators: {
                    regexp: {
                    regexp: /^[a-z\s]+$/i,
                            message: "Nationality can consist of characters and spaces only."
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
                        message: "The email address is not valid."
                    },
                    remote: {
                        headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                        url: '/employee/validateemail',
                        type: 'POST',
                        data: function (validator, $field, value) {
                            return  {
                            email_id: value,
                            emp_id: $('#user_id').val() 
                            };
                        },
                        delay: 2000, // Send Ajax request every 2 seconds
                        message: "Email already exists."
                    }
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
            // edit_pan_card_image: {
            //     validators: {
            //         file: {
            //             extension: 'pdf,doc,docx,jpeg,png,jpg,JPG',
            //             type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
            //             maxSize: 2048 * 1024,
            //             message: 'The selected file is not valid'
            //         },
            //     }
            // },
            uan_number:{
                validators: {
                    regexp: {
                        regexp: '^[0-9]{12,12}$',
                        message: "Please enter 12 digits."
                    }  
                }
            },
            grade:{
                validators: {
                    regexp: {
                        regexp: '^[A-Z0-9]{1,3}$',
                        message: "Please enter uppercase and numeric."
                    }  
                }
            },

   }}).on('success.form.bv', function (event) {
    event.preventDefault();
    var datastring = '';
    var datastring = $("#submit_form").serialize();
    
    $.ajax({
    url: '/employee/updateuser',
            data: datastring,
            type: 'get',
            beforeSend: function () {
            $('[class="basicInfoLoader"]').show();
            $(".basicInfoOverlay").show();
                },
            complete: function () {
                $('[class="basicInfoLoader"]').hide();
                $(".basicInfoOverlay").hide();
            },
            success: function (response) {

            var data = $.parseJSON(response);
            if (data.status!=400) {
                    console.log("im here");
                    $('#flass_message').text("User successfully updated.");
                    $('#alert_msg_div').show();
                    $('#alert_msg_div').removeClass('hide');
                    $('#alert_msg_div').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    $("#preview_department").text("");
                    $("#preview_designation").text("");
                    $("#email_error").html('');
                    $('#default_show1').show();
                    $('#edit_basic_info').hide();
                    $("#preview_aadhar").text(data.data.aadhar_number);
                    $("#preview_prefix").text(data.data.prefix);
                    $("#preview_first_name").text(data.data.firstname);
                    $("#preview_last_name").text(data.data.lastname);
                    $("#preview_email_id").text(data.data.email_id);
                    $("#preview_mno").text(data.data.mobile_no);
                    $("#preview_emp_type").text(data.data.employment_type);
                    $("#preview_role").text(data.data.role_name);
                    $("#preview_rmp").text(data.data.reporting_manager_name);
                    $("#preview_department").text(data.data.department_name);
                    $("#preview_designation").text(data.data.designation_name);
                    $("#preview_emp_code").text(data.data.emp_code);
                    $("#preview_cost").text(data.data.business_unit_id);
                    $("#preview_dob").text(data.data.dob);
                    $("#preview_gender").text(data.data.gender);
                    $("#preview_marital").text(data.data.marital_status);
                    $("#preview_nationality").text(data.data.nationality);
                    $("#preview_blood_group").text(data.data.blood_group);
                    $("#preview_fathername").text(data.data.father_name);
                    $("#preview_mothername").text(data.data.mother_name);

                    $("#preview_middle_name").text(data.data.middlename);
                    $("#preview_alternative_mno").text(data.data.alternative_mno);

                    if(data.data.landline_ext == 0){
                        $("#preview_extension_no").text("");
                    }else{
                    $("#preview_extension_no").text(data.data.landline_ext);
                    }
                    $("#preview_pan_no").text(data.data.pan_card_number);
                    $("#preview_uan_no").text(data.data.uan_number);
                    $("#preview_doj").text(data.data.doj);
                    $("#preview_grade").text(data.data.grade);
                    wrap_text();
                    location.reload();
                }else{
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    $("#alert_msg_div").attr("class","alert alert-danger").html(data.message).show().delay(3000).fadeOut(350);
                    wrap_text();
                    location.reload();
                }
            }
    });
    });
    function nextTab()
    {
    $('a[href="#tab22"]').tab('show');
    $('a[href="#tab_4-5"]').tab('show');
    $('a[href="#tab_4-4"]').tab('show');
    }
    function goBack()
    {
    $('a[href="#tab11"]').tab('show');
    }

    $('[name="role_id[]"]').change(function () {
    $('[name="reporting_manager_id"]').empty();
    $('[name="reporting_manager_id"]')
            .append($("<option></option>")
                    .attr("value", '')
                    .text('Please Select...'));
    $('[name="reporting_manager_id"]').select2({placeholder: "Please Select..."});
    var token = $("#csrf_token").val();
    var user_id = $("#user_id").val();
    var datastring = {role_id: $(this).val(), "emp_id": user_id};
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/employee/getreportingmanagers',
            data: datastring,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
            if (response != "")
            {
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
            }
    });
    });
    $("#empdocs").validate({
        rules:
        {
            documentType:
            {
                required: true
            },
            ref_no:
            {
                required: true,
            },
            upload_file:
            {
                required: false,
                extension: "pdf|doc|docx|jpg|jpeg|png"
            }
        },
        submitHandler: function (form)
        {
            $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: "/employee/employeeDocs",
                    type: "POST",
                    data: new FormData(form),
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function (xhr) {
                    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('started', 'box2Load', true);
                    },
                    complete: function (jqXHR, textStatus) {
                    $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('finished', 'box2Load', true);
                    },
                    success: function (response) {
                    //$('#ajaxResponseDoc').html(response.message);
                    document.getElementById("empdocs").reset();
                    if (response.refresh)
                    {
                    $('#supplier_doc_table tbody').html(response.docText);
                    $('#flass_message').text('Saved successfully');
                    $('#alert_msg_div').show();
                    $('#alert_msg_div').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                    $('#alert_msg_div').not('.alert-important').delay(5000).fadeOut("450");
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    } else
                    {
                    $('#supplier_doc_table').append(response.docText);
                    $('#flass_message').text('Saved successfully');
                    $('#alert_msg_div').show();
                    $('#alert_msg_div').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                    $('#alert_msg_div').not('.alert-important').delay(5000).fadeOut("450");
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    }

                    if (response.count > 0)
                    {
                    $('#no_rec_id').css('display', 'none');
                    }
                    },
                    error: function (response) {
                    $('#ajaxResponseDoc').html('Unable to save Documents');
                    }
            });
            }
    });
    $(document).on('click', '.grn-del-doc', function () {
        var docId = $(this).attr("id");
        if (confirm('Do you want to delete this document?')) {
            deleteDoc(docId);
            $(this).closest('tr').remove();
            $('#flass_message').text('Document deleted successfully');
            $('#alert_msg_div').show();
            $('#alert_msg_div').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
            $('#alert_msg_div').not('.alert-important').delay(5000).fadeOut("450");
            $('html, body').animate({scrollTop: '0px'}, 800);
            var tbody = $("#supplier_doc_table tbody");
            if (tbody.children().length == 0) 
            {
                $('#table_err').html("</p>No Records Found.</p>");
            }
        }
    });
    function deleteDoc(id)
    {
    $.ajax({
    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/employee/deletedoc/" + id,
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (response) {
                $('#ajaxResponse').html(response.message);
                console.log(response);
            },
            error: function (response) {
            $('#ajaxResponse').html('Unable to delete');
            }
    });
    }
    $('#edit_file').click(function ()
    {
    $('#file_open').trigger('click');
    });
    $('#file_open').on('change', function (e) {
    var profile_pic = $('#profile_pic').attr('src');
    var headerPic = $('#profile_pic_default').attr('src');
    var form = document.forms.namedItem("getImage");
    var formdata = new FormData(form);
    console.log(formdata);
    console.log(profile_pic);
    console.log(headerPic);
    $.ajax({
    url: '/employee/saveProfilePic/' + $("#user_id").val(),
            data: formdata,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (result)
            {
            var response = JSON.parse(result);
            if (response.status == true) {
            $('#profile_pic').attr('src', response.path);
            $('#profile_pic_default').attr('src', response.path);
            } else {
            alert(response.message);
            $('#profile_pic').attr('src', profile_pic);
            $('#profile_pic_default').attr('src', headerPic);
            }
            }
    });
    });
    //form validation

    $('#exit_from_data').formValidation({

    message: 'This value is not valid',
            icon: {
            validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                join_date: {
                validators: {
                notEmpty: {
                message: 'Joining date is required',
                },
                }
            },
                employee_exit_date: {
                    validators: {
                    notEmpty: {
                    message: 'Employee Exit date is required',
                    },
                    }
                },
                    employee_email_id: {
                    validators: {
                    notEmpty: {
                    message: 'Email Id is required',
                    },
                    regexp: {
                      regexp: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
                      message: 'Official Email is not valid.'
                    },
                    remote: {
                        headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                        url: '/employee/checkoffcialemailid',
                        type: 'POST',
                        async: false,
                        message: "Office Email Already Exist"
                    }
                    }
            },
        }

    }).on('success.form.fv', function(e){

    e.preventDefault();
    var frmData = $('#exit_from_data').serialize();
    var token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/employee/approvalRequest',
            type: "post",
            data: frmData,
            beforeSend: function () {
            $('[class="loader"]').show();
            $(".overlay").show();
            },
            complete: function () {
            $('[class="loader"]').hide();
            $(".overlay").hide();
            },
            success: function (respData)
            {
            $("#comments").val('');
            alert(respData);
            window.location.reload();
            //$("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+' <button type="button" class="close" data-dismiss="alert"></button></div></div>');
            //$(".alert-success").fadeOut(20000)

            }
    });
    });
    function historyapprovalhrmsdata(emp_id) {

    var token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/employee/gethistoryofapproval/' + emp_id,
            success: function (data)
            {
            $('#hrmshistoryContainer').html(data.historyHTML);
            }
    });
    }



    var date_input = $('input[name="employee_exit_date"]'); //our date input has the name "date"
    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
    var options = {
    format: 'dd-M-yyyy',
            container: container,
            todayHighlight: true,
//            startDate: '+0d',
            autoclose: true,
    };
    date_input.datepicker(options).on('changeDate', function(e) {
    // Revalidate the date field
    $('#exit_from_data').formValidation('revalidateField', 'employee_exit_date');
    });
    var date_input = $('input[name="join_date"]'); //our date input has the name "date"
    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
    var options = {
    format: 'dd-M-yyyy',
            container: container,
            todayHighlight: true,
            autoclose: true,
    };
    date_input.datepicker(options).on('changeDate', function(e) {
    // Revalidate the date field
    $('#exit_from_data').formValidation('revalidateField', 'join_date');
    });

    function wrap_text()
    {
        var off_email_text=$("#preview_email_id").text();
        var off_email =$.trim(off_email_text).length;
        var email_text=$("#preview_email_id").text();
         var email =$.trim(email_text).length;
        if(off_email>28)
        {
            var split_var = off_email_text.split("@");
            $("#preview_office_email_id").html(split_var[0]+"<br>"+"@"+split_var[1]);
        }
        if(email>28)
        {
            var split_var = email_text.split("@");
            $("#preview_email_id").html(split_var[0]+"<br>"+"@"+split_var[1]);
        }
    }
    $(document).on('click', '.delete_educations', function () {
        var docId = $(this).attr("id");
        if (confirm('Do you want to delete this record?')) {
            deleteEducation(docId);
            $(this).closest('tr').remove();
            $('#flass_message').text('Education type deleted successfully');
            $('#alert_msg_div').show();
            $('#alert_msg_div').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
            $('#alert_msg_div').not('.alert-important').delay(5000).fadeOut("450");
            $('html, body').animate({scrollTop: '0px'}, 800);
            var tbody = $("#emp_education_table tbody");
            if (tbody.children().length == 0) 
            {
                $("#table_msg").show();
            }
        }
    });
    function deleteEducation(id)
    {
    $.ajax({
    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/employee/deleteeducation/" + id,
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (response) {
                $('#ajaxResponse').html(response);
                console.log(response);
            },
            error: function (response) {
            $('#ajaxResponse').html('Unable to delete');
            }
    });
    }


function showhideimg(){
        $('#myModal').modal('toggle');
    }

   

    var start = new Date();    
    var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
    $('#certified').datepicker({        
        endDate: '+0d', 
        autoclose: true,
        format: 'dd-M-yyyy'    
    }).on('changeDate', function () 
    {      
      stDate = new Date($(this).val());
      $('#valid_upto').datepicker('setStartDate', stDate);   
    });

    $('#valid_upto').datepicker({       
        startDate: start,
        endDate: end,
        autoclose: true,        
        format: 'dd-M-yyyy'    
    }).on('changeDate', function () 
    {        
        $('#certified').datepicker('setEndDate', new Date($(this).val()));    
    });

   
    $(document).on('click', '.delete_cert', function () {
        var docId = $(this).attr("id");
        if (confirm('Do you want to delete this certification?')) {
            delete_certifications(docId);
            $(this).closest('tr').remove();
            $('#flass_message').text('Deleted successfully');
            $('#alert_msg_div').show();
            $('#alert_msg_div').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
            $('#alert_msg_div').not('.alert-important').delay(5000).fadeOut("450");
            $('html, body').animate({scrollTop: '0px'}, 800);
            var tbody = $("#emp_certification_table tbody");
            if (tbody.children().length == 0) 
            {
                $("#cer_table_msg").show();
                $("#cer_table_msg").text("No Records Found.");
            }
        }
    });
    function delete_certifications(emp_cer_id)
    {
        var emp_id = $("#user_id").val();
        var token = $("#csrf-token").val();
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/employee/deleteCertification/' + emp_cer_id+'/'+emp_id,
            async:false,
            success: function (data)
            {
                $('#ajaxResponse').html(data.message);
                
            },error: function (response) {
            $('#ajaxResponse').html('Unable to delete');
            }
        });
    }
    var spouse_dob = $('input[name="spouse_dob"]'); 
    var ch1_dob = $('input[name="child_one_dob"]'); 
    var ch2_dob = $('input[name="child_two_dob"]'); 

    $('#add_hrm_skill').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            skill: {
                validators: {
                    notEmpty: {
                        message: 'Please select skill'
                    },
                }
            },
            
        }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#add_hrm_skill').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/employee/saveskills',
        data: frmData,
        beforeSend: function () {
            $('[class="loader"]').show();
            $(".overlay").show();
            },
            complete: function () {
            $('[class="loader"]').hide();
            $(".overlay").hide();
            },
        success: function (data)
        {
                $('#emp_skill_id_master').val('');
                $('#skill').val('');
                $('#historyContainer').html(data.historyHTML.historyHTML);
                $('#add_hrm_skill').data("formValidation").resetForm(true);
                $("#skill").val('');
                if(data.data == 'Success'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Skill added successfully<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                } else if(data.data == 'Danger'){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">You have already added this skill<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                    $(".alert-success").fadeOut(10000);
                }
        }
    });
});
   
function loadSkillsData(emp_id,myProfile_id,editColAccess){
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/employee/getskillsbyemployeeid/'+emp_id,
        success: function (respData)
        {
           $('#historyContainer').html(respData.historyHTML);
    
        }
    });
   }


function deleteskill(emp_id){

    token  = $("#csrf-token").val();
        var skill_delete = confirm("Are you sure you want to delete this skill ?"), self = $(this);
            if ( skill_delete == true )
            {
              $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "POST",
                url: '/employee/deleteskill/'+emp_id,
                 beforeSend: function () {
                    $('[class="loader"]').show();
                    $(".overlay").show();
                    },
                    complete: function () {
                    $('[class="loader"]').hide();
                    $(".overlay").hide();
                    },
                success: function (respData)
                {
                $(".delete_"+emp_id).remove();
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Skill Deleted Successfully!<button type="button" class="close" data-dismiss="alert"></button></div></div>');
                $(".alert-success").fadeOut(20000);


        }
    });
    }
}

    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
    var options1 = {
    format: 'dd-M-yyyy',
            container: container,
            todayHighlight: true,
            autoclose: true,
            endDate: '+0d',
    };
    spouse_dob.datepicker(options1);
    ch1_dob.datepicker(options1);
    ch2_dob.datepicker(options1);

    $("#insurance_edit_btn").click(function(){
        insurDiable();
    });
    function insurDiable()
    {
        var marital_status = $("#preview_marital").text();
        console.log(marital_status);
        marital_status = $.trim(marital_status);
        if(marital_status == "Single")
        {
            $(".diable_insu").prop("disabled", true);
        }else
        {
            $(".diable_insu").prop("disabled", false);
        }
    }

    // ajax search by name
$( "#skill" ).autocomplete({
        minLength:1,
        source: '/employee/getskilllist',
        select: function (event, ui) {
            var label = ui.item.label;
            var firstname = ui.item.firstname;
             var skill_id = ui.item.skill_id;
            $("#emp_skill_id_master").val(skill_id);
        }
});

$("#showskill").click(function(e){
    $('.hrmsskills').toggle();
});


$(function(){
 $("#appStatus").on('change', function(event){
   var nxtStatus = $('option:selected',this).attr("data-status");
   var condition = $('option:selected',this).attr("data-condition");
    $("#nextstatusname").val(nxtStatus);
    $("#condition").val(condition);
 })
  
});

$('#change_password_button').click(function (){
    token  = $("#csrf-token").val();
    var formValid = $('#changePasswordForm').formValidation('validate');
     formValid = formValid.data('formValidation').$invalidFields.length;
      if(formValid !=0){
        return false;
      }
   else{
    $('#change_password_button').attr('disabled',false);
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        url: '/employee/changePassword',
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
                        //token : $("#csrf-token").val(),
                        headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                        url: '/employee/checkPassword',
                        data: {oldpassword: $('[name="oldpassword"]').val(),empid:$('[name="empid_update_password"]').val()},
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
     $('#newpassword').keyup(function(){
        verifyNewPassword();
        verifyConfirmPassword();
    });
    $('#confirmpassword').keyup(function(){
        verifyNewPassword();
        verifyConfirmPassword();
    });

    function verifyNewPassword(){
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
    };

    function verifyConfirmPassword(){
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
    };


    $('#edit_pan_card_file').click(function ()
    {
       $('#file_open_pan').trigger('click');
    });

    $('#file_open_pan').on('change', function (e) {
        var pan_card_image = $('#edit_pan_card_image').attr('src');
        var form = document.forms.namedItem("submit_form");
        var formdata = new FormData(form);
        $.ajax({
        url: '/employee/savePanPic/' + $("#user_id").val(),
            data: formdata,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (result)
            {
                var response = JSON.parse(result);
                if (response.status == true) {
                   $('#edit_pan_card_image').attr('src', response.path);
                } else {
                alert(response.message);
                   $('#edit_pan_card_image').attr('src', edit_pan_card_image);
                }
                location.reload();
            }
        });
    });

    $('#edit_aadhar_file').click(function ()
    {
       $('#file_open_aadhar').trigger('click');
    });
    $('#file_open_aadhar').on('change', function (e) {
        var aadhar_image = $('#edit_aadhar_image').attr('src');
        var form = document.forms.namedItem("submit_form");
        var formdata = new FormData(form);
        $.ajax({
        url: '/employee/saveAadharPic/' + $("#user_id").val(),
            data: formdata,
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (result)
            {
                var response = JSON.parse(result);
                if (response.status == true) {
                   $('#edit_aadhar_image').attr('src', response.path);
                   $('#show_edit_aadhar').attr('src', response.path);
                } else {
                alert(response.message);
                   $('#edit_aadhar_image').attr('src', edit_aadhar_image);
                   $('#show_edit_aadhar').attr('src', edit_aadhar_image);
                }
                location.reload();
            }
        });
    });


</script>
@stop