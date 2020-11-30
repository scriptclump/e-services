@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                   Vehicle Attendance Report
                </div>
                <div class="actions">
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="portlet-body">
    <div class="row" style="margin-left: -2px;">
     <!-- {{ Form::open(array('id' => 'inventory-snapshot'))}} -->
     {{ Form::open(array('url' => '/vehicleattdownload', 'id' => 'vehile_form_submit'))}}
        <div class="custom_range" id="custom_range" >
          <div class="col-md-3">
              <div class="form-group">
                  <label class="control-label">Vehicle List</label>
                  <select id="vehicle_list_id"  name="vehicle_list" class="form-control select2me" >
                    <option value = "">All Vehicle List</option>
                      @foreach($vehicle_list as $details)
                          <option value = "{{$details['vehicle_id']}}">{{$details['reg_no']}}</option>
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
        </div>
        <div class="col-md-2">
          <button type="submit" id="vehicle_search" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut">Download</button>
        </div>
        {{ Form::close() }}
    </div>
</div>
@stop


@section('style')

<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />

@stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>



<script type="text/javascript">
 $(document).ready(function(){
     
       var start = new Date();
      var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
      $('#from_date').datepicker({
          endDate: "0d",
          autoclose: true, 
          format: 'yyyy-mm-dd'    
      }).on('changeDate', function () 
      {        
          stDate = new Date($(this).val());    
          $('#to_date').datepicker('setStartDate', $('#from_date').val());
      }); 
      $('#to_date').datepicker({        
          endDate: "0d",       
          autoclose: true,        
          format: 'yyyy-mm-dd'    
      }).on('changeDate', function () { 
          $('#from_date').datepicker('setEndDate', $('#to_date').val());
      });



      $("#vehicle_search").click(function(e){
        e.preventDefault();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var vehicle_list = $("#vehicle_list_id").val();
        if(from_date!= "" && to_date !="")
        {    
          $("#vehile_form_submit").submit();
          $("#vehile_form_submit")[0].reset();
          $("#vehicle_list_id").select2('val', '');
        }
        else
        {
          alert("Please select dates.");
        }
      });

    });
</script>
@stop

