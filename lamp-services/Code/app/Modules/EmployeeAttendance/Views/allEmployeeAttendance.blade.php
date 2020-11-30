@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption">Subordinates Attendance</div>
                </div> 
                <form class="submit_form" id="my_team_att_form" method="post">
                  <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                  <div class="row" style="margin-top: 24px;">
                    <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label">Employee List</label>
                          <select class="form-control select2me" name="employee_list" id="employee_list">
                            <option value="0">Please select...</option>
                              @foreach($allEmps as $allEmpsValues)
                                <option value="{{$allEmpsValues['emp_code']}}">{{$allEmpsValues['full_name']}} </option>
                              @endforeach
                          </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">From Date</label>
                            <input type="text" class="form-control " name="from_date" id="from_date" placeholder="From Date">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">To Date</label>
                            <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date">                            
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
@stop
@section('userscript')
@include('includes.ignite')

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
@include('includes.validators')

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
{{HTML::script('assets/admin/pages/scripts/hrms/allEmpAttendanceReports.js')}}
<script>
$(document).ready(function(){
  $("#all_emp_attendance_grid").hide();
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