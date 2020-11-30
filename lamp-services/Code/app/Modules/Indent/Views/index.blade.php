@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/indents/index">Indents</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Manage Indents</li>
        </ul>
    </div>
</div>
<div id="success_message_ajax"></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">Manage Indents</div>
                <div class="actions">
                <!-- <a href="/indents/autoindent" class="btn btn-success">Auto Indent</a> -->

                @if(isset($exportInd) && $exportInd==1)               
                <a href="#tag_1" data-toggle="modal" id = "" class="btn btn-success">Export Indents</a>
                @endif

                @if(isset($stockistInd) && $stockistInd==1)               
                <a href="#tag_2" data-toggle="modal" id="" class="btn btn-success">Export Stockist Indents</a>
                @endif

                @if(isset($createAccess) && $createAccess==1)               
                <a href="/indents/createIndent" class="btn btn-success">Create Indent</a>
                @endif
                    <a href="javascript:void(0);" id="toggleFilter" class="btn btn-success"><i class="fa fa-filter  "></i></a>
                    
                </div>
            </div>

            <form id="frm_indent" action="" method="post">  
                <div id="filters" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" id="indent_code" name="indent_code" class="form-control" placeholder="Indent No">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <select id="supplier" name="supplier" class="form-control select2me" data-live-search="true">
                                <option value="">All Supplier</option>
                                @foreach($suppliers as $supplierId=>$supplierName)
                                <option value="{{$supplierId}}">{{$supplierName}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <select id="indent_status" name="indent_status" class="form-control">
                                <option value="">All Status</option>
                                @foreach($allStatusArr as $status=>$name)
                                <option value="{{$status}}">{{$name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" id="fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" id="tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-md-2 text-right">
                            <div class="form-group">
                                <input type="button" value="Filter" class="btn btn-success" onclick="filterIndent();">
                                    <input type="reset" value="Reset" id="reset" class="btn btn-success">
                            </div>
                        </div>  

                    </div>
                </div>
                </div>
            </form>

            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />		 
			
            
            <div class="portlet-body">		   
                
                <div class="row">
                    <div class="col-md-12">
                        <table id="pickList" class="table table-striped table-bordered table-hover"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="modal modal-scroll fade in" id="tag_1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Export Indents</h4>
            </div>
            <div class="modal-body">
                <form id="indentsExportForm" action="/indents/createExport" class="text-center" method="GET">
                <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">

                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="Off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                               <div class="col-md-7">
                                <div class="form-group">
                                  <select  name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box  avoid-clicks" multiple="multiple" placeholder="Please Select DC " required="required">
                                    <!-- <option value="0" >All DC'S</option> -->
                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
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
                      </div>
                    </div>
                   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>






<div class="modal modal-scroll fade in" id="tag_2" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Stockist Indents</h4>
            </div>
            <div class="modal-body">
                <form id="stockistExportForm" action="/indents/createStockits" class="text-center" method="GET">
                <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">

                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="fsdate" name="fsdate" class="form-control" placeholder="From Date" autocomplete="Off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="tsdate" name="tsdate" class="form-control" placeholder="To Date"autocomplete="Off">
                                    </div>
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
                      </div>
                    </div>
                   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



@stop

@section('style')
<style type="text/css">

.fa-eye {
    color: #3598dc !important;
}
.fa-print {
    color: #3598dc !important;
}
.fa-download {
    color: #3598dc !important;
}
    .centerAlignment { text-align: center;}
    #pickList_indentID {text-align:center !important;}
    #pickList_indentType {text-align:center !important;}
    #pickList_indentDate {text-align:center !important;}
    #pickList_Status {text-align:center !important;}
    #pickList_Actions {text-align:center !important;}
    #pickList_qty {text-align:center !important;}
	
    .captionmarg{margin-top:15px;}
    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px; margin-top:15px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}
.SumoSelect > .optWrapper > .options {
    text-align: left;
}
.avoid-clicks {
  pointer-events: none;
}
</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/select2/select2.css')}}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />


<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.core.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.lob.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
function filterIndent() {
    var formData = $('#frm_indent').serialize();

    $("#pickList").igGrid({
        dataSource: "/indents/getOrderIndent?"+formData,
        autoGenerateColumns: false
    });
}

function getNextDay(select_date){
    select_date.setDate(select_date.getDate() + 1);
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth()+1),2)+'/'+zeroPad(setdate.getDate(),2)+'/'+setdate.getFullYear();
    return nextdayDate;
}
function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}
$(document).ready(function () {

    getOrderIndentList();

    $('#fdate').datepicker({
            onSelect: function() {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#tdate').datepicker('option', 'minDate', nextdayDate);        
            }
        });
    $('#tdate').datepicker();
    $("#toggleFilter").click(function () {
        $("#filters").toggle("slow", function () {
        });
    });

   

    $('#fsdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#tsdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#tsdate').datepicker({
        maxDate: 0,
    });

    $.validator.addMethod("DateFormat", function (value, element) {
        if (value != '') {
            return value.match(/^(0[1-9]|1[012])[- //.](0[1-9]|[12][0-9]|3[01])[- //.](19|20)\d\d$/);
        } else {
            return true;
        }
    },
            "Please enter a date in the format mm/dd/yyyy"
            );
    $.validator.addMethod("maxDate", function (value, element) {
        var now = new Date();
        var tomorrow = new Date(now.getTime() + (24 * 60 * 60 * 1000));
        var myDate = new Date(value);
        return this.optional(element) || myDate <= tomorrow;
    },
            "should not be future date"
            );

    $.validator.addMethod("minDate", function (value, element) {
    var fdate = new Date($('#fsdate').val());
    var myDate = new Date(value);
    return this.optional(element) || myDate >= fdate;
  },
        "should not be less than from date"
        );
    $('#stockistExportForm').validate({
    rules: {
        fsdate: {
            required: false,
            DateFormat: true,
            maxDate: true,
        },
        tsdate: {
            required: false,
            DateFormat: true,
            maxDate: true,
            minDate: true,
        },
    },
    submitHandler: function (form) {
        var form = $('#stockistExportForm');
        window.location = form.attr('action') + '?' + form.serialize();
        $('.close').click();
    }
 });
});


     var dateFormat = "dd/mm/yy";
    from = $( "#fromdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          }).on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
      to = $( "#todate" ).datepicker({
            //defaultDate: "+1w",
              dateFormat : dateFormat,
              maxDate:0,
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

     $("#fromdate").keydown(function(e) {
            e.preventDefault();  
        });
        $("#todate").keydown(function(e) {
            e.preventDefault();  
        });




function deleteData(did){
    
    token  = $("#csrf-token").val();
        var bannerdelete = confirm("Are you sure you want to delete ?"), self = $(this);
            if ( bannerdelete == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: {"indent_id" : did},
                type: "POST",
                url: '/indents/deleteindent',
                success: function( data ) {
                 
                var formData = $('#frm_indent').serialize();

             $("#pickList").igGrid({
                                  dataSource: "/indents/getOrderIndent?"+formData,
                                autoGenerateColumns: false
                                 }).igGrid("dataBind");

                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+data+'</div></div>');
                $(".alert-success").fadeOut(20000)
                        
                    }
            });  
        }
}

var button_my_button = "#reset";
$(button_my_button).click(function(){
    $('#supplier').select2("val", "");
});
$('#tag_1').on('hidden.bs.modal', function (e) {
  // do something when this modal window is closed...
      $('#fromdate').val("");
      $('#todate').val("");
});
</script>
@stop
