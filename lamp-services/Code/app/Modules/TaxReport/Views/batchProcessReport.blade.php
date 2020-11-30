@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget" style="height: auto;">
                <div class="portlet-title">
                    <div class="caption">{{trans('taxReportLabels.batch_process_heading')}}</div>
                    <div class="tools ">
                        <div class="actions">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                        <a href="#tag_1" data-toggle="modal" id = "" class="btn btn-success">Batch Process Reports</a>
                        </div>      
                        <div class="modal modal-scroll fade in" id="tag_1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
                        `   <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <button type="button" id="modalclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="basicvalCode">Export Batch Process Reports</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="bannersExportForm" action="/taxreport/downloadbatchreport" class="text-center" method="POST" onsubmit="return validateform();">
                                        <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">
                                        <div class="row">
                                        <!-- <div class="col-md-12" align="center"> -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-icon right">
                                                    <i class="fa fa-calendar"></i>
                                                    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                                                    <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-icon right">
                                                    <i class="fa fa-calendar"></i>
                                                    <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                    <label class="control-label">DC <span class="required">*</span></label>
                                                    <select class="form-control select2me" id="warehousebanner" name="warehouse[]" autocomplete="Off">
                                                        <option value="0">All</option>
                                                        @foreach($dcs as $dc)
                                                        @if($dc->lp_wh_name!='')
                                                        <option value="{{ $dc->le_wh_id}}"> {{ $dc->lp_wh_name }}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                    </div>   
                                                </div> 
                                            </div> 
                                       <hr/>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stop
                        @section('userscript')
                            <!--Ignite UI Required Combined CSS Files-->
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
                            <link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
                            <!--Sumoselect CSS Files-->
                            <link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

                            <!--Ignite UI Required Combined JavaScript Files--> 
                            <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
                            <script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
                            <!-- jquery validation file -->
                            <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
                            <!-- jquery validatin file included -->
                            <!--Sumoselect JavaScriptFiles-->
                            <script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
                            @extends('layouts.footer')
                            <script type="text/javascript">
                                                            $(document).ready(function () {
                                                               
    var dateFormat = "dd/mm/yy";
    from = $( "#fromdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          })
    /*.on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          })*/,
      to = $( "#todate" ).datepicker({
            //defaultDate: "+1w",
             /* dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,  */
            defaultDate: "+1w",
              changeYear: true,
            yearRange: "-10:+0", // last ten years
              dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,       
          })
      /*.on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          })*/;

          from = $( "#fsdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          })
    /*.on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          })*/,
      to = $( "#tsdate" ).datepicker({
            //defaultDate: "+1w",
             /* dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true, */
             defaultDate: "+1w",
            changeYear: true,
          yearRange: "-10:+0", // last ten years
            dateFormat : dateFormat,
            changeMonth: true,         
          })
      /*.on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          })*/;
          
     function getDate( element ) {
        var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        } 
      return date;
    }  

     $("#fromdate").keydown(function(e) {
            e.preventDefault();  
        });
        $("#todate").keydown(function(e) {
            e.preventDefault();  
        });
    });



 $('#uploadfile').on('click', function() {
    
    var flag=$("#select_flags_banner").val();

    var dc=$("#warehousebanner").val();

    var startDate = document.getElementById("fromdate").value;
    var endDate = document.getElementById("todate").value;

    if ((Date.parse(endDate) < Date.parse(startDate))) {
      alert("To date should be greater than From date");
     // document.getElementById("ed_endtimedate").value = "";
     return false;
    }

    if(startDate==""){
        alert("Please Select From Date");
        return false;
    }

    if(endDate==""){
        alert("Please Select To Date");
        return false;
    }


});                                                            

</script>
 @stop
@extends('layouts.footer')