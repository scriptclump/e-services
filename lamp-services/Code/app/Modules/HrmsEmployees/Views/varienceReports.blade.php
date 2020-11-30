@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
  @if($checkVariable == "no values")
  <span>
    <div class="flash-message">
      <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert"></button>
        There is no data for the current search criteria
        </div>
    </div>
  </span>
  @endif
<div class="row">
<div class="col-md-12">
<div class="portlet light tasks-widget">
    <div class="portlet-title">
        <div class="caption">Reports</div>
        <div class="tools">
        <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
        </div>
    </div>
    
  <form  action ="{{url('employee/checkthereportname')}}" method="POST" id = "frm_report_tmpl" name = "frm_report_tmpl">
  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
    <div class="portlet-body">
        <div class="row">
          <div class="col-md-2">
                <div class="form-group">
                    <label for="select_report" class="control-label">Emp Type</label>
                   <select id="emp_type_report" name="emp_type_report" class="form-control">
                          @foreach($employee_type as $typ )
                            <option value="{{$typ->value}}">{{$typ->master_lookup_name}}</option>
                          @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="select_report" class="control-label">Status</label>
                   <select id="emp_type_status" name="emp_type_status" class="form-control">
                          <option value ="">Please Select</option>
                          <option value = "1">Active</option>
                          <option value = "0">In Active</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="select_report" class="control-label">Report Type</label>
                   <select id="select_report" name="select_report" class="form-control">
                          <option value ="">Please Select</option>
                          <option value = "variencereport">Variance Report</option>
                          <option value = "attendancereport">Attendance Report</option>
                    </select>
                </div>
            </div>
        
            <div class="col-md-2">
                <div class="form-group">
                    <label for="month" class="control-label">From Date</label>
                    <input type="text" class="form-control " name="from_date_report" id="from_date_report" placeholder="From Date" autocomplete="off">
                
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="year" class="control-label">To Date</label>
                    <input type="text" class="form-control " name="to_date_report" id="to_date_report" placeholder="To Date" autocomplete="off">
                        
                </div>
            </div>

            <div class="col-md-2">
            <div class="form-group genra">
                <button type="submit"class="btn green-meadow">Generate</button>
            </div>
          </div>
            </div>
    </div>
</div>
</form>    
</div>
</div>
</div>


@stop
@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>

<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>


@extends('layouts.footer')
<style>
  .genra{
    margin-top: 24px;
  }
</style>
<script>
$('#frm_report_tmpl').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            emp_type_report: {
                validators: {
                    notEmpty: {
                        message: 'Select emp type'
                    }
                }
            },
            emp_type_status: {
                validators: {
                    notEmpty: {
                        message: 'Select emp status'
                    }
                }
            },
            select_report: {
                validators: {
                    notEmpty: {
                        message: 'Select report type'
                    }
                }
            },
            from_date_report: {
                validators: {
                    notEmpty: {
                        message: 'Select from date'
                    }
                }
            },
            to_date_report: {
                validators: {
                    notEmpty: {
                        message: 'Select to date '
                    }
                }
            }

        }
}).on('success.form.fv', function(e){

   


});



$(".alert-warning").fadeOut(20000);

$('#download-document').on('show.bs.modal', function (e) {

    $('#download_varience').data("formValidation").resetForm(true);
    // Clear out the fields
    $("#emp_code").val('');
    $("#month").val('');
    $("#year").val('');
    
});


  var date_input = $('input[name="from_date_report"]'); //our date input has the name "date"
    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
    var options = {
    format: 'yyyy-mm-dd',
            container: container,
            todayHighlight: true,
//            startDate: '+0d',
            autoclose: true,
    };
    date_input.datepicker(options).on('changeDate', function(e) {
    // Revalidate the date field
    $('#frm_report_tmpl').formValidation('revalidateField', 'from_date_report');
    });
    var date_input = $('input[name="to_date_report"]'); //our date input has the name "date"
    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
    var options = {
    format: 'yyyy-mm-dd',
            container: container,
            todayHighlight: true,
            autoclose: true,
    };
    date_input.datepicker(options).on('changeDate', function(e) {
    // Revalidate the date field
    $('#frm_report_tmpl').formValidation('revalidateField', 'to_date_report');
    });


</script>
@stop
@extends('layouts.footer')