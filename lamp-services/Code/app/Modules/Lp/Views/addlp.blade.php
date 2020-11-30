@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget" >
      <div class="portlet-title">
        <div class="caption"> LOGISTIC SETUP </div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
      </div>
      <div class="portlet-body">
        <div id="form-wiz" class="portlet-body">
          <div class="tabbable-line">
            <ul class="nav nav-tabs ">
              <li class="active"><a href="#tab_15_1" data-toggle="tab">Logistics Partner Setup </a></li>
              <li><a href="#tab_15_2" data-toggle="tab" id="warehouses_id">Warehouses </a></li>
            </ul>
            
            
            
            <div class="tab-content">
              <div class="tab-pane active" id="tab_15_1">
                <form   id="submit_form"  method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Company Name<span class="required" aria-required="true">*</span>
                        <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span>
                        </label>
                        <input type="text" name="lp_name" class="form-control" id="lp_name" value="@if(isset($logisticPartner->lp_name)){{$logisticPartner->lp_name}}@endif">
                        <input type="number" name="lp_id" style="display:none;" id="lpId" class="form-control" value="@if(isset($logisticPartner->lp_id)){{$logisticPartner->lp_id}}@endif" >
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Description<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="description" id="description" class="form-control" value="@if(isset($logisticPartner->description)){{$logisticPartner->description}}@endif">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Address 1<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="address_1" id="address_1" class="form-control" value="@if(isset($logisticPartner->address_1)){{$logisticPartner->address_1}}@endif">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Address 2</label>
                        <input type="text" name="address_2" id="address_2" class="form-control" value="@if(isset($logisticPartner->address_2)){{$logisticPartner->address_2}}@endif">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Pincode<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="pincode" id="logistic_picode" class="form-control" value="@if(isset($logisticPartner->pincode)){{$logisticPartner->pincode}}@endif">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Country<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <select name="country" id="logistic_country" class="form-control">
                          <option value="">Please select Country</option>
                          
  @if(isset($countries))
    @foreach($countries as $country_value)
    
        @if(isset($logisticPartner->country) && $logisticPartner->country==$country_value['country_id'])
        
                  <option value="{{$country_value['country_id']}}" selected="true">{{$country_value['name']}}</option>
                  
        @else
        
                  <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                  
        @endif
    @endforeach
@endif

                        </select>
                      </div>
                    </div>
                  </div>
                  <?php //echo $logisticPartner->state; ?>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">State<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <?php //echo "<pre>"; print_r($logisticPartner->state); die;?>
                        <select name="state" id="logistic_state" class="form-control">
                          <option value="">Please select State</option>
                          
  @foreach($states_data as $stateVal )
  @if(isset($logisticPartner->state) && $logisticPartner->state == $stateVal->id)
  
                          <option value="{{$stateVal->id}}" selected>{{$stateVal->state_name}}</option>
                          
  @else
  
                          <option value="{{$stateVal->id}}" >{{$stateVal->state_name}}</option>
                          
  @endif  
  
  @endforeach

                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">City<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="city" class="form-control" id="logistic_city" value="@if(isset($logisticPartner->city)){{$logisticPartner->city}}@endif">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Phone<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="phone" class="form-control" id="phone" value="@if(isset($logisticPartner->phone)){{$logisticPartner->phone}}@endif">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Email<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="email" class="form-control" id="email" value="@if(isset($logisticPartner->email)){{$logisticPartner->email}}@endif">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="control-label">Website<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                        <input type="text" name="website" class="form-control" id="website" value="@if(isset($logisticPartner->website)){{$logisticPartner->website}}@endif">
                      </div>
                    </div>
                    
                   
<div class="col-md-6">


<div class="form-group">
<label class="control-label">Upload Logo<span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
<div class="fileinput fileinput-new form-control " data-provides="fileinput">
<div class="input-group" style="margin-top:-7px; margin-right:-13px;">
<div class="uneditable-input input-fixed" data-trigger="fileinput">
<i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"> </span>
</div>
<span class="input-group-btn btn btn blue btn-file">
<span class="fileinput-new"> Browse </span>
<span class="fileinput-exists"> Change </span>
</span>
</div>
<input type="file" name="files" id="files" class="browsehide">
<?php
$bp = url('uploads/logistic_partner');
$base_path = $bp."/";
?>
<a class="files_edit" href="@if(isset($logisticPartner->files)){{$base_path.$logisticPartner->files}}@endif">@if(isset($logisticPartner->files)){{$logisticPartner->files}}@endif</a> </div>
</div>




