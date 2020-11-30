@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Picklist</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Sales Orders</li>
        </ul>
    </div>
</div>

<div class="row">
<div class="col-md-12 col-sm-12">
  <div class="portlet light tasks-widget">
	 <div class="portlet-title">
		<div class="caption">SALES ORDERS</div>
                <div class="actions">
                    <a href="/salesorders/downloadOrders" class="btn green-meadow">Export to Excel</a>
                    <?php /*<a href="javascript:void(0);" id="toggleFilter" class="btn green-meadow"><i class="fa fa-filter"></i></a>*/?>
                </div>
	 </div>
	<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

    <div class="portlet-body">
		<?php /*@include('Orders::orderFilter')*/?>
		<div class="row">
			<div class="col-md-12">
				<div class="caption captionmarg">
					<span class="caption-subject bold font-blue uppercase"> Filter By :</span>
					<span class="caption-helper sorting">
						<a href="{{$app['url']->to('/')}}/salesorders/index/open" class="active">Open (<span id="allorders">{{$totOpened}}</span>)</a> &nbsp;&nbsp;
					</span>
				</div>
			</div>
				</div>
		<div class="row">
		<div class="col-md-12">&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table id="orderList" class="table table-striped table-bordered table-hover salesorder_style thline"></table>
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
<style type="text/css">
.centerAlignment { text-align: center;}
/* #orderList_OrderID {text-align:center !important;} */
#orderList_ChannelID {text-align:center !important;}

#orderList_OrderValue, .dataaliright {
    padding-right: 40px;
    text-align: right !important;
}

    .dataaliright{ padding-right:40px!important;}

    .ui-widget-content a{color:#5b9bd1!important;}

/*
    #orderList_ChannelName{text-align:center !important;}
    #orderList_Status {text-align:center !important;}
#orderList_OrderDate {text-align:center !important;}
#orderList_OrderID {text-align:center !important;}

    */
#orderList_Actions {text-align:center !important;}
#orderList_ChannelName{text-align:left!important; padding-left:20px;}

.captionmarg{margin-top:15px;}
.sortingborder{border-bottom:1px solid #eee;border-top:1px solid #eee; padding:10px 0px; margin-top:15px;}

.sorting a{ list-style-type:none !important;text-decoration:none !important;}
.sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting a:active{text-decoration:none !important;}
.active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
.inactive{text-decoration:none !important; color:#ddd !important;}
.ui-iggrid-footer{height: 25px !important; padding-left: 10px !important;}
</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.core.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.lob.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders.js') }}" type="text/javascript"></script>
<script type="text/javascript">
function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}
function getNextDay(select_date){
    select_date.setDate(select_date.getDate() + 1);
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth()+1),2)+'/'+zeroPad(setdate.getDate(),2)+'/'+setdate.getFullYear();
    return nextdayDate;
}
$(document).ready(function() {
	getPicklistOrderList('opened');
	$('#order_fdate').datepicker({
            onSelect: function() {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#order_tdate').datepicker('option', 'minDate', nextdayDate);
            }
        });
	$('#order_tdate').datepicker();
	$('#order_exp_fdate').datepicker({
            onSelect: function() {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#order_exp_tdate').datepicker('option', 'minDate', nextdayDate);
            }
        });
	$('#order_exp_tdate').datepicker();

  $('.generatePL').on('click', function() {

      if($.trim($('#orderList').html())!='') {

        var rows = $("#orderList").igGridSelection("selectedRows");

        var selected = [];

        $.each(rows, function (ux, el) {

          selected.push(el.id);

       });


       if(selected.length>0) {

         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: "/picklist/createAjax",
             type: "POST",
             data: {ids: selected},
             dataType: 'json',
             success: function (response) {
                 $('#ajaxResponse').html(response.message);
             },
             error: function (response) {
                 $('#ajaxResponse').html("{{Lang::get('picklists.errorInputData')}}");
             }
         });


       }


      }

  });

	$( "#toggleFilter" ).click(function() {
	  $( "#filters" ).toggle( "slow", function() {
	  });
	});
});
</script>
@stop
