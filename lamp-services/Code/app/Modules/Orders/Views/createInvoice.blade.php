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
                <div class="caption uppercase">Order #{{$orderdata->gds_order_id}} {{date('M d, Y h:i:s',strtotime($orderdata->order_date))}}</div>
                <div class="tools">&nbsp;</div>
            </div>
			@include('Orders::navigationTab')
        </div>
    </div>
</div>
@stop

@section('script')
@include('Orders::gridJsFile')
<script type="text/javascript">
$(document).ready(function () {
           
$("#add_invoice_form").validate({
        rules: {
            invoice_status:{
                required: true
            },
        },
        submitHandler: function(form) {             
            var form = $('#add_invoice_form');
            var order_id = '{{$orderdata->gds_order_id}}';
            $('#btnSubmit').attr('disabled', true);
            $.ajax({
                url: form[0].action,
                type: form[0].method,
                data: form.serialize(),
                dataType: 'json',
                success: function (data) {
                    $('#btnSubmit').removeAttr('disabled');
                    if (data.status == 200) {
                        $('#ajaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
                        window.location.href='/salesorders/invoicedetail/'+data.invoice_grid_id+'/'+order_id;
                    } else {
                        $('#ajaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
                    }
                },
                error: function (response) {
                    $('#btnSubmit').removeAttr('disabled');
                    $('#ajaxResponse').html('Unable to saved comment');
                }
            });            
        }
    });

});
</script>
@stop