</div>
                  </div>
                  <h4 class="block">Services Information</h4>
                  <div class="row">
                    <div class="col-md-12">
                  
                  <div class="checkbox-list">
                    <label class="checkbox-inline">
                      <?php  if(isset($logisticPartner->full_service) && $logisticPartner->full_service=='true')
{
	echo'<input type="checkbox" id="inlineCheckbox1" name="full_service" value="" checked>';
}else
{
	echo'<input type="checkbox" id="inlineCheckbox1" name="full_service" value="">';
}
?>
                      Fulfilment Services </label>
                    <label class="checkbox-inline">
                      <?php  if(isset($logisticPartner->for_service) && $logisticPartner->for_service=='true')
{
	echo'<input type="checkbox" id="inlineCheckbox2" name="for_service" value="" checked>';
}else
{
	echo'<input type="checkbox" id="inlineCheckbox2" name="for_service" value="">';
}
?>
                      Forwarding </label>
                    <label class="checkbox-inline">
                      <?php  if(isset($logisticPartner->cod_service) && $logisticPartner->cod_service=='true')
{
	echo'<input type="checkbox" id="inlineCheckbox3" name="cod_service" value="" checked> ';
}else
{
	echo'<input type="checkbox" id="inlineCheckbox3" name="cod_service" value=""> ';
}
?>
                      COD </label>
                  </div>
                  </div>
                  </div>
                  
                  <hr>
                  <div class="row">
                    <div class="col-md-12 text-center">
                     <button type="submit"  class="btn green-meadow btnn"   >Cancel</button> <button type="submit"  class="btn green-meadow btnn"   >Save & Continue</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="tab-pane" id="tab_15_2">
                <div class="row" style="margin-top:5px;">
                  <div class="col-md-12 text-right"> @if(isset($logisticPartner) && $whs_count > 0) <a class="btn green-meadow" href="{{ URL::to('logisticpartners/downloadExcel/xls',['lp_id'=> $logisticPartner->lp_id != '' ? $logisticPartner->lp_id : '']) }}" class="btn btn-primary">Download Excel xls </a> @endif <a class="btn green-meadow default" data-toggle="modal" href="#small">Upload Warehouses</a> <a class="btn green-meadow addwh" data-toggle="modal" id="click_addlp" href="#addlp">Add New Warehouse</a> </div>
                </div>
                <div class="row" >
                  <div class="col-md-12"> &nbsp;</div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <table id="addWarehouseGrid">
                    </table>
                    <!--<table class="table table-striped table-advance table-hover dataTable no-footer" id="sample_editable_1">

<thead>
<tr>

<th>Warehouse Name</th>
<th>Area</th>
<th>City</th>
<th>Email ID</th>
<th>Phone</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<tr>
<td> Ekart Hyderabad </td>
<td> Uppal </td>
<td> Hyderabad </td>
<td>ramakanth@ebutor.com </td>
<td>9876543210 </td>
<td class="center">
<a data-toggle="modal"  href="#editlp"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a>
</td>
</tr>
</tbody>
</table>--> 
                  </div>
                </div>
                
                <hr  style="margin-top:160px;">
                <div class="row">
                  <div class="col-md-12 text-center"> <a class="btn green-meadow" href="{{ URL::to('/logisticpartners') }}" class="btn btn-primary">Back</a> <a class="btn green-meadow" href="{{ URL::to('/logisticpartners') }}" class="btn btn-primary">Done</a> </div>
                </div>
              </div>
            </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<form action="" class="" id="submit_form_wh" method="POST">
  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
  <input type="hidden" name="wh_lp_id" id="wh_lp_id" value="" />
  <input type="hidden" name="wh_latitude" id="wh_latitude" value="" />
  <input type="hidden" name="wh_logitude" id="wh_logitude" value="" />
  <div class="modal fade modal-scroll in" id="addlp" tabindex="-1" role="addlp" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="close" data-dismiss="modal" aria-hidden="true"></button>
          <h4 class="modal-title">ADD WAREHOUSE</h4>
          <input type="hidden" id="status" name="status" value="">
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Warehouse Name <span class="required" aria-required="true">*</span></label>
                <input type="text" name="wh_name" id="wh_name" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Contact Name <span class="required" aria-required="true">*</span></label>
                <input type="text" name="wh_cont_name" id="wh_cont_name" class="form-control">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Email <span class="required" aria-required="true">*</span></label>
                <input type="text" name="wh_email" id="wh_email" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Phone <span class="required" aria-required="true">*</span></label>
                <input type="text" name="wh_phone" id="wh_phone" class="form-control">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                    <input type="text" name="wh_address1"  id="wh_address1" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Address 2 </label>
                    <input type="text" name="wh_address2" id="wh_address2" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                    <input type="text" name="wh_pincode" id="wh_pincode" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                    <input type="text" name="wh_city" id="wh_city" class="form-control">
                  </div>
                </div>
              </div>
              <?php $state = 'ee';?>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                    <select name="wh_state" id="wh_state" class="form-control">
                      <option value="">Please select State.</option>
                          
