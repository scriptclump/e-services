@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> Attendance Report </div>
                <div class="actions">  </div>
            </div>
            <form method="post" id="report_form">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <div class="row margtop">
                    <div class="col-md-3">
                        <div class="form-group err">
                            <label>User Role</label>
                            <div class="input-icon right" style="width: 100%">
                                
                                <select class="form-control" name="user_role" id="user_role">
                                    <option value="">Select Role</option>

                                    @foreach($roles as $role )

                                    <option value="{{$role}}">{{$role}}</option>

                                    @endforeach 

                                </select>
                            </div>
                        </div>
                    </div>         
                    <div class="col-md-3">
                        <div class="form-group err">
                            <label>Start Date</label>
                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_start_date" id="reports_start_date" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group err">
                            <label>End Date</label>

                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_end_date" id="reports_end_date" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group err">
                            <div class="input-icon right" style="width: 100%"> <br/>                               
                                <input type="submit"  class="btn green-meadow btnn range_info" value='Submit'id="report_range" >                           
                                <input type="button"  class="btn green-meadow btnn range_info" value='Reset'id="filterReset" > 			
								<a href="attendreports/excelattendancereports" id="export_reports" class="btn green-meadow btnn range_info" ><i class="fa fa-download" aria-hidden="true"></i></a>                                                          
                                
                            </div>                                                    
                        </div>
                    </div>
                </div>

            </form>
                                                                    
            <div class="portlet-body">
                <table id="attendance_grid"></table>
            </div>
        </div>
    </div>
</div>
{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<style type="text/css">
    .margtop{margin-top:15px;}
	#ui-datepicker-div{z-index:999 !important;}

</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('assets/admin/pages/scripts/reports/attendance_grid.js')}}
<!--<script src="http://cdn-na.infragistics.com/igniteui/2016.1/latest/js/infragistics.loader.js"></script>
<script src="http://www.igniteui.com/js/external/FileSaver.js"></script>
<script src="http://www.igniteui.com/js/external/Blob.js"></script>-->

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
        
<script>
$(document).ready(function () {
    var dateFormat = "mm/dd/yy",
      from = $( "#reports_start_date" ).datepicker({
          //defaultDate: "+1w",
            changeMonth: true,          
          }).on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
      to = $( "#reports_end_date" ).datepicker({
            //defaultDate: "+1w",
            changeMonth: true,        
          }).on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          });
     function getDate( element ) {
        var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        } 
      return date;
    }   
         $("#filterReset").click(function () {      
             var filterURL = "/attendreports/getattendancereports"; 
             $("#report_form").validate().resetForm();
             $('#report_form')[0].reset();
                 
            $("#attendance_grid").igGrid({dataSource: filterURL});
            $( "#reports_start_date" ).datepicker( "option", "maxDate", getDate( this ));
         $( "#reports_end_date" ).datepicker( "option", "maxDate", getDate( this ));
        });
        $("#reports_start_date").keydown(function(e) {
            e.preventDefault();  
        });
        $("#reports_end_date").keydown(function(e) {
            e.preventDefault();  
        });
    });
    
jQuery.validator.addMethod("tax_class", function (value, element) {
    return this.optional(element) || /^[0-9]\d{0,9}(\.\d{1,2})?%?$/.test(value);
}, "Only 2 decimals are allowed");

$('#report_form').validate({
    rules: {
        reports_start_date: {
            required: true
        },
        reports_end_date: {
            required: true
        },                       
    },
    highlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');
    },
    unhighlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');
    },
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function (error, element) {
        if (element.length) {
            error.insertAfter(element);
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form) {
            event.preventDefault();
            var start_date = $("#reports_start_date").val();
            var end_date   = $("#reports_end_date").val();  
            var user_role = $("#user_role").val();
            var filterURL = "/attendreports/getattendancereports?start_date=" + start_date+'&end_date='+end_date+"&user_role="+user_role;
            $("#attendance_grid").igGrid({dataSource: filterURL});
    }
});

/* Exceport excel using ig grid
$.ig.loader({
    scriptPath: "http://cdn-na.infragistics.com/igniteui/2016.1/latest/js/",
    cssPath: "http://cdn-na.infragistics.com/igniteui/2016.1/latest/css/",
    resources: 'igGrid,' +
            'igGrid.Hiding,' +
            'igGrid.Filtering,' +
            'igGrid.Sorting,' +
            'igGrid.Paging,' +
            'igGrid.Summaries,' +
            'modules/infragistics.documents.core.js,' +
            'modules/infragistics.excel.js,' +
            'modules/infragistics.gridexcelexporter.js'
});
*/

//$("#reports_start_date, #reports_end_date").change(function() {
//    var staDate = $("#reports_start_date").val();    
//    var endDate = $("#reports_end_date").val();    
//    $("#export_reports").attr("href", "ffreports/excelSalesReports?start_date=" + staDate+"&end_date="+endDate);
//});

$("#export_reports").click(function() {
    var sta = $("#reports_start_date").val();
    var end = $("#reports_end_date").val();
    var user_role = $("#user_role").val(); 
    $("#export_reports").attr("href", "attendreports/excelattendancereports?start_date=" + sta+"&end_date="+end+"&user_role="+user_role);
    if(sta == '' && end =='') {
        return true;
    } else if(sta != '' && end !='') {
        return true;
   } else {
        alert('please select start date and end date');
  return false;
  }
 
});

</script>



@stop

@extends('layouts.footer')