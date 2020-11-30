@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> PROFILE </div>
        <div class="tools"> <a href="/seller/index" id="back"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a>
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
                <li class="active"> <a data-toggle="tab" href="#tab_1-1"> Basic Information</a> <span class="after"> </span> </li>
                <li> <a data-toggle="tab" href="#tab_2-2">Documents </a> </li>
                <li> <a data-toggle="tab" href="#tab_3-3">Business Information</a> </li>
                <li> <a data-toggle="tab" href="#tab_4-4">Bank Information </a> </li>
              </ul>
            </div>
            </form>
            <div class="col-md-10">
              <div class="tab-content">
                <div id="tab_1-1" class="tab-pane active">
                <div id="default_show1">
                <div class="col-md-12 actions " style="margin-bottom:10px;">
                    <h3 align="center"><strong>Basic Info</strong></h3>
                </div>
                 <div class="row form-group">
                     <label class="control-label col-md-2">First Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_firstname">@if(isset($user_data->firstname)){!! $user_data->firstname !!}@endif </p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Last Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_lastname">@if(isset($user_data->lastname)) {!! $user_data->lastname !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Email :</label>
                     <div class="col-md-10">
                      <p class="form-control-static">@if(isset($user_data->email_id)) {!! $user_data->email_id !!}@endif </p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Mobile No :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_mobile_no">@if(isset($user_data->mobile_no)) {!! $user_data->mobile_no !!}@endif </p> </div>
                    </div>
                </div>     
                
                </div>
                <div id="tab_2-2" class="tab-pane">
                  <p> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. </p>
                 
                </div>
                <div id="tab_3-3" class="tab-pane">
                 <div id="default_show2">
                   <div class="col-md-12 actions " style="margin-bottom:10px;">
                    <h3 align="center"><strong>Business Info</strong></h3>
                  </div>
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
                     <label class="control-label col-md-2">Pincode :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_pin">@if(isset($business_info->pincode)) {!! $business_info->pincode !!}@endif </p> 
                      </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Pan Number :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_pan">@if(isset($business_info->pan_number)) {!! $business_info->pan_number !!}@endif </p> 
                      </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Tin Number :</label>
                      <div class="col-md-10">
                       <p class="form-control-static" id="p_tin">@if(isset($business_info->tin_number)) {!! $business_info->tin_number !!} @endif</p> 
                      </div>
                    </div>
                  </div>     
                </div>
                <div id="tab_4-4" class="tab-pane">
                   <div id="default_show4">
                <div class="col-md-12 actions " style="margin-bottom:10px;">
                    <h3 align="center"><strong>Bank Info</strong></h3>
                </div>
                 <div class="row form-group">
                     <label class="control-label col-md-2">Account Holder Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_ac_name">@if(isset($bank_details->account_name)) {!! $bank_details->account_name !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Bank Name :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_bank_name">@if(isset($bank_details->bank_name)) {!! $bank_details->bank_name !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Account No :</label>
                     <div class="col-md-10">
                      <p class="form-control-static" id="p_ac_no"> @if(isset($bank_details->account_no)){!! $bank_details->account_no !!}@endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Account Type :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_ac_type">@if(isset($bank_details->account_type)) {!! $bank_details->account_type !!} @endif</p> </div>
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
                     <label class="control-label col-md-2">MICR Code :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_micr">@if(isset($bank_details->micr_code)) {!! $bank_details->micr_code !!} @endif</p> </div>
                    </div>
                    <div class="row form-group">
                     <label class="control-label col-md-2">Currency :</label>
                     <div class="col-md-10">
                       <p class="form-control-static" id="p_curr">@if(isset($bank_details->currency_code)) {!! $bank_details->currency_code !!} @endif</p> </div>
                    </div>
                </div>     
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
@section('style')
<style>
.control-label{
	text-align:right !important;
	 padding-top: 9px;
	}
	.form-group {
    margin-bottom: 5px !important;
}
 .glyphicon-remove, .glyphicon-ok, .glyphicon-refresh{right: 10px !important;}
</style>
@stop
@section('userscript')
{{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
{{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script type="text/javascript">
$(document).ready(function (){
 

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
@stop
@extends('layouts.footer')