@if(isset($states_data))
    @foreach($states_data as $state_value)
    
        @if($state_value->state_name == $state)
        
                      <option value="{{$state_value->id}}" selected="true">{{$state_value->state_name}}</option>
                      
        @else
        
                      <option value="{{$state_value->id}}">{{$state_value->state_name}}</option>
                      
        @endif
    @endforeach
@endif

                    </select>
                  </div>
                </div>
              </div>
            </div>
            <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDnlzWPi5o-x-CBj1g46LsN7nMgXcVW5VA&libraries=places"></script> 
            <script type="text/javascript">
        window.onload = function () {
            var latt = $( "#wh_latitude" ).val();
            var logg = $( "#wh_logitude" ).val();
            if(latt == '')
            {
              latt = 17.3850;  
            }
            if(logg == '')
            {
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
            
            //var myLatLng = {lat: 17.3850, lng: 78.4867};
            var myLatLng = {lat: latt, lng: logg};
            var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'Your Ware House'
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
            $('#wh_lat').val(search_lat);
            $('#wh_log').val(search_lng);
			
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
                $('#wh_lat').val(e.latLng.lat());
                $('#wh_log').val(e.latLng.lng());
            });
        }
    </script>
            <div class="col-md-6">
<div id="dvMap"></div>
<div class="input-icon">
<i class="fa fa-search" style="position: absolute;top: -250px;right:12px;"></i>
<i class="fa fa-bars" style="position: absolute;top: -250px;left: 2px;"></i>
<input type="text" class="form-control" name="keyword" id="keyword" style="position: absolute;top:-250px; left:4px;z-index: 2; width:260px;" />
</div>
</div>
          </div>
          <?php $country = 'ee' ?>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Country<span class="required" aria-required="true">*</span></label>
                <select name="wh_country" id="wh_country" class="form-control">
                  <option value="">Please select Country</option>
                      
@if(isset($countries))
    @foreach($countries as $country_value)
    
        @if($country_value['name'] == $country)
        
                  <option value="{{$country_value['country_id']}}" selected="true">{{$country_value['name']}}</option>
                  
        @else
        
                  <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                  
        @endif
    @endforeach
@endif

                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Latitude</label>
                    <input type="text" name="wh_lat" id="wh_lat" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Logitude</label>
                    <input type="text" name="wh_log" id="wh_log" class="form-control">
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <hr>
          
          <div class="row">
            <div class="col-md-12 text-center">
              <button type="button" class="btn green-meadow savewh">Save</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.modal-content --> 
    </div>
    <!-- /.modal-dialog --> 
  </div>
