@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class=" portlet-title" id="tabs" style="margin-top: 20px;">
                  <div class="row">
                    <div class="col-md-12">
                      <ul class="list-inline" style="border: black;">
                        <li><a data-toggle="tab" id="my_att_tab" href="#my_att" >My Attendance</a></li>
                        @if(!empty($allEmps))
                        <li><a  data-toggle="tab" href="#subord_att">Subordinates Attendance</a></li>
                        @endif
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="tab-content">
                  <div id="my_att" class="tab-pane fade">
                    <form class="submit_form" id="att_form" method="post">
                      <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                      <br>
                      <div class="row" >
                        <div class="col-md-4">
                          <span style ="color: #1BBC9B;font-size: small;"><strong>
                          <span value = "">Upcoming Holiday: {{$upcoming_holiday}}</span>
                          </strong>
                        </div>
                        <div class="col-md-4">
                          <span style ="color: #1BBC9B;font-size: small;"><strong>
                          @if(is_array($today_birthday) && count($today_birthday) > 0)
                            <div class="col-md-6">Today Birthday(s):</div>
                            @foreach($today_birthday as $index => $keyval)
                            <?php $empdata[0] = $keyval; ?>
                            @if($index>0)
                            <div class="col-md-6">&nbsp;</div>
                            <div class="col-md-6" style="margin-left: -67px;font-size: 13px">{{$empdata[0]}}</div>
                            @else
                            <div class="col-md-6" style="margin-left: -67px;font-size: 13px">{{$empdata[0]}}</div>
                            @endif
                            <br />
                            @endforeach
                          @else
                          <div class="col-md-6">Upcoming Birthday(s):</div>
                          @foreach($upcoming_birthday as $index => $keyval)
                            <?php $empdata[0] = $keyval; ?>
                            @if($index>0)
                            <div class="col-md-6">&nbsp;</div>
                            <div class="col-md-6" style="margin-left: -42px;font-size: 13px">{{$empdata[0]}}</div>
                            @else
                            <div class="col-md-6" style="margin-left: -42px;font-size: 13px">{{$empdata[0]}}</div>
                            @endif
                            <br />
                          @endforeach
                          @endif  
                          </strong>
                        </div>
                        <div class="col-md-4">
                          <span @if($morehoursdata =='') style ="color: #1BBC9B;font-size: small;" @else style ="color: red;font-size: small;" @endif><strong>
                          <span value = "">Total Deviation: {{$total_deviation}}</span>
                          </strong>
                          <div>
                            <span style ="color: black;" value = "">(-)Indicates more productive hours</span>
                          </div>
                        </div>
                      </div>
                      <div class="row" >
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">From Date</label>
                                <input type="text" class="form-control " name="my_att_from_date" id="my_att_from_date" placeholder="From Date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">To Date</label>
                                <input type="text" class="form-control" name="my_att_to_date" id="my_att_to_date" placeholder="To Date" autocomplete="off">                            
                            </div>
                        </div>
                        <div class="col-md-3">
                          <button class="btn green-meadow" style="margin-top: 24px;" >Search</button>
                        </div>
                      </div>   
                    </form>
                    <div class="portlet-body">
                      <div id="attendance_grid"></div>
                    </div>  
                  </div>
                  <div id="subord_att" class="tab-pane fade"> 
                    <form class="submit_form" id="my_team_att_form" method="post">
                      <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                      <div class="row" style="margin-top: 24px;">
                        <div class="col-md-3">
                          <div class="form-group">
                              <label class="control-label">Employee List</label>
                              <select class="form-control select2me" name="employee_list" id="employee_list">
                                <option value="0">Please select...</option>
                                  @if(!empty($allEmps))
                                  @foreach($allEmps as $allEmpsValues)
                                    <option value="{{$allEmpsValues['emp_code']}}">{{$allEmpsValues['full_name']}} </option>
                                  @endforeach
                                  @endif
                              </select>
                          </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">From Date</label>
                                <input type="text" class="form-control " name="from_date" id="from_date" placeholder="From Date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">To Date</label>
                                <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date" autocomplete="off">                            
                            </div>
                        </div>
                        <div class="col-md-3">
                          <button class="btn green-meadow" style="margin-top: 24px;" id="emp_att_search" >Search</button>
                        </div>
                      </div>   
                    </form>
                    <div class="portlet-body">
                      <div id="all_emp_attendance_grid"></div>
                    </div>
                  </div>
                </div>
                
            </div>
        </div>
    </div>
