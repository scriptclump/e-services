@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> Commission Report </div>
                <div class="actions">  </div>
            </div>
            <form method="post" id="report_form">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <div class="row margtop">
                    <div class="col-md-3">
                        <div class="form-group err">
                            <label><strong>Users</strong></label>
                            <div class="input-icon right" style="width: 100%">
                                <select class="form-control select2me" id="users_group" name="users_group">
                                                        </select>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group err">
                            <label><strong>Roles</strong></label>
                            <div class="input-icon right" style="width: 100%">
                                <select class="form-control select2me" id="role_group" name="role_group">
                                                        </select>
                            </div>
                        </div>
                    </div>         
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label><strong>Start Date</strong></label>
                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_start_date" id="reports_start_date" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label><strong>End Date</strong></label>

                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_end_date" id="reports_end_date" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group err">
                            <div class="input-icon right" style="width: 100%"> <br/>                               
                                <input type="submit"  class="btn green-meadow btnn range_info" value='Submit'  id="report_range" >                    
                                
                            </div>                                                    
                        </div>
                    </div>
                </div>
            </form>
                  
            <div class="portlet-body">
                <table id="reports_grid" ></table>
            </div>
        </div>
    </div>
</div>
{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<style type="text/css">
    	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}
    .rightAlign {
    text-align:right;
    }
    .numericAlignment {
    text-align: right;
    padding-right: 10px;
}
th.ui-iggrid-header{

           text-align: center !important;
}


    .margtop{margin-top:15px;}
    .ui-iggrid-featurechooserbutton{display:none !important}
	.ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
	.rightAlign {
    text-align:right;
	}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('assets/admin/pages/scripts/reports/commission_reports_grid.js')}}
<!--<script src="http://cdn-na.infragistics.com/igniteui/2016.1/latest/js/infragistics.loader.js"></script>
<script src="http://www.igniteui.com/js/external/FileSaver.js"></script>
<script src="http://www.igniteui.com/js/external/Blob.js"></script>-->
 <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
        
<script>


    $( "#ff_name" ).autocomplete({        
      source: 'ffreports/getffnames',
    });    
    
$(document).ready(function () {
    userList();
    roleList();
    mygrid('/cashbagGrid','/cashbagGridHeadings');
    var dateFormat = "yy-mm-dd",
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
            var filterURL = "/ffreports/getreports";                            
             $("#report_form").validate().resetForm();
             $('#report_form')[0].reset();
            $("#reports_grid").igGrid({dataSource: filterURL});
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
            var user_id = $("#users_group").val();
            var role_id = $("#role_group").val();
            if(user_id != 0 && role_id ==0)
            {
                alert("please select role.");
            }else
            {
                var filterURL = "cashbagGrid?from_date=" + start_date+'&to_date='+end_date+"&user_id="+user_id+"&role_id="+role_id;
                var headerUrl ="/cashbagGridHeadings?from_date=" + start_date+'&to_date='+end_date+"&user_id="+user_id+"&role_id="+role_id;
                mygrid(filterURL,headerUrl);
            }   
    }
});

$("#export_reports").click(function() {
    //alert('here');
    var sta = $("#reports_start_date").val();
    var end = $("#reports_end_date").val();
     var ffName = $("#ff_name").val(); 
     $("#export_reports").attr("href", "ffreports/excelSalesReports?start_date=" + sta+"&end_date="+end+"&ff_name="+ffName);
    if(sta == '' && end =='') {
        return true;
    } else if(sta != '' && end !='') {
        return true;
   } else {
        alert('please select start date and end date');
  return false;
  }
 
});
function userList()
{
     $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/cashbagUsersList',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#users_group").html(rs);
                $("#users_group").select2().select2('val',0);
            }
        });
}
function roleList()
{
     $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/cashbagRolesList',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#role_group").html(rs);
                $("#role_group").select2().select2('val',0);
            }
        });
}
$("#users_group").change(function () {
        var id = this.value;
         $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getRoleId/'+id,
            type: 'GET',                                             
            success: function (rs) 
            {
                console.log(rs);
                $("#role_group").select2().select2('val',rs);
            }
        });
    });
</script>

@stop

@extends('layouts.footer')