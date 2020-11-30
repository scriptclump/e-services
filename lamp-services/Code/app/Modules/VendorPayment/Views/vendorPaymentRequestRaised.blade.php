@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Payment Request Raised</li>
        </ul>
    </div>
</div>
<div class="row">
    <!-- <div class="col-md-12 col-sm-12 text-right">
        <button type="button" class="btn green-meadow submitRequest">Raise Payment Request</button>
    </div> -->
</div>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Payment Request Raised </div>               
            </div>
            <div class="portlet-body">                
                <div class="row">
                    <div class="col-md-12">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                            <table id="poList"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modalbox for showing the history -->
<div class="modal fade" id="view-history" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>

                </button>
                <h4 class="modal-title" id="myModalLabel"> VIEW HISTORY</h4>
            </div>
            <div class="modal-body">                          
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-body">
                        <div class="tab-pane" id ="append_rows_details">
                            <div id="historyContainer" style="height: 250px;overflow-x:hidden;overflow-y: scroll; margin-right: -15px;">
                            </div>
                        </div>                                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<style type="text/css">
    /*.portlet > .portlet-title { margin-bottom:0px !important;}*/

    .imgborder{border:1px solid #ddd !important;}
    .tabs-left.nav-tabs > li.active > a, .tabs-left.nav-tabs > li.active > a:hover > li.active > a:focus {
        border-radius: 0px !important;  
    }
    .nav>li>a:visited{
        color:red !important;
    }
    tabs.nav>li>a {
        padding-left: 10px !important;
    }
    .note.note-success {
        background-color: #c0edf1 !important;
        border-color: #58d0da !important;
        color: #000 !important;
    }
    hr {
        margin-top:0px !important;
        margin-bottom:10px !important;
    }
    .portlet > .portlet-title {
        border-bottom: 0px !important;
    }

    .SumoSelect > .optWrapper > .options {
    text-align: left;
    }
    .favfont i{font-size:18px !important;}
    .actionss{padding-left: 22px !important;}

    .sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px;}

    .sorting a{ list-style-type:none !important;text-decoration:none !important;font-size: 12px;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#676767 !important;}

    .fa-eye{color:#3598dc !important;}
    .fa-print{color:#3598dc !important;}
    .fa-download{color:#3598dc !important;}
    #poList_poId{padding-left:8px !important;}
    #poList_Supplier{padding-left:6px !important;}
    #poList_Status{padding-left:-3px !important;}
    #poList_fixedBodyContainer{overflow-x: auto!important;}
    .nowrap{
        white-space: nowrap !important;
    }
    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{
        border-spacing: 0px !important;
    }
    #poList_fixedContainerScroller{
         height: 0px !important;
    }
    .centerAlignment { text-align: center;}
    .rightAlignment { text-align: right;}
    th.ui-iggrid-header:nth-child(6), th.ui-iggrid-header:nth-child(7), th.ui-iggrid-header:nth-child(8){
        text-align: right !important;
    } 
.SumoSelect > .optWrapper > .options {
    text-align: left;
}
#poList_pager_label{
    margin-left: -20%;
}   
.submitRequest{
    margin-top: 10px;
    margin-right: 10px;
}

</style>
@stop

@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/VendorPayment/vpscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
/**
 * Submit the PO payment request
 * @param  array   var checkboxes Purchase order ID's
 * @return HTML     Message for successful or fail request
 */
$(".submitRequest").click(function(){
    token  = $("#csrf-token").val(); 
    var checkboxes = document.getElementsByClassName('check_box');
    var poIds = [];
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            poIds.push(checkboxes[i].value);
        }
    }
    var postData = {
        "poIds": poIds
    };
    if (poIds.length === 0) {
        alert('Please select at least one purchase order');
    }else{
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: "/vendor/raise-payment-request",
            type: 'POST',
            data: postData,
            async: false,
            beforeSend: function (xhr) {
                $('.spinnerQueue').show();
              //  $("#poList").igGrid({dataSource: '/vendor/payments'}).igGrid("dataBind"); 
              // console.log('Sending the request' + postData);
            },
            success: function (data) {
               $('.spinnerQueue').hide();
               alert('Payment request raised sucessfully');
               window.refresh();               
            }
        });
    }    
});    

function viewhistory(id){
    $('#view-history').modal('toggle');
    $("#historyContainer").empty();
    token  = $("#csrf-token").val(); 
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/vendor/payment-request-history/'+id,
        success: function( data ) { 
            console.log(data);
            $('#historyContainer').append(data);     
        }        
    });
}

