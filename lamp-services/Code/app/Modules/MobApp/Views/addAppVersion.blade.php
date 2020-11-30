@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="portlet light">
    <div class="row">
        <div class="col-md-12">
            <div id="form_wizard_1" class="portlet box">
                <div class=" tabbable-line">
                    <ul class="nav nav-tabs "></ul>
                   </div>
                  </div>
                   <div class="portlet-title">
                    <div class="caption"> <span class="caption-subject font-purple-soft bold uppercase"> {{$add_update_flag}} App Version</span> 
              </div>
           </div>
        </div>
     </div>
     <br>
     <div class="portlet light">                
        <form  action = @if(empty($update)) {{"/mobapp/saveappversion"}} @else {{"/mobapp/updateId"}} @endif  method="POST" id = "frm_version_template" name = "frm_version_template" >
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
        <input type = "hidden" name = "version_id" id = "version_id" @if(!empty($update)) { value = "{{$update->version_id}}" } @endif />

        <div class="row">
            <div class="col-md-2">
                <label for="version_name">Version Name</label>
                  </div>
                    <div class="col-md-6">
                <input type="text" class="form-control" name="version_name" id="version_name" @if(!empty($update)) { value = "{{$update->version_name}}" } @endif />
                @if ($errors->has('version_name'))<p style="color:red;">{!!$errors->first('version_name')!!}</p>@endif
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-2">
                <label for="version_number">Version Number</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="version_number" id="version_number" @if(!empty($update)) { value = "{{$update->version_number}}" } @endif />
                @if ($errors->has('version_number'))<p style="color:red;">{!!$errors->first('version_number')!!}</p>@endif
            </div>
        </div>
            <br>
        <div class="row">
            <div class="col-md-2">
                <label for="app_type">App Type</label>
            </div>
             <div class="col-md-6">
                @if(empty($update))
                <select id = "app_type"  name =  "app_type" class="form-control">
                    <option value="">Select</option>
                    <option value="android">Android</option>
                    <option value="ios">IOS</option>
                    <option value="windows">Windows</option>
                </select> 
                @else
                <select id = "app_type"  name =  "app_type" class="form-control">
                    <option value="#">Select</option>
                    <option value="android" @if($update->app_type == 'android') {{'selected'}}@endif>Android</option>
                    <option value="ios" @if($update->app_type == 'ios') {{'selected'}}@endif>IOS</option>
                    <option value="windows" @if($update->app_type == 'windows') {{'selected'}}@endif>Windows</option>
                </select> 
                @endif
                @if ($errors->has('app_type'))<p style="color:red;">{!!$errors->first('app_type')!!}</p>@endif
            </div>
        </div>
        <br>
<div class="row">   
  <div class="col-md-2">
     </div>
         <div class="col-md-6">
                  <div class="col-md-12 text-center"><button type="submit" class="btn btn-primary">Submit</button></div>
         </div>
     </div>
</form>
</div>
</div>

@stop
@section('userscript')

<style type="text/css">
.glyphicon-remove, .glyphicon-ok {
    margin-right: 30px;
}
.glyphicon-remove{
    color: red;
}
.glyphicon-ok {
    color: green;
}
.help-block {
    color: red !important;
}
.help-block {
    width: 100% !important;
}
</style>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/mobapp/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/mobapp/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    
$(document).ready(function() {
    $('#frm_version_template').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            version_name: {
                message: 'version Name is required',
                validators: {
                    notEmpty: {
                        message: 'version Name is required'
                    },
                    stringLength: {
                        min: 3,
                        max: 10,
                        message: 'Version Name is required must be 3 and less than 10 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9._ ]+$/,
                        message: 'The version can only consist of alphabetical, number, space ,dot and underscore'
                    }
                }
            },
          version_number: {
                 message: 'version Number is required',
                  validators: {
                    notEmpty: {
                        message: 'version Number is required'
                     },
                  stringLength: {
                    min: 1,
                      max: 3,
                        message: 'version Number is required must be 3 digit'
                     },
                    regexp: {
                        regexp: /^[0-9.]+$/,
                          message: 'The version can only consist of number and dot '
                     }
                }
            },
           app_type: {
                validators: {
                    notEmpty: {
                        message: 'The App Type is required and can\'t be empty'
                    }
                }
            }
        },
    });
});

</script>
@extends('layouts.footer')
@stop
@extends('layouts.footer')