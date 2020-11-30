@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Sales Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Order Details</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
			<div class="portlet-title">
				<div class="caption uppercase">Order #{{$orderdata->order_code}}&nbsp;&nbsp;{{date('d-m-Y H:i:s',strtotime($orderdata->order_date))}}</div>
				<div class="tools uppercase">&nbsp;</div>
			 </div>
            <div class="portlet-body">
				@include('Orders::navigationTab')
		  </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('userscript')
	@include('Orders::gridJsFile')
  @include('Orders::paymentsScript')
                                          
    <style type="text/css"> .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
.error{color: red;}
.alignRight {
    text-align: right!important;
    padding-right: 102px!important;
 }
</style>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop

<script type="text/javascript">
    
function rollbackList(order_id) {
    $(".loderholder").show();
     $.ajax({
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/salesorders/rollback",
            type: "POST",
            data: {order_id:order_id},
            //dataType: 'json',
             beforeSend: function () {
            $(".loderholder").show();
            },
             success: function (data)
            {
                console.log(data);
                $(".loderholder").hide();
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+JSON.stringify(data)+'</div></div>');
                $(".alert-success").fadeOut(40000);
            },
            error: function(data)
            {
                console.log(data);
            }
    });    
}
</script>
