@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/detail/{{$orderdata->gds_order_id}}">Order Details</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Create Invoice</li>
        </ul>
    </div>
</div>
	
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption"><span class="caption-subject font-dark sbold uppercase">Order #{{$orderdata->gds_order_id}} <span class="hidden-xs">| {{date('M d, Y h:i:s',strtotime($orderdata->order_date))}}</span> | CREATE INVOICE</span> </div>
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

$(document).on("click", "label.edit", function () {
    var txt = $(this).text();
    var sno = $(this).attr('data-sno');
    $(".edit_"+sno).replaceWith("<input style='width: 40px;' class='edit edit edit_"+sno+"' data-sno='"+sno+"'/>");
    $(".edit_"+sno).val(txt);
    $(".edit_"+sno).focus();
});
$(document).on("focusout", "input.edit", function () {
    var txt = $(this).val();
    var sno = $(this).attr('data-sno');
    $(this).replaceWith("<label style='cursor: pointer;' class='edit edit edit_"+sno+"'  data-sno='"+sno+"'></label>");
    $(".edit_"+sno).text(txt);
    $(".edit_shipped_qty_"+sno).val(txt);
});
function getCheckedBox() {
    var checked = false;
    $("input[name='invorderItems[]']").each( function () {
        if($(this).prop('checked') == true){
            checked = true;
            return;
        }
    });
    return checked;
}

            
$("#add_invoice_form").validate({
        rules: {
            order_comment:{
                required: false
            },
            invoice_status:{
                required: false
            },
        },
        submitHandler: function(form) {             
            var form = $('#add_invoice_form');
            var order_id = {{$orderdata->gds_order_id}};
            $.ajax({
                url: form[0].action,
                type: form[0].method,
                data: form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 200) {
                        $('#ajaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
                        window.location.href='/salesorders/invoicedetail/'+data.invoice_grid_id+'/'+order_id;
                    } else {
                        $('#ajaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
                    }
                },
                error: function (response) {
                    $('#ajaxResponse').html('Unable to saved comment');
                }
            });            
        }
    });

});
</script>
@stop