</form>
<form action="" class="" id="submit_formm" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
  <div class="modal fade modal-scroll in bs-modal-md" id="small" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header"> UPLOAD WAREHOUSES
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 text-center"><a class="btn green-meadow" href="{{ URL::to('logisticpartners/downloadTemplate/xls') }}" class="btn btn-primary">Download Warehouses Template</a> </div>
            <div class="col-md-12 text-center" style="margin-top: 20px;">
              <p> Import Warehouses template here.</p>
            </div>
            <div class="col-md-12 text-center">
              <div class="form-group">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                  <div class="input-group input-large">
                    <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput"> <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"> </span> </div>
                    <span class="input-group-addon btn green-meadow btn-file"> <span class="fileinput-new"> Upload Your Warehouses List </span> <span class="fileinput-exists"> Change </span>
                    <input type="file" class="btn import_file" id="import_file" name="import_file">
                    </span> </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 text-center">
              <p>Import your warehouses here, with the template provided above.</p>
              <button type="button" class="btn default" data-dismiss="modal">Close</button>
              <button type="button" id="import_save" name="import_save" class="btn blue import_save">Import Warehouses</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.modal-content --> 
    </div>
  </div>
</form>
<div class="modal fade modal-scroll in" id="editlp" tabindex="-1" role="editlp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">EDIT WAREHOUSE</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Warehouse Name</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Address 1</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Address 2</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Area</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">City</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Pincode</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <label class="control-label">Latitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <label class="control-label">Logitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-2" style="margin-top:28px;"> <a href="" class="btn btn-sm blue"><i class="fa fa-map-marker"></i></a> </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Landmark</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Email</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Phone</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button type="button" class="btn green-meadow">Add New</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
@stop
@section('script') 
<script type="text/javascript"> 

    $(document).ready( function(){
    FormWizard.init();
    });

   
    $(document).on('click', '.import_save', function (event) {
        event.preventDefault();
   var lp_id = $('#lpId').val();
   
   if(lp_id=='')
   {
       alert('Please select a Logistic partner or create new Logistic partner.');
   }
   
   //var lp_id = 1;
   var formData = new FormData($("#submit_formm")[0]);
  $.ajax({
  url:'/logisticpartners/importExcel/'+lp_id,
  data:formData,
  async:false,
  type:'post',
  processData: false,
  contentType: false,
  success:function(response){
  alert(response);
  $("#addWarehouseGrid").igHierarchicalGrid({"dataSource":'/logisticpartners/getWarehouseList/'+$('#lpId').val()});
  },
 });
   
 });    


/*     $(document).on('click', '.savewh', function (event) {
         
         formwh.valid();
        //event.preventDefault();
   //var lp_id = 1;
   var formData = new FormData($("#submit_form_wh")[0]);
  $.ajax({
  url:'/logisticpartners/savewh/',
  data:formData,
  async:false,
  type:'post',
  processData: false,
  contentType: false,
  success:function(response){
  alert(response);
  },
 });
   
 });*/   
</script> 
@stop
@section('style')
<style type="text/css">
.uneditable-input {
    border: 0px !important;
	background-color:transparent !important;
}
.fileinput-filename{ width:60%px !important;}

h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important; font-weight:400 !important;}



.fa-question-circle-o{font-size: 15px !important; color:#3598dc}
.fa-question{color:#fff !important;}
.pac-container .pac-logo{    z-index: 9999999 !important;}
.pac-container{    z-index: 9999999 !important;}
.pac-logo{    z-index: 9999999 !important;}
#dvMap{height:304px !important; width:269px !important;}

label {
    margin-bottom: 0px !important;
	padding-bottom: 0px !important;
}
.modal-header {
    padding: 5px 15px !important;
}

.modal .modal-header .close {
    margin-top: 8px !important;
}

.form-group {
    margin-bottom: 5px !important;
}
.checkbox-list > label.checkbox-inline:first-child {
  padding-left: 20px !important;
}
</style>
<style type="text/css">
.browsehide{margin-top: -27px !important;  position: absolute; right: 23px; z-index: 1; width:90%; opacity:0;filter: alpha(opacity=0);-moz-opacity:0;}
.in .item-label{font-size:14px !important;}
.in .datetime{font-size:14px !important; float:right;}

.chats li.in .message {
	font-size:14px;
    text-align: left;
    border-left: 0px !important;
    margin-left: 55px;
    background: #eef5fb !important;
}
.chats li .datetime {
    color: #C0C9CC !important;
    font-size: 13px;
    font-weight: 400;
}

.chats li .message {
    position: static !important;
}

.out .item-label{font-size:14px !important; float:right;}
.out .datetime{font-size:14px !important; float:left;}

.chats li.out .message {
	font-size:14px;
    text-align: left;
    border-right: 0px !important;
    margin-right: 55px;
	margin-top:20px !important;
    background: #eef5fb !important;
}


.general-item-list > .item > .item-body {
    background: #eef5fb !important;
    padding: 5px !important;
    line-height: 21px !important;
}

list > .item > .item-head > .item-details > .item-label {
    color: #000 !important;
}

.form-actions {
    padding: 10px 10px 0px 10px !important;
}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    vertical-align: middle !important;
}
.table.dataTable{font-size:11.5px !important;}
.table.dataTable span{font-size:9px;}

.fileUpload{float:none !important;}
.form-group .col-md-12{margin-bottom: 15px; padding-left: 0 !important;}
/*#upload_file, #pan_file{ right:0px !important;}*/
.table-advance thead tr th {
    font-size: 12px !important;
}
table.dataTable thead th, table.dataTable thead td {
    padding-left:5px !important;
}
table.dataTable tbody th, table.dataTable tbody td {
    padding:3px !important}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px !important;}


