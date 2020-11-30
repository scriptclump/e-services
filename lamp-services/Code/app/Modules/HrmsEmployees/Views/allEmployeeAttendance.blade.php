@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption">Attendance Reports</div>
                </div> 
                <div class="row" style="margin-top: 24px;">
                   @if(!empty($allEmps))
                    <div class="col-md-3">
                      <div class="form-group">
                          <label class="control-label">Employee List</label>
                          <select class="form-control select2me" name="employee_list" id="employee_list">
                            <option value="0">Please select...</option>
                              @foreach($allEmps as $allEmpsValues)
                                <option value="{{$allEmpsValues['emp_id']}}">{{$allEmpsValues['firstname']}} {{$allEmpsValues['lastname']}}</option>
                              @endforeach
                          </select>
                      </div>
                    </div>
                    @endif
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
						<button class="btn btn-success" id="search" style="margin-top: 24px;">Search</button>  
                    </div>
                </div>   
                <div class="portlet-body">
                    <div id="attendance_grid"></div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('userscript')
@include('includes.ignite')

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
{{HTML::script('assets/admin/pages/scripts/hrms/attendanceReportGrid.js')}}

<script>
    $(document).ready(function(){
      var from_input=$('input[name="from_date"]');
      var to_input=$('input[name="to_date"]'); //our date input has the name "date"
      var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options={
        format: 'yyyy-mm-dd',
        container: container,
        todayHighlight: true,
        endDate: '+0d',
        autoclose: true,

      };
      from_input.datepicker(options);
      to_input.datepicker(options);

      $("#search").click(function(){
      	var from_date = $("#from_date").val();
      	var to_date = $("#to_date").val();
        var emp_id = $("#employee_list").val();
      	if(from_date!= "" && to_date !="" && emp_id!=0)
      	{    
      		$("#attendance_grid").igGrid({dataSource: 'getAllAttendancegridedata?page=0&pageSize=10&from_date='+from_date+'&to_date='+to_date+'&emp_id='+emp_id});
      	}
      	else
      	{
      		alert("Please fill all information.");
      	}
      });
    });
</script>
@stop