@stop
@section("style")
<style type="text/css">
  .timeAlignment{
    padding-left: 15px !important;
  }
  .timeAlignment2{
    padding-left: 24px !important;
  }
  .timeAlignment3{
    padding-left: 36px !important;
  }
  .timeAlignment4{
    padding-left: 15px;
  }
  .timeGridAlignment1{
    padding-left: 5px !important;
  }
  .timeGridAlignment2{
    padding-left: 8px !important;
  }
  .timeGridAlignment3{
    padding-left: 14px;
  }
  a {
    text-shadow: none;
    color: #ccc;
}a:focus, a:hover {
     color: #ccc; 
}
</style>
@stop
@section('userscript')
@include('includes.ignite')

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
@include('includes.validators')

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
{{HTML::script('assets/admin/pages/scripts/hrms/attendanceReportGrid.js')}}
{{HTML::script('assets/admin/pages/scripts/hrms/allEmpAttendanceReports.js')}}
<script>
$(document).ready(function(){
  $("#all_emp_attendance_grid").hide();
  $("#my_att_tab").click();
  $("#my_att_tab").css("border","black");
  var start = new Date();
  var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
  $('#from_date').datepicker({
      endDate: "-1d",
      autoclose: true, 
      format: 'dd-mm-yyyy'    
  }).on('changeDate', function () 
  {        
      stDate = new Date($(this).val());    
      $('#to_date').datepicker('setStartDate', $('#from_date').val());
  }); 
  $('#to_date').datepicker({        
      endDate: "-1d",       
      autoclose: true,        
      format: 'dd-mm-yyyy'    
  }).on('changeDate', function () { 
      $('#from_date').datepicker('setEndDate', $('#to_date').val());
  });
  $('#my_att_from_date').datepicker({
      endDate: "-1d",
      autoclose: true,        
      format: 'dd-mm-yyyy'    
  }).on('changeDate', function () 
  {        
      stDate = new Date($(this).val());    
      $('#my_att_to_date').datepicker('setStartDate', $('#my_att_from_date').val());
  }); 
  $('#my_att_to_date').datepicker({        
      endDate: "-1d",       
      autoclose: true,        
      format: 'dd-mm-yyyy'    
  }).on('changeDate', function () { 
      $('#my_att_from_date').datepicker('setEndDate', $('#my_att_to_date').val());
  });
   $('#att_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            my_att_from_date: {
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            my_att_to_date: {
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            }
        }
  }).on('change', '[name="my_att_from_date"]', function(e) {
            $('#att_form').formValidation('revalidateField', 'my_att_from_date');
  }).on('change', '[name="my_att_to_date"]', function(e) {
          $('#att_form').formValidation('revalidateField', 'my_att_to_date');
  }).on('success.form.bv', function (event) {
    event.preventDefault();
      var from_date = $("#my_att_from_date").val();
      var to_date = $("#my_att_to_date").val();
      $("#attendance_grid").igGrid({dataSource: 'getattendancegridedata?page=0&pageSize=10&from_date='+from_date+'&to_date='+to_date});
  });
 $('#my_team_att_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            employee_list: {
              validators: {
                callback:{
                  message: "Please select.",
                    callback: function(value, validator) {
                    return value > 0;
                    }
                  }
                }                    
            },
            from_date: {
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            to_date: {
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            }
        }
  }).on('change', '[name="from_date"]', function(e) {
            $('#my_team_att_form').formValidation('revalidateField', 'from_date');
  }).on('change', '[name="to_date"]', function(e) {
          $('#my_team_att_form').formValidation('revalidateField', 'to_date');
  }).on('success.form.bv', function (event) {
    event.preventDefault();
      $("#all_emp_attendance_grid").show();
      var from_date = $("#from_date").val();
      var to_date = $("#to_date").val();
      var emp_id = $("#employee_list").val();
      $("#all_emp_attendance_grid").igGrid({dataSource: 'getAllAttendancegridedata?page=0&pageSize=10&from_date='+from_date+'&to_date='+to_date+'&emp_code='+emp_id});
  });
});
</script>
@stop