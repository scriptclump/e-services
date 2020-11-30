@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> FF Daily Log Report </div>
                <div class="actions">  </div>
            </div>
            <form method="post" id="report_form">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <div class="row margtop">
                    <div class="col-md-3">
                        <div class="form-group err">
                            <label class="control-label">Business Units</label>
                            <input type="hidden" id="hidden_buid" name="hidden_buid" value='<?php if (isset($bu_id) && $bu_id!=''){ echo $bu_id;}else{ echo '';}?>'>
                            <select id="business_unit_id" name="business_unit_id" class="form-control business_unit_id select2me"></select>
                        </div>
                    </div>         
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label>FF Name</label>
                                <input type="text" class="form-control auto-comp" name="ff_name" id="ff_name" >
                                <input type="hidden" class="form-control" name="ff_id" id="ff_id" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label class="control-label">Start Date</label>
                            <div class="input-icon right" style="width: 100%" >
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_start_date" id="reports_start_date" autocomplete="Off" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group err">
                            <label>End Date</label>
                            <div class="input-icon right" style="width: 100%">
                                <i class="fa fa-calendar" style="line-height: 5px"></i>
                                <input type="text" class="form-control" name="reports_end_date" id="reports_end_date" autocomplete="Off" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group err">
                            <div class="input-icon right" style="width: 100%"> <br/>
                                <input type="submit"  class="btn green-meadow btnn range_info" value='Submit'id="report_range" >                           
                                <input type="button"  class="btn green-meadow btnn range_info" value='Reset'id="filterReset" > 			
								<a href="ffreportsdata/excelSalesReports" id="export_reports" class="btn green-meadow btnn range_info" ><i class="fa fa-download" aria-hidden="true"></i></a>
                            </div>                                                    
                        </div>
                    </div>
                </div>
            </form>
                                                                    
            <div class="portlet-body">
                <table id="reports_grid"></table>
            </div>
        </div>
    </div>
</div>
{{HTML::style('css/switch-custom.css')}}
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}
    .business_unit_id{
  height: 29px;
}

    .margtop{margin-top:15px;}
    .ui-iggrid-featurechooserbutton{display:none !important}
	.ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
	.rightAlign {
    text-align:right;
	}

    .bu1{
    margin-left: 10px;
    font-size: 18px;
    color:#000000;
}
.bu2{
    margin-left: 20px;
    font-size: 16px;
    color:#1d1d1d;
}.bu3{
    margin-left: 30px;
    font-size: 15px;
    color:#3a3a3a;
}.bu4{
    margin-left: 40px;
    font-size: 14px;
    color:#535353;
}.bu5{
    margin-left: 50px;
    font-size: 13px;
    color: #6d6c6c;
}.bu6{
    margin-left: 60px;
    font-size: 11px;
    color:#868383;
}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('assets/admin/pages/scripts/ffreportsdata/reports_grid.js')}}
<!--<script src="http://cdn-na.infragistics.com/igniteui/2016.1/latest/js/infragistics.loader.js"></script>
<script src="http://www.igniteui.com/js/external/FileSaver.js"></script>
<script src="http://www.igniteui.com/js/external/Blob.js"></script>-->

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<style type="text/css">
    .ui-iggrid-results{
    z-index: 1 !important;
 }
</style>
        
<script>

    $( "#ff_name" ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "ffreportsdata/getffnames",
          dataType: "json",
          data: {
            term: request.term,
            buid: $("#business_unit_id").val(),
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        console.log( "Selected: " + ui.item.value + " aka " + ui.item.id );
        var ff_name = ui.item.label;
        var ff_id = ui.item.ff_id;
        $('#ff_id').val(ff_id);
      }
    } );
$(document).ready(function () {
    $( "#reports_start_date" ).datepicker({  maxDate: new Date() });
    $( "#reports_end_date" ).datepicker({  maxDate: new Date() });
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
            var filterURL = "/ffreportsdata/getreports";     
            $("#report_form").validate().resetForm();
            $('#report_form')[0].reset();
            $('#ff_id').attr('value','');
            $('#business_unit_id').val(1).trigger('change');
            $("#reports_grid").igGrid({dataSource: filterURL});
            $( "#reports_start_date" ).datepicker({  maxDate: new Date() });
            $( "#reports_end_date" ).datepicker({  maxDate: new Date() });         
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
            var business_unit_id = $("#business_unit_id").val(); 
            var ffName = $("#ff_name").val();
            var ff_id = $("#ff_id").val();
            var filterURL = "/ffreportsdata/getreports?start_date=" + start_date+'&end_date='+end_date+"&business_unit_id="+business_unit_id+"&ff_name="+ffName+"&ff_id="+ff_id;
            //$("#reports_grid").igGrid({dataSource: filterURL});
            makeAjaxCallForigGrid(filterURL,"reports_grid");

            $("#reports_grid").on("iggriddatarendered", function (event, args) {
                $("th.ui-iggrid-rowselector-header.ui-iggrid-header.ui-widget-header").html("<span class='ui-iggrid-headertext' title='S. No'><p style='text-align: right !important; margin: 0px 5px !important;'>S. No</p></span>");
                var columns = $("#reports_grid").igGrid("option", "columns");
                formatigGridContent(columns,"reports_grid");
            });
    }
});

$(function(){
    var token=$('#csrf-token').val();
    var hidden_buid=$('#hidden_buid').val();
    $.ajax({
    type:'get',
    headers: {'X-CSRF-TOKEN': token},
    url:'/getbu',
    success: function(res){        
        res.forEach(data=>{
            $('#business_unit_id').append(data);
        });
    $('#business_unit_id').select2('val',hidden_buid);
        }

      });
  });


$("#export_reports").click(function() {
    var sta = $("#reports_start_date").val();
    var end = $("#reports_end_date").val();
    var business_unit_id = $("#business_unit_id").val();
    var ffName = $("#ff_name").val();
    var ff_id = $("#ff_id").val();
    $("#export_reports").attr("href", "ffreportsdata/excelSalesReports?start_date=" + sta+"&end_date="+end+"&business_unit_id="+business_unit_id+"&ff_name="+ffName+"&ff_id="+ff_id);
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