function approveStatus(id){
    $('#paymentRequestModal').modal('toggle');
    token  = $("#csrf-token").val(); 
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/approvalworkflow/approvalHistorygrid/'+id,
        success: function( data ) { 
           
        }      
    });  
}



   $(document).ready(function () {

        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
  
    });   
function filterPO(status) {
    var formData = $('#frm_po').serialize();
    $("#poList").igGrid({
        dataSource: "/po/ajax/" + status + "?" + formData,
        autoGenerateColumns: false
    });
}
function getNextDay(select_date) {
    select_date.setDate(select_date.getDate() + 1);
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth() + 1), 2) + '/' + zeroPad(setdate.getDate(), 2) + '/' + setdate.getFullYear();
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
    vendorPaymentRequestList('{{$filter_status}}');

    $('#fdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#tdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#tdate').datepicker({
        maxDate: 0,
    });
    $('#pofdate,#hsnfdate').datepicker({
        maxDate: 0,
        onSelect: function () {
            var select_date = $(this).datepicker('getDate');
            var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
            $('#potdate').datepicker('option', 'minDate', seldayDate);
        }
    });

    $('#potdate,#hsntdate').datepicker({
        maxDate: 0,
    });
   $('#gstfdate').datepicker({
    maxDate: 0,
    onSelect: function () {
        var select_date = $(this).datepicker('getDate');
        var seldayDate = zeroPad((select_date.getMonth() + 1), 2) + '/' + zeroPad(select_date.getDate(), 2) + '/' + select_date.getFullYear();
        $('#gsttdate').datepicker('option', 'minDate', seldayDate);
    }
});

$('#gsttdate').datepicker({
    maxDate: 0,
});

    $("#toggleFilter").click(function () {
        $("#filters").toggle("slow", function () {
        });
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
        var fdate = new Date($('#fdate').val());
        var myDate = new Date(value);
        return this.optional(element) || myDate >= fdate;
    },
            "should not be less than from date"
            );
    $('#exportPOForm').validate({
        rules: {
            fdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
            },
            tdate: {
                required: true,
                DateFormat: true,
                maxDate: true,
                minDate: true,
            },
            "loc_dc_id[]": {
                required: true,
            },
        },
        submitHandler: function (form) {
            var form = $('#exportPOForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
    $('#POReportForm').validate({
        rules: {
            pofdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
            },
            potdate: {
                required: false,
                DateFormat: true,
                maxDate: true,
                minDate: true,
            },
            "loc_dc_id[]":{
                required: true,
            },
        },
        submitHandler: function (form) {
            var form = $('#POReportForm');
            window.location = form.attr('action') + '?' + form.serialize();
            $('.close').click();
        }
    });
 $('#POGSTReportForm').validate({
  rules: {
    gstfdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
    },
    gsttdate: {
        required: false,
        DateFormat: true,
        maxDate: true,
        minDate: true,
    },
    "loc_dc_id[]":{
        required: true,
    },
},
submitHandler: function (form) {
    var form = $('#POGSTReportForm');
    window.location = form.attr('action') + '?' + form.serialize();
    $('.close').click();
}
});

    $('#importpoform').submit(function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $(this).attr('action');
        $('.spinnerQueue').show();
        $('.close').trigger('click');
        $("#uploadfile").attr("disabled", "disabled");
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            beforeSend: function (xhr) {
                $('.spinnerQueue').show();
                $('.close').trigger('click');
            },
            success: function (data) {
                $('.spinnerQueue').hide();
                $('.close').trigger('click');
                if(data.status == 200){
                    window.open('/'+data.url);
                }else if(data.status == 400){
                    if(data.url !="")
                        window.open('/'+data.url);
                    else
                        alert(data.message);
                }else{
                    alert('Server Error!');
                }
                $("#uploadfile").removeAttr("disabled");
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                $(".alert-success").fadeOut(30000)
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
    $("#importpo").on('hide.bs.modal', function () {
        $('#importpoform')[0].reset();
        $("#uploadfile").removeAttr("disabled");
    });
});
$('a.link').on('click touchend', function(e) {
  var link = $(this).attr('href');
  window.location = link;
});
$('#exportpo').on('hidden.bs.modal', function (e) {
  // do something when this modal window is closed...
      $('#fdate').val("");
      $('#tdate').val("");
});
</script>
@stop
@extends('layouts.footer')