.actionss{font-size:24px !important;}
.green-meadow {
    color: #1BBC9B;
}
.code {
    font-size: 24px !important;
}


.headings h3{border-bottom:1px solid #ddd; padding-bottom:5px; font-weight:bold; font-size:16px;}

label {
    margin-bottom: 0px !important;
	padding-bottom: 4px !important;
}
.modal-header {
    padding: 5px 15px !important;
}

.modal .modal-header .close {
    margin-top: 8px !important;
}

.form-group {
    margin-bottom: 5px !important;
}
.checkbox {
    display: initial !important;
}


.actionscolors i{ color:#a5bece !important;}

.tabbable-line > .nav-tabs > li > a:visited {
    color: #179d81;
}



</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript') 
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/table-datatables-editable.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js')}}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js')}}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-lp.js') }}" type="text/javascript"></script> 

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>

<script type="text/javascript">


$('#warehouses_id').on('click',function() {
	if($('#lpId').val()!='' && $.trim($('#addWarehouseGrid').html())=='')
	{
		addWarehouseGrid();
	}	
});

$('.addwh').on('click',function() {
  $( "h4.modal-title" ).replaceWith( "<h4 class="+'modal-title'+">ADD WAREHOUSE</h4>" );
});

 function sel_selbx(dv_id, sel_val) {
                var options = $(dv_id + ' option');
                $(dv_id + ' option').removeAttr('selected');
                var ind = '';

                $.map(options, function(option) {
                    if (option.text == sel_val)
                    {
                        ind = $(option).index();
                        $(dv_id + ' option').eq(ind).prop("selected", "selected")
                    }if (option.value == sel_val)
                    {
                        ind = $(option).index();
                        $(dv_id + ' option').eq(ind).prop("selected", "selected")
                    }
                });
                return;
            }

function deleteWarehoust(wh_id)
{
  console.log("deleted id is ="+wh_id);
  if (confirm('Are you sure you want to delete?'))
  {
    token  = $("#csrf-token").val();
     $.ajax({
          headers: {'X-CSRF-TOKEN': token},
          url: '/logisticpartners/deletewh/'+wh_id,
          processData: false,
          contentType: false,                                             
          success: function (rs) 
          {
			$("#addWarehouseGrid").igHierarchicalGrid({"dataSource":'/logisticpartners/getWarehouseList/'+$('#lpId').val()});
           //alert(rs);
          }
      });
}}

