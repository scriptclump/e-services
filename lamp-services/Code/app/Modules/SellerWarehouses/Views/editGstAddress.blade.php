@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> ADD GST Address </div>
        <div class="tools"> </div>
        <input type="hidden" id="save_id" name="save_id">
      </div>
      <div class="portlet-body">
        <div class="tabbable-line">
          <ul class="nav nav-tabs ">
            <li class="active"><a id="tab1" href="#tab_11" data-toggle="tab">GST Address Information</a></li>
          </ul>
          <div class="tab-content headings">
            <div class="tab-pane active" id="tab_11" >
              <form id="update_form_gst">
                <input type="hidden" name="gst_latitude" id="gst_latitude" value="" />
                <input type="hidden" name="gst_logitude" id="gst_logitude" value="" />
                  <input type="hidden" name="billing_id" id="billing_id" value="{{$gst_edit_list->billing_id}}">
                  <input type="hidden" name="status" id="status" value="{{$gst_edit_list->status}}">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="hidden" name="bu_ids" id="bu_ids" value="{{$gst_edit_list->bu_id}}" />
               
                <div class="row">
                 
                <div class="col-md-3">
                          <div class="form-group">
                            <label class="control-label">Business Unit<span class="required" aria-required="true">*</span></label>
                              <select class="form-control select2me" name="businessUnit2" id="businessUnit2"></select>
                          </div>
                      </div>
                 <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Display Name<span class="required" aria-required="true">*</span></label>
                        <input type="text" name="display_name" id="display_name" value="{{$gst_edit_list->display_name}}" class="form-control">
                    </div>
                </div> 
                <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">GSTIN Number <span class="required" aria-required="true">*</span></label>
                      <input type="hidden" id="gst_state_code" value=""/>
                      <input type="text" name="tin_number" id="tin_number" value= "{{$gst_edit_list->gstin}}" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Jurisdiction<span class=""></span></label>
                          <input type="text" name="Jurisdiction_id" value= "{{$gst_edit_list->jurisdiction}}" id="Jurisdiction_id" class="form-control">
                        </div>
                      </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                      <select name="gst_state" id="gst_state" class="form-control">
                          <option value="">Please select State.</option>
                              @if(isset($states))
                                @foreach($states as $state)
                                  @if($state->state_id == $gst_edit_list->state)
                          <option value="{{$state->state_id}}" selected="true">{{$state->state}}</option>
                              @else
                          <option value="{{$state->state_id}}" >{{$state->state}}</option>
                              @endif
                                @endforeach
                                  @endif
                      </select>
                    </div>
                  </div>

                 <div class="col-md-6">
                  <div class="form-group">
                      <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                      <input type="text"  name="gst_city" value="{{$gst_edit_list->city}}" id="gst_city" class="form-control">
                  </div>
                </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="gst_address1" value= "{{$gst_edit_list->address1}}"  id="gst_address1" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Address 2 </label>
                          <input type="text" value= "{{$gst_edit_list->address2}}" name="gst_address2" id="wh_address2" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">FSSAI</label>
                      <input type="text" name="fssai" value= "{{$gst_edit_list->fssai}}" id="fssai" class="form-control">
                    </div>
                  </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="gst_pincode" value= "{{$gst_edit_list->pincode}}"  id="gst_pincode" class="form-control">
                        </div>
                      </div>
                       <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Landmark<span class=""></span></label>
                          <input type="text" name="gst_landmark" value= "{{$gst_edit_list->landmark}}" id="gst_landmark" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Country<span class="required" aria-required="true">*</span></label>
                     <select name="gst_country" id="gst_country" class="form-control">
                              <option value="">Please select Country</option>
                              @if(isset($countries))
                              @foreach($countries as $country_value)
                              @if($gst_edit_list->country == $country_value->country_id)
                              <option value="{{$country_value->country_id}}" selected="true">{{$country_value->country}}</option>
                              @else
                              <option value="{{$country_value->country_id}}">{{$country_value->country}}</option>
                              @endif
                              @endforeach
                              @endif
                    </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Email<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="email" value= "{{$gst_edit_list->email}}" id="email" class="form-control">
                    </div>
                  </div>
                </div>
                  </div>
                  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBq_oSY3B2sPg9h606CT_TLYJ5COErzW-A&libraries=places"></script> 
                  <script type="text/javascript">
                      window.onload = function () {

                      var latt = Number($("#gst_lat").val());
                      var logg = Number($("#gst_log").val());
                      if ( latt == '' ){
                      latt = 17.3850;
                      }
                      if ( logg == '' ){
                      logg = 78.4867;
                      }
                      var mapOptions = {
                      center: new google.maps.LatLng(latt, logg),
                      zoom: 18,
                      mapTypeId: google.maps.MapTypeId.ROADMAP
                      };
                      var infoWindow = new google.maps.InfoWindow();
                      var latlngbounds = new google.maps.LatLngBounds();
                      var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);

                      //var myLatLng = {lat: 17.3850, lng: 78.4867};
                      var myLatLng = {lat: latt, lng: logg};
                      var marker = new google.maps.Marker({
                      position: myLatLng,
                      map: map,
                      title: 'Your GST Address'
                      });

                      google.maps.event.addListener(map, 'mousemove', function () {
                      google.maps.event.trigger(map, 'resize');
                      });

                      var input = document.getElementById("keyword");
                      var autocomplete = new google.maps.places.Autocomplete(input);
                      autocomplete.bindTo("bounds", map);

                      var marker = new google.maps.Marker({map: map});

                      google.maps.event.addListener(autocomplete, "place_changed", function ()
                      {
                      var place = autocomplete.getPlace();
                      var search_lat = place.geometry.location.lat();
                      var search_lng = place.geometry.location.lng();
                      $('#gst_lat').val(search_lat);
                      $('#gst_log').val(search_lng);

                      if ( place.geometry.viewport ) {
                         map.fitBounds(place.geometry.viewport);
                      } else {
                         map.setCenter(place.geometry.location);
                         map.setZoom(15);
                      }

                      marker.setPosition(place.geometry.location);
                      });

                      google.maps.event.addListener(map, "click", function (event)
                      {
                      marker.setPosition(event.latLng);
                      });

                      google.maps.event.addListener(map, 'click', function (e) {
                      //alert("Latitude: " + e.latLng.lat() + "\r\nLongitude: " + e.latLng.lng());
                      $('#gst_lat').val(e.latLng.lat());
                      $('#gst_log').val(e.latLng.lng());
                      });
                      }
                                             </script>
                  <div class="col-md-6">
                    <div id="dvMap"></div>
                    <div class="input-icon"> <i class="fa fa-bars" style="position: absolute;top: -250px;left: 2px;"></i>
                      <input type="text" class="form-control" name="keyword" id="keyword" style="position: absolute;top:-250px; left:4px;z-index: 2; width:260px;" />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Latitude</label>
                          <input type="text" name="gst_lat" id="gst_lat" value= "{{$gst_edit_list->latitude}}" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="control-label">Longitude</label>
                          <input type="text" name="gst_log" value= "{{$gst_edit_list->longitude}}" id="gst_log" class="form-control">
                        </div>
                      </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Contact Name<span class="required" aria-required="true">*</span></label>
                      <input type="text" value= "{{$gst_edit_list->contact_name}}" name="contact_name" value="" id="contact_name" class="form-control">
                    </div>

                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Phone Number<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="phone_no" value= "{{$gst_edit_list->phone_no}}"   id="phone_no" class="form-control">
                    </div>
                  </div>
                  
                </div>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <input type="button" name="" id="save_gstaddress" class="btn green-meadow" value="Save">
                    <a href="/warehouse/gstaddress" id="cancel3" class="btn default"> Cancel </a> </div>
                </div>
              </form>
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
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">

