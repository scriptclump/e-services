@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/index">Orders</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/salesorders/detail/{{$invoicedProdArr[0]->gds_order_id}}">Order Details</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Invoice Detail</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            
            <div class="portlet-title">
				<div class="caption">Invoice #{{$invoicedProdArr[0]->invoice_code}} <span class="hidden-xs">| {{date('d-m-Y H:i:s',strtotime($orderdata->order_date))}}</span></div>

                <div class="pull-right margin-top-10">
    <a target="_blank" class="btn green-meadow" onclick='window.open("/salesorders/printinvoice/{{$invoicedProdArr[0]->gds_invoice_grid_id}}/{{$invoicedProdArr[0]->gds_order_id}}/1", "", "scrollbars=yes,width=1000,height=800");' href="#"><i class="fa fa-print"></i></a>&nbsp;&nbsp;
    <a class="btn green-meadow" href="/salesorders/invoicepdf/{{$invoicedProdArr[0]->gds_invoice_grid_id}}/{{$invoicedProdArr[0]->gds_order_id}}/1"><i class="fa fa-download"></i></a></div>
    
			 </div>
            
           
            @include('Orders::navigationTab')
			
        </div>
    </div>
</div>
@stop
@section('userscript')
  @include('Orders::paymentsScript')                                         
    <style type="text/css"> .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
.error{color: red;}
</style>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
@section('script')
    @include('Orders::gridJsFile')

<script type="text/javascript">
    $(document).ready(function () {

        $("#add_invoice_form").validate({
            rules: {
              invoice_remarks: "required" 
            },
            submitHandler: function (form) {
                $('.loderholder').show();
                var form = $('#add_invoice_form');
                $.ajax({
                    url: form[0].action,
                    type: form[0].method,
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if (data.status == 200) {
                            $('#ajaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
                            window.setTimeout(function () {
                                location.reload()
                            }, 2000);
                        } else {
                            $('#ajaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
                        }
                    },
                    error: function (response) {
			$('.loderholder').hide();	
                        $('#ajaxResponse').html('Unable to saved comment');
                    }
                });
            }
        });

    });
</script>
@stop