function editWarehoust(wh_id)
{
 
    token  = $("#csrf-token").val();
 $.ajax({
      headers: {'X-CSRF-TOKEN': token},
      url: '/logisticpartners/editwh/'+wh_id,
      processData: false,
      contentType: false,                                             
      success: function (rs) 
      {

        var test =rs[0].lp_wh_name;
        $("#wh_name").val(rs[0].lp_wh_name);
        $("#wh_cont_name").val(rs[0].contact_name);
        $("#wh_email").val(rs[0].email);
        $("#wh_phone").val(rs[0].phone_no);
        $("#wh_address1").val(rs[0].address1);
        $("#wh_address2").val(rs[0].address2);
        $("#wh_pincode").val(rs[0].pincode);
        $("#wh_city").val(rs[0].city);
        $("#wh_lat").val(rs[0].longitude);
        $("#wh_log").val(rs[0].latitude);
        
        $("#wh_latitude").val(rs[0].latitude);
        $("#wh_logitude").val(rs[0].longitude);
        sel_selbx('#wh_state',rs[0].state);
        sel_selbx('#wh_country',rs[0].country);

        $("#click_addlp").trigger('click');
          $( "h4.modal-title" ).replaceWith( "<h4 class="+'modal-title'+">EDIT WAREHOUSE</h4>" );
          $("#status").val("EDIT");
      }
    });
}

$("#click_addlp").on('click',function(e) {
	if(e.hasOwnProperty('originalEvent'))
	{
		$('form[id="submit_form_wh"]')[0].reset();
                $("#status").val("ADD");
	}
});
$("#logistic_picode").blur(function ()
    {
        var token  = $("#csrf-token").val();
        var pincode = $("#logistic_picode").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                url: "/logisticpartners/googlepincode/"+pincode,
                type: "GET",
               success: function (data) {
               
            //success data
            var country1 = '';
            var country = '';
            var state = '';
            var city = '';
            
            var data = jQuery.parseJSON(data);
            var address_components=data['results'][0]['address_components'];
            var types =address_components;
            var my_city ='';
            var my_postalcode;
            var my_dist = '';
            var my_state;
            var my_country;
            
            $.each(types, function(idx, obj) {
            if(obj.types[0] == "postal_code")
            {    
            my_postalcode = obj.long_name;
            }
            if(obj.types[0] == "locality" && obj.types[1] == "political")
            {    
            my_city =obj.long_name;
            }
            if(obj.types[0] == "administrative_area_level_2" && obj.types[1] == "political")
            {    
	    my_dist = obj.long_name;
            }
            if(obj.types[0] == "administrative_area_level_1" && obj.types[1] == "political")
            {    
	    my_state = obj.long_name;
            }
            if(obj.types[0] == "country" && obj.types[1] == "political")
            {    
	    my_country = obj.long_name;
            }
            });
            
            if(my_city.length != 0)
            {    
            $("#logistic_city").val(my_city);
            }
            else
            {
            $("#logistic_city").val(my_dist);
            } 
            sel_selbx('#logistic_state',$.trim(my_state));
            sel_selbx('#logistic_country',$.trim(my_country));
            }
            });
    });        
$("#wh_pincode").blur(function ()
    {
        var token  = $("#csrf-token").val();
        var pincode = $("#wh_pincode").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                url: "/logisticpartners/googlepincode/"+pincode,
                type: "GET",
               success: function (data) {
               
            //success data
            var country1 = '';
            var country = '';
            var state = '';
            var city = '';
            
            var data = jQuery.parseJSON(data);
            var address_components=data['results'][0]['address_components'];
            var types =address_components;
            var my_city ='';
            var my_postalcode;
            var my_dist = '';
            var my_state;
            var my_country;
            
            $.each(types, function(idx, obj) {
            if(obj.types[0] == "postal_code")
            {    
            my_postalcode = obj.long_name;
            }
            if(obj.types[0] == "locality" && obj.types[1] == "political")
            {    
            my_city =obj.long_name;
            }
            if(obj.types[0] == "administrative_area_level_2" && obj.types[1] == "political")
            {    
	    my_dist = obj.long_name;
            }
            if(obj.types[0] == "administrative_area_level_1" && obj.types[1] == "political")
            {    
	    my_state = obj.long_name;
            }
            if(obj.types[0] == "country" && obj.types[1] == "political")
            {    
	    my_country = obj.long_name;
            }
            });
            
            if(my_city.length != 0)
            {    
            $("#wh_city").val(my_city);
            }
            else
            {
            $("#wh_city").val(my_dist);
            } 
            sel_selbx('#wh_state',$.trim(my_state));
            sel_selbx('#wh_country',$.trim(my_country));
            }
            });
    }); 		
</script> 
@stop
@extends('layouts.footer')