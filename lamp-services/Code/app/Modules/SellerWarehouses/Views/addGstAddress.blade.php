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
            <li class="active"><a id="tab1" href="#tab_11" data-toggle="tab">GST Address </a></li>
          </ul>
          <div class="tab-content headings">
            <div class="tab-pane active" id="tab_11" >
              <form id="submit_form_gst" autocomplete ="off">
                <input type="hidden" name="gst_latitude" id="gst_latitude" value="" />
                <input type="hidden" name="gst_logitude" id="gst_logitude" value="" />
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="hidden" name="bu_ids" id="bu_ids" value="" />
               
                <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Business Unit<span class="required" aria-required="true">*</span></label>
                        <select class="form-control select2me" name="businessUnit1" id="businessUnit1"></select>
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Display Name<span class="required" aria-required="true">*</span></label>
                       <input type="text" name="display_name" id="display_name" class="form-control">
                    </div>
                </div> 
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">GSTIN Number <span class="required" aria-required="true">*</span></label>
                      <input type="text" name="tin_number" id="tin_number" class="form-control">
                    </div>
                  </div>
                    <div class="col-md-3">
                    <div class="form-group">
                    <label class="control-label">Jurisdiction<span class=""></span></label>
                    <input type="text" name="Jurisdiction_id" id="Jurisdiction_id" class="form-control">
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
                        <option value="">--Select State--</option>
                        @foreach($states as $key => $state)
                        <option value="{{$state->state_id}}">{{$state->state}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                          <select name="gst_city" id="gst_city" class="form-control">
                                       
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="gst_address1"  id="gst_address1" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Address 2 </label>
                          <input type="text" name="gst_address2" id="wh_address2" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">FSSAI</label>
                      <input type="text" name="fssai" id="fssai" class="form-control">
                    </div>
                  </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                          <input type="text" name="gst_pincode" id="gst_pincode" class="form-control">
                        </div>
                      </div>
                       <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Landmark<span class=""></span></label>
                          <input type="text" name="gst_landmark" id="gst_landmark" class="form-control">
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                   <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Country<span class="required" aria-required="true">*</span></label>
                      <select name="gst_country" id="gst_country" class="form-control">
                        <option value="">--Select Country--</option>
                        @foreach($countries as $key => $country_value)
                        <option value="{{$country_value->country_id}}">{{$country_value->country}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                     <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Email<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="email" value="" id="email" class="form-control">
                    </div>
                  </div>
                     </div>
                  </div>
                  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBq_oSY3B2sPg9h606CT_TLYJ5COErzW-A&libraries=places"></script> 
                  <script type="text/javascript">
                      window.onload = function () {
                            var latt = $( "#gst_latitude" ).val();
                            var logg = $( "#gst_logitude" ).val();
                              if(latt == ''){
                                 latt = 17.3850;  
                              }
                              if(logg == ''){
                                logg = 78.4867;  
                              }
                              var mapOptions = {
                                  center: new google.maps.LatLng(latt, logg),
                                  zoom: 8,
                                  mapTypeId: google.maps.MapTypeId.ROADMAP
                              };
                              var infoWindow = new google.maps.InfoWindow();
                              var latlngbounds = new google.maps.LatLngBounds();
                              var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
                              var myLatLng = {lat: latt, lng: logg};
                              var marker = new google.maps.Marker({
                              position: myLatLng,
                              map: map,
                              title: 'Your GST Address'
                          });
                          
                          google.maps.event.addListener(map,'mousemove',function() {
                                          google.maps.event.trigger(map, 'resize');
                          });
                          
                          var input = document.getElementById("keyword");
                          var autocomplete = new google.maps.places.Autocomplete(input);
                          autocomplete.bindTo("bounds", map);
                      
                          var marker = new google.maps.Marker({map: map});
                      
                          google.maps.event.addListener(autocomplete, "place_changed", function()
                          {
                          var place = autocomplete.getPlace();
                          var search_lat = place.geometry.location.lat();
                          var search_lng = place.geometry.location.lng();
                          $('#gst_lat').val(search_lat);
                          $('#gst_log').val(search_lng);
                      
                          if (place.geometry.viewport) {
                          map.fitBounds(place.geometry.viewport);
                          } else {
                          map.setCenter(place.geometry.location);
                          map.setZoom(15);
                          }
                      
                          marker.setPosition(place.geometry.location);
                          });
                      
                      google.maps.event.addListener(map, "click", function(event)
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
                  
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Latitude</label>
                          <input type="text" name="gst_lat" id="gst_lat" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Longitude</label>
                          <input type="text" name="gst_log" id="gst_log" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Contact Name<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="contact_name" value="" id="contact_name" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="control-label">Phone Number<span class="required" aria-required="true">*</span></label>
                      <input type="text" name="phone_no" value="" id="phone_no" class="form-control">
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
<script src="{{ URL::asset('assets/global/plugins/getBusinessUnitsDowpDown.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
   <script type="text/javascript">
    function Redirect() {
               window.location = "/warehouse/gstaddress";
        }  

     $('#save_gstaddress').click( function(){
            var formValid = $('#submit_form_gst').formValidation('validate');
            formValid = formValid.data('formValidation').$invalidFields.length;
            //alert(formValid);
             if(formValid != 0){
               return false;
             }else{
               $.ajax({
               url: '/warehouse/saveGStAddress',
               data: $('#submit_form_gst').serialize(),
               type: 'POST',
               success: function (result){
               var response = JSON.parse(result);
                  if(response.status == true){
                     alert(response.message);
                     setTimeout('Redirect()', 1000);
                    
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
    
   $('#submit_form_gst').formValidation({
           framework: 'bootstrap',
               icon: {
                 validating: 'glyphicon glyphicon-refresh'
             },
           fields: {
               gst_state:{
                   validators: {
                       
                    notEmpty: {
                            message: ' '
                        },
                       regexp: {
                        regexp: /^[a-zA-Z0-9 \-,\#]+$/i,
                        message: ' '
                     },
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
               
           tin_number:{
              validators: {
                  notEmpty: {
                      message: ' '
                  },
                  regexp: {
                    regexp: /^([0][1-9]|[1-2][0-9]|[3][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/i,
                    message: 'Please enter valid GSTIN number '
                  },
                  remote: {
                    url: '/warehouse/checkgstin',
                    data: function (validator, $field, value){
                        return{
                          tin_number: $('[name="tin_number"]').val(),
                           billing_id: $('#billing_id').val(),
                        };
                      },
                    type: 'POST',
                    delay: 1000,
                    message: 'GSTIN Number already exists  or Invalid State code'
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
            fssai: {
              validators: {
                regexp: {
                  regexp: /^\d{14}$/ ,
                  message: 'Please enter valid fssai number'
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
</script>
<script type="text/javascript">
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