.help-block {
    width: 270px !important;
}
.help-block {
    text-align: left;
    padding: 0px 10px;
}
.tabbable-line>.tab-content{padding-top:20px !important;}
.portlet.box .portlet-body {
     border: 0px solid #ccc !important;
}

.glyphicon-remove{color: #e02222 !important;}
.glyphicon-ok {
    color: #3c763d !important;
}
.has-feedback label~.form-control-feedback {
    top: 34px !important;
}
#dvMap{height:304px !important; width:100% !important;}
.fileinput-exists .fileinput-new, .fileinput-new .fileinput-exists{
    display: run-in !important;
}
.thumbnail img {display:run-in!important; max-height: 100% !important;}
.thumbnail{padding: 0px !important; border: 0px !important;}
.mt-checkbox, .mt-radio{margin-right: 20px;}
</style>
@stop
@section('script')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('js/helper.js')}}

@stop
@section('userscript') 
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script> 
<!-- <script src="{{ URL::asset('assets/global/plugins/getBusinessUnitsDowpDown.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
   <script type="text/javascript">
   $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/bussinessunits',
            type: 'GET',                                             
            success: function (rs) 
            {
                var buid = $("#bu_ids").val();
                $("#businessUnit2").html(rs);
                var  eq = $("#businessUnit2").select2().select2('val',buid);
            }
        });
  
    function Redirect() {
               window.location = "/warehouse/gstaddress";
        }  
    $('#save_gstaddress').click( function(){
      var formValid = $('#update_form_gst').formValidation('validate');
      formValid = formValid.data('formValidation').$invalidFields.length;
      //alert(formValid);
      if(formValid != 0){
        return false;
      }else{
        var id = $('#billing_id').val();
        $.ajax({
          url: '/warehouse/updateGstAddress/' + id,
          data: $('#update_form_gst').serialize(),
          type: 'POST',
          success: function (result) {
            var response = JSON.parse(result);
            console.log(response);
            if(response.status == true){
              alert(response.message);
              setTimeout('Redirect()',1000);
            }               
          }
        });
      }
    });

    $.ajaxSetup({
      headers:
      {
        'X-CSRF-Token': $('input[name="_token"]').val()
      }
    });
    
   $('#update_form_gst').formValidation({
           framework: 'bootstrap',
               icon: {
                 validating: 'glyphicon glyphicon-refresh'
             },
           fields: {
               gst_state:{
                   validators: {
                    remote: {
                        url: '/warehouse/checkstate',
                        data: function(validator, $field, value){
                          return {
                           gst_state: $('#gst_state').val(),
                            billing_id: $('#billing_id').val(),
                          };
                        },
                        type: 'POST',
                        delay: 1000,
                        message: 'State name already exists'
                    },
                       notEmpty: {
                           message: ' '   
                       },
                       regexp: {
                        regexp: /^[a-zA-Z0-9 \-,\#]+$/i,
                        message: ' '
                     },
                }
               },
               wh_code:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 _"!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        }
                   }
               },
               gst_address1:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        }
                   }
               },
              
              wh_address2:{
                  validators:{
                     regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.\-\,\/,\#,\:]+$/,
                            message: ' '
                        },
                  }
              },
                gst_pincode:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       },
                        regexp: {
                            regexp: /^\d{6}$/,
                            message: 'Please enter valid pincode'
                        }
                   }
               },
               gst_city:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
           tin_number: {
              validators: {
                notEmpty: {
                  message: ' '
                },
                regexp: {
                  regexp:  /^([0][1-9]|[1-2][0-9]|[3][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/i,
                  message: 'Please enter valid gstin number'
                },
                remote: {
                  url: '/warehouse/checkgstin',
                  data: function (validator, $field, value) {
                      return {
                         tin_number: $('[name="tin_number"]').val(),
                         billing_id: $('#billing_id').val(),
                      };
                    },
                  type: 'POST',
                  delay: 1000,
                  message: 'Gstin number already exists  or invalid state code'
                },
              }
            },
        
              businessUnit1: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
                display_name:{
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },                 
               gst_country:{
                   validators: {
                   notEmpty: {
                           message: ' '
                       }
                   }
               },
               gst_log:{
                validators:{
                  between: {
                        min: -180,
                        max: 180,
                        message : ' '
                    }
                }
               },
               gst_lat:{
                validators: {
                    between: {
                        min: -90,
                        max: 90,
                        message: ' '
                    }
                }
               },
              fssai: {
              validators: {
                regexp: {
                  regexp: /^\d{14}$/ ,
                  message: 'Please enter valid fssai number'
                }
              }
            },
               contact_name:{
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
            email: {
              validators: {
                notEmpty: {
                  message: ' '
                },
                regexp: {
                  regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                  message: ' '
                }
              }
            },
            phone_no: {
              validators: {
                notEmpty: {
                  message: ' '
                },
                regexp: {
                  regexp: /^\d{10}$/ ,
                  message: 'Please enter valid phone number'
                }
              }
            }
           }
       }).on('success.form.fv', function(event) {
             event.preventDefault();
             console.log('here in success');
       }); 

    $('#gst_state').on('change', function() {
        var state_id=$(this).val();
        var token  = $("#csrf-token").val();
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/getcitiesbystateid',
        data:{
            state_id:state_id,
        },
        success: function (respData)
        { 
        $("#gst_city").html(respData);  
        }
    });
});


</script>
@stop
@extends('layouts.footer')